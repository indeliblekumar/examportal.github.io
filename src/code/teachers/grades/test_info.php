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

$test_id=$_GET['test_id'];
$menu_submit = $_POST['menu_submit'];

$yes_input = 1;
if (empty($test_id) && empty($menu_submit))
  $yes_input = 0;


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


function startTable($border="", $c_padding=0, $c_spacing=0)
{
  $cellpadding = "cellpadding=" . $c_padding;
  $cellspacing = "cellspacing=" . $c_spacing;
  return "<TABLE $border $cellspacing $cellpadding>";
}


function endTable()
{
  return "</TABLE>";
}


function show_test_table ($test_r)
{
  echo startTable("border");
  $description = $test_r['description'];
  $isbn = $test_r['isbn'];
  $title = $test_r['title'];
  $author = $test_r['author'];
  $page = $test_r['page'];
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



function get_test_r($tid = "", $test_id)
{
  $table = "tests" . "$tid";
  $test_r = array();
  $query = "select * from $table where test_id = $test_id";
  $result = mysql_query($query);  
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $test_r["description"]=$row['description'];
  $test_r["isbn"]=$row['isbn'];
  $test_r["title"]=$row['title'];
  $test_r["author"]=$row['author'];
  $test_r["page"]=$row['page'];
  return $test_r;
}


$tag_title = "gnuschool.org Find a Test";
$page_title = "Test information";

start_html($tag_title);
stylesheet_link("../../style_sheet");
start_body("main_background");

if (!$yes_input)
{
  $comment = "Please access this page thru the grades page.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

title_comment_div_noend($page_title);

$test_r = get_test_r($tid, $test_id);

show_test_table ($test_r);
echo "</DIV>";

?>
</body></html>
