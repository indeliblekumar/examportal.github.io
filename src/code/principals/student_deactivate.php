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

include('../../up/up.php');
include ('./functions.php');
include ('./login.php');
include ('./search.php');

$id=$_GET['id'];
$inactive=$_POST['inactive'];
$reason=$_POST['reason'];

$yes_input = 1;
if (empty($id))
  $yes_input = 0;


function get_inactive_form($student_arr,$active)
{
  $id = $student_arr['id'];
  $fname = $student_arr['fname'];
  $mname = $student_arr['mname'];
  $lname = $student_arr['lname'];
  $hr_num = $student_arr['hr'];

  $active_str="";
  if($active == 'y')
  {
    $active_str = "inactive";
  }
  else
  {
    $active_str = "active";
  }
  
  $edit_form = <<<EOQ
<form method=post action="$PHP_SELF">
<b>Student's name:</b>&nbsp$fname $mname $lname
<br><br>

<b>Homeroom:</b>&nbsp$hr_num
<br><br>

<b>Reason for making $active_str:</b><br>
<textarea name="reason" cols=50 rows=3 maxlength=255"></textarea>

<input type=hidden name=id value="$id">
<input type=hidden name=old_hr_num value="$hr_num">

<input type=submit name=inactive value="Make student $active_str"
onClick="return doneConfirm()">
</form>
EOQ;
  return $edit_form;
}


function endTable()
{
  return "</TABLE>";
}


function show_table_column_title($hr_num)
{
  echo startTable("border");
  $cell_data = tDataSource("Homeroom $hr_num student", "center","",1);
  echo rowPrint($cell_data);
}


function show_empty_table()
{
  echo startTable();
  $message = "The student is now inactive.";
  $cell_data = tDataSource("$message", "left");
  echo rowPrint($cell_data);
  echo endTable();
}


function get_student_arr($id)
{
  $query = "select * from stu_blocks where id = $id";
  $result = mysql_query($query);
  //query_outcome_echo($query,$result,0);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function show_student_info_table($student_arr,$active)
{
  $hr_num = $student_arr['hr'];
  show_table_column_title($hr_num);
  $edit_form = get_inactive_form($student_arr,$active);
  $cell_data = tDataSource($edit_form, "left");
  echo rowPrint($cell_data);
  echo endTable();
}


function insert_in2gone($id=0,$date="",$reason="")
{
  $query = "insert into gone(date,id,reason)
            values('$date',$id,'$reason')";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,0);
  return $result;
}


function set_stu_active ($id=0, $active)
{
  $query = "update stu_blocks set active='$active'
            where id=$id";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,0);
  return $result;
}


function insert_reason ($id=0, $date="", $outin='', $reason="")
{
  $query = "insert into gone(date,outin,id,reason)
            values('$date','$outin',$id,'$reason')";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);
  return $result;
}


$tag_title = "gnuschool.org Grades search";
start_html($tag_title);

stylesheet_link("../style_sheet");

echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function doneConfirm() {
var str='You will make the student inactive. Educators will
not see the student in the attendance lists. Do you want to continue?';
var ok=confirm(str);
if (ok) return true; else return false;
}

</script>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in you browser.</p>
</noscript>
EOQ;

start_body("main_background");

$page_title = "Student Activation";

if (!$yes_input)
{
  $comments="Please access this page using the site menu.";
  title_comment_div_noend($page_title, $comments);
  end_html_exit();  
}

$stud_r = get_student_arr($id);

$active = $stud_r['active'];

$comments="";
if(!empty($inactive))
{
  if($active == 'y')
  {
    $err = 0;
    $result = set_stu_active($id,'n');
    if($result === FALSE) $err++;
    else $active = 'n';

    $reason = addslashes($reason);
    $result = insert_reason ($id, $date, 'o', $reason);
    if($result === FALSE) $err++;
  
    if($err)
      $comments="Error updating some student data, see your administrator.";
    else
      $comments="The student is currently inactive.<br>
                 In other words, the student does not attend this school.";
  }
  else
  {
    $err = 0;
    $result = set_stu_active($id,'y');
    if($result === FALSE) $err++;

    $reason = addslashes($reason);
    $result = insert_reason ($id, $date, 'i', $reason);
    if($result === FALSE) $err++;
    else $active = 'y';
  
    if($err)
      $comments="Error updating some student data, see your administrator.";
    else
    $comments="The student is currently active.<br>
               In other words, the student is attending this school.";
  }
}
else
{
  if($active=='y')
    $comments="The student is currently active.<br>
               In other words, the student is attending this school.";
  else
    $comments="The student is currently inactive.<br>
               In other words, the student does not attend this school.";
}
title_comment_div_noend($page_title, $comments);  
show_student_info_table ($stud_r,$active);

echo "</DIV>";
?>

</body></html>
