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
$submit=$_POST['submit'];
$id=$_POST['id'];
$menu_submit=$_POST['menu_submit'];

// if (empty($period))
//  $yes_input = 0;


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


function tFormDataSource($data="", $align="", $width="", $colspan="")
{
  if ($align != "") $align = "align=" . $align;
  if ($colspan != "") $colspan = "colspan=" . $colspan;
  if ($width != "") $width = "width=" . $width;
$data_html = <<<EOQ
<form method=post action="./test_monitor2.php">
<TD $align $colspan $width>
$data
</TD>
</form>
EOQ;
return $data_html;
}


function tFormDataSource2($data="", $align="", $width="", $colspan="")
{
  if ($align != "") $align = "align=" . $align;
  if ($colspan != "") $colspan = "colspan=" . $colspan;
  if ($width != "") $width = "width=" . $width;
$data_html = <<<EOQ
<form method=post action="$PHP_SELF">
<TD $align $colspan $width>
$data
</TD>
</form>
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


function get_periods_array($tid)
{
  $periods_r = array();

  $table = "spanish" . "$tid";
  $query = "select DISTINCT period from $table order by period";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  while ($row = mysql_fetch_row($result))
  {
    $periods_r[] = $row[0];
  }
  return $periods_r;
}


function get_today_fields($tid, $periods_r)
{
  $today_fields_r = array();

  $table = "today" . "$tid";
  $query = "select names from $table";
  $result = mysql_query($query);
  if($result === FALSE)
  {
    create_today_table($tid, $periods_r);
    $result = mysql_query($query);
  }
//  query_outcome_echo($query,$result);
  $row = mysql_fetch_row($result);
  $fields_str = $row[0];
  $today_fields_r = explode(",", $fields_str);
  return $today_fields_r;
}


function today_columns_ok($tid, $periods_r)
{
  $today_fields_r = get_today_fields($tid, $periods_r);
  if (sizeof($periods_r) != sizeof($today_fields_r)) return 0;
  $flag = 1;
  for ($i=0; $i<sizeof($periods_r); $i++)
  {
    $name = "period" . $periods_r[$i];
    if ($name != $today_fields_r[$i]) $flag = 0;
  }
  return $flag;
}


function create_today_table($tid, $periods_r)
{
  $names_str = "";
  $fields = "";
  $column_type = "varchar(12)"; 
  while (list(,$value) = each($periods_r))
  {
    $fields .= "period$value " . "$column_type,"; 
    $names_str .= "period$value,";
    $values_str .= "1,";
  }
  $values_str = substr($values_str, 0, strlen($values_str) - 1);
  $names_str = substr($names_str, 0, strlen($names_str) - 1);
  $fields = substr($fields, 0, strlen($fields) - 1);
  $names_field = "names varchar(125),";
  $fields="($names_field$fields)";

  $table = "today" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  $fields = "(names,$names_str)";
  $names_str = "'$names_str'";
  $query =
  "insert into $table $fields values($names_str,$values_str)";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
}


function drop_today_table($tid)
{
  $table = "today" . "$tid";
  $query = "drop table $table";
  $result = mysql_query($query);
}


function get_periods_r_verified($tid)
{
  $periods_r = get_periods_array($tid);
  if( !today_columns_ok($tid, $periods_r))
  {
    drop_today_table($tid);  
    create_today_table($tid, $periods_r);  
  }
  return $periods_r;
}


function get_test_ids_arr ($tid="")
{
  $table = "today" . "$tid";
  $query = "select * from $table";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $today_fields_str=$row["names"];
  $today_fields_r = explode(",", $today_fields_str);
  $test_ids_r = array();
  for ($i=0; $i<sizeof($today_fields_r); $i++)
  {
    $field_name = $today_fields_r[$i];
    $test_ids_r[]=$row["$field_name"];  
  }
  return $test_ids_r;
}


function today_table ($tid="", $periods_r)
{
  $test_ids_arr = get_test_ids_arr ($tid);
  echo "<DIV class=main_table>";
  echo startTable("border");
  $cell_data = tDataSource("Tests set up for today", "center", "",2);
  echo rowPrint($cell_data);
  $cell_data = tDataSource("&nbsp");
  $cell_data .= tDataSource("Test Description", "left");
  echo rowPrint($cell_data);

  $table = "tests" . "$tid";
  for ($i=0; $i<sizeof($test_ids_arr); $i++)
  {
    $test_id = $test_ids_arr[$i];
    $query="select description from $table where test_id=$test_id";
    $result = mysql_query($query);
//  query_outcome_echo($query,$result);
    $row = mysql_fetch_row($result);
    $description = $row[0];
    $period_form = get_period_form($periods_r[$i]);
    $cell_data = tFormDataSource($period_form, "left");
    $test_form = test_id_button($test_id,$description);
    $cell_data .= tFormDataSource2($test_form, "left");
//    $cell_data .= tFormDataSource($test_form, "left");
//    $cell_data .= tDataSource($description, "left");
    echo rowPrint($cell_data);
  }
  echo endTable();
  echo '</form>';
  echo "</div>";
}


function get_period_form($period)
{
  $name = "Period $period";
  $form = <<<EOQ
<input type="hidden" name="period" value="$period">
<input type="hidden" name="menu_submit" value="Monitor">
<input type="submit" name="submit" value="$name">
EOQ;
  return $form;
}


function test_id_button ($test_id,$description)
{
  $page = "../teachers/grades/test_info.php?test_id=$test_id";
  $window_description =
       "width=600,height=400,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<input type="hidden" name="menu_submit" value="Monitor a Test">
<input type=submit name=submit value="$description"
onClick="window.open('$page','grades2window','$window_description')">
EOQ;
  return $form_html;  
}


$tag_title = "gnuschool.org Take attendance";

start_html($tag_title);
stylesheet_link("../../style_sheet");
start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Tests";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
main_menu($menu_r);

$page_title = "Monitor a test";


$comment = "Please click on the period to monitor.";
title_comment_div_noend($page_title, $comment);


$periods_r = get_periods_r_verified($tid);
today_table ($tid, $periods_r);

?>
</body></html>
