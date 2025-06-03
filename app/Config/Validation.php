<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;
use App\Validation\MyRules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        MyRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    // Custom upload validation rules for files
    public $uploadFile = [
        'file' => [
            'label' => 'File',
            'rules' => 'uploaded[file]|max_size[file,2048]|mime_in[file,image/jpg,image/jpeg,image/png,application/pdf]',
            'errors' => [
                'uploaded' => 'Anda harus memilih file yang akan diupload.',
                'max_size' => 'Ukuran file tidak boleh lebih dari 2MB.',
                'mime_in'  => 'Hanya File JPG, JPEG, PNG atau PDF yang dapat diupload.',
            ],
        ],
    ];
}
