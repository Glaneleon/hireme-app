<?php

// // session_start();
include 'auth.php';
include 'jobs.php';
include 'connect.php';
include 'user.php';
include 'smtp.php';


$authDb = new AuthService();
$jobDb = new JobService();
$userDb = new UserService();


//Get Key
$config = parse_ini_file('.env');
$db_key = $config['key'];

$date = (new \DateTime())->format('Y-m-d');
$md5_key = md5($db_key . $date);

// API  Key Verification    
if (isset($_POST['api_key'])) {
    $api_key = ($_POST['api_key']);

    if ($api_key == $md5_key) {
        // States
        if (isset($_POST['state'])) {
            $state = ($_POST['state']);

            switch ($state) {
                /////////////////////////////////////
                //Auth
                case "state_log_in":
                    echo $authDb->log_in($_POST['username'], $_POST['password']);
                    break;
                case "state_register":
                    echo $authDb->register(
                        $_POST['username'],
                        $_POST['password'],
                        $_POST['email'],
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['birthdate'],
                        $_POST['address'],
                        $_POST['contact_number']
                    );
                    break;

                /////////////////////////////////////
                //user
                case "state_user_profile":
                    echo $userDb->user_profile(
                        $_POST['user_id']
                    );
                    break;

                /////////////////////////////////////
                //Job
                case "state_job_listing":
                    echo $jobDb->list_jobs(
                        $_POST['job_desc'],
                        $_POST['salary_min'],
                        $_POST['salary_max'],
                        $_POST['job_loc']
                    );
                    break;
                case "state_job_rec":
                    echo $jobDb->list_job_rec(
                        $_POST['user_id']
                    );
                    break;
                case "state_list_job_application":
                    echo $jobDb->job_application_list(
                        $_POST['user_id']
                    );
                    break;
                case "state_list_interview":
                    echo $jobDb->interview_list(
                        $_POST['user_id']
                    );
                    break;
               /* case "state_apply_job":
                    echo $jobDb->apply_job(
                        $_POST['user_id'],
                        $_POST['resume'],
                        $_POST['job_id']
                    );
                    break; */
                    
                  /*  case "state_apply_job":
                        $response = $jobDb->apply_job($_POST['user_id'], $_POST['resume'], $_POST['job_id']);
                        if (!$response) {
                            header("Content-Type: JSON");
                            echo json_encode(['verdict' => false, 'message' => 'Unexpected error occurred'], JSON_PRETTY_PRINT);
                        } else {
                            echo $response;
                        }
                        break; */
                        
                 case "state_apply_job":
                    $user_id = $_POST['user_id'] ?? '';
                    $resume = $_POST['resumefile'] ?? ''; // File picker uploads
                    $resume_path = $_POST['ResumeFilePath'] ?? ''; // Google Drive links
                    $job_id = $_POST['job_id'] ?? '';
                
                    // Validate inputs
                    if (empty($user_id) || (empty($resume) && empty($resume_path)) || empty($job_id)) {
                        echo json_encode(['verdict' => false, 'message' => 'Missing required parameters!'], JSON_PRETTY_PRINT);
                        break;
                    }
                
                    // Handle file picker uploads
                    if (!empty($resume)) {
                        echo $jobDb->apply_job_with_file($user_id, $resume, $job_id);
                    }
                    // Handle Google Drive link uploads
                    elseif (!empty($resume_path)) {
                        echo $jobDb->apply_job_with_link($user_id, $resume_path, $job_id);
                    }
                    break;
    
                case "state_verify_user":
                    echo $authDb->user_verification(
                        $_POST['user_id'],
                        $_POST['otp']
                    );
                    break;
                default:
                    header("Content-Type: JSON");
                    $response['verdict'] = false;
                    $response['message'] = "Invalid State!";
                    echo json_encode($response, JSON_PRETTY_PRINT);
            }

        } else {
            header("Content-Type: JSON");
            $response['verdict'] = false;
            $response['message'] = "No State!";
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    } else if ($api_key != $md5_key) {
        header("Content-Type: JSON");
        $response['verdict'] = false;
        $response['key'] = $md5_key;
        $response['message'] = "Incorrect Key!";
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
} else {
    header("Content-Type: JSON");
    $response['verdict'] = false;
    $response['message'] = "No Key!";
    echo json_encode($response, JSON_PRETTY_PRINT);
}
