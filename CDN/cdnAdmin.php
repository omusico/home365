<?php
//	please add container names
	switch ($_domainName) {
		case 'city365.ca':
			$_cdnDomain=$_liveDomain;
			$_cdnSSLDomain=$_liveSSLDomain;
			$_containerName='city365.ca';
			break;
		case 'http://devel.weidaily.com/':
			$_cdnDomain=$_develDomain;
			$_cdnSSLDomain=$_develSSLDomain;
			$_containerName='devel';
			break;
		case 'demo.city365.ca':
			$_cdnDomain=$_demoDomain;
			$_cdnSSLDomain=$_demoSSLDomain;
			$_containerName='';
			break;
		case 'home365.ca':
			$_cdnDomain=$_home365domain;
			$_containerName='home365';
			break;
	}
?>