 <?php
require_once 'RETS.php';

# set up RETS 
# note: all values within <> need to be replaced
# note: all the resource, class, lookup, and field identifiers in the examples 
#       are from the BRC system
$rets = new RETS();
$rets->url='http://brc.retsca.interealty.com/Login.asmx/Login';
$rets->user='RETSALLISONJ';
$rets->password='RE@LE$7AT3';
$rets->useragent='RETSAllisonJiang/1.0';
$rets->useragent_password='8tHIMi7aL#e4Utd';

# login example
print"===========Login===========\n";
$response=$rets->Login();
print"===========Login Finish===========\n";
# login example using SafeMLS
# $rets->safemls_pin="<SafeMLS Device PIN>";
# $rets->password="<Password Generated by SafeMLS Token>";
# $response=$rets->Login();


# get metadata examples
#
###       # resource metadata where ID=0 (all resources)
#print"===========get metadata:resource===========\n";
#$response=$rets->GetMetadata('METADATA-RESOURCE','0');
#print $response;
#print"===========get metadata:resource finish===========\n";
#print "\n\n";

#
###       # class metadata where ID=0 (all resources and classes)
#print"===========get metadata:class===========\n";
#$response=$rets->GetMetadata('METADATA-CLASS','0');
#print $response;
#print"===========get metadata:class finish===========\n";
#print "\n\n";

#
###       # class metadata where ID=Property:0 (all classes in Property resource)
#print"===========get metadata:class (ID=Property:0)===========\n";
#$response=$rets->GetMetadata('METADATA-CLASS','Property:0');
#print $response;
#print"===========get metadata:class (ID=Property:0) finish===========\n";
#print "\n\n";
#
###       # table metadata where ID=Property:11 (table structure for XPROP class in Property resource)
#$response=$rets->GetMetadata('METADATA-TABLE','Property:11');
#print $response;
#
###       # lookup metadata where ID=Property:* (all lookups for Property resource) 
#$response=$rets->GetMetadata('METADATA-LOOKUP_TYPE','Property:*');
#print $response;
#print "\n\n";
#
###       # lookup metadata where ID=Property:1_299 (1_299 lookup, associated with Status field, for Property resource) 
#$response=$rets->GetMetadata('METADATA-LOOKUP_TYPE','Property:1_299');
#print $response;
#print "\n\n";
# get record count where: Resource=Property, 
#                         Class=11 (XPROP), 
#                         Query=Status (field 363) is Active
/*$response=$rets->GetCount('Property','11','(363=|A)');
print $response;
print "\n\n";*/
# get record count where: Resource=Property, 
#                         Class=11 (XPROP), 
#                         Query=Status (field 363) is Active and
#                               LastTransDate (field 217) is in 2010
//$response=$rets->GetCount('Property','11','(363=|A),(217=2010-01-01T00:00:00-2010-12-31T23:59:59)');
//print $response;
//print "\n\n";
# get data in an array where: Resource=Property, 
#                             Class=11 (XPROP), 
#                             Query=Status (field 363) is Active,
#                             Select Fields=sysid,248,363,217 (sysid, MLS#, Status, LastTransDate)
#                             Num Records=Limit to 2
//$response=$rets->GetDataArray('Property','11','(363=|A)','sysid,248,363,217',2);
//var_dump($response);
//print "\n\n";
#get record where LastTransDate(fild 217) is on May 14, 2014
//$response=$rets->GetDataArray('Property','11','(363=|A),(217=2014-05-14T00:00:00-2014-05-14T23:59:59)',null,null);
//var_dump($response);
//$response=$rets->GetDataArray('Property','11','(363=|A)',null,1);
//var_dump($response);
$response=$rets->GetDataArray('Property','11','(248=V1037517)',null,null);
var_dump($response);
foreach($response as $key=>$value){
	foreach($value as $field=>$row_value){
		echo "[$field]".get_column_name($field).":$row_value\n";
	}
}
print "\n\n";

# get data using full key download 
# and write to file where: Resource=Property, 
#                          Class=11 (XPROP), 
#                          Query=Status (field 363) is Active,
#                          Select Fields=All,
#                          Num Records=Unlimited
//$rets->CreateDataFile('Property','11','(363=|A)',null,null,"data/active_listings.txt");
//print "\n\n";
# get image examples
# note: that the ids passed through are photo keys which, dependent
#       on the RETS server are not the same as the MLS#;
#       in the case of BRC the photo key is the listing SYSID
#
###       # get primary image
//$response=$rets->GetPhoto('Property','62400551:1', 'c:\images');
//print "\n\n";
# get all images for a given listing
//$response=$rets->GetPhoto('Property','253110929:*', 'c:\images');
//print "\n\n";
# logout example
$response=$rets->Logout();

function get_column_name($column_number){
	switch($column_number){
		case 186:$column_name="# of Images"; break;
		case 14:$column_name="Address"; break;
		case 3976:$column_name="Address AWP Display"; break;
		case 34:$column_name="Age"; break;
		case 405:$column_name="Amenities"; break;
		case 16:$column_name="Approx. Year Built"; break;
		case 2283:$column_name="Area"; break;
		case 2233:$column_name="Area Desc"; break;
		case 46:$column_name="Basement Area"; break;
		case 43:$column_name="Board ID"; break;
		case 28:$column_name="Broker Recip Flag"; break;
		case 3794:$column_name="City"; break;
		case 2667:$column_name="Depth"; break;
		case 2317:$column_name="Display Address on Internet"; break;
		case 404:$column_name="Features Included"; break;
		case 167:$column_name="Fireplaces"; break;
		case 3922:$column_name="Floor Area -Grand Total"; break;
		case 249:$column_name="Floor Area Fin - Main Floor"; break;
		case 2651:$column_name="For Tax Year"; break;
		case 2653:$column_name="Frontage"; break;
		case 2655:$column_name="Frontage - Metric"; break;
		case 166:$column_name="Full Baths"; break;
		case 2673:$column_name="Gross Taxes"; break;
		case 180:$column_name="Half Baths"; break;
		case 181:$column_name="House#"; break;
		case 2311:$column_name="House # Alpha"; break;
		case 2923:$column_name="Internet Remarks"; break;
		case 30:$column_name="Last Img Trans Date"; break;
		case 217:$column_name="Last Trans Date"; break;
		case 224:$column_name="List Date"; break;
		case 222:$column_name="List Firm 1 Code"; break;
		case 2675:$column_name="List Firm 1 FAX"; break;
		case 2679:$column_name="List Firm 1 Name"; break;
		case 2681:$column_name="List Firm 1 Phone"; break;
		case 2685:$column_name="List Firm 1 URL"; break;
		case 2689:$column_name="List Firm 2 Code"; break;
		case 2677:$column_name="List Firm 2 FAX"; break;
		case 2325:$column_name="List Firm 2 Name"; break;
		case 2683:$column_name="List Firm 2 Phone"; break;
		case 2687:$column_name="List Firm 2 URL"; break;
		case 227:$column_name="List or Sell Agent"; break;
		case 228:$column_name="List or Sell Firm"; break;
		case 226:$column_name="List Price"; break;
		case 342:$column_name="List Realtor 1 ID"; break;
		case 2703:$column_name="List Realtor 1 Name"; break;
		case 2711:$column_name="List Realtor 1 Phone"; break;
		case 2705:$column_name="List Realtor 1 URL"; break;
		case 2697:$column_name="List Realtor 2 ID"; break;
		case 2327:$column_name="List Realtor 2 Name"; break;
		case 2713:$column_name="List Realtor 2 Phone"; break;
		case 2707:$column_name="List Realtor 2 URL"; break;
		case 2699:$column_name="List Realtor 3 ID"; break;
		case 2329:$column_name="List Realtor 3 Name"; break;
		case 3893:$column_name="List Realtor 3 Office ID"; break;
		case 3895:$column_name="List Realtor 3 Office Name"; break;
		case 2717:$column_name="List Realtor 3 Phone"; break;
		case 2701:$column_name="List Realtor 3 URL"; break;
		case 2453:$column_name="Lot Sz (Acres)"; break;
		case 2455:$column_name="Lot Sz (Hectares)"; break;
		case 2457:$column_name="Lot Sz (Sq.Ft.)"; break;
		case 2460:$column_name="Lot Sz (Sq.Mtrs.)"; break;
		case 248:$column_name="MLS Number"; break;
		case 221:$column_name="No. Floor Levels"; break;
		case 11:$column_name="Postal Code"; break;
		case 1:$column_name="Property Type"; break;
		case 88:$column_name="Province"; break;
		case 411:$column_name="Public Remarks"; break;
		case 3985:$column_name="Public Remarks 2"; break;
		case 3:$column_name="Publish Listing on Internet"; break;
		case 3926:$column_name="Site Influences"; break;
		case 363:$column_name="Status"; break;
		case 375:$column_name="Storeys in Building"; break;
		case 2967:$column_name="Strata Maint Fee"; break;
		case 94:$column_name="Street Dir"; break;
		case 364:$column_name="Street Name"; break;
		case 365:$column_name="Street Type"; break;
		case 2731:$column_name="Style of Home"; break;
		case 2568:$column_name="Sub-Area/Community"; break;
		case 2570:$column_name="Sub-Area/Community Desc"; break;
		case 2737:$column_name="Title to Land"; break;
		case 3928:$column_name="Total Baths"; break;
		case 378:$column_name="Total Bedrooms"; break;
		case 2733:$column_name="Type of Dwelling"; break;
		case 2971:$column_name="Unit #"; break;
		case 3798:$column_name="View"; break;
		case 2975:$column_name="View - Specify"; break;
		case 26:$column_name="Virtual Tour URL"; break;
		case "sysid":$column_name="sysid"; break;
		default: $column_name="unknown column".$column_number;
	}
	return $column_name;
}
?> 