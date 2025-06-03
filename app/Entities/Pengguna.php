<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Pengguna extends Entity
{
    protected $attributes = [
        'id'         => null,
        'nama' => null, // Represents a username
        'email'       => null,
        'username'   => null,
        'password'   => null,
        'role'   => null,
        'created_at' => null,
    ];

    
}