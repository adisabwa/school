<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Upload extends BaseConfig
{
    public $allowedTypes = 'pdf'; // Allowed file types
    public $maxSize = 5120; // Max size in KB (5MB)
    public $overwrite = true; // Do not overwrite existing files
    public $encryptName = true; // Encrypt the file name (optional)
    public $uploadPath = WRITEPATH . 'uploads/'; // Path to save the uploaded files
}
