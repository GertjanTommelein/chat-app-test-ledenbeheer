<?php
require_once('./db.php');
require_once('./models/Chatsession.php');
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

if($_GET['q'] === 'chatsession') {
    
    if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email'])) {
        $chatSession = new Chatsession($conn);
        //check if a session already exists
        if($chatSession->read($_POST['firstname'], $_POST['lastname'], $_POST['email'])) {
            exit(json_encode($chatSession->getSession()));
        }else {
            // create new session
            $chatSession->create($_POST['firstname'], $_POST['lastname'], $_POST['email']);
            exit(json_encode($chatSession->read($_POST['firstname'], $_POST['lastname'], $_POST['email'])));
        }
    }
}
