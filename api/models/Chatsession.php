<?php 

class Chatsession {
    private $id;
    private $firstname;
    private $lastname;
    private $email;
    private $created_at;
    private $conn;

    function __construct($conn) {
        $this->conn = $conn;
    }

    public function getSession() {
        return array
        (
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'created_at' => $this->created_at,
        );
    }

    public function create($firstname, $lastname, $email) {
        $stmt = $this->conn->prepare('INSERT INTO chat_sessions (firstname, lastname, email, created_at) VALUES (?, ?, ?, NOW()');
        $stmt->bind_param('sss', $firstname, $lastname, $email);
        try {
            if($stmt->execute()) {

            }else {
                throw new Exception('Failed to create chat session');
            }
        } catch (\Exception $e) {
            exit(json_encode(array('error' => $e->getMessage())));
        }
    }

    public function read($firstname, $lastname, $email) {
        $stmt = $this->conn->prepare('SELECT * FROM chat_sessions WHERE firstname = ? AND lastname = ? AND email = ?');
        $stmt->bind_param('sss', $firstname, $lastname, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if($row) {
            // Session found
            $this->id = $row['id'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->email = $row['email'];
            $this->created_at = $row['created_at'];
        }else {
            // Session not found
            return false;
        }
    }
}