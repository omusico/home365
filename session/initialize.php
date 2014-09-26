<?php 
if (!isset($_SESSION)) {
	session_start();
	$_SESSION['RemoteIP'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['RemoteIPLong'] = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
	$_SESSION['session_id']=session_id();
}
//header("Content-Type: text/html; charset=utf-8");
// sets default character encoding to UTF-8
//mb_internal_encoding("UTF-8");
?>