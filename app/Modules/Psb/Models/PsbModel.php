<?php

namespace Modules\Psb\Models;

use App\Models\BaseModel;

class PsbModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'sch_psb';
        $this->relations = [
            'ayah_penghasilan' => [
                'foreign_key' => 'ayah_penghasilan',
                'table' => 'sch__penghasilan',
                'alias' => 'pel1',
                'type' => 'left',
                'selects' => [
                    'label ayah_peng_label', 
                    'dari ayah_peng_dari', 
                    'hingga ayah_peng_hingga',
                ]
            ],
            'ibu_penghasilan' => [
                'foreign_key' => 'ibu_penghasilan',
                'table' => 'sch__penghasilan',
                'alias' => 'pel2',
                'type' => 'left',
                'selects' => [
                    'label ibu_peng_label', 
                    'dari ibu_peng_dari', 
                    'hingga ibu_peng_hingga',
                ]
            ],
            'wali_penghasilan' => [
                'foreign_key' => 'wali_penghasilan',
                'table' => 'sch__penghasilan',
                'alias' => 'pel3',
                'type' => 'left',
                'selects' => [
                    'label wali_peng_label', 
                    'dari wali_peng_dari', 
                    'hingga wali_peng_hingga',
                ]
            ],
        ];
    }

    public function getSummary()
    {
        return $this->db->table('sch_psb')
                        ->select("status, count(id) jumlah")
                        ->groupBy('status')
                        ->get()
                        ->getResultObject();
    }
}