$(document).ready(function() {
  getotp.init();
  let otp_id = null;

  document.getElementById("email").addEventListener("input", function () {
      let emailValue = this.value.trim();
      let verifyBtn = document.getElementById("checkEmailBtn");
      verifyBtn.disabled = emailValue === "";
  });

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
          let otp_url = response.link;
          otp_id = response.otp_id;
          getotp.showModal(String(otp_url));
        },
      error: function(xhr) {
          console.error('Error:', xhr.responseText);
      }
    });

  });

  getotp.onSuccess(function (payload) {
    let callback_otp_id = payload.otp_id;
    let redirect_url = payload.redirect_url;
    if(callback_otp_id == otp_id){
      alert('it is the same!');
    }else{
      alert('it is not the same!');
    }
    alert(redirect_url);
    getotp.closeModal(); 
  });

  getotp.onFailed(function (payload) {
    let callback_otp_id = payload.otp_id;
    let redirect_url = payload.redirect_url;
    if(callback_otp_id == otp_id){
      alert('it is the same!');
    }else{
      alert('it is not the same!');
    }
    alert(redirect_url);
    getotp.closeModal(); 
  });

});
