<?php

namespace App\Controllers;

use App\Controllers\BaseDataController;
use App\Libraries\NotificationManager;
use stdClass;

class NotificationController extends BaseDataController
{
  public $modelSubscription;

  public function __construct()
  {      
    $this->model = model('NotificationModel');
    $this->modelSubscription = model('NotificationSubscriptionModel');
  }

  public function saveSubscription()
  {
      $json = $this->request->getJSON();
      $agent = $this->request->getUserAgent();

      // Deteksi browser dan platform (Contoh: Chrome on Windows 10)
      $deviceInfo = $agent->getBrowser() . ' on ' . $agent->getPlatform();

      $data = [
          'endpoint'    => $json->endpoint,
          'p256dh'      => $json->keys->p256dh,
          'auth'        => $json->keys->auth,
          'device_type' => $deviceInfo, 
          'id_guru'     => userdata()->id ?? null, 
      ];
      
      // Gunakan replace(): Jika endpoint sudah ada, maka akan diupdate (Upsert)
      // Ini mencegah duplikasi jika user melakukan subscribe ulang di browser yang sama
      $this->modelSubscription->replace($data);

      return $this->respondCreated(['status' => 'Subscription saved', 'device' => $deviceInfo]);
  }

  public function triggerNotifications() {
    $notif = new NotificationManager;
    $notif->sendAllNotifications();
  }

  public function test()
  {
    $notif = new NotificationManager;
    $notif->sendNotifications([
      (object)[
        'judul' =>'Percobaan',
        'pesan' => 'in pesan coba',
        'next_url' => '/ppmda',
        'id' => '-1',
        'id_guru' => '2',
      ]
    ]);

  }
}