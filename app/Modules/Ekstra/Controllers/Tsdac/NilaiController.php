<?php

namespace Modules\Ekstra\Controllers\Tsdac;

use App\Controllers\BaseDataController;

class NilaiController extends BaseDataController
{
    public $currentModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('Modules\Ekstra\Models\Tsdac\NilaiModel');
        $this->currentModel = model('Modules\Ekstra\Models\Tsdac\CurrentMatchModel');
    }

    public function get_match_results()
    {
        $partai = $this->request->getGetPost('partai');
        $results = $this->model->getAll(whereAnd: ['partai >=' => ($partai-1)*10+1, 'partai <=' => $partai*10], order: 'sisi ASC');
        $groups = [];
        foreach ($results as $row) {
            $id_peserta = $row->sisi == 'biru' ? $row->id_peserta : $row->vs_peserta;
            $inv_peserta = $row->sisi == 'biru' ? $row->vs_peserta : $row->id_peserta;
            $inv_sisi = $row->sisi == 'biru' ? 'kuning' : 'biru';
            // if ($row->sisi == 'biru') {
                if (!isset($groups[$id_peserta])) {
                    $groups[$id_peserta] = (object)[
                        "id_peserta_$row->sisi" => $id_peserta,
                        "nama_$row->sisi" => $row->nama,
                        "kelas_$row->sisi" => $row->kelas,
                        "id_peserta_$inv_sisi" => $inv_peserta,
                        "nama_$inv_sisi" => $row->nama_lawan,
                        "kelas_$inv_sisi" => $row->kelas_lawan,
                        'total_nilai_biru' => 0,
                        'total_nilai_kuning' => 0,
                        'match' => [],
                    ];
                }

                $juri = ['1' => [], '2' => [], '3' => [], '4' => []];
                if (!isset($groups[$id_peserta]->match[$row->babak])) {
                    $groups[$id_peserta]->match[$row->babak] = (object)[
                        'babak' => $row->babak,
                        'biru' => $juri,
                        'kuning' => $juri,
                        'nilai_juri_biru' => [0, 0, 0, 0],
                        'nilai_juri_kuning' => [0, 0, 0, 0],
                        'average_biru' => 0,
                        'average_kuning' => 0,
                        'ids_biru' => [],
                        'ids_kuning' => [],
                    ];
                }

                $groups[$id_peserta]->match[$row->babak]->{$row->sisi}[$row->no_juri] = (object)[
                    'total_nilai' => $row->total_nilai,
                    'daftar_nilai' => $row->daftar_nilai,
                ];
                $groups[$id_peserta]->match[$row->babak]->{"ids_$row->sisi"}[] = $row->id;
                $groups[$id_peserta]->match[$row->babak]->{"nilai_juri_$row->sisi"}[($row->no_juri - 1)] = $row->total_nilai;
            // } else {
            //     $groups[$row->vs_peserta]->nama_kuning = $row->nama;
            //     $groups[$row->vs_peserta]->kelas_kuning = $row->kelas;
            //     $groups[$row->vs_peserta]->id_peserta_kuning = $row->id_peserta;
            //     $groups[$row->vs_peserta]->match[$row->babak]->kuning[$row->no_juri] = (object)[
            //         'total_nilai' => $row->total_nilai,
            //         'daftar_nilai' => $row->daftar_nilai,
            //     ];
            //     $groups[$row->vs_peserta]->match[$row->babak]->nilai_juri_kuning[($row->no_juri - 1)] = $row->total_nilai;
            // }
        }

        foreach ($groups as $key => $value) {
            $value->isJuryMismatchBiru = false;
            $value->isJuryMismatchKuning = false;
            $value->showDetails = false;
           foreach ($value->match as $key => $match) {
                $mostNilaiBiru = cari_elemen_terbanyak($match->nilai_juri_biru);
                $mostNilaiKuning = cari_elemen_terbanyak($match->nilai_juri_kuning);
                $match->is_match_biru = array_map(function($n_biru) use ($mostNilaiBiru) {
                    return $n_biru == $mostNilaiBiru;
                }, $match->nilai_juri_biru);
                $match->is_match_kuning = array_map(function($n_kuning) use ($mostNilaiKuning) {
                    return $n_kuning == $mostNilaiKuning;
                }, $match->nilai_juri_kuning);
                $match->average_biru = round(array_average($match->nilai_juri_biru));
                $match->average_kuning = round(array_average($match->nilai_juri_kuning));
                $value->total_nilai_biru += $match->average_biru;
                $value->total_nilai_kuning += $match->average_kuning;
                $value->isJuryMismatchBiru = $value->isJuryMismatchBiru || in_array(false, $match->is_match_biru);
                $value->isJuryMismatchKuning = $value->isJuryMismatchKuning || in_array(false, $match->is_match_kuning);
           }
        }
        return $this->respondCreated($groups);
    }

    public function get_current_match()
    {
        return $this->respondCreated($this->currentModel->getDataWhere());
    }


    public function set_current_match()
    {
        $id = $this->currentModel->getDataWhere()->id ?? NULL;
        
        $this->currentModel->transBegin();
        
        $postData = $this->request->getGetPost();
        $rematch = $postData['rematch'] ?? '1';
        unset($postData['rematch']);

        if ($id) {
            $this->currentModel->update($id, $postData);
        } else {
            $this->currentModel->insert($postData);
        }

        if (($postData['status'] ?? '') == 'run' && $rematch == '0') {
            $this->generate_data($postData);
        }

        if ($this->currentModel->transStatus() === false) {
            $this->currentModel->transRollback();
            return $this->failServerError();
        } else {
            $this->currentModel->transCommit();
            return $this->respondCreated($this->currentModel->getDataWhere());
        }
    }

    public function generate_data($postData)
    {
        $datas = [];
        foreach (range(1, 4) as $key => $no_juri) {
            $datas[] = [
                'sisi' => 'kuning',
                'partai' => $postData['partai'],
                'babak' => $postData['babak'],
                'id_peserta' => $postData['kuning'],
                'vs_peserta' => $postData['biru'],
                'no_juri' => $no_juri,
            ];
            $datas[] = [
                'sisi' => 'biru',
                'partai' => $postData['partai'],
                'babak' => $postData['babak'],
                'id_peserta' => $postData['biru'],
                'vs_peserta' => $postData['kuning'],
                'no_juri' => $no_juri,
            ];
        }
        return $this->model->insertBatch($datas);
    }

    public function summary()
    {
        $this->model->selects[] = "{n}GROUP_CONCAT('.PREFIX_TABLE.'ts_tsdac_penilaian.id) ids, {n}GROUP_CONCAT(total_nilai) list_nilai, {n}SUM(total_nilai) total_nilai";
        $results = $this->model->getAll(groupBy:['id_peserta'], order: 'total_nilai DESC');

        return $this->respondCreated($results);
    }
}
