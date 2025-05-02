<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    
    $data = array(
        'channel' => 'email',
        'email' => $email,
        'embed' => 'compact',
        'callback_url' => 'https://hireme-app.online/register.php',
        'success_redirect_url' => 'https://hireme-app.online/register.php/?status=success',
        'fail_redirect_url' => 'https://hireme-app.online/register.php/?status=fail',
        'hide' => 'true',
        'captcha' => 'false',
        'lang' => 'en',
        'metadata' => '{"order_id":"xfdu48sfdjsdf", "agent_id":2258}',
    );
    
    $api_url = 'https://otp.dev/api/verify/';
    $username = 'yq6b5zQ1lWJMDc7mFNHfYji8rvePIKx2'; // key
    $password = 'c8ixvo40mf1lkj2hyr5t9su7wqze6dga'; // token
    $auth_header = 'Basic ' . base64_encode($username . ':' . $password);
    
    $ch = curl_init($api_url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: ' . $auth_header,
        'Content-Type: application/x-www-form-urlencoded'
    ));
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo json_encode(array('error' => 'cURL Error: ' . curl_error($ch)));
        exit;
    }
    
    curl_close($ch);
    
    $response_data = json_decode($response, true);

    if (isset($response_data['message']) && isset($response_data['code'])) {
        http_response_code(400);
        echo $response;
    } else {
        http_response_code(200);
        echo $response;
    }

} else {
    http_response_code(400);
    echo "OTP error. Please try again.";
}
