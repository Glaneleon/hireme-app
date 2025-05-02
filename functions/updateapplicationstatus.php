<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once '../classes/jobseekerapplication.php';

$jobapplication = new JobSeekerApplication($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobName = $_POST['jobName'];
    $companyName = $_POST['companyName'];
    $jobID = $_POST['jobID'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $applicationID = $_POST['applicationID'];
    $statuserr = 0;
    $applicationIDerr = 0;
    $reason = $_POST['reason'] === '' ? null : $_POST['reason'];

    if(empty($status) || ($status !== "Rejected" && $status !== "Verified")) {
        $statuserr = 1;

        if(empty($status)){
            $response = array('status' => 'error', 'message' => 'Status is missing. Please try again.', 'redirect' => '');
        }
        else{
            $response = array('status' => 'error', 'message' => 'Status is invalid. Please try again.', 'redirect' => '');
        }
    }
    
    if(empty($applicationID) || !is_numeric($applicationID)) {
        $applicationIDerr = 1;
        $response = array('status' => 'error', 'message' => 'Application ID is invalid. Please try again.', 'redirect' => '');
    }

    if($statuserr == 0 && $applicationIDerr == 0) {
        if($jobapplication->changeJobApplicationStatus($status, $applicationID, $reason)) {
        
                $subject = "Application Status Update";
                $texthere = "Dear Applicant,\n\nYour application status has just been updated to: $status.\n" .
                    ($status === 'Verified'
                        ? "Please wait for a confirmation for the date of your interview.\n\nJob: $jobName\nCompany: $companyName"
                        : "We regret to inform you that your application has been unsuccessful.\n\nJob: $jobName\nCompany: $companyName\nReason: " . ucfirst($reason)) .
                    "\n\nBest regards,\nHireMe-App Team";
                $message = $jobapplication->createEmailTemplate($subject, $texthere);
            if($jobapplication->sendEmailNotification($email, $subject, $message)) {
                if($status=='Verified'){
                    $response = array('status' => 'success', 'message' => 'Successfully verified.', 'redirect' => './candidates.php');
                }elseif($status=='Rejected'){
                    $response = array('status' => 'success', 'message' => 'Successfully rejected.', 'redirect' => './jobs.php');
                }
            } else {
                $response = array('status' => 'error', 'message' => 'Failed to update status.', 'redirect' => '');
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to update status.', 'redirect' => '');
        }
    }
} else {
    $response = array('status' => 'error', 'message' => 'Invalid request method.', 'redirect' => '');
}

echo json_encode($response);
?>
