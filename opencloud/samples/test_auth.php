<?php
/**
 * (c)2012 Rackspace Hosting. See COPYING for license details
 * This script attempts to validate an authentication bug that appears daily.
 *
 */
require_once "../lib/php-opencloud.php";

/**
 * Relies upon environment variable settings — these are the same environment
 * variables that are used by python-novaclient. Just make sure that they're
 * set to the right values before running this test.
 
define('AUTHURL', $_ENV['NOVA_URL']);
define('USERNAME', $_ENV['OS_USERNAME']);
define('TENANT', $_ENV['OS_TENANT_NAME']);
define('APIKEY', $_ENV['NOVA_API_KEY']);
 */
define('AUTHURL', 'https://identity.api.rackspacecloud.com/v2.0/');
define('USERNAME', 'onyourblog');
define('TENANT', '839319');
define('APIKEY', 'b3fa0f633b6f09d61c8e37b2b414769b');

$mysecret = array(
    'username' => 'onyourblog',
    'password' => 'YVR2013ca'
);

// establish our credentials
if ($connection = new \OpenCloud\OpenStack(AUTHURL, $mysecret)) {
	echo 'connection established';
} else {
	echo 'connection failed';
};

$rackspace = new \OpenCloud\Rackspace(AUTHURL,
	array( 'username' => USERNAME,
		   'apiKey' => APIKEY ));
		   
//while(TRUE) {
	$rackspace->Authenticate();
	$arr = $rackspace->ExportCredentials();
	printf("%s Token [%s] expires in %5d seconds\n", 
		date('r'),
		$arr['token'],
		$arr['expiration']-time());
//	sleep(60);
//}