<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\ChatServer;

// 1. Load thư viện Composer (đi ra ngoài thư mục libraries để tìm vendor)
require dirname(__DIR__) . '/vendor/autoload.php';

// 2. Load file Logic vừa tạo ở Bước 1
require __DIR__ . '/ChatServer.php';

// 3. Cấu hình chạy ở cổng 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    8081
);

$server->run();