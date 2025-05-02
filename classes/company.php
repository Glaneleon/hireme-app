<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/companydetails.php';
require_once __DIR__ . '/companydocumentscheck.php';
require_once __DIR__ . '/companyprofile.php';

class Company
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addCompany($companyname, $companydesc, $companyaddress, $userID)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO companies (UserID, CompanyName, CompanyDescription, CompanyAddress) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $userID, $companyname, $companydesc, $companyaddress);
            $result = $stmt->execute();
            $stmt->close();
            if (!$result) {
                // handle error
                return false;
            }
            return true;
        } catch (Exception $e) {
            // handle exception
            return false;
        }
    }

    public function getCompanyDetails($userID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE UserID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $companyDetails = new CompanyDetails(
                $row['CompanyID'],
                $row['UserID'],
                $row['CompanyName'],
                $row['CompanyDescription'],
                $row['CompanyAddress'],
                $row['VerificationStatus']
            );

            $stmt->close();

            return $companyDetails;
        } else {
            return null;
        }
    }

    public function getCompanyDetailsByCompanyID($companyID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE CompanyID = ?");
        $stmt->bind_param("i", $companyID);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $companyDetails = new CompanyDetails(
                $row['CompanyID'],
                $row['UserID'],
                $row['CompanyName'],
                $row['CompanyDescription'],
                $row['CompanyAddress'],
                $row['VerificationStatus']
            );

            $stmt->close();

            return $companyDetails;
        } else {
            return null;
        }
    }

    public function getAllCompanies()
    {
        $query = "SELECT VerificationStatus, COUNT(*) as count FROM companies GROUP BY VerificationStatus";
        $result = $this->conn->query($query);
        $data = array();
        
        if ($result) {       
            while ($row = $result->fetch_assoc()) {
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

    public function getAllVerifiedCompanies()
    {
        $status = 'Verified';
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE VerificationStatus = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();

        $result = $stmt->get_result();

        $companies = array();

        while ($row = $result->fetch_assoc()) {
            $companyDetails = new CompanyDetails(
                $row['CompanyID'],
                $row['UserID'],
                $row['CompanyName'],
                $row['CompanyDescription'],
                $row['CompanyAddress'],
                $row['VerificationStatus']
            );

            $companies[] = $companyDetails;
        }

        $stmt->close();

        return $companies;
    }

    public function getAllUnverifiedCompanies()
    {
        $status = 'Pending';
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE VerificationStatus = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();

        $result = $stmt->get_result();

        $companies = array();

        while ($row = $result->fetch_assoc()) {
            $companyDetails = new CompanyDetails(
                $row['CompanyID'],
                $row['UserID'],
                $row['CompanyName'],
                $row['CompanyDescription'],
                $row['CompanyAddress'],
                $row['VerificationStatus']
            );

            $companies[] = $companyDetails;
        }

        $stmt->close();

        return $companies;
    }

    public function getAllRejectedCompanies()
    {
        $status = 'Rejected';
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE VerificationStatus = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();

        $result = $stmt->get_result();

        $companies = array();

        while ($row = $result->fetch_assoc()) {
            $companyDetails = new CompanyDetails(
                $row['CompanyID'],
                $row['UserID'],
                $row['CompanyName'],
                $row['CompanyDescription'],
                $row['CompanyAddress'],
                $row['VerificationStatus']
            );

            $companies[] = $companyDetails;
        }

        $stmt->close();

        return $companies;
    }

    public function updateCompanyStatus($companyID, $status)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE companies SET VerificationStatus = ? WHERE CompanyID = ?");
            $stmt->bind_param("si", $status, $companyID);
            $result = $stmt->execute();
            $stmt->close();
            if (!$result) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addCompanyDetails($companyID, $companyName, $address, $contactNumber, $email, $repPosition, $repName, $repNumber)
    {
        $stmt = $this->conn->prepare("INSERT INTO companyprofile (CompanyID, company_name, address, contact_number, email, rep_position, rep_name, rep_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $companyID, $companyName, $address, $contactNumber, $email, $repPosition, $repName, $repNumber);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

        $stmt->close();
    }

    public function getCompanyDocumentsCheckByCompanyID($companyID)
    {
        $query = "SELECT * FROM companydocuments WHERE CompanyID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $companyID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $companyDocumentsCheck = new CompanyDocumentsCheck(
                $row['listID'],
                $row['CompanyID'],
                $row['sec'],
                $row['businesspermit'],
                $row['bir'],
                $row['mayorpermit'],
                $row['certificate']
            );

            return $companyDocumentsCheck;
        } else {
            return null;
        }
    }

    public function addCompanyProfile($name, $address, $contactNumber, $email, $repPosition, $repName, $repNumber, $companyId)
    {
        $count = 0;

        $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM companyprofile WHERE name = ? OR email = ?");
        $checkStmt->bind_param("ss", $name, $email);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();
    
        if ($count > 0) {
            return "Duplicate entry for name or email";
        }
    
        $this->conn->begin_transaction();
    
        try {
            $docCount = 0;
            $stmt1 = $this->conn->prepare("INSERT INTO companyprofile (name, address, contact_number, email, rep_position, rep_name, rep_number, companyID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt1->bind_param("sssssssi", $name, $address, $contactNumber, $email, $repPosition, $repName, $repNumber, $companyId);
            $stmt1->execute();
            $stmt1->close();
        
            $checkDoc = $this->conn->prepare("SELECT COUNT(*) FROM companydocuments WHERE CompanyID = ?");
            $checkDoc->bind_param("i", $companyId);
            $checkDoc->execute();
            $checkDoc->bind_result($docCount);
            $checkDoc->fetch();
            $checkDoc->close();
        
            if ($docCount == 0) {
                $stmt2 = $this->conn->prepare("INSERT INTO companydocuments (CompanyID, sec, businesspermit, bir, mayorpermit, certificate) VALUES (?, 0, 0, 0, 0, 0)");
                $stmt2->bind_param("i", $companyId);
                $stmt2->execute();
                $stmt2->close();
            }
        
            $this->conn->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            return "Error: " . $e->getMessage();
        }
    }


    public function getCompanyProfile($companyId) {
        $stmt = $this->conn->prepare("SELECT * FROM companyprofile WHERE companyID = ?");
        $stmt->bind_param("s", $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $companyDetails = $result->fetch_assoc();
    
        if ($companyDetails) {
            return new CompanyProfile(
                $companyDetails['profileid'],
                $companyDetails['name'],
                $companyDetails['address'],
                $companyDetails['contact_number'],
                $companyDetails['email'],
                $companyDetails['rep_position'],
                $companyDetails['rep_name'],
                $companyDetails['rep_number'],
                $companyDetails['CompanyID']
            );
        } else {
            return null;
        }
    }

    public function addNotif($companyName, $notiftype, $status, $userID)
    {
        $sql = "INSERT INTO notifications (user_id, content, type, is_read) VALUES (?, ?, ?, ?)";

        $ver = "application_verified";
        $rej = "application_rejected";
        if ($status === 'Verified'){
            $type = $ver;
        } elseif ($status === 'Rejected'){
            $type = $rej;
        }
        $is_read = 0;
        if ($notiftype === 0) {
            $content = "An application has been posted by company: $companyName";
        } else {
            $content = "Your application has been ".$status."!";
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
    
}
