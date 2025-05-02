<?php
class JobService
{
    function list_jobs(
        $jobDesc,
        $salMin,
        $salMax,
        $jobLoc
    ) {
        $conn = new ConnectDb();
        if ($conn->get_db()) {

            $filter_array = array();
            $filterString = '';

            if ($jobDesc != '') {
                $data = "a.JobDescription = '$jobDesc'";
                array_push($filter_array, $data);
            }
            if ($salMin != '') {
                $data = "a.SalaryMin = '$salMin'";
                array_push($filter_array, $data);
            }
            if ($salMax != '') {
                $data = "a.SalaryMax = '$salMax'";
                array_push($filter_array, $data);
            }
            if ($jobLoc != '') {
                $data = "a.JobLocation = '$jobLoc'";
                array_push($filter_array, $data);
            }

            if ($jobDesc != '' || $salMin != '' || $salMax != '' || $jobLoc != '') {
                for ($i = 0; $i < count($filter_array); $i++) {
                    $filterString = $filterString . " && ";
                    $filterString = $filterString . $filter_array[$i];
                }
            }

            $sql = "SELECT b.CompanyName, a.*  FROM `jobs` a, `companies` b WHERE  a.CompanyID = b.CompanyID AND a.VerificationStatus = 'Verified'" . $filterString;
            $result = mysqli_query($conn->get_db(), $sql);
            if (mysqli_num_rows($result) >= 1) {

                header("Content-Type: JSON");
                $response['verdict'] = true;
                $response['rows'] = mysqli_num_rows($result);
                $data_array = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    array_push($data_array, $row);
                }
                $response['job_list'] = $data_array;
                return json_encode($response, JSON_PRETTY_PRINT);

            } else {
                header("Content-Type: JSON");
                $response['verdict'] = true;
                $response['rows'] = 0;
                $response['message'] = "No listings available!";
                return json_encode($response, JSON_PRETTY_PRINT);
            }

        } else {
            header("Content-Type: JSON");
            $response['verdict'] = false;
            $response['message'] = "No DB Connection!";
            return json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    function list_job_rec(
        $user_id
    ) {
        if ($user_id == "") {
            header("Content-Type: JSON");
            $response['verdict'] = false;
            $response['message'] = "No user specified!";
            return json_encode($response, JSON_PRETTY_PRINT);
        } else {

            $conn = new ConnectDb();
            if ($conn->get_db()) {

                $filter_array = array();
                $filterString = '';

                $sql = "SELECT a.WorkType FROM jobs a, jobseekerapplication b WHERE b.UserID = '$user_id' AND a.JobID = b.JobID";

                $result = mysqli_query($conn->get_db(), $sql);
                if (mysqli_num_rows($result) >= 1) {

                    while ($row = mysqli_fetch_assoc($result)) {
                        $row_worktype = $row['WorkType'];
                        $rec_sql = "SELECT b.CompanyName, a.*  FROM `jobs` a, `companies` b WHERE  a.CompanyID = b.CompanyID AND WorkType = '$row_worktype'";

                        $rec_result = mysqli_query($conn->get_db(), $rec_sql);
                        if (mysqli_num_rows($rec_result) >= 1) {
                            header("Content-Type: JSON");
                            $response['verdict'] = true;
                            $response['rows'] = mysqli_num_rows($rec_result);
                            $data_array = array();
                            while ($row = mysqli_fetch_assoc($rec_result)) {
                                array_push($data_array, $row);
                            }
                            $response['job_list'] = $data_array;
                            return json_encode($response, JSON_PRETTY_PRINT);
                        }
                    }

                } else {
                    header("Content-Type: JSON");
                    $response['verdict'] = true;
                    $response['rows'] = 0;
                    $response['message'] = "No recommendations available!";
                    return json_encode($response, JSON_PRETTY_PRINT);
                }

            } else {
                header("Content-Type: JSON");
                $response['verdict'] = false;
                $response['message'] = "No DB Connection!";
                return json_encode($response, JSON_PRETTY_PRINT);
            }
        }
    }



    function job_application_list(
        $user_id
    ) {
        $conn = new ConnectDb();
        if ($conn->get_db()) {
            if ($user_id == "") {
                header("Content-Type: JSON");
                $response['verdict'] = false;
                $response['message'] = "User ID empty!";
                return json_encode($response, JSON_PRETTY_PRINT);
            } else {
                $sql = "SELECT b.JobDescription, c.CompanyName, a.* FROM `jobseekerapplication` a, `jobs` b, `companies` c WHERE a.UserID = '$user_id' && a.JobID = b.JobID && b.CompanyID = c.CompanyID;";
                $result = mysqli_query($conn->get_db(), $sql);
                if (mysqli_num_rows($result) >= 1) {

                    header("Content-Type: JSON");
                    $response['verdict'] = true;
                    $response['rows'] = mysqli_num_rows($result);
                    $data_array = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        array_push($data_array, $row);
                    }
                    $response['application_list'] = $data_array;
                    return json_encode($response, JSON_PRETTY_PRINT);

                } else {
                    header("Content-Type: JSON");
                    $response['verdict'] = true;
                    $response['rows'] = 0;
                    $response['message'] = "No job application yet!";
                    return json_encode($response, JSON_PRETTY_PRINT);

                }
            }
        } else {
            header("Content-Type: JSON");
            $response['verdict'] = false;
            $response['message'] = "No DB Connection!";
            return json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    function interview_list(
        $user_id
    ) {
        $conn = new ConnectDb();
        if ($conn->get_db()) {
            if ($user_id == "") {
                header("Content-Type: JSON");
                $response['verdict'] = false;
                $response['message'] = "User ID empty!";
                return json_encode($response, JSON_PRETTY_PRINT);
            } else {
                $sql = "SELECT d.Status AS 'JobAppStat', b.JobTitle, c.CompanyName, a.* FROM `interviews` a, `jobs` b, `companies` c, `jobseekerapplication` d WHERE a.JobSeekerApplicationID = d.JobSeekerApplicationID && a.JobID = b.JobID && d.UserID = '$user_id' && b.CompanyID = c.CompanyID;";
                $result = mysqli_query($conn->get_db(), $sql);
                if (mysqli_num_rows($result) >= 1) {

                    header("Content-Type: JSON");
                    $response['verdict'] = true;
                    $response['rows'] = mysqli_num_rows($result);
                    $data_array = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        array_push($data_array, $row);
                    }
                    $response['application_list'] = $data_array;
                    return json_encode($response, JSON_PRETTY_PRINT);

                } else {
                    header("Content-Type: JSON");
                    $response['verdict'] = true;
                    $response['rows'] = 0;
                    $response['message'] = "No job interviews yet!";
                    return json_encode($response, JSON_PRETTY_PRINT);

                }
            }
        } else {
            header("Content-Type: JSON");
            $response['verdict'] = false;
            $response['message'] = "No DB Connection!";
            return json_encode($response, JSON_PRETTY_PRINT);
        }
    }

/*    function apply_job(
        $user_id,
        $resume,
        $job_id
    ) {
        $conn = new ConnectDb();
        if ($conn->get_db()) {
            if ($job_id == "") {
                header("Content-Type: JSON");
                $response['verdict'] = false;
                $response['message'] = "Job ID empty!";
                return json_encode($response, JSON_PRETTY_PRINT);
            } else if ($resume == "") {
                header("Content-Type: JSON");
                $response['verdict'] = false;
                $response['message'] = "Please submit your resume!";
                return json_encode($response, JSON_PRETTY_PRINT);
            } else if ($user_id == "") {
                header("Content-Type: JSON");
                $response['verdict'] = false;
                $response['message'] = "User ID empty!";
                return json_encode($response, JSON_PRETTY_PRINT);
            } else {
                //Check if application already exists
                $sql = "SELECT JobSeekerApplicationID FROM `jobseekerapplication` WHERE UserID ='$user_id' && JobID = '$job_id'";
                $result = mysqli_query($conn->get_db(), $sql);
                if (mysqli_num_rows($result) == 1) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        header("Content-Type: JSON");
                        $response['verdict'] = false;
                        $response['message'] = "Application already exists!";
                        return json_encode($response, JSON_PRETTY_PRINT);
                    }
                } else if (mysqli_num_rows($result) == 0) {
                    //Create Job application
                    $insert = "INSERT INTO `jobseekerapplication` (`JobID`, `UserID`, `ResumeFilePath`, `ApplicationDate`, `Status`) VALUES ('$job_id', '$user_id', '$resume', current_timestamp(), 'Pending');";

                    if (mysqli_query($conn->get_db(), $insert)) {
                        $check = "SELECT JobSeekerApplicationID FROM `jobseekerapplication` WHERE UserID ='$user_id' && JobID = '$job_id'";
                        $result = mysqli_query($conn->get_db(), $check);
                        if (mysqli_num_rows($result) == 1) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                header("Content-Type: JSON");
                                $response['verdict'] = true;
                                $response['application_id'] = $row['JobSeekerApplicationID'];
                                $response['message'] = "Job application created";
                                return json_encode($response, JSON_PRETTY_PRINT);
                            }
                        }
                    } else {
                        header("Content-Type: JSON");
                        $response['verdict'] = false;
                        $response['message'] = "Application failed!";
                        return json_encode($response, JSON_PRETTY_PRINT);
                    }
                }
            }
        } else {
            header("Content-Type: JSON");
            $response['verdict'] = false;
            $response['message'] = "No DB Connection!";
            return json_encode($response, JSON_PRETTY_PRINT);
        }
    }
} */
/*
function apply_job($user_id, $resume, $job_id) {
    $conn = new ConnectDb();
    if (!$conn->get_db()) {
        return json_encode(['verdict' => false, 'message' => 'No DB Connection!'], JSON_PRETTY_PRINT);
    }
    if (empty($job_id) || empty($resume) || empty($user_id)) {
        return json_encode(['verdict' => false, 'message' => 'Missing required parameters!'], JSON_PRETTY_PRINT);
    }
    
    try {
        $sql = "INSERT INTO `jobseekerapplication` (`JobID`, `UserID`, `ResumeFilePath`, `ApplicationDate`, `Status`) 
                VALUES ('$job_id', '$user_id', '$resume', current_timestamp(), 'Pending')";
        if (mysqli_query($conn->get_db(), $sql)) {
            return json_encode(['verdict' => true, 'message' => 'Application successful!'], JSON_PRETTY_PRINT);
        } else {
            return json_encode(['verdict' => false, 'message' => 'Failed to insert application!'], JSON_PRETTY_PRINT);
        }
    } catch (Exception $e) {
        return json_encode(['verdict' => false, 'message' => $e->getMessage()], JSON_PRETTY_PRINT);
    }
}
}
*/

function apply_job_both($user_id, $resume, $resume_path, $job_id) {
    $conn = new ConnectDb();
    $db = $conn->get_db();
    if (!$db) {
        return json_encode(['verdict' => false, 'message' => 'No DB Connection!'], JSON_PRETTY_PRINT);
    }

    if (!is_uploaded_file($resume['tmp_name'])) {
        return json_encode(['verdict' => false, 'message' => 'Invalid file uploaded!'], JSON_PRETTY_PRINT);
    }

    $fileData = file_get_contents($resume['tmp_name']);
    $stmt = $db->prepare("INSERT INTO `jobseekerapplication` (`JobID`, `UserID`, `resumefile`, `ResumeFilePath`, `ApplicationDate`, `Status`) 
                          VALUES (?, ?, ?, ?, current_timestamp(), 'Pending')");
    $stmt->bind_param("isss", $job_id, $user_id, $fileData, $resume_path);
    
    if ($stmt->execute()) {
        return json_encode(['verdict' => true, 'message' => 'Application submitted via file and link!'], JSON_PRETTY_PRINT);
    } else {
        return json_encode(['verdict' => false, 'message' => 'Application failed!'], JSON_PRETTY_PRINT);
    }
}

function apply_job_with_file($user_id, $resume, $job_id) {
    $conn = new ConnectDb();
    $db = $conn->get_db();
    if (!$db) {
        return json_encode(['verdict' => false, 'message' => 'No DB Connection!'], JSON_PRETTY_PRINT);
    }

    if (!is_uploaded_file($resume['tmp_name'])) {
        return json_encode(['verdict' => false, 'message' => 'Invalid file uploaded!'], JSON_PRETTY_PRINT);
    }

    $fileData = file_get_contents($resume['tmp_name']);
    $stmt = $db->prepare("INSERT INTO `jobseekerapplication` (`JobID`, `UserID`, `resumefile`, `ApplicationDate`, `Status`) 
                          VALUES (?, ?, ?, current_timestamp(), 'Pending')");
    $stmt->bind_param("iis", $job_id, $user_id, $fileData);

    if ($stmt->execute()) {
        return json_encode(['verdict' => true, 'message' => 'Application submitted via file!'], JSON_PRETTY_PRINT);
    } else {
        return json_encode(['verdict' => false, 'message' => 'Application failed!'], JSON_PRETTY_PRINT);
    }
}

function apply_job_with_link($user_id, $resume_path, $job_id) {
    $conn = new ConnectDb();
    if (!$conn->get_db()) {
        return json_encode(['verdict' => false, 'message' => 'No DB Connection!'], JSON_PRETTY_PRINT);
    }

    // Insert the URL into the database
    $sql = "INSERT INTO `jobseekerapplication` (`JobID`, `UserID`, `ResumeFilePath`, `ApplicationDate`, `Status`) 
            VALUES ('$job_id', '$user_id', '$resume_path', current_timestamp(), 'Pending')";
    if (mysqli_query($conn->get_db(), $sql)) {
        return json_encode(['verdict' => true, 'message' => 'Application submitted via link!'], JSON_PRETTY_PRINT);
    } else {
        return json_encode(['verdict' => false, 'message' => 'Application failed!'], JSON_PRETTY_PRINT);
    }
}
}