<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CLevel.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['levelID']) and isset($_POST['gjp']) and $_POST['accountID']!=""
	and $_POST['levelID']!="" and $_POST['gjp']!=""){
	$uid=(int)$_POST['accountID'];
	$id=(int)$_POST['levelID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$desc=(empty($_POST['levelDesc'])?"":exploitPatch_remove($_POST['levelDesc']));
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cl=new CLevel($dbm);
		$cl->id=$id;
		if($cl->isOwnedBy($uid)){
			echo $cl->updateDescription($desc);
		}else{
			echo "-1";
		}
	}else{
		echo "-1";
	}
	$r=0;
}else{
	echo "-1";
	$r=1;
}
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__." ::with".($r==1?"out":"")." auth data";
	err_handle("ENDPOINT","verbose",$former);
}