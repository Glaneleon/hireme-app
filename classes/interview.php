<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/interviewdetails.php';

class Interview {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addInterview($interviewDetails) {
      $jobID = $interviewDetails->getJobID();
      $jobSeekerApplicationID = $interviewDetails->getJobSeekerApplicationID();
      $interviewDate = $interviewDetails->getInterviewDate();
      $dateMade = $interviewDetails->getDateMade();
      
      $sql = "INSERT INTO interviews (JobID, JobSeekerApplicationID, InterviewDate, DateMade) VALUES (?, ?, ?, ?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("iiss", $jobID, $jobSeekerApplicationID, $interviewDate, $dateMade);
      
      if ($stmt->execute()) {
          // ADD NOTIFICATION
          // $notificationSql = "INSERT INTO job_update_notifications (JobID, JobSeekerApplicationID, Type, DateMade) VALUES (?, ?, ?, ?)";
          // $notificationStmt = $this->conn->prepare($notificationSql);
          // $type = 'InterviewScheduled';
          // $notificationStmt->bind_param("iiss", $jobID, $jobSeekerApplicationID, $type, $dateMade);
          // $notificationStmt->execute();
          // $notificationStmt->close();
      
          return true;
      } else {
          return false;
      }
    }

    // public function getAllInterviews() {
    //     $sql = "SELECT InterviewID, JobID, JobSeekerApplicationID, InterviewDate, DateMade, Status FROM interviews";
    //     $result = $this->conn->query($sql);
    
    //     if ($result->num_rows > 0) {
    //       $interviews = [];
    //       while ($row = $result->fetch_assoc()) {
    //         $interview = new InterviewDetails(
    //           $row["InterviewID"],
    //           $row["JobID"],
    //           $row["JobSeekerApplicationID"],
    //           $row["InterviewDate"],
    //           $row["DateMade"],
    //           $row["Status"]
    //         );
    //         $interviews[] = $interview;
    //       }
    //       return $interviews;
    //     } else {
    //       return [];
    //     }
    // }

    public function getAllInterviewsByJobID($jobID) {
      try {
        $stmt = $this->conn->prepare("SELECT * FROM interviews WHERE JobID=?");
        $stmt->bind_param("i", $jobID);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
          $interviews = [];
          while ($row = $result->fetch_assoc()) {
            $interview = new InterviewDetails(
              $row["InterviewID"],
              $row["JobID"],
              $row["JobSeekerApplicationID"],
              $row["InterviewDate"],
              $row["DateMade"],
              $row["Status"]
            );
            $interviews[] = $interview;
          }
          return $interviews;
        } 
        else {
          return [];
        }
      } catch (Exception $e) {
          error_log($e->getMessage());
          throw $e;
      }
    }

    public function getInterviewByJobSeekerApplicationID($applicationID) {
      try {
          $stmt = $this->conn->prepare("SELECT * FROM interviews WHERE JobSeekerApplicationID=? LIMIT 1");
          $stmt->bind_param("i", $applicationID);
          $stmt->execute();
          $result = $stmt->get_result();
          $stmt->close();
  
          if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $interview = new InterviewDetails(
                  $row["InterviewID"],
                  $row["JobID"],
                  $row["JobSeekerApplicationID"],
                  $row["InterviewDate"],
                  $row["DateMade"],
                  $row["Status"]
              );
              return $interview;
          } else {
              return null;
          }
      } catch (Exception $e) {
          error_log($e->getMessage());
          throw $e;
      }
  }
  
}
