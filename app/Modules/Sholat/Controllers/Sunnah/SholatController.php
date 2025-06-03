<?php

namespace Modules\Sholat\Controllers\Sunnah;

use App\Controllers\BasePageController;

class SholatController extends BasePageController
{
    private $data;

    public function __construct()
    {
        parent::__construct();
        $this->model = model('SholatSunnahModel');
        $this->data = model('DataSholatSunnahModel');
    }

    public function get_initial()
    {
        $postData = $this->request->getGetPost();
        $tanggal = $postData['tanggal'];

        $data = $this->model->getAll([
            'id_anggota' => $postData['id_anggota'],
            'tanggal' => $tanggal,
        ]);

        $keys = [];
        foreach ($data as $key => $d) {
            $keys[$d->id_sholat] = $key;
        }

        $sholats = $this->data->findAll();
        // $sholats = [$sholats[0]];
        $datas = [];
        foreach ($sholats as $key => $d) {
            $exist = isset($keys[$d->id]);
            if (!$exist && $d->type != 'main') continue;
            $datas[$d->id] = (object) [
                'ref'           => "dropdown$tanggal$d->nama_sholat",
                'id_sholat'     => $d->id,
                'nama_sholat'   => $d->nama_sholat,
                'do'            => $exist,
                'edit'          => false,
                'rakaat'        => $exist ? $data[$keys[$d->id]]->rakaat : ($d->rakaat == 'even' ? 2 : 1),
                'min'           => $d->rakaat == 'even' ? 2 : 1,
                'optionsRakaat' => $d->rakaat == 'even'
                    ? ['2','4','6'] : ['1','3','5'],
            ];
        }

        return $this->respondCreated($datas);

    }
    
    public function store()
    {
        $data = $this->request->getPost();
        $this->model->transBegin();
        
        $params = [
            'id_anggota' => $data['id_anggota'],
            'tanggal' => $data['tanggal'],
            'id_sholat' => $data['id_sholat'],
        ];

        $this->model->where($params)->delete();
        
        if ($data['insert']) {
            $params['rakaat'] = $data['rakaat'];
            $save = $this->model->insert($params, TRUE);
        }

        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            // var_dump($this->model->error());
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated($data);
        }

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