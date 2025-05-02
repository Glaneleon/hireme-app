<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/jobdetails.php';

class Job
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addJob($companyId, $jobTitle, $jobDescription, $jobType, $salaryMin, $salaryMax, $workHours, $jobLocation, $jobLocationType, $jobIndustry, $otherIndustry, $workType, $skills, $qualifications, $vacancies)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO jobs (CompanyID, 
                                                            JobTitle, 
                                                            JobDescription, 
                                                            JobType, 
                                                            SalaryMin, 
                                                            SalaryMax, 
                                                            WorkHours, 
                                                            JobLocation, 
                                                            JobLocationType, 
                                                            PostingDate, 
                                                            VerificationStatus,
                                                            JobIndustry,
                                                            OtherIndustry,
                                                            WorkType,
                                                            Skills,
                                                            Qualifications,
                                                            Vacancies)
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending', ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "isssddssssssssi",
                $companyId,
                $jobTitle,
                $jobDescription,
                $jobType,
                $salaryMin,
                $salaryMax,
                $workHours,
                $jobLocation,
                $jobLocationType,
                $jobIndustry,
                $otherIndustry,
                $workType,
                $skills,
                $qualifications,
                $vacancies
            );

            $result = $stmt->execute();

            $stmt->close();

            if (!$result) {
                return 'No result';
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function addNotif($companyName, $jobTitle, $notiftype, $userID)
    {
        $sql = "INSERT INTO notifications (user_id, content, type, is_read) VALUES (?, ?, ?, ?)";

        $type = "job_posted";
        $is_read = 0;
        if ($notiftype === 0) {
            $content = "A new job, '$jobTitle', has been posted by company: $companyName";
        } else {
            $content = "Your job posting, '$jobTitle', has been verified!";
        }

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("issi", $userID, $content, $type, $is_read);
            $stmt->execute();
            $stmt->close();

            return true;
        } catch (Exception $e) {
            return "Error adding notification: " . $e->getMessage();
        }
    }

    public function addUserNotif($content)
    {
        $sql = "INSERT INTO notifications (user_id, content, type, is_read, created_at) VALUES ('-1', ?, 'user_notif', '0', current_timestamp());";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $content);
            $stmt->execute();
            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Error adding notification: " . $e->getMessage());
            return "Error adding notification";
        }
    }

    public function getNotifications($userID)
    {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();

            $result = $stmt->get_result();
            $notifications = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $notifications[] = $row;
                }
            }

            $stmt->close();
            return $notifications;
        } catch (Exception $e) {
            return "Error fetching notifications: " . $e->getMessage();
        }
    }

    public function readNotification($notifID)
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $notifID);
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            return "Error reading notifications: " . $e->getMessage();
        } finally {
            $stmt->close();
        }
    }

    public function getAllJobs($companyId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM jobs WHERE CompanyID=?");
            $stmt->bind_param("i", $companyId);
            $stmt->execute();
            $result = $stmt->get_result();
            $jobs = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (empty($jobs)) {
                return [];
            }

            $jobObjects = [];
            foreach ($jobs as $job) {
                if (isset(
                    $job['JobID'],
                    $job['CompanyID'],
                    $job['JobTitle'],
                    $job['JobDescription'],
                    $job['JobType'],
                    $job['SalaryMin'],
                    $job['SalaryMax'],
                    $job['WorkHours'],
                    $job['JobLocationType'],
                    $job['PostingDate'],
                    $job['VerificationStatus'],
                    $job['JobIndustry']
                )) {
                    $jobObjects[] = new JobDetails(
                        $job['JobID'],
                        $job['CompanyID'],
                        $job['JobTitle'],
                        $job['JobDescription'],
                        $job['JobType'],
                        $job['WorkType'],
                        $job['SalaryMin'],
                        $job['SalaryMax'],
                        $job['WorkHours'],
                        $job['JobLocation'],
                        $job['JobLocationType'],
                        $job['PostingDate'],
                        $job['VerificationStatus'],
                        $job['JobIndustry'],
                        $job['OtherIndustry'],
                        $job['Skills'],
                        $job['Qualifications'],
                        $job['Vacancies'],
                        $job['RejectionReason']
                    );
                } else {
                    error_log("Incomplete job data for JobID: " . ($job['JobID'] ?? 'Unknown JobID'));
                }
            }

            return $jobObjects;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getJobDetailsByID($jobID)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM jobs WHERE JobID=?");
            $stmt->bind_param("i", $jobID);
            $stmt->execute();
            $result = $stmt->get_result();
            $job = $result->fetch_assoc();

            if (!$job) {
                return [];
            }

            $jobdetails = new JobDetails(
                $job['JobID'],
                $job['CompanyID'],
                $job['JobTitle'],
                $job['JobDescription'],
                $job['JobType'],
                $job['WorkType'],
                $job['SalaryMin'],
                $job['SalaryMax'],
                $job['WorkHours'],
                $job['JobLocation'],
                $job['JobLocationType'],
                $job['PostingDate'],
                $job['VerificationStatus'],
                $job['JobIndustry'],
                $job['OtherIndustry'],
                $job['Skills'],
                $job['Qualifications'],
                $job['Vacancies'],
                $job['RejectionReason']
            );

            return $jobdetails;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllVerifiedJobs()
    {
        $status = 'Verified';
        try {
            $stmt = $this->conn->prepare("SELECT * FROM jobs WHERE VerificationStatus=?");
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
            $jobs = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (empty($jobs)) {
                return [];
            }

            $jobObjects = [];
            foreach ($jobs as $job) {
                if (isset(
                    $job['JobID'],
                    $job['CompanyID'],
                    $job['JobTitle'],
                    $job['JobDescription'],
                    $job['JobType'],
                    $job['SalaryMin'],
                    $job['SalaryMax'],
                    $job['WorkHours'],
                    $job['JobLocationType'],
                    $job['PostingDate'],
                    $job['VerificationStatus'],
                    $job['JobIndustry']
                )) {
                    $jobObjects[] = new JobDetails(
                        $job['JobID'],
                        $job['CompanyID'],
                        $job['JobTitle'],
                        $job['JobDescription'],
                        $job['JobType'],
                        $job['WorkType'],
                        $job['SalaryMin'],
                        $job['SalaryMax'],
                        $job['WorkHours'],
                        $job['JobLocation'],
                        $job['JobLocationType'],
                        $job['PostingDate'],
                        $job['VerificationStatus'],
                        $job['JobIndustry'],
                        $job['OtherIndustry'],
                        $job['Skills'],
                        $job['Qualifications'],
                        $job['Vacancies'],
                        $job['RejectionReason']
                    );
                } else {
                    error_log("Incomplete job data for JobID: " . ($job['JobID'] ?? 'Unknown JobID'));
                }
            }

            return $jobObjects;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getFaveJobsCountByJobID($jobID)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS Interested FROM favoritejobs WHERE JobID = ?");
        $stmt->bind_param("i", $jobID);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $interested = $row['Interested'];

            $stmt->close();

            return $interested;
        } else {
            return null;
        }
    }

    public function getApplicantsCountByJobID($jobID)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS Applicants FROM jobseekerapplication WHERE JobID = ?");
        $stmt->bind_param("i", $jobID);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $applicants = $row['Applicants'];

            $stmt->close();

            return $applicants;
        } else {
            return null;
        }
    }

    public function getFaveJobsIDByJobID($jobID)
    {
        $stmt = $this->conn->prepare("SELECT JobID FROM favoritejobs WHERE JobID = ?");
        $stmt->bind_param("i", $jobID);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $faveID = $row['JobID'];

            $stmt->close();

            return $faveID;
        } else {
            return null;
        }
    }

    public function addFavoriteJob($jobSeekerID, $jobID)
    {
        $sql = "INSERT INTO FavoriteJobs (JobSeekerID, JobID) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $jobSeekerID, $jobID);
        if (!$stmt->execute()) {
            return false;
        }
        $rowsAffected = $stmt->affected_rows;
        $stmt->close();

        $affected = ($rowsAffected > 0) ? true : false;
        return $affected;
    }

    public function deleteFavoriteJob($jobSeekerID, $jobID)
    {
        $sql = "DELETE FROM FavoriteJobs WHERE JobSeekerID = ? AND JobID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $jobSeekerID, $jobID);
        if (!$stmt->execute()) {
            return false;
        }
        $rowsAffected = $stmt->affected_rows;
        $stmt->close();

        $affected = ($rowsAffected > 0) ? true : false;
        return $affected;
    }

    public function updateJobStatus($status, $jobID, $rejectionReason)
    {
        if ($status === 'Rejected' && $rejectionReason !== null) {
            $stmt = $this->conn->prepare("UPDATE jobs SET VerificationStatus = ?, RejectionReason = ? WHERE JobID = ?");
            $stmt->bind_param("ssi", $status, $rejectionReason, $jobID);
        } else {
            $stmt = $this->conn->prepare("UPDATE jobs SET VerificationStatus = ? WHERE JobID = ?");
            $stmt->bind_param("si", $status, $jobID);
        }

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getJobCountByStatus($companyID)
    {
        $query = "SELECT VerificationStatus, COUNT(*) as count FROM jobs WHERE CompanyID = $companyID GROUP BY VerificationStatus";

        $result = mysqli_query($this->conn, $query);

        if ($result) {
            $data = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = array(
                    'label' => $row['VerificationStatus'],
                    'value' => (int)$row['count']
                );
            }

            return json_encode($data);
        } else {
            return json_encode(array('error' => 'Failed to fetch data from the database'));
        }
    }

    public function getApplicantCountByMonth($jobID)
    {
        $query = "SELECT MONTH(ApplicationDate) AS month, COUNT(*) AS applicants 
                  FROM jobseekerapplication 
                  WHERE JobID = ? 
                  GROUP BY MONTH(ApplicationDate)";

        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param('i', $jobID);

            $stmt->execute();

            $result = $stmt->get_result();

            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'month' => $row['month'],
                    'applicants' => $row['applicants']
                );
            }

            return json_encode($data);

            $stmt->close();
        } else {
            http_response_code(500);
            return json_encode(array('error' => 'Database error: ' . $this->conn->error));
        }
    }

    public function getJobsByWorkType($workType)
    {
        $stmt = $this->conn->prepare("SELECT JobID FROM `jobs` WHERE `WorkType` = ?");
        $stmt->bind_param("s", $workType);
        $stmt->execute();

        $result = $stmt->get_result();

        $jobs = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $jobs ? $jobs : null;
    }

    // public function newJobNotif($email, $jobName, $jobDescription)
    // {
    //     if (empty($email) || empty($jobName)) {
    //         return false;
    //     }

    //     $hireme_mail = "hiremeapp722@gmail.com";
    //     $hireme_pass = "rrqbzkjdcmfyudpy";

    //     $mail = new PHPMailer(true);
    //     $mail->isSMTP();
    //     $mail->SMTPAuth = true;
    //     $mail->Host = "smtp.gmail.com";
    //     $mail->Username = $hireme_mail;
    //     $mail->Password = $hireme_pass;
    //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //     $mail->Port = 587;

    //     $mail->setFrom($hireme_mail, "Hire Me");
    //     $mail->addAddress($email);
    //     $mail->addReplyTo($hireme_mail, "Admin-hireme");
    //     $mail->IsHTML(true);
    //     $mail->Subject = "A new Job (" . ucfirst($jobName) . ") has been posted!";
    //     if (!empty($jobDescription)) {
    //         $mail->Body = "$jobDescription";
    //     } else {
    //         $mail->Body = "No Job Description Included.";
    //     }
    //     $mail->AltBody = "Code not retrieved";

    //     if (!$mail->send()) {
    //         return false;
    //     } else {
    //         return true;
    //     }
    // }
}