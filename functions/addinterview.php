<?php
require_once '../classes/interview.php';
require_once '../classes/jobseekerapplication.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $interview = new Interview($conn);
    $jobseekerapplication = new JobSeekerApplication($conn);

    $jobName = $_POST['jobName'];
    $companyName = $_POST['companyName'];
    $email = $_POST['email'];
    $jobID = $_POST['jobID'];
    $jobSeekerApplicationID = $_POST['jobSeekerApplicationID'];
    $interviewDate = $_POST['interviewDate'];
    $formattedDate = date('l, F j, Y \a\t g:i A', strtotime($interviewDate));
    $dateMade = date('Y-m-d H:i:s');
    $subject = "Interview Scheduled";
    $texthere = "Dear Applicant, \n\nWe are pleased to inform you that an interview has been scheduled for the position you applied for. \n\nJob: ".$jobName."\nCompany: ".$companyName."\nWhen: ".$formattedDate."\n\nBest regards,\nHireMeApp Team";

    $interviewDetails = new InterviewDetails(null, $jobID, $jobSeekerApplicationID, $interviewDate, $dateMade, null);

    if ($interview->addInterview($interviewDetails)) {
        $message = $jobseekerapplication->createEmailTemplate($subject, $texthere);
        $emailcheck = $jobseekerapplication->sendEmailNotification($email, $subject, $message);
        if($emailcheck) {
            $response = array('status' => 'success', 'message' => 'Interview set successfully. '.$emailcheck);
        } else {
            $response = array('status' => 'error', 'message' => 'Sending email notification failed. Please try again.');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Setting interview failed. Please try again.');
    }

    echo json_encode($response);
    exit();
}
?>