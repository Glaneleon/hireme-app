<?php
if (!isset($_SESSION)) {
    session_start();
} else {
    session_destroy();
    session_start();
}

require_once '../classes/user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $token = null;

    $user = new User($conn);
    $register = $user->addUser($username, $password, $email, $role, $token);

    if($role){
        if ($register === true) {
            $response = array('status' => 'success', 'message' => 'Successfully registered. You will be redirected to the login page shortly.', 'redirect' => './login.php');
        } elseif ($register !== true) {
            $response = array('status' => 'error', 'message' => $register, 'redirect' => './register.php');
        } else {
            $response = array('status' => 'error', 'message' => 'There was an error. Please try again.', 'redirect' => './register.php');
        }        
    } else {
        $response = array('status' => 'error', 'message' => 'Role is required.', 'redirect' => './register.php');
    }    

    http_response_code(200);
    echo json_encode($response);
    exit();
}
    
    else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'message' => 'Invalid request.'));
        exit();
    }
?>
