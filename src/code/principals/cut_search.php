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

$yes_input = 1;
$stu_query = post2_stu_blocks_query();
if (empty($stu_query))
  $yes_input = 0;

function get_stu_blocks_info_rr($stu_query)
{
  $info_rr = array();
  $result = mysql_query($stu_query);
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $stu_r=array();
    $stu_r['id'] = $row['id'];
    $stu_r['hr'] = $row['hr'];
    $stu_r['periods'] = $row['periods'];
   
    $stu_r['name']=$row['fname'] ." ". $row['mname'] ." ". $row['lname'];
    $info_rr[]=$stu_r;
  }
  return $info_rr;
}


function get_teacher_id_r ()
{
  $db_r = array();
  $query = "select tid from teachers";
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  return $row;
}


function get_cut_rr($date_clause="")
{
  $cut_rr = array();
  $query = "select * from cut where $date_clause";

  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $stu_r=array();
    $id = $row['id'];
    $stu_r[] = $row['date'];
    $stu_r[] = $row['period'];
    $cut_rr["$id"][]= $stu_r;
  }
  return $cut_rr;
}


function get_dates_arr ($date_clause="")
{
  $dates_arr = array();
  $query = "select distinct date from attendance where
            $date_clause order by date";
  $result = mysql_query($query);
  while ($row = mysql_fetch_row($result))
  {  
    $dates_arr[] = $row[0];
  }
  return $dates_arr;
}


function get_attendance_arr($id=0,$date_clause="")
{
  $date_time_in = array();
  $query = "select date,time_in from attendance where id=$id
            and $date_clause";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $did = $row['date'];
    $did_time_in["$did"] = $row['time_in'];
  }
  return $did_time_in;
}


function is_in_hr_atten($tid, $id, $did)
{
  $is_in="";
  $table = "hr_attendance" . $tid;
  $query = "select is_in from $table where id=$id
            and date=$did";
  $result = mysql_query($query);
//query_outcome_echo($query,$result,0);
  if($row = mysql_fetch_row($result))
    $is_in = $row[0]; 

  return $is_in;
}


function get_today_cuts($id,$in_hr,$periods,$did,$date,&$date_period_r)
{
 $per_tid_r = get_period_tid_r($periods);
 $cut_hr_flag = 0;
 $in_pers = 0;
 $p_names_r=array();
 $p_in_r=array();

 while (list($period,$tid) = each($per_tid_r))
 {
   $table = "attendance" . $tid;
   $query="select is_in from $table where id=$id
           and period='$period' and date=$did";
   $result = mysql_query($query);
   if ($row = mysql_fetch_row($result))
   {
     $is_in = $row[0];
     $p_names_r[] = "$period";
     if($is_in == 1)
     {
       $in_pers = 1;
       $p_in_r["$period"] = 1;
     }
     else
     {
       $p_in_r["$period"] = 0;
     }
   }
 }
/*
// this shows if student cut homeroom
// this is already shown by hr_attendance search
// so we'll comment out for now
 if($in_hr !== "")
   if($in_pers && !$in_hr)
   {
     $date_period_r[] = "$date Period HR";
   }
*/
 for($i=0;$i<count($p_names_r);$i++)
 {
   $period = $p_names_r[$i];
   $is_in = $p_in_r["$period"];
     
   if (!$is_in)
     if ($in_hr || $in_pers)
       $date_period_r[] = "$date Period $period";
 }
}


function echo_rows($row_num, $name, $date_period_r, $bgcolor_r)
{
  if($row_num++ & 1)$bgcolor = $bgcolor_r[1];
  else $bgcolor = $bgcolor_r[0];

  $alignright = "right";
  $alignleft = "left";
  $date_period = $date_period_r[0];
  $current_r = explode(" ",$date_period);
  $c_date = $current_r[0];
  $date_period = "&nbsp" . $date_period;
    
  $cell_data = tDataSource("$name&nbsp", $alignright);
  $cell_data .= tDataSource($date_period, $alignleft);          
  echo rowPrint($cell_data, "", $bgcolor);
  for ($k=1;$k<sizeof($date_period_r);$k++)
  {
    $date_period = $date_period_r[$k];
    $current_r = explode(" ",$date_period);
    $n_date = $current_r[0];
    $date_period = "&nbsp" . $date_period;
    if($c_date != $n_date)
    {
      if($row_num++ & 1)$bgcolor = $bgcolor_r[1];
      else $bgcolor = $bgcolor_r[0];
      $c_date = $n_date;
    }
    $alignright = "right";
    $alignleft = "left";
      
    $cell_data = tDataSource("&nbsp", $alignright);
    $cell_data .= tDataSource( $date_period, $alignleft);          
    echo  rowPrint($cell_data, "", $bgcolor);
  }
  return $row_num;
}


function show_table_hr_title($hr)
{
  echo startTable("border");
  $cell_data = tDataSource("<b>Homeroom $hr cuts</b>", "center", "","");
  echo rowPrint($cell_data);
}


function display_today_only_cut_tables($date, $stu_query)
{
$hr_tid_r = get_hroom_teacherID_assoc_r();

$today_id = get_date_id ($date);

$bgcolor_r[0] = "class=lpink";
$bgcolor_r[1] = "class=lgreen";
$row_num = 0;

$hr = "";
$t_end = 0;

$result = mysql_query($stu_query);
if(mysql_num_rows($result) == 0)
{ echo "No matches were found";
  end_html_exit();
}
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{ 
  $hr_next = $row['hr'];
  $name = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
  $periods = $row['periods'];
  $id = $row['id'];
  
  $date_period_r = array();

  if($today_id)
  {
    $hr_tid = $hr_tid_r["$hr_next"];
    $in_hr = is_in_hr_atten($hr_tid, $id, $today_id);
    get_today_cuts($id,$in_hr,$periods,$today_id,$date,$date_period_r);
  }
  sort($date_period_r);

  if(!empty($date_period_r))
  {
    if($hr != $hr_next)
    {
      if($t_end)
      {
        echo"</TABLE><br><br>";
      }
      show_table_hr_title($hr_next);

      $hr = $hr_next;
      $hr_tid = $hr_tid_r["$hr"];
      $t_end = 1;
    }
    
    echo "<TR><TD><TABLE width=\"100%\">";
    $row_num = echo_rows($row_num, $name, $date_period_r, $bgcolor_r);
    echo "</TABLE></TD></TR>";
  }
}
if($t_end) echo "</TABLE>";
return $row_num;
}


//the cut table makes cut searches faster
//data from all teacher attendance$tid tables is collected in
//the attendance table and the cut table
//afterwards redundant data from teacher databases could be deleted
//to streamline database
function display_cut_tables($date, $date1, $date2, $stu_query)
{
$hr_tid_r = get_hroom_teacherID_assoc_r();

$today_id = 0;

$lt_e = "<=";
if($date == $date2)
{
  $today_id = get_date_id ($date);
  $lt_e = "<";
}

//$did_ends_r[0]=>id of lowest date, [1]=>id of greatest date
//$did_date_r[date id]=> word date
//$did_rr[0]=>$did_ends_r  $did_rr[1]=>$did_date_r
$did_rr = get_2id_iddate_rr($date1, $lt_e, $date2);
//print_r($did_rr);print"<br><br>";

$did_ends_r = $did_rr[0];
$did_date_r = $did_rr[1];
$date_id1 = $did_ends_r[0];
$date_id2 = $did_ends_r[1];
if(empty($did_ends_r))
{ echo "No matches were found";
  end_html_exit();
}

$date_query= get_date_query($date_id1, $date_id2);
//print"date_query=$date_query<br>";
$bgcolor_r[0] = "class=lpink";
$bgcolor_r[1] = "class=lgreen";
$row_num = 0;

$hr = "";
$t_end = 0;

$all_cuts_rr = get_cut_rr($date_query);
if(!is_array($all_cuts_rr))
{ echo "No matches were found";
  end_html_exit();
}

$result = mysql_query($stu_query);
if(mysql_num_rows($result) == 0)
{ echo "No matches were found";
  end_html_exit();
}
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{ 
  $hr_next = $row['hr'];
  $name = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
  $periods = $row['periods'];
  $id = $row['id'];
  
  $id_cuts_r = array();
  $id_cuts_r = $all_cuts_rr["$id"];
  $date_period_r = array();
  for ($i=0;$i<count($id_cuts_r);$i++)
  {
    $did1 = $id_cuts_r[$i][0];
    $period = $id_cuts_r[$i][1];
    $ndate = $did_date_r["$did1"];
    $date_period_r[] = "$ndate Period $period";
  }

  if($today_id)
  {
    $hr_tid = $hr_tid_r["$hr_next"];
    $in_hr = is_in_hr_atten($hr_tid, $id, $today_id);
    get_today_cuts($id,$in_hr,$periods,$today_id,$date,$date_period_r);
  }
  sort($date_period_r);

  if(!empty($date_period_r))
  {
    if($hr != $hr_next)
    {
      if($t_end)
      {
        echo"</TABLE><br><br>";
      }
      show_table_hr_title($hr_next);

      $hr = $hr_next;
      $hr_tid = $hr_tid_r["$hr"];
      $t_end = 1;
    }
    
    echo "<TR><TD><TABLE width=\"100%\">";
    $row_num = echo_rows($row_num, $name, $date_period_r, $bgcolor_r);
    echo "</TABLE></TD></TR>";
  }
}
if($t_end) echo "</TABLE>";
return $row_num;
}


$tag_title = "gnuschool.org Cut search";
start_html($tag_title);
stylesheet_link("../style_sheet");

echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function set_s_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");

  if ( radio.checked == true)
  {
    if (document.getElementById)
    {
      document.getElementById('s1').value = date_arr[0];
      document.getElementById('s2').value = date_arr[1];
      document.getElementById('s3').value = date_arr[2];
    }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
}

function set_e_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");

  if ( radio.checked == true)
  {
    if (document.getElementById)
    {
      document.getElementById('e1').value = date_arr[0];
      document.getElementById('e2').value = date_arr[1];
      document.getElementById('e3').value = date_arr[2];
    }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
}
</SCRIPT>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in your browser.</p>
</noscript>
EOQ;

start_body("main_background");

$page_title = "Cut search<br>
               (this page was opened $date at $time)";

if(empty($menu_submit))  $menu_submit = "Cut Search";
if($menu_submit == "Cut Search") $defaults = "yes";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
$menu_r['defaults'] = $defaults;
main_menu($menu_r);

if (!$yes_input)
{
  $submit_val = "Cut search";
  $def_r = get_date_settings_r();
  $comment = "Search all classes that students may have cut except homeroom.";  
  $comment .= "<br>Please complete the following information:";
  title_comment_div_noend($page_title, $comment);
  date_search_form($def_r, $submit_val, $date);
  end_html_exit();
}

$page_title="Cut records <br>
             (this page was opened $date at $time)";
title_comment_div_noend($page_title);

//searches of thousands of entries would be too slow, they need to be optimized 
//attendance table consolidates hr_attendance data from all teachers into one table
//function update_cut_att is used to fill attendance table and cut table
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

$row_num=0;
if($date==$date1 && $date==$date2)
   $row_num=display_today_only_cut_tables($date, $stu_query);
else
{
//search cut table for all dates except today
//then search all hr_attendance$tid and attendance$tid tables for today only
  $row_num = display_cut_tables($date, $date1, $date2, $stu_query);
}

if(empty($row_num))
{
  $comment = "No records found.<br>$errors";
  title_comment_div_noend("", $comment);
  end_html_exit();
}

echo "</DIV>";
?>
</body></html>
