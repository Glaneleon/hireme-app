<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/user.php';
require_once '../classes/company.php';

header('Content-Type: application/json');

function respond($status, $message, $redirect = null) {
    http_response_code(200);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = new User($conn);
    $username = $_SESSION['username'] ?? null;
    $userID = $user->getUserDetails($username)?->getUserID();

    if (!$userID) {
        respond('error', 'Error. User ID not found.');
    }

    $company = new Company($conn);

    if ($_POST["type"] === "profile") {
        $result = $company->addCompanyDetails(
            $_POST['companyID'],
            $_POST['name'],
            $_POST['address'],
            $_POST['contact_number'],
            $_POST['email'],
            $_POST['rep_position'],
            $_POST['rep_name'],
            $_POST['rep_number']
        );

        if ($result === true) {
            respond('success', 'Company registered. You will be redirected shortly.', '../company/dashboard.php');
        } else {
            respond('error', is_string($result) ? $result : 'Error. Failed to register.');
        }
    } else {
        $success = $company->addCompany(
            $_POST['companyName'],
            $_POST['companyDescription'],
            $_POST['companyAddress'],
            $userID
        );

        if ($success) {
            respond('success', 'First step completed. You will be redirected shortly.', '../company/registration.php');
        } else {
            respond('error', 'Company registration failed.');
        }
    }
}
?>
