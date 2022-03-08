<?php

class Chatmessage {
    private $id;
    private $chat_session_id;
    private $moderator_id;
    private $message;
    private $created_at;
    private $conn;

    public $dummyMessages = ['We cant help you with that','I will see what i can do','Can you tell me your full name please?',
                             'Can you tell me your client ID please?','Ah yes i have found the problem, it should be fixed now','Have a good day!'];

    function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($chatsessionId, $moderator_id, $message) {
        $stmt = $this->conn->prepare('INSERT INTO chat_message (chat_session_id, moderator_id, message, created_at) VALUES (?,?,?, NOW())');
        $stmt->bind_param('iis', $chatsessionId, $moderator_id, $message);
        try {
            if($stmt->execute()) {
                return array(
                    'message' => 'chat message created',
                );
            }else {
                throw new Exception('Failed to send message');
            }
        } catch (Exception $e) {
            exit(json_encode(array('error' => $e->getMessage())));
        }
    }

    public function getMessages($chatsessionId) {
        $stmt = $this->conn->prepare('SELECT ms.id, ms.chat_session_id, ms.moderator_id, md.firstname, md.lastname, ms.message, ms.created_at FROM
                                      chat_message as ms
                                      LEFT JOIN chat_moderator as md
                                      on md.id = ms.moderator_id
                                      WHERE chat_session_id = ? ORDER BY created_at asc');
        $stmt->bind_param('i', $chatsessionId);
        $stmt->execute();
        
        return $stmt;
    }

    public function getModeratorId($chatsessionId) {
        $stmt = $this->conn->prepare('SELECT moderator_id FROM chat_message WHERE chat_session_id = ? AND moderator_id IS NOT NULL ORDER BY created_at desc');
        $stmt->bind_param('i', $chatsessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}