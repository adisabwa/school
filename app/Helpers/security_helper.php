<?php
use Config\Services;
$key = config('Encryption')->key;
// app/Helpers/security_helper.php
function encrypt_id($id)
  {
    return $id;
      if (empty($id)) return null;
      
      try {
          $encrypter = Services::encrypter();
          // Menggunakan bin2hex agar hasilnya berupa string alphanumeric yang aman untuk URL
          return bin2hex($encrypter->encrypt((string)$id));
        // return hash_hmac('sha256', (strin g)$id, $key);
      } catch (\Exception $e) {
        // var_dump($e);
          return null;
      }
  }

function decrypt_id($hash) {
    if (empty($hash) || is_numeric($hash)) return $hash;
    try {
        $encrypter = \Config\Services::encrypter();
        return $encrypter->decrypt(hex2bin($hash));
    } catch (\Exception $e) {
        return null; // Token tidak valid
    }
}
