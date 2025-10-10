<?php

namespace Modules\Psb\Controllers\Admin;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;

class Psb extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
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
        // var_dump($data);exit;
        $save = $this->model->update($id,$data);

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    
    public function status_many()
    {
        $ids = $this->request->getPostGet('id') ?? -1;
        $status = $this->request->getPostGet('status') ?? 0;
        
        $data = [];
        foreach($ids as $key => $id) {
            $data[] = [
                'id' => $id,
                'status' => $status,
                'no_pendaftaran' => $status == '2' ? getNomorPendaftaran($key) : '',
            ];
        }
        // var_dump($data);
        $save = $this->model->where('status', $status - 1)
                            ->updateBatch($data,'id');
        // var_dump($this->model->getLastQuery());
        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }

    public function download($id)
    {
        $PdfBuilder = new PdfBuilder();

        $data  = $this->model->getData(['p.id' => $id]);        
        $html = view('Modules\Psb\dokumens/kartu-pendaftaran', ['content' => $data]);
        // echo $html;exit;
        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    }

    public function download_many()
    {
        ini_set('max_input_vars', -1);

        $ids = $this->request->getPostGet('id') ?? ['-1'];
        $PdfBuilder = new PdfBuilder();

        $data  = $this->model->getAll(['id IN ('.implode(',',$ids).')' => NULL]);
        $html = '';
        foreach($data as $d) {
            $html .= view('Modules\Psb\dokumens/kartu-pendaftaran', ['content' => $d]);
        }

        $PdfBuilder->generatePdf($html, TRUE, [0, 0, 500, 842]);
    }

}