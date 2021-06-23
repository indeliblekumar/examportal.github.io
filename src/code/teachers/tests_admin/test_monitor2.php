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

$period=$_POST['period'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;
if (empty($period))
  $yes_input = 0;

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


function get_seating_data( $tid="", $id=0, $period="" )
{
  $id_arr = array();

  $table = "seating" . "$tid";
  $query = "select * from $table where id = $id and
            period='$period'";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  $row = mysql_fetch_object($result);
  return $row;
}


function get_test_id($tid, $period="")
{
  $period = "period" . $period;

  $table = "today" . "$tid";
  $query = "select $period from $table";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if($result === FALSE) return FALSE;
  $row = mysql_fetch_row($result);
  $test_id = $row[0];
  return $test_id;
}


function array_to_html($tid="", $students_arr=array(), $period="",$did)
{
  $test_id = get_test_id($tid,$period);
  $return = "";
  if($test_id===FALSE)return FALSE;
  $source = "";
  while (list ($id , $name) = each($students_arr))
  {
    $status = is_done_with_test($tid,$id,$period,$test_id,$did);
    if ($status == "not done")
      $class_str = "red_button";
    elseif ($status == "done")
      $class_str = "green2_button";
    elseif ($status == "absent")
      $class_str = "gray_button";
    else
    {
      $page_title = "Monitor a test";
      $comment = "You need to take attendance before monitoring a test.";
      title_comment_div_noend($page_title, $comment);
      return 0;
    }
    $seating_arr = get_seating_data( $tid, $id, $period);
    $seat_x = $seating_arr->seat_x;
    $seat_y = $seating_arr->seat_y;

    $info_form = get_info_form($id, $test_id, "$name", "$period",
                               $class_str);
echo <<<EOQ
<DIV STYLE="position:absolute; top:$seat_y; left:$seat_x;">
$info_form
</DIV>
EOQ;
  }
  return 1;
}


function get_info_form ($id=0, $test_id=0, $name, $period, $class_str)
{
  $page = "./test_monitor3.php";
  $window_description =
"width=600,height=400,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<form method=post target="newwindow" action="./test_monitor3.php"> 
<input type=hidden name=id value="$id">
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=period value="$period">
<input type=submit name=name value="$name" class=$class_str
onClick="window.open('$page','newwindow','$window_description')">
</form>
EOQ;
  return $form_html;  
}


function is_done_with_test($tid,$id,$period,$test_id,$did)
{
  $table = "attendance" . "$tid";
  $query = "select time_in from $table where date=$did
            and id=$id and period='$period'";
  $result = mysql_query($query);  
//  query_outcome_echo($query,$result);
  if (mysql_num_rows($result) == 0) return "zero";
  else $row = mysql_fetch_row($result);
  $time_in = $row[0];
  if ($time_in == "00:00:00") return "absent";
  else
  {
    $table = "scores" . "$tid";
    $query = "select * from $table where id=$id and
            date=$did and test_id=$test_id and period='$period'";
    $result = mysql_query($query);
//  query_outcome_echo($query,$result);
    if (mysql_num_rows($result) == 0)
      return "not done";
    else
      return "done";
  }
}


$tag_title = "gnuschool.org Take attendance";


start_html($tag_title);
stylesheet_link("../../style_sheet");

if ($yes_input)
{
/*
  echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function doneConfirm() {
var ok=confirm("checkbox is checked")
if (ok) return true; else return false;
}

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


function set_bgcolor(my_checkbox, ids_str, number_of_ids)
{
  id_arr = ids_str.split("_");
  if (my_checkbox.checked)
  {
    if (document.getElementById)
    {
      for( i = 0; i < number_of_ids; i++)
      {
        id = id_arr[i];
        document.getElementById(id).style.backgroundColor = 'red';
      }
    }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
  else
  {
    if (document.getElementById)
    {
      for( i = 0; i < number_of_ids; i++)
      {
        id = id_arr[i];
        document.getElementById(id).style.backgroundColor = 'white';
      }
    }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
}


function set_action(action, id)
{
  if (document.getElementById)
    document.getElementById(id).action = action;
  else
    alert("Sorry, but the Javascript code did not work. This page will not work properly.");
}
</SCRIPT>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in you browser.</p>
</noscript>
EOQ;
*/
}

start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Tests";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
$menu_r['period'] = "$period";
main_menu($menu_r);

if (!$yes_input)
{
  $page_title = "Monitor a test";
  $comment = "Please access this page using the site menu.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

$did = get_dateID($date);
$def_r = get_defaults_r($tid);
$students_arr = get_students_arr($tid, $period, $def_r);  

$found = array_to_html($tid,$students_arr,$period,$did);
// img_div();
if ($found === FALSE)
{
  $page_title = "Monitor a test";
  $comment = "There are no tests setup for today.";
  title_comment_div_noend($page_title, $comment);
}

?>
</body></html>
