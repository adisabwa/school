<?php

namespace Modules\Quran\Controllers\Hafal;

use App\Controllers\BasePageController;
use App\Libraries\PdfBuilder;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class QuranController extends BasePageController
{
    protected $quran;
    protected $data;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('QuranHafalModel');
        $this->quran = model('DataSuratQuranModel');
        $this->data = model('QuranHafalDataModel');
    }

    public function get()
    {
        $id = $this->request->getGet('id');
        $data = $this->model->find($id);
        if (!empty($data)) {
            $data->parentSelect = (object)[
                'surat_mulai-ayat_mulai' => $data->surat_mulai,
                'surat_selesai-ayat_selesai' => $data->surat_selesai,
            ];
            $data->{'surat_mulai-ayat_mulai'} = "$data->surat_mulai-$data->ayat_mulai";
            $data->{'surat_selesai-ayat_selesai'} = "$data->surat_selesai-$data->ayat_selesai";
        }
        return $this->respondCreated($data);
    }
    
    public function save()
    {
        $posted_data = $this->request->getPost();
        $countAyat = $this->quran->countAyat($posted_data['surat_mulai'], $posted_data['ayat_mulai'],     $posted_data['surat_selesai'], $posted_data['ayat_selesai']);
        $posted_data['total_ayat'] = $countAyat;
        $this->request->setGlobal('post', $posted_data);
        $this->save_data();
        return $this->store();
    }

    public function save_data()
    {
        $posted_data = $this->request->getPost();
        $surat_mulai = $posted_data['surat_mulai'];
        $ayat_mulai = $posted_data['ayat_mulai'];
        $surat_selesai = $posted_data['surat_selesai'];
        $ayat_selesai = $posted_data['ayat_selesai'];
        $id_anggota = userdata()->id_anggota;
        $data = $this->data->get_last_data($id_anggota, $surat_mulai, $ayat_mulai);
        if (!empty($data)) {
            $data->surat_selesai = $surat_selesai;
            $data->ayat_selesai = $ayat_selesai;
            $save = $this->data->update($data->id, $data);
        } else {
            $save = $this->data->insert([
                'id_anggota' => $id_anggota,
                'surat_mulai' => $surat_mulai,
                'ayat_mulai' => $ayat_mulai,
                'ayat_selesai' => $ayat_selesai,
                'surat_selesai' => $surat_selesai,
            ]);
        }
        return $this->merge_data();
    }

    public function merge_data()
    {
        $id_anggota = userdata()->id_anggota;
        do {
            $data = $this->data->get_merge($id_anggota);
            foreach ($data as $key => $value) {
                $this->data->update($value->id, [
                    'surat_mulai' => $value->surat_mulai_2,
                    'ayat_mulai' => $value->ayat_mulai_2,
                ]);
                $this->data->delete($value->id_2);
            }
        } while (!empty($data));
        // var_dump($this->data->getLastQuery());
        // return $this->respondCreated($data);    
        return;
    }

    public function get_last()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $last = $this->model->get_last($id_anggota);
        $juz = $this->data->get_juz($id_anggota);

        return $this->respondCreated(compact('last','juz'));
    }

    public function dashboard()
    {
        return $this->createChart();
    }

    public function get_before()
    {
        return parent::get_before();
    }
}