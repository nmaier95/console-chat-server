<?php
declare(strict_types=1);
require_once(__DIR__ . '/../vendor/autoload.php');


use Chat\ChatHandler;

$address = '0.0.0.0';
$port = 12345;

//// Create WebSocket.
//$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
//socket_bind($socket, $address, $port);
//socket_listen($socket);
//$client = socket_accept($socket);
//
//// Send WebSocket handshake headers.
//$request = socket_read($client, 5000);
//preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
//$key = base64_encode(pack(
//    'H*',
//    sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
//));
//$headers = "HTTP/1.1 101 Switching Protocols\r\n";
//$headers .= "Upgrade: websocket\r\n";
//$headers .= "Connection: Upgrade\r\n";
//$headers .= "Sec-WebSocket-Version: 13\r\n";
//$headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
//socket_write($client, $headers, strlen($headers));







$chatHandler = new ChatHandler();

$socketResource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socketResource, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socketResource, $address, $port);
socket_listen($socketResource);

$null = NULL;
$clientSocketArray = array($socketResource);
while (true) {
    $newSocketArray = $clientSocketArray;
    socket_select($newSocketArray, $null, $null, 0, 10);

    if (in_array($socketResource, $newSocketArray)) {
        $newSocket = socket_accept($socketResource);
        $clientSocketArray[] = $newSocket;

        $header = socket_read($newSocket, 1024);
        $chatHandler->doHandshake($header, $newSocket, $address, $port);

        socket_getpeername($newSocket, $client_ip_address);
//        var_dump($client_ip_address);
        $connectionACK = $chatHandler->newConnectionACK($client_ip_address);

        $chatHandler->send($connectionACK);

        $newSocketIndex = array_search($socketResource, $newSocketArray);
        unset($newSocketArray[$newSocketIndex]);
    }

    foreach ($newSocketArray as $newSocketArrayResource) {
        while(socket_recv($newSocketArrayResource, $socketData, 2048, 0) >= 1){
            $socketMessage = $chatHandler->unseal($socketData);
//            $messageObj = json_decode($socketMessage);

            $chat_box_message = $chatHandler->createChatBoxMessage('', $socketMessage);
            $chatHandler->send($chat_box_message);
            break 2;
        }



        $socketData = @socket_read($newSocketArrayResource, 1024, PHP_NORMAL_READ);
        if ($socketData === false) {
            socket_getpeername($newSocketArrayResource, $client_ip_address);
            $connectionACK = $chatHandler->connectionDisconnectACK($client_ip_address);
            $chatHandler->send($connectionACK);
            $newSocketIndex = array_search($newSocketArrayResource, $clientSocketArray);
            unset($clientSocketArray[$newSocketIndex]);
        }
    }
}
socket_close($socketResource);


