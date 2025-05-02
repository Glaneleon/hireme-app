<?php
require_once __DIR__ . '/../classes/user.php';
require_once __DIR__ . '/../classes/company.php';

if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
  $user = new User($conn);
  $userdetails = $user->getUserDetails($username);
  $userID = $userdetails->getUserID();

  if ($userID) {
    $company = new Company($conn);
    $companydetails = $company->getCompanyDetails($userID);
    if ($companydetails !== null) {
      $status = $companydetails->getVerificationStatus();
      $companyID = $companydetails->getCompanyID();
      $companyprofile = $company->getCompanyProfile($companyID);
      $companyName = $companydetails->getCompanyName();
    }
  }
} else {
  echo "No company details found for user ID: $userID";
}
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <!-- Logo -->
  <div class="app-brand demo">
    <div class="app-brand-link gap-2">
      <span class="logotext">HireMe-App</span>
      <!-- <img src="../assets/img/icons/websiteicons/hiremeappnewlogo.png" alt="HireMeLogo" style="max-height: 100px;"> -->
    </div>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>
  <!-- /Logo -->

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item">
      <a href="./dashboard.php" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Dashboard"><strong class="small"><?= strtoupper($companyName); ?></strong></div>
      </a>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Pages</span>
    </li>
    <?php

    if ($companydetails !== null && $status === 'Verified') { ?>
      <li class="menu-item open">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-briefcase"></i>
          <div data-i18n="Jobs">Jobs</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="./jobs.php" class="menu-link">
              <div data-i18n="View Jobs">View Jobs</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="./addjob.php" class="menu-link">
              <div data-i18n="Add Job Posting">Add Job Posting</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-item">
        <a href="./candidates.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-user"></i>
          <div data-i18n="Candidates">Candidates</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="./interviews.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-chat"></i>
          <div data-i18n="Interviews">Interviews</div>
        </a>
      </li>
    <?php }

    if ($companydetails == null) { ?>

      <li class="menu-item">
        <a href="./application.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-file"></i>
          <div data-i18n="Register">Register</div>
        </a>
      </li>

    <?php }

    if ($companydetails !== null && $companyprofile == null) { ?>

      <li class="menu-item">
        <a href="./registration.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-file"></i>
          <div data-i18n="Continue Registration">Continue Registration</div>
        </a>
      </li>

    <?php
    } if ($companydetails !== null && $companyprofile !== null && $status !== 'Verified') { ?>

      <li class="menu-item">
        <a href="./verification.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-key"></i>
          <div data-i18n="Verification">Verification</div>
        </a>
      </li>

    <?php } ?>

  </ul>
</aside>