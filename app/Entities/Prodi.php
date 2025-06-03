<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Prodi extends Entity
{
    protected $attributes = [
        'id'         => null,
        'nama_prodi' => null, // Represents a username
        'kode'       => null,
        'jenjang'   => null,
        'created_at' => null,
    ];

    
}