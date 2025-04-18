<?php
if (!isset($_SESSION)) {
  session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['jobID'])) {
    $jobID = $_POST['jobID'];
  } else {
    $jobID = 0;
  }
}

require_once '../classes/job.php';
require_once '../classes/user.php';
require_once '../classes/jobseekerapplication.php';
require_once '../classes/jobseeker.php';

$user = new User($conn);
$job = new Job($conn);
$jobseeker = new JobSeeker($conn);
$jobseekerapplication = new JobSeekerApplication($conn);

$applications = $jobseekerapplication->getJobApplicationDetailsByJobID($jobID);
$jobs = $job->getJobDetailsByID($jobID);

$pagetitle = "HireMe - View Job # " . $jobID;
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<!-- Head -->
<?php require_once './head.php'; ?>
<!-- /Head -->

<body>
  <div id="toast-container"></div>
  <div class="overlay"></div>

  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Menu -->
      <?php require_once "./menubar.php"; ?>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <?php require_once "./navbar.php"; ?>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <!-- Card -->
              <div class="col-lg-12 mb-4 order-0">
                <?php if (!empty($jobs)) : ?>
                  <div class="card p-2 mb-4">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-10">
                          <h4 class="card-title"><?= $jobs->getJobTitle(); ?></h4>
                          <h6 class="card-subtitle mb-4 text-muted">
                            <?php $location = empty($jobs->getJobLocation()) ? 'No location added.' : $jobs->getJobLocation();
                            echo $location;
                            ?>
                          </h6>
                        </div>
                        <div class="col-2">
                          <?php if ($jobs->getVerificationStatus() !== 'Closed') { ?>
                            <button type="button" id="closejob" class="btn btn-danger" data-job-id="<?= $jobs->getJobID(); ?>">Close Job</button>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="mb-4"><?= $jobs->getJobDescription(); ?></div>

                      <ul class="list-group list-group-flush">
                        <li class="list-group-item">Job Type: <?= $jobs->getJobType(); ?></li>
                        <li class="list-group-item">Work Type: <?= $jobs->getWorkType(); ?></li>
                        <li class="list-group-item">Job Industry: <?= $jobs->getJobIndustry(); ?></li>
                        <li class="list-group-item">Location Type: <?= $jobs->getJobLocationType(); ?></li>
                        <li class="list-group-item">Minimum Salary: ₱<?= $jobs->getSalaryMin(); ?></li>
                        <li class="list-group-item">Maximum Salary: ₱<?= $jobs->getSalaryMax(); ?></li>
                        <li class="list-group-item">Work Hours: <?= $jobs->getWorkHours(); ?></li>
                        <li class="list-group-item">Skills: <?= $jobs->getSkills(); ?></li>
                        <li class="list-group-item">Qualifications: <?= $jobs->getQualifications(); ?></li>
                        <li class="list-group-item">Vacancies: <?= $vacancies = $jobs->getVacancies()==0 ? 'Indefinite Hiring' : $jobs->getVacancies() ?></li>
                      </ul>

                      <?php
                      // Posting date details
                      $postingDate = new DateTime($jobs->getPostingDate());
                      $currentDate = new DateTime();
                      $interval = $currentDate->diff($postingDate);

                      $timePassed = '';
                      if ($interval->y) {
                        $timePassed = $interval->format('%y year/s ago');
                      } elseif ($interval->m) {
                        $timePassed = $interval->format('%m month/s ago');
                      } elseif ($interval->d) {
                        $timePassed = $interval->format('%d day/s ago');
                      } elseif ($interval->h) {
                        $timePassed = $interval->format('%h hour/s ago');
                      } elseif ($interval->i) {
                        $timePassed = $interval->format('%i minute/s ago');
                      } else {
                        $timePassed = $interval->format('%s seconds ago');
                      }
                      ?>

                      <p class="card-text"><small class="text-muted">Posted on: <?= $jobs->getPostingDate(); ?></small></p>
                      <p class="card-text"><small class="text-muted">Time posted: <?= $timePassed; ?></small></p>
                      <p class="card-text"><small class="text-muted">Verification:
                          <?php
                          $verification = $jobs->getVerificationStatus();

                          if ($verification == 'Pending') {
                            echo '<span class="text-warning">' . $verification . '</span>';
                          } else {
                            echo '<span class="text-success">' . $verification . '</span>';
                          } ?></small></p>
                    </div>
                    <a href="./jobs.php" class="btn btn-secondary">Back</a>
                  </div>
                <?php else : ?>
                  <div>
                    <p>This place is nonexistent. Pray tell, how didst thou arrive hither? Go back whence you came, human.</p>
                  </div>
                <?php endif; ?>
                <h4>Applicants for "<?= $jobs->getJobTitle(); ?>"</h4>

                <?php if (empty($applications)) : ?>
                  <div class="row">
                    <div class="col-lg-12 mb-2 order-0">
                      <div class="card p-2">
                        No applicants found.
                      </div>
                    </div>
                  </div>
                <?php else : ?>
                  <div class="row">
                    <table class="table table-striped table-hover">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Birth Date</th>
                          <th>Address</th>
                          <th>Email</th>
                          <th>Contact Number</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($applications as $application) :
                          if (empty($application)) {
                            continue;
                          }
                          $jobseekerdetails = $jobseeker->getJobSeekerDetailsByUserID($application->getUserID());
                          if (!empty($user->getUserDetailsByUserID($jobseekerdetails->getUserID()))) {
                            $email = $user->getUserDetailsByUserID($jobseekerdetails->getUserID())->getEmail();
                          } else {
                            $email = "Unknown";
                          }
                        ?>
                          <tr>
                            <td><?= ucfirst($jobseekerdetails->getFirstName()) . ' ' . ucfirst($jobseekerdetails->getLastName()) ?></td>
                            <td><?= $jobseekerdetails->getBirthDate() ?></td>
                            <td><?= $jobseekerdetails->getAddress() ?></td>
                            <td><?= $email ?></td>
                            <td><?= $jobseekerdetails->getContactNumber() ?></td>
                            <td>
                              <form action="./viewapplicant.php" method="post">
                                <input type="text" value="<?= $application->getUserID(); ?>" name="applicantID" hidden>
                                <input type="text" value="<?= $jobID; ?>" name="jobID" hidden>
                                <input type="text" value="ViewJob" name="referer" hidden>
                                <button type="submit" class="btn btn-primary">View</button>
                              </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
              <!-- /Card -->
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
  <?php require_once "./endscripts.php"; ?>
  <script src="./assets/vendor/libs/jquery/jquery.js"></script>

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
      $('#closejob').click(function() {
        var jobId = $(this).data('job-id');

        if (confirm("Are you sure you want to close this job?")) {
          $.ajax({
            type: 'POST',
            url: '../functions/closejob.php',
            data: {
              jobId: jobId
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                console.log(response.message);
                $('.overlay').show();
                showToast(response.message, 'success');
                setTimeout(function() {
                  window.location.href = response.redirect;
                }, 1400);
              } else if (response.status === 'error') {
                console.error(response.message);
                showToast(response.message, 'warning');
              } else {
                console.error('Unknown response status:', response.status);
              }
            },
            error: function(xhr, status, error) {
              console.error('AJAX Error:', error);
              showToast('An error occurred. Please try again.', 'error');
            }
          });
        }
      });
    });
  </script>
</body>
</html>