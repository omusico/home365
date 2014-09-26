<?php
$google_url="https://accounts.google.com/o/oauth2/auth";
$google_client_id = '284374674672-jf0biqksc1ervnom4j6kdr4kt4hr2ai4.apps.googleusercontent.com';
$google_response_type = 'token';
$google_call_back_url = 'http://www.home365.ca/googleoauth2callback.php';
$google_scope = 'https://www.googleapis.com/auth/userinfo.profile';
$google_login_url=$google_url."?client_id=".$google_client_id."&&response_type=".$google_response_type."&&redirect_uri=".$google_call_back_url."&&scope=".$google_scope;
?>