<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require 'vendor/autoload.php';
require 'functions/Database.php';

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $db;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->db = new Database();
    }

    public function onOpen(ConnectionInterface $conn) {
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParams);

        $conn->userId = isset($queryParams['user_id']) ? intval($queryParams['user_id']) : null;
        $this->clients->attach($conn);

        error_log("New connection: " . $conn->resourceId . " (User ID: " . $conn->userId . ")");
    }
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (!isset($data['receiver_id'], $data['message'])) {
            return;
        }

        $this->db->execQuery('INSERT INTO chat_messages (sender_id, receiver_id, message)  VALUES (' . intval($from->userId) . ', ' . intval($data['receiver_id']) . ', "' . htmlspecialchars($data['message']) . '")
        ');

        $stmt = $this->db->pdo->prepare("SELECT firstname || ' ' || lastname AS sender_name FROM users WHERE id = ?");
        $stmt->execute([$from->userId]);
        $senderName = $stmt->fetchColumn();

    foreach ($this->clients as $client) {
        if (
            ($client->userId == $data['receiver_id'] && $from->userId != null) || 
            ($client->userId == $from->userId && $data['receiver_id'] != null)
        ) {
            $client->send(json_encode([
                'sender_id' => $from->userId,
                'sender_name' => $senderName ?: 'Unknown',
                'receiver_id' => $data['receiver_id'],
                'message' => $data['message'],
                'sent_at' => date('Y-m-d H:i:s')
            ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        error_log("Connection closed: " . $conn->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        error_log("Error: " . $e->getMessage());
        $conn->close();
    }
}

$port = 8080;
$server = Ratchet\Server\IoServer::factory(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new Chat()
        )
    ),
    $port
);

error_log("WebSocket server started on port " . $port);
$server->run();