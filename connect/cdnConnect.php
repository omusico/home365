<?php
	$document_root='/home/city365/www';
	require_once($document_root.'/Connections/cdnDefine.php');
	require_once($document_root.'/opencloud/lib/php-opencloud.php');
	//require_once $_SERVER['DOCUMENT_ROOT'].'/opencloud/lib/php-opencloud.php';
	//require_once $_SERVER['DOCUMENT_ROOT'].'/Connections/cdnDefine.php';
	if(isset($_SERVER["SERVER_NAME"])){
		$_domainName=preg_replace('/^www\./','',$_SERVER["SERVER_NAME"]);
	}else{
		$_domainName="city365.ca";
	}
	require_once($document_root.'/Connections/cdnAdmin.php');
	//require_once $_SERVER['DOCUMENT_ROOT'].'/Connections/cdnAdmin.php';

	$cloud = new \OpenCloud\Rackspace(AUTHURL,array('username'=>USERNAME,'apiKey' => APIKEY ));
	if ($cloud) {
		$cloud->Authenticate();
		$cloud->SetDefaults('ObjectStore', 'cloudFiles', 'ORD');
	};
	
?>