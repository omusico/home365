<?php
###
###   ##################################################################################
###
###   RETS.PHP was created by Real Estate Board of Greater Vancouver, Fraser Valley Real 
###   Estate Board, Realtors Association of Edmonton and Calgary Real Estate Board ("REB4").  
###   Copyright © 2008, REB4.
###
###   If this software has been modified by any third party, that third party is required 
###   to include their name and the date of modification here:  James Zhao 03/12/2014.
###
###   This software is licensed AS IS (with no representations, warranties or guarantees) 
###   pursuant to the terms of the RETS.PHP licence which you should have received with 
###   this software.  If you did not receive the licence, you can obtain a copy here: 
###   http://www.rebgv.org/rets/license.asp
###
###   ##################################################################################
###
###   Name ...........: RETS.php
###
###   Function .......: This class library provides a working example of RETS Client
###                     functionality. It it tailored to the Interealty RETS Server,
###                     but should work against any RETS Server.
###                     
###   Developer ......: XMundi Enterprises (on behalf of REB4)
###               
###                     REB4 is comprised of:
###                       Real Estate Board of Greater Vancouver: <http://www.rebgv.org> 
###                       Fraser Vally Real Estate Board: <http://www.fvreb.bc.ca> 
###                       Realtors Association of Edmonton: <http://www.ereb.com> 
###                       Calgary Real Estate Board: <http://www.creb.com> 
###
###   Requirements ...: PHP 5.x (testing has been done with 5.3.2)
###
###   Usage ..........: PHP command line (see examples below)
###
###   Comments .......: This code can be used as is, or as a model for another development 
###                     effort. Please note that it has not been optimized in any way,
###                     does not use caching or streaming. The intention is to provide
###                     the minimum requirements for a working RETS Client in PHP and
###                     to give developers a sense of what is involved in putting a RETS
###                     Client together. 
### 
###                     Many of the procedures in this class take inputs that are directly 
###                     tied to the requirements of RETS. For clarification on DMQL (the 
###                     RETS query language) or any of the other parameters used by RETS,
###                     it may be helpful to refer to the RETS Specification. This
###                     documentation is available here: http://rets.org/documentation.
###                     Please note that this code is developed for a RETS 1.x RETS Server,
###                     RETS 1.5 or later.
###
###                     Feel free to share your feedback or code enhancements
###                     by emailing rets@rebgv.org.
###
###   Revision: ......: 1.5
###   Date: ..........: 03/12/2010
###
###   ---------------------------------------------------------------------------
###   Release Notes History
###
###   Revision:
###   1.0   04/17/2008   Initial Release.
###   1.1   11/17/2008   Fixed GetDataArray to read XML properly.
###   1.2   02/10/2009   Fixed parsing of multi-part images to handle end-of-line characters,
###                      and changed quotes around passwords to handle special characters.
###   1.3   11/05/2009   Added method for SafeMLS authentication.
###   1.4   03/11/2010   Added handling for RETS Full Key Download,
###                      Modified GetPhoto to return number of photos extracted rather than a true,
###                      Modified GetObjectParts to better handle errors and null responses,
###                      Updated Example Usage with more examples.
###   1.5   03/12/2010   Fixed bug in cookie handling.
###   ----------------------------------------------------------------------------------
###
###   Example Usage:
###
###       <-- ?php #note that this line needs to be modified to be a closing php tag 
###       require_once 'RETS.php';
###     
###       # set up RETS instance
###       # note: all values within <> need to be replaced
###       # note: all the resource, class, lookup, and field identifiers in the examples 
###       #       are from the BRC system
###       $rets = new RETS();
###       $rets->url='<rets server including full path to login transaction>'
###       $rets->user='<user id>';
###       $rets->password='<user password>';
###       $rets->useragent='<application user-agent>';
###       $rets->useragent_password='<application password>';
###
###       # login example
###       $response=$rets->Login();
###     
###       # login example using SafeMLS
###       $rets->safemls_pin="<SafeMLS Device PIN>";
###       $rets->password="<Password Generated by SafeMLS Token>";
###       $response=$rets->Login();
###     
###       # get metadata examples
###       #
###       # resource metadata where ID=0 (all resources)
###       $response=$rets->GetMetadata('METADATA-RESOURCE','0');
###       print $response;
###       print "\n\n";
###       #
###       # class metadata where ID=0 (all resources and classes)
###       $response=$rets->GetMetadata('METADATA-CLASS','0');
###       print $response;
###       print "\n\n";
###       #
###       # class metadata where ID=Property:0 (all classes in Property resource)
###       $response=$rets->GetMetadata('METADATA-CLASS','Property:0');
###       print $response;
###       print "\n\n";
###       #
###       # table metadata where ID=Property:11 (table structure for XPROP class in Property resource)
###       $response=$rets->GetMetadata('METADATA-TABLE','Property:11');
###       print $response;
###       #
###       # lookup metadata where ID=Property:* (all lookups for Property resource) 
###       $response=$rets->GetMetadata('METADATA-LOOKUP_TYPE','Property:*');
###       print $response;
###       print "\n\n";
###       #
###       # lookup metadata where ID=Property:1_299 (1_299 lookup, associated with Status field, for Property resource) 
###       $response=$rets->GetMetadata('METADATA-LOOKUP_TYPE','Property:1_299');
###       print $response;
###       print "\n\n";
###     
###       # get record count where: Resource=Property, 
###       #                         Class=11 (XPROP), 
###       #                         Query=Status (field 363) is Active
###       $response=$rets->GetCount('Property','11','(363=|A)');
###       print $response;
###       print "\n\n";
###       
###       # get record count where: Resource=Property, 
###       #                         Class=11 (XPROP), 
###       #                         Query=Status (field 363) is Active and
###       #                               LastTransDate (field 217) is in 2010
###       $response=$rets->GetCount('Property','11','(363=|A),(217=2010-01-01T00:00:00-2010-12-31T23:59:59)');
###       print $response;
###       print "\n\n";
###       
###       # get data in an array where: Resource=Property, 
###       #                             Class=11 (XPROP), 
###       #                             Query=Status (field 363) is Active,
###       #                             Select Fields=sysid,248,363,217 (sysid, MLS#, Status, LastTransDate)
###       #                             Num Records=Limit to 2
###       $response=$rets->GetDataArray('Property','11','(363=|A)','sysid,248,363,217',2);
###       var_dump($response);
###       print "\n\n";
###
###       # get data using full key download 
###       # and write to file where: Resource=Property, 
###       #                          Class=11 (XPROP), 
###       #                          Query=Status (field 363) is Active,
###       #                          Select Fields=All,
###       #                          Num Records=Unlimited
###       $rets->CreateDataFile('Property','11','(363=|A)',null,null,"c:\data\active_listings.txt");
###       print "\n\n";
###
###       # get image examples
###       # note: that the ids passed through are photo keys which, dependent
###       #       on the RETS server are not the same as the MLS#;
###       #       in the case of BRC the photo key is the listing SYSID
###       #
###       # get primary image
###       $response=$rets->GetPhoto('Property','62400551:1', 'c:\images');
###       print "\n\n";
###       # get all images for a given listing
###       $response=$rets->GetPhoto('Property','253110929:*', 'c:\images');
###       print "\n\n";
###     
###       # logout example
###       $response=$rets->Logout();
###       ? --> #note that this line needs to be modified to be a closing php tag 
###   ##################################################################################
	  $document_root='/home/home365/public_html';
	  error_reporting(E_ALL);
	  require_once $document_root.'/CDN/cdnConnect.php';
	  //echo $_cdnDomain."<br/>";
	  //echo $_containerName.'<br/>';

class RETS {

#------------------------------------------------------------------------------------
# Public Instance Variables
#
  public $url;
  public $user;
  public $password;
  public $safemls_pin;
  public $useragent;
  public $useragent_password;
  public $rets_version = "RETS/1.7";

  public $metadata_types = array(
      "METADATA-SYSTEM",
      "METADATA-RESOURCE",
      "METADATA-FOREIGNKEYS",
      "METADATA-CLASS",
      "METADATA-OBJECT",
      "METADATA-LOOKUP",
      "METADATA-LOOKUP_TYPE",
      "METADATA-TABLE"
    );

  public $keyfield="sysid";
  public $batchsize=500;
#
#------------------------------------------------------------------------------------

#------------------------------------------------------------------------------------
# Private Instance Variables
#
  private $headers = array();
  private $cookies = array();
  private $rets_ua_authorization;

  # the following are used for parsing getobject responses
  private $content_type;
  private $content_multipart_boundary;
  private $content_parts;
  private $safemls_serverinfo;

  private $login_info = null;
  private $root_url;

  # $capbability_urls stores the URLs for the various transaction types
  private $capability_urls = array(
    'Login' => null,
    'Logout' => null,
    'Search' => null,
    'GetMetadata' => null,
    'GetObject' => null,
    'ChangePassword' => null
  );
#
#------------------------------------------------------------------------------------

#------------------------------------------------------------------------------------
# Procedure: Login
#
# Purpose:   Login to the RETS Server
#
# Input:     None
#
# Returns:   Returns true if successful
#
# Note:      None
#------------------------------------------------------------------------------------
  public function Login() {
    $login_success = false;

    if ($this->safemls_pin == null) {
      print "Logging in ...\n";
      try {
        $response = $this->GetRequest($this->url);
        $login_success = true;
      } catch(Exception $e) {
        $login_success = false;
      }
    } else {
      print "Logging in with SafeMLS ...\n";
      $response = $this->GetRequest($this->url, "", "RETS-Challenge: scheme=\"SAFEMLS\" user=\"$this->user\"", true);
      $response_password = $this->password . $this->safemls_pin;
      try {
        $response = $this->GetRequest($this->url, "", "RETS-Challenge: scheme=\"SAFEMLS\" response=\"$response_password\" challenge=\"safemls\" serverinfo=\"$this->safemls_serverinfo\"", false);
        $login_success = true;
      } catch(Exception $e) {
        $login_success = false;
      }
    }
  
    if ($login_success) {
      print "parse capability urls and store\n";
      try {
        $xml = new XMLReader(); 
        $xml->XML($response);
        while ($xml->read()) {
          if ($xml->name=="RETS-RESPONSE") {
            $xml->read();
            $inner_response = $xml->value;
            break;
          }
        }
  
        # inspect the response and parse the capability urls for other transaction types
        $response_lines = preg_split('/[\r\n]+/', $inner_response, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($response_lines as $line) {
          if ($line=="") {
            continue;
          }
          list($key,$value) = explode("=", $line);
          if (array_key_exists($key, $this->capability_urls)) {
            #if url is relative swap it on to default url
            if (preg_match('/^\//', $value)) {
              if ($this->root_url=="") {
                preg_match('@^(?:http://)?([^/]+)@i', $this->url, $matches); 
                $this->root_url = $matches[0];
              }
              $value = $this->root_url.$value;
            }
            $this->capability_urls[$key]=$value;
          }
          else
          {
            $this->login_info[$key]=$value;
          }
        }
      } catch (Exception $e) {
        $login_success = false;      
      }
    }
    return $login_success;
  }

#------------------------------------------------------------------------------------
# Procedure: Logout
#
# Purpose:   Logout from the RETS Server
#
# Input:     None
#
# Returns:   Returns true if successful
#
# Note:      None
#------------------------------------------------------------------------------------
  public function Logout() {
    print "Logging out ...\n";
    $response = $this->GetRequest($this->capability_urls['Logout']);
    return true;
  }

#------------------------------------------------------------------------------------
# Procedure: IsValid User
#
# Purpose:   Verifies User on RETS Server
#
# Input:     None
#
# Returns:   Returns true if successful
#
# Note:      None
#------------------------------------------------------------------------------------
  public function isValidUser() {
    $retStatus = $this->Login();
    if ($retStatus) {
      $this->Logout();
    }
    return $retStatus;
  }

#------------------------------------------------------------------------------------
# Procedure: GetMetadata
#
# Purpose:   Retrieves Metadata from the RETS Server
#
# Input:     $medadata_type: Type of metadata to retrieve (see $metadata_types)
#            $id: The ID to retrieve
#
# Returns:   Returns the raw response from the RETS Server
#
# Note:      This function shows how to retrieve the metadata but does
#            parse the output in any way. A optimized RETS Client would
#            cache metadata locally and check for modifications when
#            deciding whether or not to download a given set of metadata
#            again.
#------------------------------------------------------------------------------------
  public function GetMetadata($metadata_type,$id) {
    if (!in_array($metadata_type, $this->metadata_types)) {
      print "Invalid metadata_type. Valid values are:\n";
      foreach ($this->metadata_types as $mdt) {
        print "$mdt\n";
      }
      return "";
    }
    print "Getting Metadata $metadata_type (ID=$id)...\n";
    $request_string=$this->capability_urls['GetMetadata']."?Format=COMPACT&Type=$metadata_type&ID=$id";
    $response = $this->GetRequest($request_string);
    return $response;
  }

#------------------------------------------------------------------------------------
# Procedure: GetDataKeysArray
#
# Purpose:   Retrieves Data Keys from the RETS Server
#
# Input:     $resource: RETS Resource
#            $class: RETS Class
#            $query: RETS DMQL query
#            $maxrows: Maximum number of rows of data to retrieve
#
# Returns:   Returns RETS key data in an array.
#
# Note:      This function parses through a RETS Search response and builds
#            an array out of the results. For details on DMQL, please set the 
#            RETS Specification. 
#------------------------------------------------------------------------------------
  public function GetDataKeysArray($resource,$class,$query,$maxrows) {
    print "Getting Keys ...\n";
    # get the keys
    $keyresponse = $this->GetDataArray($resource,$class,$query,$this->keyfield,$maxrows);
    return $keyresponse;
  }

#------------------------------------------------------------------------------------
# Procedure: GetDataArrayFromKeyData
#
# Purpose:   Retrieves Data from the RETS Server
#
# Input:     $resource: RETS Resource
#            $class: RETS Class
#            $query: RETS DMQL query
#            $selectfields: Comma separated list of fields to select
#            $keydata: Key Data array from GetDataKeysArray request
#            $index: Start index
#            $numrecs: Number of records to return (<200)
#
# Returns:   Returns the RETS data in an array.
#
# Note:      This function parses through a RETS Search response and builds
#            an array out of the results. For details on DMQL, please set the 
#            RETS Specification. 
#------------------------------------------------------------------------------------
  public function GetDataArrayFromKeyData($resource,$class,$selectfields,$keydata,$index,$numrecs) {
    print "Getting Data from Key Data ... $numrecs records starting at $index\n";

    # loop through keys
    $data = null;
    $keystr = "";
    $index_start = $index;
    $index_stop = $index_start + $numrecs;
    if ($index_stop > sizeof($keydata)) {
      $index_stop = sizeof($keydata);
    }
    for ($i=$index; $i<$index_stop; $i++) {
          if (strlen($keystr)>0) {
            $keystr .= ",";
          }
          $keystr .= $keydata[$i][$this->keyfield];
          if ((fmod(($i+1),$numrecs)==0) || $i==sizeof($keydata)-1)  {
            $keyquery = "($this->keyfield=$keystr)";
            $response = $this->GetData($resource,$class,$keyquery,$selectfields,null);
        $this->ParseRetsSearchResponse($response,$data);
        $keystr = "";
      }
    }
    return $data;
  }

#------------------------------------------------------------------------------------
# Procedure: GetDataArray
#
# Purpose:   Retrieves Data from the RETS Server
#
# Input:     $resource: RETS Resource
#            $class: RETS Class
#            $query: RETS DMQL query
#            $selectfields: Comma separated list of fields to select
#            $maxrows: Maximum number of rows of data to retrieve
#
# Returns:   Returns the RETS data in an array.
#
# Note:      This function parses through a RETS Search response and builds
#            an array out of the results. For details on DMQL, please set the 
#            RETS Specification. 
#------------------------------------------------------------------------------------
  public function GetDataArray($resource,$class,$query,$selectfields,$maxrows) {
    $response = $this->GetData($resource,$class,$query,$selectfields,$maxrows); 
    $data = null;
    $this->ParseRetsSearchResponse($response,$data);
    return $data;
  }

#------------------------------------------------------------------------------------
# Procedure: GetData
#
# Purpose:   Retrieves data from the RETS Server
#
# Input:     $resource: RETS Resource
#            $class: RETS Class
#            $query: RETS DMQL query
#            $selectfields: Comma separated list of fields to select
#            $maxrows: Maximum number of rows of data to retrieve
#
# Returns:   Returns the raw response from the RETS Server
#
# Note:      This function shows how to retrieve the metadata but does
#            parse the output in any way. For details on DMQL, please set the
#            RETS Specification.
#------------------------------------------------------------------------------------
  public function GetData($resource,$class,$query,$selectfields,$maxrows) {
    print "Getting Data ...\n";
    $request_string=$this->capability_urls['Search']."?Format=COMPACT-DECODED&QueryType=DMQL2&Count=1&SearchType=$resource&Class=$class&Query=$query";
    if ($selectfields!=null) {
      $request_string = $request_string . "&Select=" . $selectfields;
    }
    if ($maxrows!=null) {
      $request_string = $request_string . "&Limit=" . $maxrows;
    }
    $response = $this->GetRequest($request_string);
    return $response;
  }


#------------------------------------------------------------------------------------
# Procedure: CreateDataFile
#
# Purpose:   Retrieves records using full key download and write to file
#
# Input:     $resource: RETS Resource
#            $class: RETS Class
#            $query: RETS DMQL query
#            $selectfields: Comma separated list of fields to select
#            $maxrows: Maximum number of rows of data to retrieve
#            $filepath: Local path to save data to
#
# Returns:   Returns the numeric count of records that match the criteria of the DMQL 
#            query.
#
# Note:      This function parses the count out of the RETS Response. For details on 
#            DMQL, please set the RETS Specification.
#------------------------------------------------------------------------------------
  public function CreateDataFile($resource,$class,$query,$selectfields,$maxrows,$outputfile) {
    $numkeys=-1;
    $keys=$this->GetDataKeysArray($resource,$class,$query,$maxrows);
    if ($keys!=null) {
     $numkeys=sizeof($keys);
     print "found $numkeys keys\n";
     if ($numkeys>0) {
       print "exporting records to $outputfile\n";
       $f = fopen($outputfile, "w");
       for ($i=0; $i<$numkeys; $i=$i+$this->batchsize) {
         $response=$this->GetDataArrayFromKeyData($resource,$class,$selectfields,$keys,$i,$this->batchsize);
         if ($response!=null) {
           foreach ($response as $key => $value) {
			   $dataline="New Record\n";
               $dataline.=implode("|",$value) . "\n";
			   //$dataline="$key=>$value||\n";
               fwrite($f, $dataline);
           }
         }
       }
       fclose($f);
     }
   }
   return $numkeys;
  }

#------------------------------------------------------------------------------------
# Procedure: GetCount
#
# Purpose:   Retrieves record count from the RETS Server
#
# Input:     $resource: RETS Resource
#            $class: RETS Class
#            $query: RETS DMQL query
#
# Returns:   Returns the numeric count of records that match the criteria of the DMQL 
#            query.
#
# Note:      This function parses the count out of the RETS Response. For details on 
#            DMQL, please set the RETS Specification.
#------------------------------------------------------------------------------------
  public function GetCount($resource,$class,$query) {
    print "Getting Count ...\n";
    $request_string=$this->capability_urls['Search']."?Format=COMPACT-DECODED&QueryType=DMQL2&Count=2&SearchType=$resource&Class=$class&Query=$query";
    $response = $this->GetRequest($request_string);

    # parse RETS response for count
    $xml = new XMLReader();
    $xml->XML($response);
    $numRec = -1;
    while ($xml->read()) {
      if ($xml->name=="COUNT") {
        if ($xml->getAttribute("Records") != "")
        {
          $numRec = $xml->getAttribute("Records");  
        }
      }
    }
    return $numRec;
  }
 //Private function created by James Zhao to detect if an image is broken
	public function is_img($img_path){
		$image_types = array("image/jpeg", "image/jpg", "image/pjpg", "image/pjpeg", "image/gif", "image/pgif", "image/png", "image/ppng");
		$data = getimagesize($img_path);
		if(in_array($data[3],$image_types)){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
#------------------------------------------------------------------------------------
# Procedure: GetPhoto
#
# Purpose:   Retrieves images from the RETS Server
#
# Input:     $resource: RETS Resource
#            $id: RETS Photo ID (see RETS Specification for details)
#            $filepath: Local path to save images to
#
# Returns:   Returns true if successful.
#
# Note:      None
#------------------------------------------------------------------------------------
  public function GetPhoto($resource,$id,$filepath) {
    print "Getting Photos ...\n";
    global $cloud, $_cdnDomain,$_containerName,$_home365domain;
	
    $numphotos = 0;
    $request_string=$this->capability_urls['GetObject']."?Location=0&Type=Photo&Resource=$resource&ID=$id";
    $response = $this->GetRequest($request_string,'','',true);
    $this->GetObjectParts();
	$photo_array=array();
    print "Extracting " . count($this->content_parts) . " images ...\n";
    # loop through objects and create files
    $i=0;
    foreach ($this->content_parts as $part) {
    if(!empty($part["Content-ID"])&&!empty($part["Object-ID"])){
      $part_filename = $part["Content-ID"]."_".$part["Object-ID"].".jpg";
    }else{
      echo"<br/>Error downloading the picture: couldn't get Content-ID or Object-ID<br/>";
      error_log("Error downloading the images for ".$id.": There are ".count($this->content_parts)." image(s).");
    }
	  
	  
	  if(!empty($part_filename)){
		  $part_filepath = $filepath."/".$part_filename;
		  echo $part_filepath.'<br/>';
		  $fh = fopen($part_filepath, 'w') or die("can't open file $part_filepath");
		  if(fwrite($fh, $part["Object"])==false){
			  echo "There is something wrong writing file to disk<br/>";
		  }else{
			  echo "There is nothing wrong writing file to disk<br/>";
		  }
		  fclose($fh);	  
	  }else{
		  die("Part file name is not set");
	  }
	  $image_types = array("image/jpeg", "image/jpg", "image/pjpg", "image/pjpeg", "image/gif", "image/pgif", "image/png", "image/ppng");
	  $data = getimagesize($part_filepath);
	  if(is_array($data)&&in_array($data['mime'],$image_types)){
		  $is_image=true;
	  }else{
		  $is_image=false;
	  }
	  if($is_image){
		  echo "<br><span style=\"color:green;\">$part_filepath image test passed, uploading to cloud server.</span><br/>";
		  $ostore = $cloud->ObjectStore();
		  if(!$ostore){
			  echo "ostore doesn't exist";
		  }
		  else{
			  $container=$ostore->Container($_containerName);
			  $mypicture = $container->DataObject();
			  try {
				// this will fail if the container doesn't exist
				$mypicture->Create(array('name'=>$part_filename, 'content_type'=>'jpg'), $part_filepath);
				if(!unlink($part_filepath)){
					echo "unlink failed";
				}
				}catch(Exception $e){
					echo ', error:'.$e->getMessage();
					error_log("Error when upload pictures to cloud server: ".$e->getMessage());
				}
				array_push($photo_array,$_home365domain.'/'.$part_filename);
		  }
	  }else{
		  echo "<br/><span style=\"color:red\">file : $part_filepath is broken</span><br/>";
		  error_log("broken file : ".$part_filepath." is not an image");
	  }
      $numphotos++;
    }
	$return_array=array("numphotos"=>$numphotos,"photo_array"=>$photo_array);
    return $return_array;
  }

#------------------------------------------------------------------------------------
# Procedure: GetRequest
#
# Purpose:   Contains common request handling for all RETS Server requests
#
# Input:     $url: URL for RETS request
#            $request_id: An optional request identifier defined by the client
#            $ignore_errors: A flag indicating that HTTP errors should be ignored
#
# Returns:   Returns the raw response from the RETS Server
#
# Note:      This method currently only supports HTTP Basic Authentication.
#            It would need to be enhanced to handle HTTP Digest Authentication.
#------------------------------------------------------------------------------------
  public function GetRequest($url, $request_id="", $optional_headers="", $ignore_errors=false)
  {
     $params = array('http' => array(
                  'method' => 'GET'
               ));
     $this->GenerateUAHeader($request_id);
     if ($optional_headers != null) {
       $optional_headers = $optional_headers  . "\r\n";
     }
     $optional_headers = $optional_headers .
		"Accept: */*\r\n" .
		"RETS-Version: RETS/1.7\r\n" .
		"User-Agent: $this->useragent\r\n" .
		"RETS-UA-Authorization: Digest $this->rets_ua_authorization\r\n";
     if ($this->safemls_pin==null && count($this->cookies)==0) {
       # no session cookies exist, so create a session using HTTP Basic Authentication
       $auth = base64_encode("$this->user:$this->password");
       $optional_headers = $optional_headers . 
		"Authorization: Basic $auth\r\n";
     } else {
       # add any existing cookies to the request
       $cookie_headers = "";
       foreach ($this->cookies as $cookie_name=>$cookie_value) {
         if ($cookie_headers != "") {
           $cookie_headers = $cookie_headers . "; ";
         }
         $cookie_headers = $cookie_headers . "$cookie_name=$cookie_value";
       }
       if ($cookie_headers != "") {
         $optional_headers = $optional_headers . "Cookie: " . $cookie_headers . "\r\n";
       }

     }

     print  "\n\n";
     print "##################################################################################\n";
     print "##################################################################################\n";
     print  "request: " . $url . "\n";
     print  "request headers: " . $optional_headers . "\n";

     if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
     }
     $context = stream_context_create($params);
     $php_errormsg="";
     $response = @file_get_contents($url, false, $context);
     list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);
     if ($status_code!="200" && !$ignore_errors) {
        $php_error_msg = $status_code; 
        throw new Exception("Problem reading data from $url, $php_errormsg");
     }

     print "##################################################################################\n";

     #parse cookies from headers
     foreach ($http_response_header as $header) {
       if (preg_match('/^set-cookie: ?([^=]*)=([^;]*);/i', $header, $matches)) {
         $this->cookies[$matches[1]] = $matches[2];
       } else if (preg_match('/^Content-Type: ?([^;]*);? ?(boundary=(.*)|( ?))$/i', $header, $matches)) {
         $this->content_type = $matches[1];
         $this->content_multipart_boundary = $matches[3];
       } else if (preg_match('/^RETS-Challenge: ?.*scheme=\"SAFEMLS\".* serverinfo=\"(.*)\"/i', $header, $matches)) {
         $this->safemls_serverinfo = $matches[1];
       } else if (preg_match('/^([A-Z-]*): ?(.*)$/i', $header, $matches)) {
         $header_key = $matches[1];
         $header_value = $matches[2];
         $this->headers[$header_key] = $header_value;
       }     
     }     

     $this->content = $response;
     #print $response;
     print "##################################################################################\n";
     print "##################################################################################\n";

     return $response;
  }

#------------------------------------------------------------------------------------
# Procedure: GenerateUAHeader
#
# Purpose:   Create the RETS-UA-Authorization Header
#
# Input:     $request_id: An optional request identifier defined by the client
#
# Returns:   None
#
# Note:      This is a private function that calculates the RETS-UA-Authorization key.
#            For more information, see the RETS Specification. Request IDs are not
#            required by the server, and if a client is not using them, this function
#            does not need to be called on a per transaction basis (in which case
#            it would make sense to trigger the calculation off the change of a
#            session_id).
#------------------------------------------------------------------------------------
  private function GenerateUAHeader($request_id)
  {
    $a1 = "$this->useragent:$this->useragent_password";
    $a1_md5 = md5($a1);
    
    $session_id = "";
    if (array_key_exists("RETS-Session-ID", $this->cookies)) {
      $session_id = $this->cookies["RETS-Session-ID"];
    }
 
    $a2 = "$a1_md5:$request_id:$session_id:$this->rets_version";
    $this->rets_ua_authorization = md5($a2);
  }

#------------------------------------------------------------------------------------
# Procedure: ParseRetsSearchResponse
#
# Purpose:   Parses data from a RETS Search response and loads a pre-existing array
#
# Input:     $response: RETS Response
#            $array: Data array
#
# Returns:   Returns the RETS data in an array.
#
#------------------------------------------------------------------------------------
  private function ParseRetsSearchResponse($response,&$data) {
    $columns=array();
    $xml = new XMLReader();
    $xml->XML($response);
    $nRec = sizeof($data);
    while ($xml->read()) {
      #var_dump($columns);
      if ($xml->nodeType == XMLReader::END_ELEMENT) {
        continue;
      } elseif ($xml->name=="DATA") {
        $xml->read();
        $datline = $xml->value;
        $datline = preg_replace('/^[^\t]*\\t/', "", $datline);
        $datline = preg_replace('/\\t[^\t]*$/', "", $datline);
        $col=0;
        foreach (preg_split('/\t/', $datline) as $datvalue) {
          $dataArray[$columns[$col]] = $datvalue;
          $col++;
        }
        $data[$nRec]=$dataArray;
        $nRec++;
        continue;
      } elseif ($xml->name=="COLUMNS") {
        $xml->read();
        #only load columns for the first batch; it needs to be the same for subsequent requests
        if (sizeof($columns)==0) {
	  $colline = $xml->value;
	  $colline = preg_replace('/^[^\t]*\\t/', "", $colline);
	  $colline = preg_replace('/\\t[^\t]*$/', "", $colline);
	  $col=0;
	  foreach (preg_split('/\t/', $colline) as $systemname) {
	    $columns[$col] = $systemname;
	    $col++;
	  }
        }
        continue;
      }
    }
  }

#------------------------------------------------------------------------------------
# Procedure: GetObjectParts
#
# Purpose:   Homongenizes GetObject response so all objects are available in an array
#
# Input:     None
#
# Returns:   None
#
# Note:      The RETS Server can return images in two ways. If there is a single image,
#            it is returned as a raw jpeg stream. If there are multiple images, the 
#            response is MIME-encoded. This private function handles both cases and
#            loads internal data structures in a standardized way. 
#------------------------------------------------------------------------------------
  # Create private _parts attribute from current _content
  private function GetObjectParts()
  {
    $this->content_parts = array();
    if (preg_match('/^multipart\//', $this->content_type)) {
      # Multiple images that are MIME-encoded 
      if ($this->content_multipart_boundary!="") {
        $parts = explode("--".$this->content_multipart_boundary, $this->content);
        $i=0;
        foreach($parts as $part) {
          $this->content_parts[$i] = array();
          $header_block="";
          $body_block="";
          if (preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $part, $matches)) {
            $header_block=$matches[1];
            $body_block=$matches[2];
          }

          #parse out the header information
          $header_lines = preg_split("/(\r\n|\r|\n)/", $header_block);
          foreach ($header_lines as $line) {
            if (preg_match('/^([A-Z-]*) ?: ?([A-Z0-9\/]*$)/i', $line, $matches)) {
              # capture MIME header
              $key = $matches[1];
              $value = $matches[2];
              $this->content_parts[$i][$key]=$value;
            }
          }

          if (array_key_exists("Content-ID", $this->content_parts[$i])) {
            $this->content_parts[$i]['Object']=$body_block;
            $i++;
          } else {
            unset($this->content_parts[$i]);
          }
        }
      }
    } else {
      # Single image case
      $this->content_parts[0] = array();
      if (array_key_exists("Content-ID", $this->headers)) {
        $this->content_parts[0]["Content-ID"]=$this->headers["Content-ID"];
      #} else {
      #  throw new Exception("Error: Can't find Content-ID for image object.");
      }
      if (array_key_exists("Object-ID", $this->headers)) {
        $this->content_parts[0]["Object-ID"]=$this->headers["Object-ID"];
      #} else {
      #  throw new Exception("Error: Can't find Object-ID for image object.");
      }
      if (!(preg_match('/No Object Found/', $this->content))) {
        $this->content_parts[0]["Object"]=$this->content;
      }
    }
  }
  
  
 
}
?>