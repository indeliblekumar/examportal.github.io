<?php
// change to something like $hour_fix = -3 if server date is 3 hrs ahead
$hour_fix = 0;
$tstamp = time() + $hour_fix * 60 * 60;
$date = date('Y-m-d',$tstamp);
$time = date("H:i:s",$tstamp);

$home = 1;   //localhost=1 MUST SET TO 0 IF USING A WEB HOSTING SITE

$Mmhost = ""; // the capital "M" stands for MySQL
$Mmuser = "";
$Mmpword = "";
$mdb = "";

$Muhost = "";
$Muuser = "";
$Mupword = "";
$udb = "";

if($home)
{
  $Mmhost="MySQL_mainSchoolDatabase_host";     // "127.0.0.1"
  $Mmuser="MySQL_mainSchoolDatabase_username"; // e.g. "pedroH1"
  $Mmpword="MySQL_mainSchoolDatabase_password";// e.g. "hrWZtH1"
  $mdb="MySQL_mainSchoolDatabase_name";        // e.g. "mainDB"

  $Muhost= "MySQL_userSchoolDatabase_host";     // "127.0.0.1"
  $Muuser= "MySQL_userSchoolDatabase_username"; // e.g. "pedroH2"
  $Mupword= "MySQL_userSchoolDatabase_password";// e.g. "hrWZtH2"
  $udb= "MySQL_userSchoolDatabase_name";        // e.g. "userDB"
}
else
{
  $Mmhost="MySQL_mainSchoolDatabase_host";//e.g."mainDB.db.6180529.hostedresource.com"
  $Mmuser="MySQL_mainSchoolDatabase_username";  // e.g. "pedroH1"
  $Mmpword="MySQL_mainSchoolDatabase_password"; // e.g. "hrWZtH1"
  $mdb="MySQL_mainSchoolDatabase_name";         // e.g. "mainDB"

  $Muhost="MySQL_userSchoolDatabase_host";//e.g."userDB.db.6180529.hostedresource.com"
  $Muuser="MySQL_userSchoolDatabase_username";  // e.g. "pedroH2"
  $Mupword="MySQL_userSchoolDatabase_password"; // e.g. "hrWZtH2"
  $udb="MySQL_userSchoolDatabase_name";         // e.g. "userDB"
}

$domain = 'http://127.0.0.1/';  //change if it's not localhost

//add to $domain the path from web server DocumentRoot to "code" directory
$path2code = $domain . 'gnuschool-2.2/src/code';

//Note: DocumentRoot should point directly to "code" directory
//      if it does you don't need $path2code

?>
