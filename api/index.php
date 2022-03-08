<?php
require_once('./db.php');
require_once('./models/Chatsession.php');
require_once('./models/Chatmessage.php');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['q'])) die('No specific API passed');


// This is an example to list all chat moderators
if ($_GET['q'] === 'moderators') {
    $result = $conn->query('SELECT * FROM chat_moderator');
    if ($result) {
        $moderators = [];
        while($data = $result->fetch_assoc()) {
            $moderators[] = $data;
        }
        exit(json_encode($moderators));
    } else {
        print_r(mysqli_error($conn));
    }
}
// Retrieve or create a chat session
if($_GET['q'] === 'chatsession') {
    
    if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email'])) {
        $chatSession = new Chatsession($conn);
        //check if a session already exists
        if($chatSession->read($_POST['firstname'], $_POST['lastname'], $_POST['email'])) {
            exit(json_encode($chatSession->getSession()));
        }else {
            // create new session
            $chatSession->create($_POST['firstname'], $_POST['lastname'], $_POST['email']);
            $chatSession->read($_POST['firstname'], $_POST['lastname'], $_POST['email']);
            $sessionId = $chatSession->getId();
            // assign random moderator
            $moderatorId = mt_rand(1,2);
            $chatMessage = new Chatmessage($conn);
            // create first response
            $chatMessage->create($sessionId,$moderatorId,'Hi there, how can we help you?');
            exit(json_encode($chatSession->read($_POST['firstname'], $_POST['lastname'], $_POST['email'])));
        }
    }
    
}
// send a message
if($_GET['q'] === 'sendmessage') {
    if(!empty($_POST['message']) && !empty($_POST['sessionId'])) {
        $chatMessage = new Chatmessage($conn);
        
        // User message
        $chatMessage->create($_POST['sessionId'], null,$_POST['message']);
        // Moderator response message
        $chatMessage->create($_POST['sessionId'],$chatMessage->getModeratorId($_POST['sessionId'])['moderator_id'],
        $chatMessage->dummyMessages[mt_rand(0,count($chatMessage->dummyMessages) -1)]);
        exit(json_encode(array('message' => 'message has been sent')));
    }
}
// Get message history
if($_GET['q'] === 'getmessages') {
    
        $chatMessage = new Chatmessage($conn);
        $result = $chatMessage->getMessages($_GET['sessionId'])->get_result();
        
        if($result->num_rows > 0) {
            
            $messages_arr = array();
            while($row = $result->fetch_assoc()) {
                $message_item = array(
                    'id' => $row['id'],
                    'chat_session_id' => $row['chat_session_id'],
                    'moderator_id' => $row['moderator_id'],
                    'firstname' => $row['firstname'],
                    'lastname' => $row['lastname'],
                    'message' => $row['message'],
                    'created_at' => $row['created_at']
                );
                array_push($messages_arr, $message_item);
            }
           
           exit(json_encode($messages_arr));
        }else {
            return false;
        }
}