<?php
Error_reporting(E_ALL ^ E_NOTICE);

$p_user_u = $_POST['user_u'];
$p_user_p = $_POST['user_p'];
$tries = $_POST['tries'];
$tid = "";

if(empty($tries)) $tries = 1;
else $tries = (int)$tries;

if(empty($p_user_u) || empty($p_user_p))
  $yes_user_pword = 0;
else
  $yes_user_pword = 1;


if($yes_user_pword)
{
  // access mysql server where the user database is
  $mylink = mysql_connect($Muhost,$Muuser,$Mupword);

  if($mylink === FALSE)
  {
    $comment = "Failure to connect to user MySQL server with
                correct user name and password.";
    get_user_pword_login($tries, $comment, $parent);
    exit();
  }  

  $query = "select access,tid from $udb.users where user='$p_user_u' and 
            pword=password('$p_user_p')";
  $result = mysql_query($query);
  // $errors = 0;
  // $errors = query_outcome_echo($query,$result,$errors);

  if($result === FALSE)
  {
    mysql_close($mylink);
    $comment = "There was an ERROR in the SQL query.";
    get_user_pword_login($tries, $comment, $parent);
    exit();
  }

  if (mysql_num_rows($result) == 0)
  {
    mysql_close($mylink);
    $comment = "Bad user name or password.";
    get_user_pword_login($tries, $comment, $parent);
    exit();
  }

  // we succeded, we have access to the user database
  $row = mysql_fetch_array($result,MYSQL_ASSOC);
  $access = $row['access'];
  $tid = $row['tid'];

  if ($access != "T") // only educators can access this directory
  {
    mysql_close($mylink);
    $comment = "Bad user name or password. You must have educator 
                access to log in.";
    get_user_pword_login($tries, $comment, $parent);
    exit();
  }

  //$m_s = time() + 365*24*60*60; // cookie lasts one year from now
  $str = rand_alphanum_str(32);
  $hash = md5($str);
  setcookie('tc_id',$tid,false,'/');  // tc stands for teacher cookie
  setcookie('tc_hash',$hash,false,'/');
  
  userdb_update_hash( $tid, $hash, $udb);
}
else
{
  if(isset($_COOKIE['tc_id']) && isset($_COOKIE['tc_hash']))
  {
    $tid = $_COOKIE['tc_id'];
    $c_hash = $_COOKIE['tc_hash'];    


    // access mysql server where the user database is
    $mylink = mysql_connect($Muhost,$Muuser,$Mupword);

    if($mylink === FALSE)
    {
      $comment = "Failure to connect to user MySQL server with
                  correct user name and password.";
      get_user_pword_login($tries, $comment, $parent);
      exit();
    }  

    $query = "select access from $udb.users where tid='$tid' and 
              hash='$c_hash'";
    $result = mysql_query($query);
  //  $errors = 0;
  //  $errors = query_outcome_echo($query,$result,$errors);

    if($result === FALSE)
    {
      mysql_close($mylink);
      $comment = "There was an ERROR in the SQL query.";
      get_user_pword_login($tries, $comment, $parent);
      exit();
    }

    if (mysql_num_rows($result) == 0)
    {
      mysql_close($mylink);
      $comment = "Data not found.";
      get_user_pword_login($tries, $comment, $parent);
      exit();
    }

    // we succeded, we have access to the user database
    $row = mysql_fetch_row($result);
    $access = $row[0];

    if ($access != "T")
    {
      mysql_close($mylink);
      $comment = "Bad user name or password. You must have educator 
                  access to log in.";
      get_user_pword_login($tries, $comment, $parent);
      exit();
    }
  }
  else
  {
    get_user_pword_login($tries, $comment, $parent);
    exit();
  }
}


// access mysql server where the school database is
$mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);

if($mylink === FALSE)
{
  $comment = "Failure to connect to MySQL server with correct 
              user name and password. Please see your system 
              administrator.";
  get_user_pword_login($tries, $comment, $parent);
  exit();
}  

mysql_select_db($mdb,$mylink);


function get_user_pword_login($tries=0, $more_comments = "", 
                                 $parent = "")
{
  $next_try = $tries + 1;

  if($tries == 1)
    $comments = "<h2>Welcome to educator administration.</h2>
                 <h3>$more_comments</h3>
                 <h3>Please log in.</h3>";
  else
    $comments="<h2>Educator database administration.</h2>
           <h3>$more_comments</h3>
           <h3>Please log in.</h3>";

  start_html("Log In");
  stylesheet_link("../../style_sheet");
  start_body("main_background");
  echo <<<EOQ
<DIV class=page_title>
$comments
<form method=post action="$PHP_SELF">
<b>User:</b><br>
<input type=text name=user_u size=30 maxlength=30>
<br><br>
<input type=hidden name=tries value="$next_try">
<input type=hidden name=parent value="$parent">
<b>Password:</b><br>
<input type=password name=user_p size=30 maxlength=30>
<br><br>
EOQ;

  echo <<<EOQ
<input type=submit name=submit_login value="Log in">
</form></DIV></body></html>
EOQ;
}


function rand_alphanum_str($q=0)
{
  $lnums = array('q','w','e','r','t','y','u','i','o','p','a',
's','d','f','g','h','j','k','l','z','x','c','v','b','n','m',
'1','2','3','4','5','6','7','8','9','0',
'Q','W','E','R','T','Y','U','I','O','P','A','S','D','F','G',
'H','J','K','L','Z','X','C','V','B','N','M');

  $len = sizeof($lnums);
  $len1 = $len - 1;

  if($q==0) $q = $len;
  $str = "";
  for ($i=0; $i<$q; $i++)
  { 
    $v = mt_rand(0,$len1);
    $str .= $lnums[$v];
  }  
  return $str;
}


function userdb_update_hash( $tid, $hash, $db)
{
  $query = "update $db.users set hash='$hash' where tid=$tid"; 
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  return $result;
}


?>
