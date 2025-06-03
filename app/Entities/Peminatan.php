<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Peminatan extends Entity
{
    protected $attributes = [
        'id'         => null,
        'id_prodi' => null, // Represents a username
        'peminatan'       => null,
        'kelas'   => null,
        'created_at' => null,
    ];

    
}