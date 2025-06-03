<?php

$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$pos = strpos($uri, 'index.php');
// if ($pos) {
//     $uri = str_replace('index.php', 'public/index.php', $uri);
//     $new_site = $uri;
//     header('location: '.$new_site);
//     die();
// } else {
//     $new_site = $uri.'index.php';
//     // var_dump($new_site);exit;
//     header('location: '.$new_site);
//     die();

// }
