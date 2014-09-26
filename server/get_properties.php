<?php 
//echo $_SERVER['DOCUMENT_ROOT'];


require_once 'get_properties_body.php';
$from=isset($_POST['from'])?$_POST['from']:$_GET['from'];
$size=isset($_POST['size'])?$_POST['size']:$_GET['size'];
$theme=isset($_POST['theme'])?$_POST['theme']:$_GET['theme'];
$properties_array=get_properties_summary($from, $size,$theme);
echo json_encode($properties_array);
//var_dump($properties_array);
?>
