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
include ('./hratt_cut_functions.php');

$menu_submit=$_POST['menu_submit'];
$hr_num=$_POST['hr_num'];
$all_hr=$_POST['all_hr'];
$fname=$_POST['fname'];
$mname=$_POST['mname'];
$lname=$_POST['lname'];

$yes_input = 1;
$stu_query = post2_stu_blocks_query();
if (empty($stu_query))
  $yes_input = 0;

$hr_cut=$_POST['hr_cut'];
if(!empty($hr_cut))$hr_cut = 1;
else $hr_cut = 0;


function endTable()
{
  return "</TABLE>";
}


function show_table_column_titles($hr, $date, $today,$title_r=array())
{
  echo startTable("border");
  $t_size = count($title_r);
  $t_size++;
  if($today)$t_size++;
  $cell_data = tDataSource("<b>Homeroom $hr attendance</b>", "center", "",
                            $t_size);
  echo rowPrint($cell_data);
  $cell_data = tDataSource("&nbsp");

  while (list($id,$ndate) = each($title_r))
  {
    $cell_data .= tDataSource("&nbsp $ndate", "right");
  }

  if($today)
    $cell_data .= tDataSource("&nbsp $date", "right");

  echo rowPrint($cell_data);
}


function is_in_attendance($id,$did)
{
  $table = "attendance";
  $query = "select is_in from $table where
            id=$id and date=$did";
  $result = mysql_query($query);
//query_outcome_echo($query,$result,0);
  if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    return $row['is_in'];
  else
    return FALSE;
}


function in_non_HR_period($id,$p_tid_r,$did)
{
  $in=0;
  while (list($period,$tid) = each($p_tid_r))
  {
    $table = "attendance" . $tid;
    $query="select is_in from $table where id=$id
            and period='$period' and date=$did";
    $result = mysql_query($query);
//query_outcome_echo($query,$result,0);
    if ($row = mysql_fetch_row($result))
    {
      $is_in = $row[0];
      if($is_in){ $in = 1; break;}
    }
  }
  return $in;
}

//this function not used, use for testing
//display homeroom attendance without using attendance table
function display_dates_tables($date1, $date2, $stu_query)
{
$hr_tid_r = get_hroom_teacherID_assoc_r();

$id_rr = get_2id_iddate_rr($date1, "<=", $date2);
$id_ends_r = $id_rr[0];
if(empty($id_ends_r))
{ echo "<b>No matches were found.</b>";
  end_html_exit();
}

$id_date_r = $id_rr[1];
//print"id_date_r="; print_r($id_date_r);print"<br>";
$date_id1 = $id_ends_r[0];
$date_id2 = $id_ends_r[1];

//print"date_id1=$date_id1 date_id2=$date_id2<br>";
$date_query= get_date_query($date_id1, $date_id2);
//print"date_query=$date_query<br>";
$result = mysql_query($stu_query);

if(mysql_num_rows($result) == 0)
{ echo "<b>No matches were found.</b>";
  end_html_exit();
}

$inHR = '<font color="#008000">&nbsp in HR</font>';
$inS = '<font color="#ff08ff">&nbsp in School</font>';
$notinS = '<font color="#000ff0">&nbsp not in School</font>';
$notinHR = '<font color="#ff0000">&nbsp not in HR</font>';

$hr = "";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{ 
  $hr_next = $row['hr'];
  $name = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
  $periods = $row['periods'];
  $p_tid_r = get_period_tid_r($periods);
  $id = $row['id'];
  $cell_data = tDataSource($name, "left");
  
  if($hr != $hr_next)
  {
    $hr = $hr_next;
    $hr_tid = $hr_tid_r["$hr"];
    show_table_column_titles($hr, $date, 0, $id_date_r);
  }

  if(!empty($id_date_r))
  {
  while (list($did,$ndate) = each($id_date_r))
  {
    $hr_r = in_hrattendance_r($id,$did,$hr_tid);
    if(!empty($hr_r))
    {
      $timein = $hr_r['time_in'];
      $isin = $hr_r['is_in'];
      if($isin)
        $cell_data .= tDataSource($inHR, "right");
      else
      {
        $isin = in_non_HR_period($id,$p_tid_r,$did);
        if($isin)
          $cell_data .= tDataSource($notinHR, "right");
        else
          $cell_data .= tDataSource($notinS, "right");
      }
    }
    else
    {
      $isin = in_non_HR_period($id,$p_tid_r,$did);
      if($isin)
        $cell_data .= tDataSource($inS, "right");
      else
        $cell_data .= tDataSource($notinS, "right");
    }
  }
  echo rowPrint($cell_data);
  reset($id_date_r);
  }
}
}


function display_today_only_atten_tables($date, $stu_query)
{
$hr_tid_r = get_hroom_teacherID_assoc_r();

$today_id = get_date_id ($date);

// not in School = 0   blue   color="#000ff0"
// in homeroom  = 1    green   color="#008000"
// not in homeroom but in periods, possible cut = 2   red  color="#ff0000"
// in school but there was no homeroom class = 3      purple  color="#ff08ff"
$color_r = array();
$color_r[0] = '<font color="#000ff0">&nbsp not in School</font>';
$color_r[1] = '<font color="#008000">&nbsp in HR</font>';
$color_r[2] = '<font color="#ff0000">&nbsp not in HR</font>';
$color_r[3] = '<font color="#ff08ff">&nbsp in School</font>';
$notinS = $color_r[0];
$inHR = $color_r[1];
$notinHR = $color_r[2];
$inS = $color_r[3];

$hr = "";
$t_title = 0;
$t_end = 0;

$bgcolor_r[0] = "class=lgray";
$bgcolor_r[1] = "class=lgreen";
$row_num = 0;
$hr_cut=$_POST['hr_cut'];
if(!empty($hr_cut))$hr_cut = 1;
else $hr_cut = 0;

$result = mysql_query($stu_query);
if(mysql_num_rows($result) == 0)
{ echo "<b>No matches were found.</b>";
  end_html_exit();
}
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{ 
  $hr_next = $row['hr'];
  $name = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
  $periods = $row['periods'];
  $p_tid_r = get_period_tid_r($periods);
  $id = $row['id'];
  $isinHR = 0;
  $isinNonHR=0;
  $cut = 0;
  $cell_data = tDataSource($name, "left");
  if($hr != $hr_next)
  {
    if($t_end) echo"</TABLE><br><br>";
    $hr = $hr_next;
    $hr_tid = $hr_tid_r["$hr"];
    $t_title++;
    $t_end = 1;
  }

  if($today_id)
  {
    $hr_r = in_hrattendance_r($id,$today_id,$hr_tid);
    if(!empty($hr_r))
    {
      $timein = $hr_r['time_in'];
      $isin = $hr_r['is_in'];
      if($isin)
        $cell_data .= tDataSource($inHR, "right");
      else
      {
        $isin = in_non_HR_period($id,$p_tid_r,$today_id);
        if($isin)
        {
          if($hr_cut) $cut=1;
          $cell_data .= tDataSource($notinHR, "right");          
        }
        else
          $cell_data .= tDataSource($notinS, "right");
      }
    }
    else
    {
      $isin = in_non_HR_period($id,$p_tid_r,$today_id);
      if($isin)
        $cell_data .= tDataSource($inS, "right");
      else
        $cell_data .= tDataSource($notinS, "right");
    }
  }
  
  if($hr_cut)
  {
    if($cut)
    {
      if($row_num++ & 1)$bgcolor = $bgcolor_r[1];
      else $bgcolor = $bgcolor_r[0];
      if($t_title)
      {
        show_table_column_titles($hr, $date, $today_id);
        $t_title--;
      }
      echo rowPrint($cell_data, "", $bgcolor);
    }
  }
  else
  {
    if($row_num++ & 1)$bgcolor = $bgcolor_r[1];
    else $bgcolor = $bgcolor_r[0];
    if($t_title)
    {
      show_table_column_titles($hr, $date, $today_id);
      $t_title--;
    }
    echo rowPrint($cell_data, "", $bgcolor);
  }
}
if($t_end) echo "</TABLE>";
return $row_num;
}


//the attendance table makes homeroom searches faster
//data from all teacher attendance$tid tables is collected in
//the attendance table and the cut table
//afterwards redundant data from teacher databases could be deleted
//to streamline database
function display_attendance_tables($date, $date1, $date2, $stu_query)
{
$hr_tid_r = get_hroom_teacherID_assoc_r();
$today_id = 0;
$lt_e = "<=";
if($date == $date2)
{
  $today_id = get_date_id ($date);
  $lt_e = "<";
}

//returns $did_rr[0]=>$did_ends_r  $did_rr[1]=>$did_date_r
$id_rr = get_2id_iddate_rr($date1, $lt_e, $date2);

//$did_ends_r[0]=$date_id lowest date  $did_ends_r[1]=$date_id greatest date
$id_ends_r = $id_rr[0];

//$did_date_r[date_id]=>actual date (all dates between low and high date inclusive)
$id_date_r = $id_rr[1];
$date_id1 = $id_ends_r[0];
$date_id2 = $id_ends_r[1];

if(empty($id_ends_r))
  if(empty($today_id))
  { echo "<b>No matches were found.</b>";
    end_html_exit();
  }
  else
  {
    $id_date_r=array();
  }

$date_query= get_date_query($date_id1, $date_id2);

// not in School = 0   blue   color="#000ff0"
// in homeroom  = 1    green   color="#008000"
// not in homeroom but in periods, possible cut = 2   red  color="#ff0000"
// in school but there was no homeroom class = 3      purple  color="#ff08ff"
$color_r = array();
$color_r[0] = '<font color="#000ff0">&nbsp not in School</font>';
$color_r[1] = '<font color="#008000">&nbsp in HR</font>';
$color_r[2] = '<font color="#ff0000">&nbsp not in HR</font>';
$color_r[3] = '<font color="#ff08ff">&nbsp in School</font>';
$notinS = $color_r[0];
$inHR = $color_r[1];
$notinHR = $color_r[2];
$inS = $color_r[3];

$hr = "";
$t_title = 0;
$t_end = 0;

$bgcolor_r[0] = "class=lgray";
$bgcolor_r[1] = "class=lgreen";
$row_num = 0;
$hr_cut=$_POST['hr_cut'];
if(!empty($hr_cut))$hr_cut = 1;
else $hr_cut = 0;

$result = mysql_query($stu_query);
if(mysql_num_rows($result) == 0)
{ echo "<b>No matches were found.</b>";
  end_html_exit();
}
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{ 
  $hr_next = $row['hr'];
  $name = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
  $periods = $row['periods'];
  $p_tid_r = get_period_tid_r($periods);
  $id = $row['id'];
  $cut = 0;
  $cell_data = tDataSource($name, "left");

//if($id=="13"){//comment and uncomment for testing match with below************************************
  if($hr != $hr_next)
  {
    if($t_end) echo"</TABLE><br><br>";
    $hr = $hr_next;
    $hr_tid = $hr_tid_r["$hr"];
    $t_title++;
    $t_end = 1;
  }

  if(!empty($id_date_r))
  {
  while (list($did,$ndate) = each($id_date_r))
  {
    $isin = is_in_attendance($id,$did);
    if($hr_cut) if($isin == 2)$cut=1;
    $color = $color_r[$isin];
    $cell_data .= tDataSource($color, "right");
  }
  reset($id_date_r);
  }

  if($today_id)
  {
    $hr_r = in_hrattendance_r($id,$today_id,$hr_tid);
    if(!empty($hr_r))
    {
      $timein = $hr_r['time_in'];
      $isin = $hr_r['is_in'];
      if($isin)
        $cell_data .= tDataSource($inHR, "right");
      else
      {
        $isin = in_non_HR_period($id,$p_tid_r,$today_id);
        if($isin)
        {
          $cut=1;
          $cell_data .= tDataSource($notinHR, "right");          
        }
        else
          $cell_data .= tDataSource($notinS, "right");
      }
    }
    else
    {
      $isin = in_non_HR_period($id,$p_tid_r,$today_id);
      if($isin)
        $cell_data .= tDataSource($inS, "right");
      else
        $cell_data .= tDataSource($notinS, "right");
    }
  }
  
  if($hr_cut)
  {
    if($cut)
    {
      if($row_num++ & 1)$bgcolor = $bgcolor_r[1];
      else $bgcolor = $bgcolor_r[0];
      if($t_title)
      {
        show_table_column_titles($hr, $date, $today_id, $id_date_r);

        $t_title--;
      }
      echo rowPrint($cell_data, "", $bgcolor);
    }
  }
  else
  {
    if($row_num++ & 1)$bgcolor = $bgcolor_r[1];
    else $bgcolor = $bgcolor_r[0];
    if($t_title)
    {
      show_table_column_titles($hr, $date, $today_id, $id_date_r);
      $t_title--;
    }
    echo rowPrint($cell_data, "", $bgcolor);
  }
}
//}//comment and uncomment for testing **************************************
if($t_end) echo "</TABLE>";
return $row_num;
}


$tag_title = "gnuschool.org Homeroom Attendance Search";

start_html($tag_title);
stylesheet_link("../style_sheet");


echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function set_s_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");


  if (document.getElementById)
  {
    if ( radio.checked == true)
    {
      document.getElementById('s1').value = date_arr[0];
      document.getElementById('s2').value = date_arr[1];
      document.getElementById('s3').value = date_arr[2];
    }
  }
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}

function set_e_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");


  if (document.getElementById)
  {
    if ( radio.checked == true)
    {
      document.getElementById('e1').value = date_arr[0];
      document.getElementById('e2').value = date_arr[1];
      document.getElementById('e3').value = date_arr[2];
    }
  }
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}
</SCRIPT>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
     You may be able to fix the problem, if you enable Javascript in your browser.
</noscript>
EOQ;


start_body("main_background");

$page_title="Homeroom attendance search <BR>
                        (this page was opened $date at $time)";
if(empty($menu_submit)) $menu_submit="Attendance Search";
if($menu_submit=="Attendance Search") $defaults="yes";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
$menu_r['defaults'] = $defaults;
main_menu($menu_r);

if (!$yes_input)
{
  $submit_val = "Attendance search";
  $def_r = get_date_settings_r();
  $comment = "Please complete the following information:";
  title_comment_div_noend($page_title, $comment);
  $only_hr_cuts = 1;
  date_search_form($def_r, $submit_val,$date, $only_hr_cuts);
  end_html_exit();
}

$comment = <<<EOQ
<font color="#000ff0">The student was not in school.</font><br>
<font color="#008000">The student was in homeroom.</font><br>
<font color="#ff0000">The student was in school but not in homeroom.</font><br>
<font color="#ff08ff">The student was in school and there was no homeroom class.</font><br>
EOQ;

title_comment_div_noend($page_title,$comment);

//searches of thousands of entries would be slow, to optimize
//attendance table consolidates hr_attendance data from all teachers into one table
//this function is used to fill attendance table and cut table
update_cut_att($date);

$search_r = glue_search_inputs();
if (!$search_r['ok'])
{
  $errors = $search_r['errors'];
  $comment = "There was a search date error.<br>$errors";
  title_comment_div_noend("", $comment);
  end_html_exit();
}

$search_r=order_date_r($date, $search_r);
$date1=$search_r[0];
$date2=$search_r[1];

//this code tests an alternate search
//search all hr_attendance$tid and attendance$tid tables 
//without using the main attendance table
//display_dates_tables($date1, $date2, $stu_query);

$row_num=0;
if($date==$date1 && $date==$date2)
{
   $row_num=display_today_only_atten_tables($date, $stu_query);
}
else
{
//search attendance table for all dates except today
//then search all hr_attendance$tid and attendance$tid tables for today only
  $row_num = display_attendance_tables($date, $date1, $date2, $stu_query);
}

if(empty($row_num))
{
  echo "<b>No matches were found.</b><br>$errors";
}
echo "</DIV>";
/*
//if we want to check the actual time student walks in classroom
// compared to the time the class starts, could do something like
              $pos = strpos($time_in, ":"); 
              $hour = substr($time_in, 0, $pos);
              $minute = substr($time_in, $pos + 1, 2);
              $total_minutes1 = (int)$hour * 60 + (int)$minute;  

              $pos = strpos($in_by, ":"); 
              $hour = substr($in_by, 0, $pos);
              $minute = substr($in_by, $pos + 1, 2);
              $total_minutes2 = (int)$hour * 60 + (int)$minute;  

              $t_min = $total_minutes2 - $total_minutes1;
              $cell_data .= tDataSource("&nbsp in $t_min &nbsp", "right");
*/
?>

</body></html>