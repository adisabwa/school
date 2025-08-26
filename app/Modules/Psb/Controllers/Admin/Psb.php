<?php

namespace Modules\Psb\Controllers\Admin;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;

class Psb extends BaseDataController
{
    public function __construct()
    {
        
        $this->model = model('PsbModel');
        helper('psb');
    }

    public function dashboard()
    {
        $data = $this->model->getSummary();
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
        $save = $this->model->update($id,$data);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function download($id)
    {
        $PdfBuilder = new PdfBuilder();

        $data  = $this->model->getData(['p.id' => $id]);
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

        $data  = $this->model->getAll(['id IN ('.implode(',',$ids).')' => NULL, 'status' => '2']);
        $html = '';
        foreach($data as $d) {
            $html .= view('Modules\Psb\dokumens/kartu-pendaftaran', ['content' => $d]);
        }

        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    }

}