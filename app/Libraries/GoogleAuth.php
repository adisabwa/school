<?php

// app/Libraries/GoogleAuth.php

namespace App\Libraries;

use Google_Client;

class GoogleAuth
{
    protected $client;

    public function __construct()
    {
        
        try {
            // Your Google Client logic here
            $this->client = new Google_Client(['client_id' => getenv('VITE_GOOGLE_CLIENT_ID')]);
            
        } catch (\Throwable $e) {
            // This will print the exact reason for the crash
            exit('Google Client Error: ' . $e->getMessage());
        }
    }

    public function verifyToken($idToken)
    {
        $payload = $this->client->verifyIdToken($idToken);
        return $payload ?: false;
    }
}
