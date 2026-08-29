<?php


namespace App\Libraries;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\NotificationModel;
use App\Models\NotificationSubscriptionModel;
use Config\Database;

class NotificationManager {

    public $model;
    public $modelSubscription;

    public function __construct()
    {
        $this->model = new NotificationModel;
        $this->modelSubscription = new NotificationSubscriptionModel;
    }

  public function saveNotification($data)
  {
    // var_dump($this->model);
    $this->model->insert($data);
    $id = $this->model->insertID();
    $this->sendNotifications($this->model->where('id', $id)->findAll());
    return $id;
  }

  public function sendAllNotifications(){
    $this->sendNotifications($notifications = $this->model->getAll(whereAnd:['send' => '0']));
  }

  public function sendNotifications($notifications = [])
  {
    // var_dump($notifications);
      $auth = [
          'VAPID' => [
              'subject'    => 'mailto:adi.sabwa@gmail.com',
              'publicKey'  => 'BPr-rw6R1GDz3JqCVU-XAzkJ_az35SRTQTSgelZ-NodnPgnkrDFm87AoQAfG7AEZ1YpPngL_dm3ZEj5xCuND1OU',
              'privateKey' => 'TyEgZIdjqme2KhPN1E44I4dJxfDxTCdywCBhKL6aQLQ',
          ],
      ];

      // 2. Buat objek WebPush tanpa embel-embel path (karena sudah ada di C:\usr\local\ssl)
      $webPush = new \Minishlink\WebPush\WebPush($auth);

      // 3. Ambil data dari database
      $subscriptions = $this->modelSubscription->findAll(); // Pastikan method ini me-return data
      
      $subs = [];
      // var_dump($subscriptions);
      foreach ($subscriptions as $key => $value) {
        $subs[$value->id_guru ?? "minus"] = $value;
      }
      // ... proses queueNotification ...
      $countQueue = 0;
      $queue = [];

      var_dump($subs);
      foreach ($notifications as $key => $value) {
        $sub = $subs[$value->id_guru] ?? NULL;

        // 1. Clean the string
        $cleanEndpoint = trim($sub->endpoint);

        // 2. Validate format before sending
        if (!filter_var($cleanEndpoint, FILTER_VALIDATE_URL)) {
            throw new Exception("Malformed URL detected: " . $cleanEndpoint);
        }

        $queue[$key] = (object)[
          'endpoint'  => trim($cleanEndpoint ?? ''),
          'p256dh'  => trim($sub->p256dh ?? ''),
          'auth'  => trim($sub->auth ?? ''),
          'title' => $value->judul,
          'body' => $value->pesan,
          'url' => $value->next_url,
          'id' => $value->id,
          'id_guru' => $value->id_guru,
        ];
        $countQueue++;
      }

      $results = $this->sendNotificationsToUser($queue);
      if (!is_array($results))
        return;
      $updates = [];
      foreach ($results['finish'] as $key => $value) {
        $data = $queue[$value] ?? NULL;
        if ($data) {
          $updates[] = [
            'id' => $data->id, 
            'send' => '1',
            'send_at' => date('Y-m-d H:i:00'),
          ];
        }
      }

      if (!empty($updates))
        $this->model->updateBatch($updates,'id');
  }

  public function sendNotificationsToUser($queue = []) {
    $auth = [
          'VAPID' => [
              'subject'    => 'mailto:adi.sabwa@gmail.com',
              'publicKey'  => 'BPr-rw6R1GDz3JqCVU-XAzkJ_az35SRTQTSgelZ-NodnPgnkrDFm87AoQAfG7AEZ1YpPngL_dm3ZEj5xCuND1OU',
              'privateKey' => 'TyEgZIdjqme2KhPN1E44I4dJxfDxTCdywCBhKL6aQLQ',
          ],
      ];

      // 2. Buat objek WebPush tanpa embel-embel path (karena sudah ada di C:\usr\local\ssl)
      $webPush = new \Minishlink\WebPush\WebPush($auth);

      if (empty($queue)) {
          return "Gagal: Database kosong, tidak ada yang dikirim.";
      }

      // ... proses queueNotification ...
      $countQueue = 0;
      $passKey = [];
      foreach ($queue as $key => $value) {
        $webPush->queueNotification(
            \Minishlink\WebPush\Subscription::create([
                'endpoint' => $value->endpoint,
                'keys'     => [
                    'p256dh' => $value->p256dh, 
                    'auth'   => $value->auth
                ],
            ]),
            json_encode([
                'title' => $value->title,
                'body'  => $value->body,
                'url'   => $value->url,
            ]),
            ['headers' => ['Urgency' => 'high']]
        );
        $countQueue++;
      }

      try {
        $results = ['finish' => [], 'error' => []];
          var_dump($countQueue, 'trying');
          foreach ($webPush->flush() as $key => $report) {
            $endpoint = $report->getEndpoint();
            if (!$report->isSuccess()) {
              $results['error'][] = $key ;
              echo "❌ Gagal: {$report->getReason()}\n";
              // Cek response detail dari Google/Mozilla
              if ($report->getResponse()) {
                  echo "Status Code: " . $report->getResponse()->getStatusCode() . "\n";
                  echo "Body: " . $report->getResponse()->getBody()->getContents() . "\n";
                // If expired (410 Gone), delete from DB
                if ($report->getResponse()->getStatusCode() === 410) {
                    $this->modelSubscription->where(['endpoint' => $endpoint])->delete();
                }
              }
            } else {
              $results['finish'][] = $key;
              echo "✅ Terkirim ke: {$endpoint}\n";
            }
          } 
        return $results;
      } catch (\ErrorException $e) {
          // var_dump($e);
          // Jika masih error, tampilkan detailnya di sini
          return $e->getMessage();
      }
      // // Send all queued notifications
      // foreach ($webPush->flush() as $report) {
      //     if (!$report->isSuccess()) {
      //         // If expired (410 Gone), delete from DB
      //         if ($report->getResponse()->getStatusCode() === 410) {
      //             $endpoint = $report->getEndpoint();
      //             $id = $this->modelSubscription->getDataWhere(whereAnd:['endpoint' => $endpoint]);
      //             $this->modelSubscription->update($id, ['endpoint', ']);
      //         }
      //     }
      // }
  }
}
?>