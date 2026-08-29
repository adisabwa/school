<?php

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$modelSubscription = model('NotificationSubscriptionModel');
$model = model('NotificationModel');

function saveNotification($data)
{
  // var_dump($model);
  $model->insert($data);
  $id = $model->insertID();
  sendNotifications($model->where('id', $id)->findAll());
  return $id;
}

function sendAllNotifications(){
  sendNotifications($notifications = $model->getAll(whereAnd:['send' => '0']));
}

function sendNotifications($notifications = [])
{
  $modelSubscription = model('NotificationSubscriptionModel');
  $model = model('NotificationModel');

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
    $subscriptions = $modelSubscription->findAll(); // Pastikan method ini me-return data
    
    $subs = [];
    foreach ($subscriptions as $key => $value) {
      $subs[$value->id_guru ?? "minus"] = $value;
    }
    // ... proses queueNotification ...
    $countQueue = 0;
    $queue = [];
    foreach ($notifications as $key => $value) {
      $sub = $subs[$value->id_guru] ?? NULL;
      $queue[$key] = (object)[
        'endpoint'  => $sub->endpoint ?? '',
        'p256dh'  => $sub->p256dh ?? '',
        'auth'  => $sub->auth ?? '',
        'title' => $value->judul,
        'body' => $value->pesan,
        'url' => $value->next_url,
        'id' => $value->id,
        'id_guru' => $value->id_guru,
      ];
      $countQueue++;
    }

    $results = sendNotificationsToUser($queue);
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
      $model->updateBatch($updates,'id');
}

function sendNotificationsToUser($queue = []) {
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
            $results['error'][] = $key;
            echo "❌ Gagal: {$report->getReason()}\n";
            // Cek response detail dari Google/Mozilla
            if ($report->getResponse()) {
                echo "Status Code: " . $report->getResponse()->getStatusCode() . "\n";
                echo "Body: " . $report->getResponse()->getBody()->getContents() . "\n";
            }
          // If expired (410 Gone), delete from DB
            if ($report->getResponse()->getStatusCode() === 410) {
                $modelSubscription->where(['endpoint' => $endpoint])->delete();
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
    //             $id = $modelSubscription->getDataWhere(whereAnd:['endpoint' => $endpoint]);
    //             $modelSubscription->update($id, ['endpoint', ']);
    //         }
    //     }
    // }
}

?>