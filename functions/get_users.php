<?php
require_once '../classes/user.php';

if (!isset($_SESSION)) session_start();

$user = new User($conn);
$data = $user->getAllUsernamesAndEmails();

header('Content-Type: application/json');
echo json_encode($data);
exit();
?>
