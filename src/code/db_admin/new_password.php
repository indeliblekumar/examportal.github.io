<?php 
Error_reporting(E_ALL ^ E_NOTICE);

/*
    gnuschool is a GNU program.
    gnuschool assists school administrators keep track of students.
    It assists educators in the assessment of students by letting 
    educators create and give online tests.
    It also gives students instant test feedback.

    Copyright (C) <2005>  <Pedro Rios>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The file COPYING is included with this program. It is a complete 
    copy of the General Public License. You should have received a
    copy of the GNU General Public License along with this program;
    if not, write to the
      Free Software Foundation, Inc.
      51 Franklin Street, Fifth Floor
      Boston, MA  02110-1301  USA


    Please contact the author at prios978 en yahoo punto com
*/

$proceed1 = $_POST['proceed1'];
$proceed2 = $_POST['proceed2'];
$fname = $_POST['fname'];
$mname = $_POST['mname'];
$lname = $_POST['lname'];
$u_name = $_POST['u_name'];
$u_only = $_POST['u_only'];
$u_too = $_POST['u_too'];
$parent=$_POST['parent'];

if(empty($parent))$parent="admin_options";

include('./login.php');

$yes_input = 1;

if(empty($proceed1) && empty($proceed2))
  $yes_input = 0;


function new_pword_forms()
{
  echo <<<EOQ
<BR>
<b>1.)  Complete the first, middle, and last name fields
            to get a new user name and a new password.</b>
<form method=post action="$PHP_SELF">
First name:<input type=text size=30 name=fname maxlength=34>
<BR><BR>
Middle name:<input type=text size=30 name=mname maxlength=34>
<BR><BR>
Last name:<input type=text size=30 name=lname maxlength=34>
<BR><BR>
<input type=checkbox name=u_only value="yes">
Do not get a new password.
<BR>
<input type=hidden name=parent value="password">
<input type=submit name=proceed1 value="Proceed with option 1">
</form>
<BR>
<b>2.) Type a user name to get a new password for that user name.</b>
<form method=post action="$PHP_SELF">
User name:<input type=text size=30 name=u_name maxlength=34>
<BR><BR>
<input type=checkbox name=u_too value="yes">
Also replace the user name with a new user name.
<br>
<input type=hidden name=parent value="password">
<input type=submit name=proceed2 value="Proceed with option 2">
</form>
EOQ;
}


function syllable_rand($start=0)
{
  $str = "";
  $vow = array('a','e','i','o','u');
  $vow_p = array('ai','au','ay','ea','ee','ia','ie'
                 ,'oa','oi','oy','oo','ou');
  $con = array('b','c','d','f','g','h','j','k','l','m','n','p'
               ,'r','s','t','v','w','z');
  $con_p = array('bl','br','ch','cl','cr','dr','fl','fr','gl'
                 ,'gr','ph','pl','pr','sc','sh','sl','sm','sn'
                 ,'sp','st','sw','th','tr','tw');

  $v = rand(0,sizeof($vow) - 1);
  $vp = rand(0,sizeof($vow_p) - 1);
  $c = rand(0,sizeof($con) - 1); 
  $cp = rand(0,sizeof($con_p) - 1); 

  $odds = rand(0,20);
  if($odds < 3 && $start)
  {
    $str = $vow[$v];
    return $str;
  }
  
  $odds = rand(0,20);
  if ($odds < 7)
    $str = $con_p[$cp];
  else
    $str = $con[$c];

  $odds = rand(0,20);
  if ($odds > 8)
    $str .= $vow[$v];
  elseif ($odds < 2)
    $str .= 'y';
  else
    $str .= $vow_p[$vp];
  
  return $str;
}


function tid_select_mdb($fname,$mname,$lname)
{
  $query = "select tid from teachers where fname='$fname'
            and mname='$mname' and lname='$lname'";
  $result = mysql_query($query);
  //  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;

  $rows = mysql_num_rows($result);
  if ($rows)
  {
    $row = mysql_fetch_row($result);
    return $row[0];
  }
  elseif($rows === 0) return 0;
  else return FALSE;
}


function using_tid_update_udb( $tid, $user, $pwd, $db)
{
  $pwd_part = "";
  if (!empty($pwd)) $pwd_part = ", pword=password('$pwd')";
  $query = "update $db.users set user='$user'
            $pwd_part where tid=$tid"; 
  $result = mysql_query($query);
  //  query_outcome_echo($query,$result);
  return $result;
}


function using_user_update_udb( $u_name, $user, $pwd, $db)
{
  $user_str = "";
  if (!empty($user)) $user_part = ", user='$user'";
  $query = "update $db.users set pword=password('$pwd')
            $user_part where user='$u_name'"; 
  $result = mysql_query($query);
  //  query_outcome_echo($query,$result);
  return $result;
}


function user_check_udb( $u_name, $db )
{
  $query = "select user from $db.users where user='$u_name'"; 
  $result = mysql_query($query);
  //  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;

  $rows = mysql_num_rows($result);
  if ($rows) return 1;
  elseif($rows === 0) return 0;
  else return FALSE;
}


$tag_title = "gnuschool.org Reset Password and User Name";
$page_title = "Reset a password and/or a user name";


start_html($tag_title);
stylesheet_link("../style_sheet");
start_body("main_background");
back_2_parentA($parent);

if (!$yes_input)
{
  $comment = "Get a new password and/or user name by supplying an 
             educator or principal's full name or user name.";
  title_comment_div_noend($page_title, $comment);
  new_pword_forms();
  back_2_parentA("admin_options");
  end_html_exit();
}

$new_pwd = "";
$new_user = "";
$show = FALSE;

if(!empty($proceed1))
{
  if ( empty($fname) || empty($mname) || empty($lname) ) 
  {
    $comment = "Get a new password and/or user name by supplying an 
             educator or principal's full name or user name.";
    title_comment_div_noend($page_title, $comment);
    new_pword_forms();
    back_2_parentA("admin_options");
    end_html_exit();
  }

  $comment=
    "Create new login data using first middle and last name.";
  title_comment_div_noend($page_title, $comment);  

  if (strlen($mname) === 1)
    $mname = $mname . ".";

  $tid = tid_select_mdb($fname,$mname,$lname);
  if ( $tid )
  {
    $mylink = mysql_connect($Muhost,$Muuser,$Mupword);
    if($mylink === FALSE) exit_no_mysql_connect($tries,"USER","password");
    $new_user = user_rand($udb);

    if (empty($u_only))
    {
      $new_pwd = pword_rand();
      $show = using_tid_update_udb( $tid, $new_user, $new_pwd, $udb );

      $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
      if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","password");

      mysql_select_db($mdb,$mylink);
      if ($show)
      {
        echo "<b>New user name:</b> $new_user<br>
            <b>New password:</b> $new_pwd <br>
            <b>Please keep them in a safe place.</b>";
      }
    }
    else
    {
      $show = using_tid_update_udb( $tid, $new_user, "", $udb);

      $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
      if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","password");

      mysql_select_db($mdb,$mylink);
      if ($show)
      {
        echo "<b>New user name:</b> $new_user<br>
            <b>Please keep it in a safe place.</b>";
      }
    }
  }
  else
  {
    echo "<b>The system was not able to find $fname $mname $lname.</b>";
  }
}
else
{
  if(empty($u_name))
  {
    $comment = "Get a new password and/or user name by supplying an 
             educator or principal's full name or user name.";
    title_comment_div_noend($page_title, $comment);
    new_pword_forms();
    back_2_parentA("admin_options");
    end_html_exit();
  }

  $comment=
    "Create new login data using a user name.";
  title_comment_div_noend($page_title, $comment);  

  $mylink = mysql_connect($Muhost,$Muuser,$Mupword);
  if($mylink === FALSE) exit_no_mysql_connect($tries,"USER","password");

  $itexists = user_check_udb( $u_name, $udb );

  if ($itexists)
  {
    if (empty($u_too))
    {
      $new_pwd = pword_rand();
      $show = using_user_update_udb( $u_name, "", $new_pwd, $udb);

      $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
      if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","password");
      mysql_select_db($mdb,$mylink);

      if ($show)
      {
        echo "<b>New password:</b> $new_pwd <br>
              <b>Please keep it in a safe place.</b>";
      }
    }
    else
    {
      $new_user = user_rand($udb);
      $new_pwd = pword_rand();
      $show = using_user_update_udb( $u_name, $new_user, $new_pwd, $udb );

      $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
      if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","password");
      mysql_select_db($mdb,$mylink);

      if ($show)
      {
        echo "<b>New user name:</b> $new_user<br>
              <b>New password:</b> $new_pwd <br>
              <b>Please keep them in a safe place.</b>";
      }
    }
  }
  else
  {
    echo "<b>The system was not able to find user $u_name.</b>";
  }
}

print "<br><br>";
back_2_parentA($parent);
echo "</DIV>";

?>
</body></html>
