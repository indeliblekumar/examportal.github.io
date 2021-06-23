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

include('./functions.php');
include('../../up/up.php');

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
  $errors = 0;
  $errors = query_outcome_echo($query,$result,$errors);

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

  if ($access != "A") // only administrators can setup gnuschool
  {
    mysql_close($mylink);
    $comment = "Bad user name or password. You must have administrator 
                access to log in.";
    get_user_pword_login($tries, $comment, $parent);
    exit();
  }

  //$m_s = time() + 30*24*60*60; // one year from now
  $str = rand_alphanum_str(32);
  $hash = md5($str);
  setcookie('c_id',$tid,false,'/');
  setcookie('c_hash',$hash,false,'/');
  $tries++;
  
  userdb_update_hash( $tid, $hash, $udb);
  $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
}
else
{
  if(isset($_COOKIE['c_id']) && isset($_COOKIE['c_hash']))
  {
    $c_id = $_COOKIE['c_id'];
    $c_hash = $_COOKIE['c_hash'];

    // access mysql server where the user database is
    $mylink = mysql_connect($Muhost,$Muuser,$Mupword);

    if($mylink === FALSE)
    {
      $comment = "Failure to connect to user MySQL server with
                  correct user name and password.";
      get_user_pword_login($tries, $comment, $parent);
      exit();
    }  

    $query = "select access from $udb.users where tid='$c_id' and 
              hash='$c_hash'";
    $result = mysql_query($query);
    $errors = 0;
    $errors = query_outcome_echo($query,$result,$errors);

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

    if ($access != "A")
    {
      mysql_close($mylink);
      $comment = "Bad user name or password. You must have administrator 
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


function get_user_pword_login($tries="", $more_comments = "", 
                                 $parent = "")
{
  if(empty($tries))
    $tries = "v1";
  else
  {
    $val = substr($tries, 1);
    $new_val = ((int) $val) + 1;
    $tries = "v" . "$new_val";
  }

  if($tries == "v1")
    $comments = "<h2>Welcome to database administration.</h2>
                 <h3>Please log in.</h3>";
  elseif(!empty($more_comments))
    $comments="<h2>Database administration.</h2>
           <h3>$more_comments</h3>";
  else
    $comments="<h2>Database administration.</h2>
           <h3>Invalid user name or password. Please try again.</h3>";

  start_html("Log In");
  stylesheet_link("../style_sheet");
  start_body("main_background");
  echo <<<EOQ
<DIV class=page_title>
$comments
<form method=post action="$PHP_SELF">
<b>Web administrator name:</b><br>
<input type=text name=user_u size=30 maxlength=30>
<br><br>
<input type=hidden name=tries value="$tries">
<input type=hidden name=parent value="$parent">
<b>Web administrator password:</b><br>
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
  query_outcome_echo($query,$result);
  return $result;
}


// Use this function when at home.
// To create a database online, web host may require you to
// use their online web site process to create database.
function create_db($errors = 0, $db)
{
  $query = "CREATE DATABASE $db";
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";
  return $errors;
}


function create_tables($errors = 0, $db)
{
  $fields="(fname varchar(30) default '' not null,
            mname varchar(30) default '' not null,
            lname varchar(30) default '' not null,
            quote char(1) default 'n' not null,
            active char(1) default 'y' not null,
            hr varchar(30) default '' not null,
            periods varchar(255) default '|' not null,
    id int(11) unsigned auto_increment, primary key(id))";
  $query = "create table $db.stu_blocks " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields="(period varchar(30) default '' not null)";
  $query = "create table $db.periods " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields="(room varchar(30) default '' not null)";
  $query = "create table $db.rooms " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields="(fname varchar(30) default '' not null
           ,mname varchar(30) default '' not null
           ,lname varchar(30) default '' not null
           ,t_name varchar(30) default '' not null
           ,email varchar(60) default '' not null
           ,hr varchar(30)
           ,subjects varchar(255) default '|' not null
           ,tid int(11) unsigned auto_increment
           ,primary key(tid))";
  $query = "create table $db.teachers " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields="(date date,search varchar(10))";
  $query = "create table $db.todate " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);

  if ($result !== FALSE)
  {
    $query="insert into $db.todate (date,search) 
          values ('0000-00-00','attendance')";
    $result = mysql_query($query);
    $errors = query_outcome_echo ($query, $result, $errors);
    $query="insert into $db.todate (date,search) 
          values ('0000-00-00','cut')";
    $result = mysql_query($query);
    $errors = query_outcome_echo ($query, $result, $errors);
    echo "<br>";
  }

  $fields="(date date,
    id smallint unsigned auto_increment, primary key(id))";
  $query = "create table $db.dates " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields="(date smallint unsigned,
            id int(11) unsigned not null,
            outin char(1),
            reason varchar(255))";
  $query = "create table $db.gone " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields="(gone char(1) default '1' not null,
            date smallint unsigned,
            time time,
            id int(11) unsigned not null,reason varchar(255))";
  $query = "create table $db.dismiss " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields = "(id int(11) unsigned, period varchar(12),
             date smallint unsigned,
             excused char(1),reason varchar(255))";
  $query = "create table $db.cut " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query, $result, $errors);
  echo "<br>";

  $fields="(id int(11) unsigned not null,
            date smallint unsigned,
            reason varchar(255))";
  $query = "create table $db.excused " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields="(time_in time,
            date smallint unsigned,
            is_in tinyint(1) default 0 not null,
            id int(11) unsigned not null)";
  $query = "create table $db.attendance " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  $fields = "(search_start date default '2013-02-02' not null,
              search_end date default '2013-06-22' not null,
              q1_start date default '2012-09-01' not null,
              q1_end date default '2012-11-13' not null,
              q2_start date default '2012-11-14' not null,
              q2_end date default '2013-02-01' not null,
              q3_start date default '2013-02-02' not null,
              q3_end date default '2013-04-15' not null,
              q4_start date default '2013-04-16' not null,
              q4_end date default '2013-06-22' not null,
              time_in time default '07:45:00' not null)";
  $query = "create table $db.date_settings " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  if ($result !== FALSE)
  {
    $query = "insert into $db.date_settings () values()";
    $result = mysql_query($query);
    $errors = query_outcome_echo ($query, $result, $errors);
    echo "<br>";
  }

  $fields = "(file_name varchar(90) default '' not null,
              file_dir varchar(90) default '../../data_text_files/' not null,

              field_delim varchar(5) default ' ' not null,
              line_delim varchar(5) default ' ' not null,

              col_id varchar(30) default 'ID' not null,
              col_id_opt char(3) default 'no' not null,
              col_hr varchar(30) default 'HOMEROOM' not null,
              col_hr_opt char(3) default 'yes' not null,
              col_pe varchar(30) default 'PERIOD' not null,
              col_pe_opt char(3) default 'no' not null,
              col_all_name varchar(90) default 'NAME' not null,
              col_all_name_opt char(3) default 'no' not null,
              col_fname varchar(30) default 'FIRSTNAME' not null,
              col_mname varchar(30) default 'MIDDLENAME' not null,
              col_lname varchar(30) default 'LASTNAME' not null)";
  $query = "create table $db.file_settings " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo ($query, $result, $errors);
  echo "<br>";

  if ($result !== FALSE)
  {
    $query = "insert into $db.file_settings () values()";
    $result = mysql_query($query);
    $errors = query_outcome_echo ($query, $result, $errors);
    echo "<br>";
  }

  return $errors;
}


$tag_title = "gnuschool.org Create Database";
$page_title = "Create a New Database";

start_html($tag_title);

stylesheet_link("../style_sheet");

start_body("main_background");
back_2_parentA("setup_steps");


$comment = "Attempting to create School Database...";
title_comment_div_noend($page_title, $comment);

// my Internet DB needs to be created via web host online process
if($home)
  $errors = create_db(0,$mdb);

$errors = create_tables($errors, $mdb);

if ($errors)
  print "<b>There were $errors error(s) while creating the database
         and/or the tables.</b><br>";
else
  print "<b> The database and tables were created successfully. 
         </b><br><br>";

back_2_parentA("setup_steps");

echo "</DIV>";

?>

</body></html>
