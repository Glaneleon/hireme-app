<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once '../classes/user.php';
require_once '../classes/company.php';

$username = $_SESSION['username'];

$user = new User($conn);
$company = new Company($conn);

$userdetails = $user->getUserDetails($username);
$role = $user->getUserDetails($_SESSION['username'])->getRole();

if (!$userdetails == null) {
    $userId = $userdetails->getUserID();
    if (!$userId == null) {
        $companydetails = $company->getCompanyDetails($userId);
        if (!$companydetails == null) {
            $companyname = $companydetails->getCompanyName();
        } else {
            echo 'Company Name not found.';
        }
    } else {
        echo 'UserID not found.';
    }
} else {
    echo 'User details not found.';
}

$rolecheck = 0;
$pagetitle = "HireMe - Registration";
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<!-- Head -->
<?php require_once __DIR__ . "/head.php"; ?>
<!-- /Head -->

<body>
    <!-- Toast Overlay -->
    <div id="toast-container"></div>
    <div class="overlay"></div>
    <!-- / Toast Overlay -->

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <?php
            if ($role == 'Company') {
                require_once __DIR__ . "/menubar.php";
            } else {
                $rolecheck = 1;
                echo '<img src="../assets/img/error1.gif" alt="Error Image">';
            }
            ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php require_once __DIR__ . "/navbar.php"; ?>
                <!-- / Navbar -->
                <?php
                if ($rolecheck == 1) {
                    echo '<img src="../assets/img/error1.gif" alt="Error Image">';
                } else {

                ?>
                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        <!-- Content -->
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="row">
                                <div class="col-lg-12 mb-4 order-0">
                                    <div class="card p-2">
                                    <small class="p-2"><span style="color: red;">*</span>This is what the PESO manager/s will see.</small>
                                        <form id="formAuthentication">
                                            <input type="email" class="form-control" id="email" name="email" value="<?= $email = $user->getUserDetails($_SESSION['username'])->getEmail() ?? 'company@email.com' ?>" hidden>
                                            <input type="text" name="companyID" id="companyID" value="<?= $companydetails->getCompanyID(); ?>" hidden />
                                            <input type="text" name="type" id="type" value="profile" hidden />

                                        <div class="mb-2">
                                            <label for="name">Full Company Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter the registered company name" required>
                                        </div>
                                        <div class="mb-2">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" required>
                                        </div>

                                            <div class="row mb-2">
                                               <div class="col-md-6">
                                                    <label for="contact_number">Company Contact Number</label>
                                                    <input type="tel" 
                                                           class="form-control" 
                                                           id="contact_number" 
                                                           name="contact_number" 
                                                           placeholder="Enter contact number" 
                                                           required 
                                                           pattern="\d{11}" 
                                                           title="Contact number must be exactly 11 digits">
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label for="rep_name">Representative Name</label>
                                                <input type="text" class="form-control" id="rep_name" name="rep_name" placeholder="Enter representative name" required>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label for="rep_position">Representative Position</label>
                                                    <input type="text" class="form-control" id="rep_position" name="rep_position" placeholder="Enter representative position" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="rep_number">Representative Contact Number</label>
                                                    <input type="tel" 
                                                           class="form-control" 
                                                           id="rep_number" 
                                                           name="rep_number" 
                                                           placeholder="Enter contact number" 
                                                           required 
                                                           pattern="\d{11}" 
                                                           title="Contact number must be exactly 11 digits">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- / Content -->

                        <!-- Footer -->
                        <footer>
                            <div class="container">
                                <div class="mb-2 mb-md-1">
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    <span class="footer-link fw-bolder"> HireMe</span>
                                </div>
                            </div>
                        </footer>
                        <!-- / Footer -->

                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
<?php }
                require_once __DIR__ . "/endscripts.php"; ?>
<script src="../assets/js/chart.js"></script>
<script>
    function showToast(message, type) {
        var toast = $('<div>', {
            class: 'toast ' + type,
            text: message
        });
        $('#toast-container').append(toast);
        toast.addClass('show');
        setTimeout(function() {
            toast.remove();
            $('.overlay').hide();
        }, 2000);
    }

    $(document).ready(function() {
        $('#formAuthentication').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '../functions/addcompanyprofile.php',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('.overlay').show();
                        showToast(response.message, 'success');
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1900);
                    } else if (response.status === 'error') {
                        showToast(response.message, 'warning');
                    } else {
                        console.error('Unknown response status:', response.status);
                        showToast(response.message, 'warning');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                }
            });
        });
    });
</script>
</body>

</html>