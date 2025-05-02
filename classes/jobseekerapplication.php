<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/jobseekerapplicationdetails.php';

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    require_once '../hireme/PHPMailer/src/Exception.php';
    require_once '../hireme/PHPMailer/src/PHPMailer.php';
    require_once '../hireme/PHPMailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

Class JobSeekerApplication {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
        /*
    public function addJobApplication($jobID, $userID, $resumeFilePath) {
        $stmt = $this->conn->prepare("INSERT INTO jobseekerapplication (JobID, UserID, ResumeFilePath, ApplicationDate, Status) VALUES (?, ?, ?, NOW(), 'Pending')");

        $stmt->bind_param("iis", $jobID, $userID, $resumeFilePath);                         /// OLD CODE ///

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }       */
    public function addJobApplication($jobID, $userID, $resumeFilePath = null, $resumefile = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO jobseekerapplication (JobID, UserID, ResumeFilePath, resumefile, ApplicationDate, Status) 
            VALUES (?, ?, ?, ?, NOW(), 'Pending')"
        );

        $stmt->bind_param("iiss", $jobID, $userID, $resumeFilePath, $resumefile);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getJobApplicationDetailsByJobID($jobID) {
        $stmt = $this->conn->prepare("SELECT * FROM jobseekerapplication WHERE JobID = ?");
        $stmt->bind_param("s", $jobID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $applications = array();
        while ($row = $result->fetch_assoc()) {
            $application = new JobSeekerApplicationDetails(
                $row['JobSeekerApplicationID'], 
                $row['JobID'], 
                $row['UserID'], 
                $row['ResumeFilePath'], 
                $row['resumefile'], 
                $row['ApplicationDate'], 
                $row['Status'],
                $row['RejectionReason']
            );
            $applications[] = $application;
        }
    
        return $applications;
    }

    public function getJobApplicationDetailsByUserID($userID, $jobID) {
        $stmt = $this->conn->prepare("SELECT * FROM jobseekerapplication WHERE UserID = ? AND JobID = ?");
        $stmt->bind_param("ii", $userID, $jobID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $applications = array();
        while ($row = $result->fetch_assoc()) {
            $application = new JobSeekerApplicationDetails(
                $row['JobSeekerApplicationID'], 
                $row['JobID'], 
                $row['UserID'], 
                $row['ResumeFilePath'], 
                $row['resumefile'], 
                $row['ApplicationDate'], 
                $row['Status'],
                $row['RejectionReason']
            );
            $applications[] = $application;
        }
    
        return $applications;
    }

    public function getJobApplicationDetailsByID($applicationID) {
        $stmt = $this->conn->prepare("SELECT * FROM jobseekerapplication WHERE JobSeekerApplicationID = ?");
        $stmt->bind_param("i", $applicationID);
        $stmt->execute();
        $result = $stmt->get_result();
      
        $application = $result->fetch_assoc();
      
        if ($application) {
          return new JobSeekerApplicationDetails(
            $application['JobSeekerApplicationID'],
            $application['JobID'],
            $application['UserID'],
            $application['ResumeFilePath'],
            $application['resumefile'], 
            $application['ApplicationDate'],
            $application['Status'],
            $application['RejectionReason']
          );
        } else {
          return null;
        }
    }  

    public function getAllVerifiedJobApplicationDetails() {
        $stmt = $this->conn->prepare("SELECT * FROM jobseekerapplication WHERE Status = 'Verified'");
        $stmt->execute();
        $result = $stmt->get_result();
    
        $applications = array();
        while ($row = $result->fetch_assoc()) {
            $application = new JobSeekerApplicationDetails(
                $row['JobSeekerApplicationID'], 
                $row['JobID'], 
                $row['UserID'], 
                $row['ResumeFilePath'], 
                $row['resumefile'], 
                $row['ApplicationDate'], 
                $row['Status'],
                $row['RejectionReason']
            );
            $applications[] = $application;
        }
    
        return $applications;
    }

    public function getAllJobApplications() {
        $sql = "SELECT
                    MONTH(ApplicationDate) AS month,
                    Status,
                    COUNT(*) AS count
                FROM
                    jobseekerapplication
                GROUP BY
                    MONTH(ApplicationDate), Status
                ORDER BY
                    month, Status;";
        $result = $this->conn->query($sql);

        if($result){
        $response = [
            'verified' => array_fill(0, 12, 0),
            'pending'  => array_fill(0, 12, 0),
            'rejected' => array_fill(0, 12, 0),
            'hired' => array_fill(0, 12, 0),
        ];
        
        foreach ($result as $row) {
            $month = $row['month'] - 1;
            $status = strtolower($row['Status']);
            $response[$status][$month] = (int)$row['count'];
        }

        return json_encode($response);
        } else {
            return json_encode(array('error' => 'Failed to fetch data from the database'));
        }
    }

    public function changeJobApplicationStatus($status, $reason, $applicationID) {
        $stmt = $this->conn->prepare("UPDATE jobseekerapplication SET Status = ?, RejectionReason = ? WHERE JobSeekerApplicationID = ?;");

        $stmt->bind_param("ssi", $status, $reason, $applicationID);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    // public function changeJobApplicationStatus($status, $reason, $applicationID) { 
    // FOR DEBUGGING ONLY
    //     $stmt = $this->conn->prepare("UPDATE jobseekerapplication SET Status = ?, RejectionReason = ? WHERE JobSeekerApplicationID = ?;");
        
    //     // Log the raw SQL with values (for debugging only)
    //     $debugQuery = sprintf(
    //         "UPDATE jobseekerapplication SET Status = '%s', RejectionReason = '%s' WHERE JobSeekerApplicationID = %d;",
    //         $this->conn->real_escape_string($status),
    //         $this->conn->real_escape_string($reason),
    //         $applicationID
    //     );
    //     $err = "DEBUG SQL: " . $debugQuery;
    
    //     $stmt->bind_param("ssi", $status, $reason, $applicationID);
    
    //     if ($stmt->execute()) {
    //         return $err;
    //     } else {
    //         return $err;
    //     }
    // }    

    public function sendEmailNotification($email, $subject, $message) {

        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hiremeapp@hireme-app.online';
            $mail->Password = 'jPkf*3+f6:fI';
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom('hiremeapp@hireme-app.online', 'HireMe-App');
            $mail->addReplyTo('hiremeapp@hireme-app.online', 'HireMe-App');
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            
            $mail->send();
            return 'An email has been sent.';
        } catch (Exception $e) {
            return "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    
    public function createEmailTemplate($subject, $content) {
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <style>
            body { margin: 0; padding: 0; background-color: #fff; font-family: Arial, sans-serif; }
            .container { max-width: 700px; margin: auto; }
            .header img { max-width: 100%; height: auto; display: block; margin: 30px auto 20px; }
            .main { background: #4a65ad url(\'https://d15k2d11r6t6rl.cloudfront.net/public/users/Integrators/0db9f180-d222-4b2b-9371-cf9393bf4764/a31367d3-56d9-4984-bf48-79331695a0bc/galaxy-bg.png\') no-repeat center top; color: #fff; padding: 40px 20px 20px; text-align: center; }
            .main h1 { font-size: 30px; margin: 0; }
            .main p { font-size: 16px; color: #d8ebf8; }
            .footer { text-align: center; padding: 25px 10px; color: #555; font-size: 14px; }
          </style>
        </head>
        <body>
          <div class="container">
            <div class="header">
              <a href="https://hireme-app.online/" target="_blank">
                <img src="https://hireme-app.online/hireme_logo2.png" alt="HireMe Logo">
              </a>
            </div>
            <div class="main">
              <h1>' . htmlspecialchars($subject) . '</h1>
              <h3>' . nl2br(htmlspecialchars($content)) . '</h3>
            </div>
            <div class="footer">
              &copy; Copyright <strong>HireMe-App</strong> All Rights Reserved
            </div>
          </div>
        </body>
        </html>
        ';
    }
}
?>
