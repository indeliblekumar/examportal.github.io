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

$fname=$_POST['fname'];
$mname=$_POST['mname'];
$lname=$_POST['lname'];
$hr_num=$_POST['hr_num'];
$id=$_POST['id'];
$get_id=$_GET['id'];
$get_hr_num=$_GET['hr_num'];
$edit_delete=$_POST['edit_delete'];

if(empty($id)) $id = $get_id;
if(empty($hr_num)) $hr_num = $get_hr_num;

$yes_input = 1;
if (empty($fname) && empty($mname) && empty($lname) && empty($hr_num)
    && empty($id))
  $yes_input = 0;


function get_edit_form($student_r)
{
  $id = $student_r['id'];
  $fname = $student_r['fname'];
  $mname = $student_r['mname'];
  $lname = $student_r['lname'];
  $hr_num = $student_r['hr'];
  $periods = $student_r['periods'];

  $periods_r = periods2periods_r($periods);

  $room_r = get_rooms_num_r();
  $room_selects = "<b>Homeroom:</b>";
  $room_selects .= array2select($room_r,$hr_num,"pHR");
  $room_selects .= "<br>";
  $tid_room_r = get_teacherID_hroom_assoc_r();
  while (list(,$val) = each($periods_r))
  {
    $pos = strpos($val, "~");
    $per = substr($val, 0, $pos);
    if($per != 'HR')
    {
      $tid = substr($val, $pos + 1);
      $room = $tid_room_r["$tid"];
      $room_selects .= "<b>Period $per:</b>";
      $per = "p" . $per;
      $room_selects .= array2select($room_r,$room,$per);
      $room_selects .= "<br>";
    }
  }

  $edit_form = <<<EOQ
<form method=post action="$PHP_SELF">
<b>Student's first name:</b><br>
<input type=text name=fname size=30 value="$fname">
<br><br>

<b>Student's middle name:</b>
<input type=text name=mname size=3 value="$mname">
<br><br>

<b>Student's last name:</b><br>
<input type=text name=lname size=30 value="$lname">
<br><br>

$room_selects
<br><br>

<input type=hidden name=id value="$id">
<input type=hidden name=old_hr_num value="$hr_num">

<input type=submit name=edit_delete
value="Edit student information">
</form>
EOQ;
  return $edit_form;
}


function periods2periods_r($periods)
{
  $length = strlen($periods);
  $periods = substr($periods, 1, $length-2);
  $periods_r = explode('|',$periods);
  sort($periods_r);
  return $periods_r;
}


function periods2period_tid_room_rr($periods)
{
  $periods_r = periods2periods_r($periods);
  $tid_room_r = get_teacherID_hroom_assoc_r();  //from teachers table
  $per_tid_room = array();
  while (list(,$val) = each($periods_r))
  {
    $pos = strpos($val, "~");
    $per = substr($val, 0, $pos);
    $tid = substr($val, $pos + 1);
    $room = $tid_room_r["$tid"];
    $new_r = array();
    $new_r[] = $per;
    $new_r[] = $tid;
    $new_r[] = $room;
    $new_r[] = 0;
    $per_tid_room[] = $new_r;
  }
  return $per_tid_room;
}


function change_periods($oldperiods, $newperiods)
{
  $oldp_r = periods2periods_r($oldperiods);
  $newp_r = periods2periods_r($newperiods);
  $room_r = get_rooms_num_r();  // from rooms table
  $tid_room_r = get_teacherID_hroom_assoc_r();  //from teachers table
  while (list(,$val) = each($periods_r))
  {
    $pos = strpos($val, "~");
    $per = substr($val, 0, $pos);
    $tid = substr($val, $pos + 1);
    $room = $tid_room_r["$tid"];
    $postper = "p" . $per;
    $postroom = $_POST["$postper"];
  }

  return $some_r;
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
  $message = "All information pertaining to this student has been
              deleted successfully";
  $cell_data = tDataSource("$message", "left");
  echo rowPrint($cell_data);
  echo endTable();
}


function get_student_r($id)
{
  $query = "select * from stu_blocks where id = $id";
  $result = mysql_query($query);
  //query_outcome_echo($query,$result,0);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function show_student_info_table($student_r)
{
  $hr_num = $student_r['hr'];
  show_table_column_title($hr_num);
  $edit_form = get_edit_form($student_r);
  $cell_data = tDataSource($edit_form, "left");
  echo rowPrint($cell_data);
  echo endTable();
}


function post2period_room_r()
{
  $per_r = get_periods_num_r(); //all periods in school
  $post_per_r = array();
  while (list(,$per) = each($per_r))
  {
    $p = "p" . $per;
    $room = $_POST["$p"];
    if(!empty($room))
    {
      $post_per_r["$p"] = $room;
    }
  }
  return $post_per_r;
}


function edit_student_info ($id=0,$fname="",$mname="",$lname="", 
                            $periods="", $hr_num="")
{
  $query = "update stu_blocks set fname='$fname',
            mname='$mname', lname='$lname',
            hr='$hr_num', periods='$periods'
            where id=$id";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);

  return $result;
}


function get_tid ($room)
{
  $errors = 0;

  $query = "select tid from teachers where hr = '$room'";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);
  $row = mysql_fetch_row($result);
  $tid = $row[0];
  return $tid;
}


function get_spanish_r ($tid, $id, $period)
{
  $errors = 0;

  $query = "select * from spanish$tid
           where id=$id and period='$period'";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row; 
}


function delete_from_tid ($tid, $id, $period)
{
  $errors = 0;

  $query = "delete from spanish$tid
              where id=$id and period='$period'";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);

  //new teacher will give new seating position
  $query = "delete from seating$tid where id=$id and period='$period'";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);

  return $result;
}


function insert_into_tid ($tid, $id, $period, $stu_r)
{
  $errors = 0;

  /*
    don't save preferred name info
    new teacher should get such info directly from student
  $fname2 = $stu_r['fname2'];
  $fname2_is = $stu_r['fname2_is'];
  $quote = $stu_r['quote'];
  */

  $fname2 = "";
  $fname2_is = 'n';
  $quote = 'n';

  $query = "insert into spanish$tid(
                fname2,fname2_is,quote,period,id)
              values('$fname2','$fname2_is','$quote','$period',$id)";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);

  $query = "insert into seating$tid(
                seat_x,seat_y,period,id)
              values(10,15,'$period',$id)";
  $result = mysql_query($query);
//  $errors = query_outcome_echo($query,$result,$errors);

  return $result;
}


function insert_in2gone($id=0,$date="",$reason="")
{
  $query = "insert into gone(date,id,reason)
            values('$date',$id,'$reason')";
  $result = mysql_query($query);
  return $result;
}


function make_stu_inactive ($id=0)
{
  $query = "update stu_blocks set active='n'
            where id=$id";
  $result = mysql_query($query);
  return $result;
}

$tag_title = "gnuschool.org Grades search";
start_html($tag_title);

stylesheet_link("../style_sheet");

echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function doneConfirm() {
var str='You will delete all information associated with this student, including attendance information. Do you want to continue?'; var ok=confirm(str);
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

$page_title = "Student information";

if (!$yes_input)
{
  $comments="Please access this page using the site menu.";
  title_comment_div_noend($page_title, $comments);
  end_html_exit();  
}

$stud_r = get_student_r($id);

$active = $stud_r['active'];
$old_periods = $stud_r['periods'];
$new_periods = "|";

$comments="";
if($edit_delete == "Edit student information")
{
  $hroom_tid_r = get_hroom_teacherID_assoc_r(); //ex. $hroom_tid_r["B222"]=2;
  $pper_room_r = post2period_room_r();  //ex.  $pper_room["p2"]="C333"
  $per_tid_room_rr = periods2period_tid_room_rr($old_periods);
  $length_ptr = sizeof($per_tid_room_rr);

  for($i=0; $i<$length_ptr; $i++)
  {
    $period = $per_tid_room_rr[$i][0];
    $pperiod = "p" . "$period";
    $tid = $per_tid_room_rr[$i][1];
    $room = $per_tid_room_rr[$i][2];
    $new_room = $pper_room_r["$pperiod"];
//    print "new_room=$new_room ; room=$room ; "; 
    if ("$new_room" == "$room")
    {
      $new_periods .= "$period~$tid|";
//      print "new_periods=$new_periods<br>"; 
    }
    else
    {
//      print "new_room = $new_room ; room = $room<br>"; 
      $new_tid = $hroom_tid_r["$new_room"];
      $new_periods .= "$period~$new_tid|";
//      print "new_periods=$new_periods<br>"; 
      $spa_stud_r = get_spanish_r ($tid, $id, $period);
      delete_from_tid ($tid, $id, $period);
      insert_into_tid ($new_tid, $id, $period, $spa_stud_r);
    }
  }

  $new_hr = $pper_room_r["pHR"];
  $result = edit_student_info($id,$fname,$mname,$lname,$new_periods,$new_hr);

  $stud_r['fname']=$fname;
  $stud_r['mname']=$mname;
  $stud_r['lname']=$lname;
  $stud_r['hr']=$new_hr;
  $stud_r['periods']=$new_periods;

  if($result === FALSE)
    $comments="Error updating some student data, see your administrator.";
  else
    $comments="The student data has been updated.";
}

title_comment_div_noend($page_title, $comments);  
show_student_info_table ($stud_r);

echo "</DIV>";
?>

</body></html>
