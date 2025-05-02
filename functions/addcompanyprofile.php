<?php
require_once('../classes/company.php');

header('Content-Type: application/json');

function respond($status, $message, $redirect = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company = new Company($conn);

    $success = $company->addCompanyProfile(
        $_POST['name'],
        $_POST['address'],
        $_POST['contact_number'],
        $_POST['email'],
        $_POST['rep_position'],
        $_POST['rep_name'],
        $_POST['rep_number'],
        $_POST['companyID']
    );

    if ($success === true) {
        respond('success', 'Company profile added successfully.', '../company/dashboard.php');
    } else {
        respond('error', $success);
    }
}

respond('error', 'Invalid request method.', null, 400);
?>
