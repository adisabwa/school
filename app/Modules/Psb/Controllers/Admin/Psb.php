<?php

namespace Modules\Psb\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\PdfBuilder;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class Psb extends BaseController
{
    private $psbModel;
    private $kolomModel;

    public function __construct()
    {
        
        $this->psbModel = model('PsbModel');
        $this->kolomModel = model('KolomModel');
        helper('psb');
    }

    
    public function index()
    {
        $nama = $this->request->getPostGet('nama');
        $nomor = $this->request->getPostGet('nomor');
        $order = implode(",", $this->request->getPostGet('order') ?? []);

        $whereAnd = [
            "nama LIKE '%$nama%'" => NULL,
        ];
        // var_dump($whereAnd);
        $whereOr = [
            "nisn LIKE '%$nomor%'" => NULL,
            "ayah_nik LIKE '%$nomor%'" => NULL,
            "ibu_nik LIKE '%$nomor%'" => NULL,
            "wali_nik LIKE '%$nomor%'" => NULL,
        ];
        $data = $this->psbModel->getAll($whereAnd, $whereOr, $order);

        // var_dump($this->psbModel->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function dashboard()
    {
        $data = $this->psbModel->getSummary();
        $labels = [];
        $datasets = [
            'backgroundColor' => [],
            'data' => [],
        ];
        foreach ($data as $key => $value) {
            $labels[] = setStatusText($value->status);
            $datasets['backgroundColor'][] = setStatusColor($value->status);
            $datasets['data'][] = ($value->jumlah);
        }
        return $this->respondCreated(['labels' => $labels, 'datasets' => [$datasets]]);
    }

    public function status($id, $status)
    {
        $data = ['status' => $status];
        if ($status == '2')
            $data['no_pendaftaran'] = getNomorPendaftaran();
        // var_dump($data);exit;
        $save = $this->psbModel->update($id,$data);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function status_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPostGet('id') ?? -1;
        $status = $this->request->getPostGet('status') ?? 1;
        // var_dump($ids);exit;
        $save = $this->psbModel->update($ids,['status' => $status]);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function delete($id)
    {
        $save = $this->psbModel->delete($id);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function delete_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPostGet('id') ?? -1;
        // var_dump($ids);exit;
        $save = $this->psbModel->delete($ids);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    
    public function download($id)
    {
        $PdfBuilder = new PdfBuilder();

        $data  = $this->psbModel->getData(['p.id' => $id]);
        if ($data->status != '2')
            exit('Data belum diverifikasi');
        
        $html = view('Modules\Psb\dokumens/kartu-pendaftaran', ['content' => $data]);
        // echo $html;exit;
        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    }

    public function download_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPostGet('id') ?? -1;

        $PdfBuilder = new PdfBuilder();

        $data  = $this->psbModel->getAll(['p.id IN ('.implode(',',$ids).')' => NULL, 'status' => '2']);
        $html = '';
        foreach($data as $d) {
            $html .= view('Modules\Psb\dokumens/kartu-pendaftaran', ['content' => $d]);
        }

        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 500]);
    }

}