<?php
	$document_root='/home/home365/public_html';
	require_once($document_root.'/CDN/cdnDefine.php');
	require_once($document_root.'/opencloud/lib/php-opencloud.php');
	$_domainName='home365.ca';
	require_once($document_root.'/CDN/cdnAdmin.php');
	//require_once $_SERVER['DOCUMENT_ROOT'].'/Connections/cdnAdmin.php';

	$cloud = new \OpenCloud\Rackspace(AUTHURL,array('username'=>USERNAME,'apiKey' => APIKEY ));
	if ($cloud) {
		$cloud->Authenticate();
		$cloud->SetDefaults('ObjectStore', 'cloudFiles', 'ORD');
	};
	
?>