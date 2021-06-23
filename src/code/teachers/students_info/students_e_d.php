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

include('../../../up/up.php');
include('../functions.php');
include ('../login.php');

$fname=$_POST['fname'];
$fname2=$_POST['fname2'];
$fname2_is=$_POST['fname2_is'];
$mname=$_POST['mname'];
$lname=$_POST['lname'];
$period=$_POST['period'];
$old_period=$_POST['old_period'];
$id=$_POST['id'];
$get_id=$_GET['id'];
$get_period=$_GET['period'];
$edit_stu=$_POST['edit_stu'];


if(empty($id)) $id = $get_id;
if(empty($period)) $period = $get_period;

$yes_input = 1;
if (empty($id) && empty($period))
  $yes_input = 0;


function fname2_default($tid)
{
  $table = "defaults" . "$tid";
  $query = "select seating_fname2, seating_fname2_on 
            from $table";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function get_edit_form($stud_r, $tid)
{
  $id = $stud_r['id'];
  $fname = $stud_r['fname'];
  $fname2 = $stud_r['fname2'];
  $fname2_is = $stud_r['fname2_is'];
  $mname = $stud_r['mname'];
  $lname = $stud_r['lname'];
  $period = $stud_r['period'];

/*
  $def_r = fname2_default($tid);
  $def_fname2 = $def_r['seating_fname2'];
  $def_fname2_on = $def_r['seating_fname2_on'];
*/

  $edit_form = <<<EOQ
<form method=post action="$PHP_SELF">
<b>Student's first name:</b><br>
<input type=text readonly="readonly" name=fname size=30 value="$fname">
<br><br>


<b>Student's preferred first name:</b><br>
<input type=text name=fname2 size=30 value="$fname2">
$pref_name
<br><br>

<b>Student's middle name:</b>
<input type=text readonly="readonly" name=mname size=3 value="$mname">
<br><br>

<b>Student's last name:</b><br>
<input type=text readonly="readonly" name=lname size=30 value="$lname">
<br><br>

<b>Period:</b><br>
<input type=text readonly="readonly" name=period size=3 value="$period">
<br>

<input type=hidden name=id value="$id">
<input type=hidden name=old_period value="$period">

<input type=submit name=edit_stu value="Save changes">
</form>
EOQ;
  return $edit_form;
}


function tDataSource($data="", $align="", $width="", $colspan="")
{
  if ($align != "") $align = "align=" . $align;
  if ($colspan != "") $colspan = "colspan=" . $colspan;
  if ($width != "") $width = "width=" . $width;
$data_html = <<<EOQ
<TD $align $colspan $width>
$data
</TD>
EOQ;
return $data_html;
}


function rowPrint($t_data="", $align="")
{
  if ($align != "") $align = "align=" . $align;
return "<TR $align> $t_data </TR>";
}


function startTable($border="", $cellpadding=0, $cellspacing=0)
{
  $cellpadding = "cellpadding=" . "$cellpadding";
  $cellspacing = "cellspacing=" . "$cellspacing";
  return "<TABLE $border $cellspacing $cellpadding>";
}

function endTable()
{
  return "</TABLE>";
}


function show_table_column_title($period)
{
  echo startTable("border");
  $cell_data = tDataSource("Period $period student", "center","",1);
  echo rowPrint($cell_data);
}


function show_empty_table()
{
  echo startTable();
  $message = "All information pertaining to this student has been
              deleted successfully";
  $cell_data = tDataSource("$message", "left");
  echo rowPrint($cell_data);
  echo endTable();
}


function get_student_arr($tid, $id, $period)
{
  $table = "spanish" . "$tid";
  $query = "select * from $table where id = $id and
            period='$period'";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function add_stu_name($tid,$stu_rr)
{
  if(isset($stu_rr))
  {
    $table = "stu_blocks";
    $id = $stu_rr['id'];
    $query = "select * from $table where id=$id";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $name = $row['fname'];
    $stu_rr['fname'] = $name;
    $name = $row['mname'];
    $stu_rr['mname'] = $name;
    $name = $row['lname'];
    $stu_rr['lname'] = $name;
  }
  return $stu_rr;
}


function show_student_info_table($student_arr, $tid)
{
  $period = $student_arr['period'];
  show_table_column_title($period);
  $edit_form = get_edit_form($student_arr, $tid);
  $cell_data = tDataSource($edit_form, "left");
  echo rowPrint($cell_data);
  echo endTable();
}


function edit_student_info ($tid,$id=0,$fname2="")
{
  $query="";

  if(!empty($fname2))
  {
     $table = "spanish" . "$tid";
     $query = "update $table set fname2='$fname2'
              ,fname2_is = 'y'
              where id=$id";
     $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  }
}


function start_main_div ()
{
  echo '<DIV class=main_table>';
}


function end_main_div ()
{
  echo '</DIV>';
}


$tag_title = "gnuschool.org Grades search";

start_html($tag_title);
stylesheet_link("../../style_sheet");

if ($yes_input)
{
  echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function doneConfirm1() {
var str='You will delete all information associated with this student, including tests taken and attendance information. Do you want to continue?'; var ok=confirm(str);
if (ok) return true; else return false;
}

</script>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in your browser.</p>
</noscript>
EOQ;
}

start_body("main_background");

$page_title = "Student information";

if (!$yes_input)
{
  $comment="Please access this page using the site menu.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

title_comment_div_noend($page_title);  

if(!empty($edit_stu))
{
  edit_student_info($tid,$id,$fname2);

}

$student_arr = get_student_arr($tid, $id, $period);
$student_arr = add_stu_name($tid,$student_arr);
show_student_info_table ($student_arr, $tid);

echo "</DIV>";

?>
</body></html>
