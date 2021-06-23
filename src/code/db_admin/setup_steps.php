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

include('./functions.php');


function tr_td_get_form($file, $text)
{
  echo "<tr>";
  td_get_form("$file","$text", "setup_steps");
  echo "</tr>";
}

function tr_td_empty()
{
  echo "<tr><td>&nbsp</td></tr>";
}


function anchors_table()
{
  echo <<<EOQ
    <table cellpadding="0" cellspacing="0">
EOQ;

  echo "<tr><td><hr noshade></td></tr>";
  $file = "./create_user.php";
  $text = "Step 1";
  tr_td_get_form("$file","$text");
  echo "<tr><td>
            <b>Create the user database.
            <br>This step also creates a web administrator.
            <br>Use the web administrator user name and password to
                access the rest of this setup.
         </b></td></tr>";
  tr_td_empty();

  echo "<tr><td><hr noshade></td></tr>";
  $file = "./create_main.php";
  $text = "Step 2";
  tr_td_get_form("$file","$text");
  echo "<tr><td>
          <b>Create the school database.
         </b></td></tr>";
  tr_td_empty();

  echo "<tr><td><hr noshade></td></tr>";
  $file = "./im_txt_2_main_stu.php";
  $text = "Step 3";
  tr_td_get_form("$file","$text");
  echo "<tr><td>
             <b>Update the main student table in the database.
             <br>The input file has the student name and homeroom information.
         </b></td></tr>";
  tr_td_empty();

  echo "<tr><td><hr noshade></td></tr>";
  $file = "./im_txt_2_teac_stu.php";
  $text = "Step 4";
  tr_td_get_form("$file","$text");
  echo "<tr><td>
             <b>Update the teacher tables in the database.
             <br>The input file has teacher information
             and student information per classroom.
         </b></td></tr>";
  tr_td_empty();

  echo "<tr><td><hr noshade></td></tr>";
  $file = "./im_txt_2_teac_pri.php";
  $text = "Step 5";
  tr_td_get_form("$file","$text");
  echo "<tr><td>
             <b>Update the principal or school supervisor tables in
                 the database.
             <br>The input file has the principal's name and
                 homeroom information.
         </b></td></tr>";
  tr_td_empty();


  echo "<tr><td><hr noshade></td></tr>";
  echo "<tr><td>
             <b>If you've completed the five steps 
             without major errors, congratulations!
             <br>You have successfully completed the gnuschool system setup.
         </b></td></tr>";
  echo "</table>";
}


/*
function anchors_table()
{
  echo <<<EOQ
    <table cellpadding="0" cellspacing="0" ><tr>
EOQ;


  echo "<table class=search1><tr><td>
            <b>1: Create the user database.
            <br>This step also creates a web administrator.
             </b></td></tr>";
  $file = "./create_user.php";
  $text = "Step 1";
  tr_td_get_form("$file","$text");
  echo "</table></tr>";
  tr_td_empty();

  echo "<TR><table class=search1>
          <tr><td><b>2: Create the school database.
            <br>The first time you use this button, you will need the user
            name and password of the web administrator created in step 1.
          </b></td></tr>";
  $file = "./create_main.php";
  $text = "Step 2";
  tr_td_get_form("$file","$text");
  echo "</table></tr>";
  tr_td_empty();
  
  echo "<TR><table class=search1>
        <tr><td><b>After completing step 2, you may edit the database's
                   default settings using this button.
        </b></td></tr>";
  $file="./file_settings.php";
  $text = "Edit default settings";
  tr_td_get_form("$file","$text");
  echo "</table></tr>";
  tr_td_empty();

  echo "<TR><table class=search1>
            <tr><td><b>3: Update the student information in the database.
                <br>The input file has the student name and homeroom information.
                <br>The main student database table is updated.
            </b></td></tr>";
  $file = "./im_txt_2_main_stu.php";
  $text = "Step 3";
  tr_td_get_form("$file","$text");
  echo "</table></tr>";
  tr_td_empty();

  echo "<TR><table class=search1>
            <tr><td><b>4: Update the teacher and student information in the database.
                     <br>The input file has teacher information
                         and student information per classroom.
                     <br>The teacher database tables are updated.
            </b></td></tr>";
  $file = "./im_txt_2_teac_stu.php";
  $text = "Step 4";
  tr_td_get_form("$file","$text");
  echo "</table></tr>";
  tr_td_empty();

  echo "<TR><table class=search1>
       <tr><td><b>5: Update the principal or school supervisor information in
                the database.
                <br>The input file has the principal's name and homeroom information.
       </b></td></tr>";
  $file = "./im_txt_2_teac_pri.php";
  $text = "Step 5";
  tr_td_get_form("$file","$text");
  echo "</table></tr>";
  tr_td_empty();


  echo "<tr><td><b>If you've completed the five steps 
         without major errors, congratulations!<br>
         You have completed the gnuschool setup.</b></td></tr>";
  echo "</table>";
}
*/

$tag_title = "gnuschool.org Setup Procedure";
$page_title = "Setup procedure for gnuschool.";

start_html($tag_title);

stylesheet_link("../style_sheet");

start_body("main_background");
back_2_parentA("setup");

$comment = "Please complete these steps in order.";
title_comment_div_noend($page_title, $comment);
anchors_table();
echo "<br>";
back_2_parentA("setup");
echo "</DIV>";

?>

</body></html>

