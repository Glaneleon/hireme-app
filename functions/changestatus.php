<?php
require_once '../classes/hiree.php';
require_once '../classes/hireedetails.php';
require_once '../classes/jobseekerapplication.php';

$hiree = new Hiree($conn);
$application = new JobSeekerApplication($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $status = $_POST["status"];
    $applicationID = filter_var($_POST['applicationID'], FILTER_SANITIZE_NUMBER_INT);

    if (!in_array($status, ['Hired', 'Rejected']) || !is_numeric($applicationID)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status or application ID.']);
        exit();
    }

    $jobID = $_POST['jobID'];
    $jobName = $_POST['jobName'];
    $userID = $_POST['userID'];
    $fullName = $_POST['fullName'];
    $companyID = $_POST['companyID'];
    $companyName = $_POST['companyName'];
    $dateHired = $_POST['dateHired'];
    $reason = $_POST['reason'] === '' ? null : $_POST['reason'];
    $formattedDate = date('l, F j, Y \a\t g:i A', strtotime($dateHired));

    if ($status === 'Hired') {
        $hireeDetails = new HireeDetails(null, $fullName, $jobName, $companyName, $userID, $jobID, $companyID, $applicationID, $dateHired, $reason);
        $changestat = $application->changeJobApplicationStatus('Hired', null, $applicationID);
        if ($changestat) {
            $subject = "Application Status Update";
            $texthere = "Dear Applicant, \n\nCongratulations! You have been hired for the position.\n\nJob: $jobName\nCompany: $companyName\nDate Hired: $formattedDate\n\nBest regards,\nHireMeApp Team";
            $message = $application->createEmailTemplate($subject, $texthere);
            $emailcheck = $application->sendEmailNotification($email, $subject, $message);
            // echo "<script>console.log(" . json_encode($changestat) . ");</script>";
            echo json_encode(['status' => 'success', 'message' => 'Hiree recorded successfully. '.$emailcheck]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to record hiree.']);
        }
    } elseif ($status === 'Rejected') {
        if ($application->changeJobApplicationStatus('Rejected', $reason, $applicationID)) {
            $subject = "Application Status Update";
            $texthere = "Dear Applicant, \n\nWe regret to inform you that your application has been rejected.\n\nJob: $jobName\nCompany: $companyName\nReason: $reason\n\nBest regards,\nHireMeApp Team";
            $message = $application->createEmailTemplate($subject, $texthere);
            $emailcheck = $application->sendEmailNotification($email, $subject, $message);
            echo json_encode(['status' => 'success', 'message' => 'Application rejected. '.$emailcheck]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update application status.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unknown error occurred. Please contact support.']);
    }
}
?>
