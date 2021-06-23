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
include ('../search.php');
include ('../login.php');

$menu_submit=$_POST['menu_submit'];
$all_periods=$_POST['all_periods'];
$period=$_POST['period'];
$fname=$_POST['fname'];
$lname=$_POST['lname'];


$periods_flag = 0;
$period_flag = 0;
$name_flag = 0;
if (!empty($all_periods)) $periods_flag = 1;
if (!empty($period)) $period_flag = 1;
if (!empty($fname) || !empty($mname) || !empty($lname)) $name_flag = 1;

$yes_input = 0;
if ($periods_flag || $period_flag || $name_flag)
 $yes_input = 1;

if($period_flag)
 if ($period == "hr" || $period == "Hr" || $period == "hR")
 {
   $period = "HR";
   $_POST['period'] = $period;
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


function get_ids_r ($spanish_query, $tid)
{
  $periods_idsNames_ass = array();
  $result = mysql_query($spanish_query);
  if(mysql_num_rows($result) == 0) return 0;

  $period_idName_num=array();
  $id_name_r=array();

  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $period = $row['period'];
  $id = $row['id'];

  $n_r = stu_name_ass_r($id);
  $name = $n_r['fname'] . " " . $n_r['mname'] . " " . $n_r['lname'];
  $id_name_r['name'] = $name;

  $id_name_r['id'] = $id;
  $period_idName_num[]=$id_name_r;

  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $next_period = $row['period'];

    $fname = "";
    $fname2 = $row['fname2'];
    if(empty($fname2))
      $fname = $row['fname'];
    else
      if($row['use_fname2'] == 'y') $fname = $fname2;
      else $fname = $row['fname'];

    $id = $row['id'];

    $n_r = stu_name_ass_r($id);
    $name = $n_r['fname'] . " " . $n_r['mname'] . " " . $n_r['lname'];
    $id_name_r['name'] = $name;

    $id_name_r['id'] = $id;

    if ($period == $next_period)
      $period_idName_num[]=$id_name_r;
    else
    {
      $periods_idsNames_ass["$period"] = $period_idName_num;
      $period_idName_num=array();
      $period_idName_num[]=$id_name_r;
      $period = $next_period;
    }
  }
  $periods_idsNames_ass["$period"] = $period_idName_num;
//show_ids_r($periods_idsNames_ass);
  return $periods_idsNames_ass;
}


function get_dates_r ($tid="", $date_clause="", $period="")
{
 $dates_r = array();
 $query=""; 

 if($period == "HR")
 {
   $table = "hr_attendance" . "$tid";
   $query = "select distinct date from $table where
             $date_clause order by date";
 }
 else
 {
   $table = "attendance" . "$tid";
   $query = "select distinct date from $table where
             $date_clause and period='$period' order by date";
 }

 $result = mysql_query($query);
//  query_outcome_echo($query,$result);
 if ($result === FALSE) return FALSE;
 if (mysql_num_rows($result) === 0)
 {
   return 0;
 }
 else
 {
   while ($row = mysql_fetch_row($result))
   {  
     $dates_r[] = $row[0];
   }
   return $dates_r;
 }
}


function get_attendance_r($tid="",$id=0,$date_clause="",$period="")
{
  $date_time_r = array();
  $query="";

  if($period == "HR")
  {
    $table = "hr_attendance" . "$tid";
    $query = "select date,time_in from $table
              where id=$id and $date_clause";
  }
  else
  {
    $table = "attendance" . "$tid";
    $query = "select date,time_in from $table where id=$id
            and $date_clause and period='$period'";
  }


 $result = mysql_query($query);
//  query_outcome_echo($query,$result);
 if ($result === FALSE) return FALSE;
 if (mysql_num_rows($result) === 0)
 {
   return 0;
 }
 else
 {
   while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
   {
     $date = $row['date'];
     $date_time_r["$date"] = $row['time_in'];
   }
   return $date_time_r;
 }
}


function show_table_column_titles($title_r=array(), $id_date_r, $period)
{
  echo startTable("border");
  $cell_data = tDataSource("Period $period attendance", "center", "",
                            sizeof($title_r) +1);
  echo rowPrint($cell_data);
  $cell_data = tDataSource("&nbsp");
  $count_r = count($title_r);
  for ($i=0; $i<$count_r; $i++)
  {
    $date_id = $title_r[$i];
    $date = $id_date_r["$date_id"];
    $cell_data .= tDataSource($date, "right");
  }
  echo rowPrint($cell_data);
}


function show_periods_attendance_table
                           ($tid="",$period_idsNames_ass,$date1,$date2)
{
//print"start=$date1 end=$date2<br>";
  $id_rr = get_id_iddate_rr($date1,$date2);
  $id_ends_r = $id_rr[0];
  $id_date_r = $id_rr[1];
//print"id_date_r="; print_r($id_date_r);print"<br>";
  $date_id1 = $id_ends_r[0];
  $date_id2 = $id_ends_r[1];

  $date_query= get_date_query($date_id1, $date_id2);
//print"date_query=$date_query<br>";
  $redstr = '<font color="#ff0000">&nbsp not in class</font>';
  while (list($period,$ids_names_num) = each($period_idsNames_ass))
  {
    $dates_r = get_dates_r ($tid, $date_query, $period);
//print"dates_r="; print_r($dates_r);print"<br>";
    show_table_column_titles($dates_r, $id_date_r, $period);
    while (list(,$id_name_r) = each($ids_names_num))
    {
      $fname = $id_name_r['fname'];
      $fname2 = $id_name_r['fname2'];
      if(!empty($fname2)) $fname .= " (" . $fname2 . ") ";

      $id = $id_name_r['id'];
      $name = $fname;
      $name .= " " . $id_name_r['mname'];
      $name .= " " . $id_name_r['lname'];
      $id = $id_name_r['id'];
      $cell_data = tDataSource($name, "left");

      $atten_in_ass=get_attendance_r($tid,$id,$date_query,$period);
//print_r($atten_in_ass);print"<br>";
      $sizeofr = count($atten_in_ass);
      for ($i=0; $i<$sizeofr; $i++)
      {
        $date_id = $dates_r[$i];
        $time_in = $atten_in_ass["$date_id"];
        if (!empty($time_in))
          if ($time_in == "00:00:00")
            $cell_data .= tDataSource($redstr, "right");
          else
            $cell_data .= tDataSource("in", "right");
        else
          $cell_data .= tDataSource("&nbsp", "right");
      }
      echo rowPrint($cell_data);
    }
    echo endTable();
    echo "<br>";
  }
}


$tag_title = "gnuschool.org Absent search";
$page_title = "Absent search";

start_html($tag_title);
stylesheet_link("../../style_sheet");

echo <<<EOQ

<SCRIPT LANGUAGE="JavaScript">

function set_s_values(radio) {
  radio_val = radio.value;
  date_r = radio_val.split("-");

  if (document.getElementById)
  {
    if ( radio.checked == true)
    {
      document.getElementById('s1').value = date_r[0];
      document.getElementById('s2').value = date_r[1];
      document.getElementById('s3').value = date_r[2];
    }
  }
  else
    alert("Sorry, but the Javascript code did not work. This page will not work properly.");
}

function set_e_values(radio) {
  radio_val = radio.value;
  date_r = radio_val.split("-");

  if (document.getElementById)
  {
    if ( radio.checked == true)
    {
      document.getElementById('e1').value = date_r[0];
      document.getElementById('e2').value = date_r[1];
      document.getElementById('e3').value = date_r[2];
    }
  }
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

start_body("main_background");

if(empty($menu_submit))  $menu_submit = "Absent Search";
if($menu_submit == "Absent Search") $defaults = "yes";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
$menu_r['defaults'] = $defaults;
main_menu($menu_r);

$page_title = "Attendance records";

if (!$yes_input)
{
  $submit_val = "Absent search";
  $def_r = get_defaults_r($tid);
  $comment = "Please complete the following information:";
  title_comment_div_noend($page_title, $comment);
  date_search_form($def_r, $submit_val, $date);
  end_html_exit();
}

title_comment_div_noend($page_title);

$query_str = "";

$defaults_r = get_defaults_r($tid);
$order = $defaults_r['order'];

$per_stu_rrr = array();
$stu_rr = array();

if($name_flag)
{
  $query_str = post2_stu_blocks_query($order);
  $stu_rr = stu_blocks2stu_rr($query_str,$period_flag,$period,$tid);
  $per_stu_rrr = add_period_fname2($tid,$stu_rr,$period);
}
else
{
  $query_str = post2_spanish_query($tid,"period");
  $stu_rr = spanish2stu_rr($query_str);
  $per_stu_rrr = add_stu_names($tid, $stu_rr);
}

$search_r = glue_search_inputs();
$date_start = $search_r['search_start'];
$date_end = $search_r['search_end'];
show_periods_attendance_table ($tid, $per_stu_rrr, $date_start, $date_end);

echo "</DIV>";
?>

</body></html>
