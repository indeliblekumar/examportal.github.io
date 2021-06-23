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

$test_id=$_POST['test_id'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;
if (empty($test_id))
  { $yes_input = 0;}


function get_periods_array($tid)
{
  $periods_r = array();
  $table = "spanish" . "$tid";
  $query = "select DISTINCT period from $table order by period";
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
  while ($row = mysql_fetch_row($result))
  {
    $periods_r[] = $row[0];
  }
  return $periods_r;
}


function get_today_fields($tid)
{
  $table = "today" . "$tid";
  $today_fields_r = array();
  $query = "select names from $table";
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
  $row = mysql_fetch_row($result);
  $today_fields_str = $row[0];
  $today_fields_r = explode(",", $today_fields_str);
  return $today_fields_r;
}


function today_columns_ok($tid, $periods_r)
{
  $today_fields_r = get_today_fields($tid);
  if (sizeof($periods_r) != sizeof($today_fields_r)) return 0;
  $flag = 1;
  for ($i=0; $i<sizeof($periods_r); $i++)
  {
    $name = "period" . $periods_r[$i];
    if ($name != $today_fields_r[$i]) $flag = 0;
  }
  return $flag;
}


function drop_create_today_table($tid, $periods_r)
{
  $table = "today" . "$tid";
  $query = "drop table $table";
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
  $names_str = "";
  $fields = "";
  $values_str = "";
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
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
  $fields = "(names,$names_str)";
  $names_str = "'$names_str'";
  $query =
  "insert into $table $fields values($names_str,$values_str)";
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
}


function get_periods_r_verified($tid)
{
  $periods_r = get_periods_array($tid);
  if( !today_columns_ok($tid, $periods_r))
    drop_create_today_table($tid, $periods_r);  
  return $periods_r;
}


function get_set_str($periods_r, $test_id=0)
{
  $and_flag = 0;
  $query = "";
  for ($i=0; $i<sizeof($periods_r); $i++)
  {
    $field_name = "period" . $periods_r[$i];
    $period_checked = $_POST["$field_name"];
    if (!empty($period_checked))
    {
      if($and_flag)
        $query .= ", $field_name = $test_id";
      else
        $query .= "$field_name = $test_id";
      $and_flag = 1;
    }
  }
  return $query;
}


function update_today($tid, $set_str="")
{
  $table = "today" . "$tid";
  $query = "update $table $set_str";
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
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


function get_test_ids_arr ($tid="")
{
  $table = "today" . "$tid";
  $query = "select * from $table";
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
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

  $table = "tests" . "$tid";
  for ($i=0; $i<sizeof($test_ids_arr); $i++)
  {
    $test_id = $test_ids_arr[$i];
    $query="select description,author,title,page,isbn from $table
            where test_id=$test_id";
    $result = mysql_query($query);
// query_outcome_echo($query,$result);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo '<TR align="left">';
    echo tDataSource("Period $periods_r[$i]", "left");
    echo "<TD>";
    show_test_table ($row);
    echo "</TD>";
    echo "</TR>";
  }
  echo endTable();
  echo "</div>";
}


function show_test_table ($test_arr=array())
{
  echo startTable("border");
  $description = $test_arr['description'];
  $isbn = $test_arr['isbn'];
  $title = $test_arr['title'];
  $author = $test_arr['author'];
  $page = $test_arr['page'];
  $cell_data = tDataSource("Description", "left");
  $cell_data .= tDataSource($description, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("Author", "left");
  $cell_data .= tDataSource($author, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("Title", "left");
  $cell_data .= tDataSource($title, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("Page", "left");
  $cell_data .= tDataSource($page, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("ISBN", "left");
  $cell_data .= tDataSource($isbn, "left");
  echo rowPrint($cell_data);
  echo endTable();
}


$tag_title = "gnuschool.org Tests for periods";
$page_title = "Tests for periods";

start_html($tag_title);
stylesheet_link("../../style_sheet");
start_body("main_background");

if(!$yes_input)
{
  $comment = "Please access this page using the site menu.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

if(empty($menu_submit)) $menu_submit = "Tests";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
main_menu($menu_r);

$comment = "";
$periods_r = get_periods_r_verified($tid);
$set_str = get_set_str($periods_r, $test_id);
if(empty($set_str))
{
  $comment = "Please choose a test for at least one period.";
}
else
{
  $set_str = "set $set_str";

  $comment= "These are the tests set up for today.";
  update_today($tid, $set_str);
  today_table($tid, $periods_r);
}
title_comment_div_noend($page_title, $comment);
echo "</DIV>";
?>
</body></html>
