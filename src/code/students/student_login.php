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

function stylesheet_link($file)
{
echo <<<EOQ
<LINK REL="STYLESHEET" TYPE="text/css" HREF="$file">
EOQ;
}


function echo_login()
{
  $comments = "<h2>Welcome to the student test page.</h2>
                 <h3>To access the test,
 please type in the user name and password.</h3>";

  start_html("Log In");
  stylesheet_link("../style_sheet");
  start_body("main_background");
  echo <<<EOQ
<DIV class=page_title>
$comments
<b>User name:</b><br>
<input type=text name="t_name" size=30 maxlength=30
onChange="set_name(this.value)">
<br><br>
<b>Password:</b><br>
<input type=password name="pword" size=30 maxlength=30
onChange="set_pwd(this.value)">
<input type=hidden name=tries value="$next_try">
<br><br>
<input type=submit class="gray_button" name=submit
value="Begin the test" onClick="open_win()">
</DIV></body></html>
EOQ;
}


function start_html($title)
{
echo <<<EOQ
<html>
<head>
<title> $title </title>
EOQ;
}


function start_body()
{
echo <<<EOQ
</head>
<body style="background: rgb(200,200,255)" onload="set_all()">
EOQ;
}

start_html("Assessment");

echo <<<EOQ

<SCRIPT LANGUAGE="JavaScript">

var url;
var s_width;
var s_height;  
var window_info;

var t_name;
var pwd;


function set_name(txt) {
  t_name = txt;
  url = './test_by_password.php?t_name=' + t_name;
  url = url + '&pword=' + pwd + '&submit=yes';
}


function set_pwd(txt) {
  pwd = txt;
  url = './test_by_password.php?t_name=' + t_name;
  url = url + '&pword=' + pwd + '&submit=yes';
}


function set_all() {
  s_width = screen.width - 17;
  s_height = screen.availHeight - 34;
   
  window_info = "top=0,left=0,width=" + s_width;
  window_info = window_info + ",height=" + s_height;
  window_info = window_info + ",scrollbars=yes,resizable=yes,status=no";
  window_info = window_info + ",directories=no,toolbar=no";
}

function open_win() {
  window.open(url,'stud_test',window_info);
}

</script>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in you browser.</p>
</noscript>
EOQ;

start_body();

$page_title = "Assessment";

echo_login();


?>
</body></html>
