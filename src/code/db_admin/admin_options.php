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

include('./main_menu.php');

echo <<<EOQ
<html>
<head>
<title> gnuschool.org Setup Procedure </title>
<LINK REL="STYLESHEET" TYPE="text/css" HREF="../style_sheet">
</head>
<body class="main_background">
EOQ;

main_menu();

echo <<<EOQ
<DIV class=page_title><h2>Administration's Home Page.</h2>
<h3>Please use the buttons below to navigate to your specific 
administrative task.</h3>

<table cellpadding="0" cellspacing="0" >
<tr><td>&nbsp</td></tr>
<tr>
<form method=post action="./file_settings.php"> 
<td align="left">
<input type=hidden name=parent value="admin_options">
<input type=submit name=submit value="Edit file settings">
</td></form>
</tr>

<tr><td>&nbsp</td></tr>
<tr>
<form method=post action="./display_fsettings_file.php"> 
<td align="left">
<input type=hidden name=parent value="admin_options">
<input type=submit name=submit value="Display file in file settings">
</td></form>
</tr>

<tr><td>&nbsp</td></tr>
<tr>
<form method=post action="./new_password.php"> 
<td align="left">
<input type=hidden name=parent value="admin_options">
<input type=submit name=submit value="Edit user name and password">
</td></form>
</tr>

<!-- The button below is for testing purposes
<tr><td>&nbsp</td></tr>
<tr>
<form method=post action="./t_p_usernames_t_p_passwords.html"> 
<td align="left">
<input type=hidden name=parent value="admin_options">
<input type=submit name=submit value="Teachers and Principals Usernames and Passwords">
</td></form>
</tr>
-->

</table></DIV>
</body></html>
EOQ;
?>