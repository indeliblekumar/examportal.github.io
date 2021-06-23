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

$back="..";
include("$back/functions.php");
include ('../login.php');

$period=$_POST['period'];
$id=$_POST['id'];
$undo=$_POST['undo'];
$undo_all=$_POST['undo_all'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;

if (empty($period)) $yes_input = 0;
else
 if ($period == "hr" || $period == "Hr" || $period == "hR") $period = "HR";

function input_form()
{
  $login = <<<EOQ
<form method=post action="$PHP_SELF">

<b>Period:</b><br>
<input type=text name=period size=4>
<br><br>

<input type=hidden name=menu_submit value="Attendance">
<input type=submit name=submit value="Take attendance">
</form>
EOQ;
  return $login;
}


function get_students_arr( $tid="", $period="", $def_r)
{
  $def_fname = $def_r['seating_fname'];
  $def_fname_on = $def_r['seating_fname_on'];
  $def_fname2 = $def_r['seating_fname2'];
  $def_fname2_on = $def_r['seating_fname2_on'];
  $def_mname = $def_r['seating_mname']; //'n' or 'y'
  $def_lname = $def_r['seating_lname'];
  $def_lname_on = $def_r['seating_lname_on'];
  $def_name_lines = $def_r['seating_name_lines'];
  $students_arr = array();

  $table = "spanish" . "$tid";
  $query = "select * from $table where period = '$period'";
  $result = mysql_query($query);

  $nline_f = "  ";
  $nline_p = "  ";
  $nline_m = "  ";

  // $name_lines = new lines when attendance buttons are displayed
  // "fpm" would display a new line after first, preferred, and middle name
  // "-p-" would display a new line only after the preferred name
  // "f" means there's a new line after the first name
  // "p" means there's a new line after the preferred first name
  // "m" means there's a new line after the middle name

  $name_lines_r = str_split($def_name_lines);
  $val = $name_lines_r[0];
  if ($val != "-")$nline_f = "\n";
  $val = $name_lines_r[1];
  if ($val != "-")$nline_p = "\n";
  $val = $name_lines_r[2];
  if ($val != "-")$nline_m = "\n";

/*
try1
  if ($val != "-")$nline_f = "<br>";
  $val = $name_lines_r[1];
  if ($val != "-")$nline_p = "<br>";
  $val = $name_lines_r[2];
  if ($val != "-")$nline_m = "<br>";
try2
  if ($val != "-")$nline_f = "&#013;";
  $val = $name_lines_r[1];
  if ($val != "-")$nline_p = "&#013;";
  $val = $name_lines_r[2];
  if ($val != "-")$nline_m = "&#013;";
try3
  if ($val != "-")$nline_f = "&#x00A;";
  $val = $name_lines_r[1];
  if ($val != "-")$nline_p = "&#x00A;";
  $val = $name_lines_r[2];
  if ($val != "-")$nline_m = "&#x00A;";
*/

  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $fname2 = $row['fname2'];
    $fname2_is = $row['fname2_is'];
    $id = $row['id'];

    $name_r = stu_name_ass_r($id);
    $fname = $name_r['fname'];
    $mname = $name_r['mname'];  
    $lname = $name_r['lname'];

    $n_f = 0;
    $n_p = 0;
    $n_m = 0;
    $n_l = 0;

    $no_fname = 0;
    if($def_fname_on == '1' && $def_fname2_on == 'y' &&
        $fname2_is == 'y')$no_fname = 1;

    $continues = "";
    if($no_fname || $def_fname == 'n' || $def_fname_on == 'n') $fname = "";
    else
    {
      $length = strlen($fname);

      if ($length > (int)$def_fname)
        {$length = (int)$def_fname; $continues = "~";}
      $fname = substr($fname, 0, $length);
      $fname .= $continues;
      if(!empty($fname))$n_f = 1;
    }

    $continues = "";
    if ($def_fname2 == 'n' || $fname2_is == 'n' ||
        $def_fname2_on == 'n') $fname2 = "";
    else
    {
      $length = strlen($fname2);

      if ($length > (int)$def_fname2)
        {$length = (int)$def_fname2; $continues = "~";}
      $fname2 = substr($fname2, 0, $length);
      $fname2 .= $continues;
      if(!empty($fname2))
      {
        $fname2 = "(" . $fname2 . ")";
        $n_p = 1;
        if($n_f)
        {
          $fname2 = "$nline_f$fname2";
          $n_f = 0;
        }
      }
    }

    if ($def_mname == 'n') $mname = "";
    else
      if(!empty($mname))
      {
        $n_m = 1;
        if($n_p)
        {
          $mname = "$nline_p$mname";
          $n_p = 0;
        }
        else
          if($n_f)
          {
            $mname = "$nline_f$mname";
            $n_f = 0;
          }
      }

    $continues = "";
    if ($def_lname == 'n' || $def_lname_on == 'n') $lname = "";
    else
    {
      $length = strlen($lname);
      if ($length > (int)$def_lname)
        { $length = (int)$def_lname; $continues = "~";}
      $lname = substr($lname, 0, $length);
      $lname .= $continues;
      if(!empty($lname))
      {
        $n_l = 1;
        if($n_m)
        {
          $lname = "$nline_m$lname";
          $n_m = 0;
        }
        elseif($n_p)
        {
          $lname = "$nline_p$lname";
          $n_p = 0;
        }
        else
          if($n_f)
          {
            $lname = "$nline_f$lname";
            $n_f = 0;
          }
      }
    }

    $all_name = "$fname" . "$fname2" . "$mname" . "$lname";

    $students_arr["$id"] = "$all_name";
  }
  return $students_arr;
}


function delete_today($tid="", $period="", $date)
{
 $query="";
 $table = "";
 if ($period != "HR")
 {
   $table = "attendance" . "$tid";
   $query = "delete from $table where date=$date
             and period='$period'";
 }
 else
 {
   $table = "hr_attendance" . "$tid";
   $query = "delete from $table where date=$date";
 }
 mysql_query($query);
}


function set_all_hr_absent ($tid="", $students_arr, $date)
{
 $table = "hr_attendance" . "$tid";
 while (list ($id,) = each($students_arr))
 {
   $query = "select id from $table where date=$date
             and id=$id";
   $result = mysql_query($query);
   if ($result !== FALSE && mysql_num_rows($result) == 0)
   {
     $query = "insert into $table
               (date, time_in,id,is_in)
               values ($date,'00:00:00',$id,0)";
     mysql_query($query);
   }
 }
}


function set_all_absent ($tid="", $students_arr, $period, $date)
{
 $table = "attendance" . "$tid";
 while (list ($id,) = each($students_arr))
 {
   $query = "select id from $table where date=$date
             and id=$id and period='$period'";
   $result = mysql_query($query);
   if ($result !== FALSE && mysql_num_rows($result) == 0)
   {
     $query="insert into $table 
             (period,date,time_in,id,is_in)
             values ('$period',$date,'00:00:00',$id,0)";
     mysql_query($query);
   }
 }
}


function update_hr_attendance ($tid="", $id=0, $undo="", $date, $time_in)
{
  $table = "hr_attendance" . "$tid";
  if ($undo == "n")
  {
    $query="update $table set time_in='$time_in',
        is_in=1 where date=$date and id=$id and is_in=0";
    mysql_query($query);
  }
  else
  {
    $query="update $table set time_in='00:00:00',
            is_in=0 where date=$date and id=$id";
    mysql_query($query);
  }
}


function update_attendance ($tid="", $id=0, $undo="", $period="", $date, $time_in)
{
  $table = "attendance" . "$tid";
  if ($undo == "n")
  {
    $query="update $table set time_in='$time_in',
        is_in=1 where date=$date and id=$id and is_in=0
        and period='$period'";
    mysql_query($query);
  }
  else
  {
    $query="update $table set time_in='00:00:00',
         is_in=0 where date=$date and id=$id and period='$period'";
    mysql_query($query);
  }
}


function get_seating_data( $tid="", $id=0, $period="" )
{
  $id_arr = array();
  $table = "seating" . "$tid";
  $query = "select * from $table where id = $id and
            period='$period'";
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  return $row;
}


function is_absent( $tid="", $id=0, $period="", $date)
{
  $table = "";
  $query = "";
  if($period != "HR")
  {
    $table = "attendance" . "$tid";
    $query = "select * from $table where id=$id and
              date=$date and is_in=0 and period='$period'";
    $result = mysql_query($query);
  }
  else
  {
    $table = "hr_attendance" . "$tid";
    $query = "select * from $table where id=$id and
              date=$date and is_in=0";
    $result = mysql_query($query);
  }
  
  if ($result !== FALSE && mysql_num_rows($result) == 0)
    return 0;
  else
    return 1;
}


function array_to_html($tid="", $students_arr=array(), $period, $date)
{
  $source = "";
  while (list ($id , $name) = each($students_arr))
  {
    if (is_absent( $tid, $id, $period, $date))
      $class_str = "gray_button";
    else
      $class_str = "green2_button";
    $seating_arr = get_seating_data( $tid, $id, $period);
    $seat_x = $seating_arr->seat_x;
    $seat_y = $seating_arr->seat_y;
    $undo = "u" . $id;
echo <<<EOQ
<DIV STYLE="position:absolute; top:$seat_y; left:$seat_x;">
<form method=post action="$PHP_SELF">
<input type="hidden" name="id" value=$id>
<input type="hidden" name="menu_submit" value="Attendance">
<input type="hidden" name="period" value="$period">
<input type="hidden" name="tid" value="$tid">
<input id="$undo" type="hidden" name="undo" value="n">
<input type="submit" class=$class_str name="submit" value="$name"
onclick="set_undo_value('$undo','check_undo');">
</form>
</DIV>
EOQ;
  }
}


$tag_title = "gnuschool.org Take attendance";

start_html($tag_title);
stylesheet_link("../../style_sheet");

if ($yes_input && empty($undo_all))
{
  echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function set_undo_value(hidden_id,checkbox_id)
{
  if (document.getElementById)
  {
    if ( document.getElementById(checkbox_id).checked == true)
      document.getElementById(hidden_id).value = 'y';
  }
  else
    alert("Sorry, but the Javascript code did not work. This page will not work properly.");

  return true;
}

</SCRIPT>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in you browser.</p>
</noscript>
EOQ;
}

start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Attendance";

$def_r = get_defaults_r($tid);

$comment = "";

if($yes_input)
{
  $students_arr = get_students_arr( $tid, $period, $def_r);  
  if (empty($students_arr)) 
  {
    $comment = "There are no students in period $period.<br>";
    $yes_input = 0;
    $menu_submit = "Main";
  }
}


if (!empty($undo_all))
{
  $date_id = get_dateID($date);
  delete_today($tid, $period, $date_id);
  $yes_input = 0;
  $menu_submit = "Main";
}

$menu_r=array();
$menu_r['active_page']= $menu_submit;
$menu_r['period'] = "$period";
main_menu($menu_r);

$page_title = "Take attendance";

if (!$yes_input)
{
  $comment .= "Please complete the following information:";
  title_comment_div_noend($page_title, $comment);
  echo input_form();
  end_html_exit();
}

$date_id = get_dateID($date);
if($date_id === 0) $date_id = insert_get_dateID($date);
if (empty($id))
{
  if ($period != "HR")
    set_all_absent ($tid, $students_arr, $period, $date_id);
  else
    set_all_hr_absent ($tid, $students_arr, $date_id);
}
else
{
  if ($period != "HR")
    update_attendance($tid, $id, $undo, $period, $date_id, $time);
  else
    update_hr_attendance($tid, $id, $undo, $date_id, $time);
}

array_to_html($tid, $students_arr, $period, $date_id);
?>

</body></html>
