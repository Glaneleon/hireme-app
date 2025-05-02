<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="./assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>HireMe - Register</title>

    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="./assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="./assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="./assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="./assets/css/demo.css" />
    <link rel="stylesheet" href="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="./assets/vendor/css/pages/page-auth.css" />
    <script src="./assets/vendor/js/helpers.js"></script>
    <script src="./assets/js/config.js"></script>
    <link rel="stylesheet" href="./assets/css/toast.css">
  </head>

  <body>
  <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
    
        $otp_id = $data['otp_id'] ?? null;
        $email = $data['email'] ?? null;
        $auth_status = $data['auth_status'] ?? null;
        $metadata = $data['metadata'] ?? null;
    
        $sender_ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $referrer = $_SERVER['HTTP_REFERER'] ?? null;

      // if($data){
      //   print_r([
      //       'otp_id' => $otp_id,
      //       'email' => $email,
      //       'auth_status' => $auth_status,
      //       'metadata' => $metadata,
      //       'sender_ip' => $sender_ip,
      //       'referrer' => $referrer
      //   ]);
      // }
    }
  ?>

  <div id="toast-container"></div>
  <div class="overlay"></div>
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register Card -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                  <img src="hireme_logo2.png" alt="HireMeLogo" style="max-height: 150px;">
              </div>
               <div class="app-brand justify-content-center">
                <span class="logotext text-black">HireMe-App</span>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Welcome to HireMe-App! 🚀</h4>
              <p class="mb-4">Job hunting has never been this easy!</p>

              <form id="formAuthentication" class="mb-3" action="./functions/register.php" method="POST">
                  <input type="text"  id="role" name="role" value="Company" hidden/>
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    pattern="^[a-zA-Z][a-zA-Z0-9_]{3,19}$"
                    title="• 4-20 characters &#10;• Only letters, numbers, and underscores &#10;• Cannot start with an underscore."
                    autofocus
                    required
                  />
                  <small class="" id="usernamewarning"></small>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <div class="input-group">
                    <input
                      type="email"
                      class="form-control"
                      id="email"
                      name="email"
                      placeholder="Enter your email"
                      required
                    />
                    <button class="btn btn-primary" type="button" id="checkEmailBtn" disabled>Verify</button>
                  </div>
                  <small class="" id="emailwarning"></small>
                  <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>

                <div class="mb-3 form-password-toggle">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="••••••••"
                      pattern="(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}"
                      title="• Minimum 6 characters &#10;• No special characters &#10;• At least 1 letter and 1 number"
                      required
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    <div class="invalid-feedback">
                      Password must be at least 6 characters long and contain a number.
                    </div>
                  </div>
                  <small id="passwordwarning"></small>
                </div>

                <!-- Popup Modal (BS5) -->
                <div class="modal fade" id="popupModal" tabindex="-1" aria-labelledby="popupModalLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Privacy Policy & Terms of Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <h2>Privacy Policy</h2>
                        <p><strong>Effective Date:</strong> September 1, 2024</p>
                        <p><strong>1. Overview</strong><br>HireMe-App values your privacy. This policy details how we collect, use, and protect your information.</p>
                        <p><strong>2. Information We Collect</strong><br>- Personal Info: Email, username, password, phone number.<br>- Job & Company Info: Details related to job openings and companies.</p>
                        <p><strong>3. How We Use Your Data</strong><br>- To provide and improve our services.<br>- To communicate with you about updates and job-related matters.</p>
                        <p><strong>4. Data Sharing</strong><br>We don’t sell your info. We may share it with service providers or as required by law.</p>
                        <p><strong>5. Security</strong><br>We use reasonable measures to protect your data but cannot guarantee complete security.</p>
                        <p><strong>6. Your Rights</strong><br>You can access, correct, or request deletion of your personal info by contacting us.</p>
                        <p><strong>7. Changes</strong><br>We may update this policy and will post changes on our website.</p>
                        <p><strong>8. Contact</strong><br>For questions, email us at hiremeapp722@gmail.com.</p>

                        <h2>Terms of Service</h2>
                        <p><strong>Effective Date:</strong> September 1, 2024</p>
                        <p><strong>1. Agreement</strong><br>By using HireMe-App, you agree to these terms. If you disagree, do not use our services.</p>
                        <p><strong>2. User Responsibilities</strong><br>- Keep your account details secure.<br>- Don’t use the site for illegal activities.</p>
                        <p><strong>3. Intellectual Property</strong><br>All content on our site is owned by us or our partners.</p>
                        <p><strong>4. Liability</strong><br>We aren’t liable for any damages arising from your use of our site.</p>
                        <p><strong>5. Termination</strong><br>We can suspend or terminate your account if you breach these terms.</p>
                        <p><strong>6. Changes</strong><br>We may update these terms, and your continued use means you accept the changes.</p>
                        <p><strong>7. Contact</strong><br>For any questions, email us at hiremeapp722@gmail.com.</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Checkbox with Modal Trigger -->
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" required>
                    <label class="form-check-label" for="terms-conditions">
                      I agree to <a href="#" data-bs-toggle="modal" data-bs-target="#popupModal">Privacy Policy & Terms</a>
                    </label>
                  </div>
                </div>

                <button id="signup" class="btn btn-secondary d-grid w-100" disabled>Sign up</button>
              </form>

              <p class="text-center">
                <span>Already have an account?</span>
                <a href="./login.php">
                  <span>Sign in instead</span>
                </a>
              </p>
            </div>
          </div>
          <!-- Register Card -->
        </div>
      </div>
    </div>
    <!-- / Content -->

    <script src="./assets/vendor/libs/jquery/jquery.js"></script>
    <script src="./assets/vendor/libs/popper/popper.js"></script>
    <script src="./assets/vendor/js/bootstrap.js"></script>
    <script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="./assets/vendor/js/menu.js"></script>
    <script src="./assets/js/main.js"></script>
    <script>
      let existingUsernames = [];
      let existingEmails = [];

      $(document).ready(function () {
          $.getJSON('./functions/get_users.php', function (data) {
              existingUsernames = data.map(user => user.Username.toLowerCase());
              existingEmails = data.map(user => user.Email.toLowerCase());
          });
        
          $('#username').on('input', function () {
              const value = $(this).val().toLowerCase();
              let usernamewarning = document.getElementById("usernamewarning");
              let signup = document.getElementById("signup");
              let usernamevalue = document.getElementById("username");

              if (existingUsernames.includes(value)) {
                  usernamewarning.textContent = "Username is already taken.";
                  usernamewarning.classList.remove("text-success");
                  usernamewarning.classList.add("text-danger");
              
                  // signup.disabled = true;
                  // signup.classList.remove("btn-warning");
                  // signup.classList.add("btn-secondary");
              } else if (!existingUsernames.includes(value) && value !== "") {
                  if (usernamevalue.checkValidity()) {
                      usernamewarning.textContent = "Username is available.";
                      usernamewarning.classList.remove("text-danger");
                      usernamewarning.classList.add("text-success");
                  }
                
                  // signup.disabled = false;
                  // signup.classList.add("btn-warning");
                  // signup.classList.remove("btn-secondary");
              }
          });
        
          $('#email').on('input', function () {
              const value = $(this).val().toLowerCase();
              const emailwarning = document.getElementById("emailwarning");
              const verifyBtn = document.getElementById("checkEmailBtn");
              const signup = document.getElementById("signup");

              if (existingEmails.includes(value)) {
                  emailwarning.textContent = "Email is already taken.";
                  emailwarning.classList.remove("text-success");
                  emailwarning.classList.add("text-danger");
              
                  verifyBtn.disabled = true;
                  // signup.disabled = true;
                  // signup.classList.remove("btn-warning");
                  // signup.classList.add("btn-secondary");
              } else if (value !== "") {
                  const emailInput = document.getElementById("email");
                  if (emailInput.checkValidity()) {
                      emailwarning.textContent = "Email is available.";
                      emailwarning.classList.add("text-success");
                      emailwarning.classList.remove("text-danger");
                  
                      verifyBtn.disabled = false;
                      // signup.disabled = false;
                      // signup.classList.add("btn-warning");
                      // signup.classList.remove("btn-secondary");
                  } else {
                      emailwarning.textContent = "Email is not valid.";
                      emailwarning.classList.remove("text-success");
                      emailwarning.classList.add("text-danger");
                  
                      verifyBtn.disabled = true;
                      // signup.disabled = true;
                      // signup.classList.remove("btn-warning");
                      // signup.classList.add("btn-secondary");
                  }
              }
          });

          document.getElementById("password").addEventListener("input", function () {
              const value = this.value;

              const novalue = value === "";
              const minLength = value.length >= 6;
              const hasLetter = /[A-Za-z]/.test(value);
              const hasNumber = /\d/.test(value);
              const noSpecialChar = /^[A-Za-z\d]*$/.test(value);
              const warning = document.getElementById("passwordwarning");

              if (novalue) {
                warning.innerHTML = `<span style="color: red"> Password required.</span>`;
              } else {
                warning.innerHTML = `
                  <li style="color: ${minLength ? 'green' : 'red'}"> Minimum 6 characters</li>
                  <li style="color: ${noSpecialChar ? 'green' : 'red'}"> No special characters</li>
                  <li style="color: ${(hasLetter && hasNumber) ? 'green' : 'red'}"> At least 1 letter and 1 number</li>
              `;
              }
              
          });

      });
    </script>
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
          }, 3000);
      }
      
      $(document).ready(function() {
          $('#formAuthentication').on('submit', function(e) {
              e.preventDefault();
          
              var formData = $(this).serialize();
          
              $.ajax({
                  type: 'POST',
                  url: './functions/register.php',
                  data: formData,
                  dataType: 'json',
                  success: function(response) {
                      if (response.status === 'success') {
                          $('.overlay').show();
                          showToast(response.message, 'success');
                          setTimeout(function() {
                              window.location.href = response.redirect;
                          }, 2500);
                      } else if (response.status === 'error') {
                          showToast(response.message, 'warning');
                      } else {
                          console.error('Unknown response status:', response.status);
                      }
                  },
                  error: function(xhr, status, error) {
                      console.error('AJAX Error:', error);
                      var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred.';
                      showToast(errorMessage, 'warning');
                  }
              });
          });
      });
    </script>
    <script src="https://otp.dev/js/getotp.min.js"></script>
    <script>
        $(document).ready(function() {
        getotp.init({
                  ui_mode: 'modal',
                  dev_mode: false,   // true for dev mode (no api usage)
              });
        let otp_id = null;
      
        document.getElementById('checkEmailBtn').addEventListener('click', function (e) {
          e.preventDefault();
          let email = document.getElementById('email').value;
        
          $.ajax({
            url: './functions/otp.php',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
            },
            success: function(response) {
              if (response.email == "Enter a valid email address.") {
                showToast('Enter a valid email address.', 'warning');
                return;
              }
                console.log('response', response);
                let otp_url = response.link;
                otp_id = response.otp_id;
                getotp.showModal(String(otp_url));
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                console.error('Error:', response);
                showToast('OTP API Error: ' + response.message, 'warning');
            }
          });
        
        });
      
        getotp.onSuccess(function (payload) {
          let callback_otp_id = payload.otp_id;
          let redirect_url = payload.redirect_url;
          if(callback_otp_id == otp_id){
            let signupbtn = document.getElementById("signup");
            signupbtn.disabled = false;
            signupbtn.classList.add("btn-warning");
            signupbtn.classList.remove("btn-secondary");
            showToast('OTP verified successfully. You can now proceed.', 'success');
          }else{

          }
          // alert(redirect_url);
          getotp.closeModal(); 
        });
      
        getotp.onFailed(function (payload) {
          let callback_otp_id = payload.otp_id;
          let redirect_url = payload.redirect_url;
          if(callback_otp_id == otp_id){
            let signupbtn = document.getElementById("signup");
            signupbtn.disabled = true;
            signupbtn.classList.remove("btn-warning");
            signupbtn.classList.add("btn-secondary");
            showToast('OTP failed. Please try again.', 'error');
          }else{
            
          }
          // alert(redirect_url);
          getotp.closeModal(); 
        });
      
      });

    </script>
  </body>
</html>
