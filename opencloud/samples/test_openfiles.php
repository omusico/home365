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
define('APIKEY', 'b3fa0f633b6f09d61c8e37b2b414769b');

$cloud = new \OpenCloud\Rackspace(AUTHURL,array('username'=>USERNAME,'apiKey' => APIKEY ));
$cloud->Authenticate();

$arr = $cloud->ExportCredentials();
printf("%s Token [%s] expires in %5d seconds\n", 
	date('r'),
	$arr['token'],
	$arr['expiration']-time());

echo '<br>';

if ($cloud) {
	echo 'rackspace connection established<br>';
	$cloud->SetDefaults('ObjectStore', 'cloudFiles', 'ORD');

	$ostore = $cloud->ObjectStore(); // uses default values
	if ($ostore) {
		echo 'ostore opened<br>';
		$containerlist = $ostore->ContainerList();
	
		while($container = $containerlist->Next()) {
			// do something with the container
			printf("Container %s has %u bytes<br>",
				$container->name, $container->bytes);
		}
		
		$oyb_01=$ostore->Container('oyb_01');
		echo 'Container '.$oyb_01->name.' has the following objects:<br>';
		$objlist = $oyb_01->ObjectList();
		while($object = $objlist->Next()) {
			printf("Object %s, size=%u\n", $object->name, $object->bytes);
		}
		
		
		$oyb_test=$ostore->Container('oyb_test');
		echo 'Container '.$oyb_test->name.' has the following objects:<br>';
		$objlist = $oyb_test->ObjectList();
		while($object = $objlist->Next()) {
			printf("Object %s, size=%u<br>", $object->name, $object->bytes);
		}
		
		$image_size=array('org_','','med_','tn_');

		$image_folder='/cmsusers/2011/12/20/10005/images/2013/08/01/';
		$cdn_folder=substr($image_folder,1);
		$image_name='1375389681_42573d73_91a7bc9d_cdef3937_5562aa08_8c386c85.jpg';
		
		$error=false;
		for ($i=0;$i<count($image_size)&&!$error;$i++) {
			$mypicture = $oyb_test->DataObject();
			try {
				echo '<br>copying '.$cdn_folder.$image_size[$i].$image_name;
				
				$mediaInfo=getimagesize($_SERVER["DOCUMENT_ROOT"].$image_folder.$image_size[$i].$image_name);
				switch ($mediaInfo[2]) {
					case IMAGETYPE_GIF: $mime_type='image/gif'; break;
					case IMAGETYPE_JPEG: $mime_type='image/jpeg'; break;
					case IMAGETYPE_PNG: $mime_type='image/png'; break;
					case IMAGETYPE_BMP: $mime_type='image/bmp'; break;
					default: $mime_type=''; break;
				}
				
				// this will fail if the container doesn't exist
				$mypicture->Create(
					array('name'=>$cdn_folder.$image_size[$i].$image_name, 'content_type'=>$mime_type),
					$_SERVER["DOCUMENT_ROOT"].$image_folder.$image_size[$i].$image_name);
			} catch (Exception $e) {
				$error=true;
				// print error
				echo ', error:'.$e->getMessage();
			}
		}
		if (!$error) {
		} else {
		}
	} else {
		echo 'ostore failed to open';
	}
	
} else {
	echo 'rackspace connection failed';
}
