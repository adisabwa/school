<?php

// app/Libraries/GoogleAuth.php

namespace App\Libraries;

use Google_Client;

class GoogleAuth
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client(['client_id' => getenv('VITE_GOOGLE_CLIENT_ID')]);
    }

    public function verifyToken($idToken)
    {
        $payload = $this->client->verifyIdToken($idToken);
        return $payload ?: false;
    }
}
