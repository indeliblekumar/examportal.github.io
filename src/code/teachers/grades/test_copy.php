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
include ('../search.php');
include ('../login.php');

$id=$_GET['id'];
$test_id=$_GET['test_id'];
$date=$_GET['date'];
$period=$_GET['period'];

$yes_input = 1;
if(empty($id) || empty($test_id) || empty($date))
  $yes_input = 0;


function get_reason($id,$date,$test_id,$tid,$period)
{
  $table = "forced_score" . "$tid";

  $query = "select reason from $table where date='$date' and
     id=$id and test_id=$test_id and period='$period'";
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  return $row[0];
}


function echo_test_copy($id,$date,$test_id,$tid,$period)
{
  $table = "scores" . "$tid";
  $query = "select test from $table where id=$id and
            test_id=$test_id and date='$date' and period='$period'";
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  $test = $row[0];
  if (empty($test))
  {
    $tag_title = "gnuschool.org Test Copy";
    $page_title = "Test copy";
    start_html($tag_title);
    stylesheet_link("../../style_sheet");
    start_body("main_background");

    $comment = "The score for this test was inserted";
    $comment .= " by the instructor.<br>There is no hardcopy.";
    $reason = get_reason($id,$date,$test_id,$tid,$period);
    if(!empty($reason))$comment.="<br>Note:$reason"; 
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }
  else
    echo $test;
}


if ($yes_input)
{
  echo_test_copy($id,$date,$test_id,$tid,$period);
}
else
{
  $tag_title = "gnuschool.org Test Copy";
  $page_title = "Test copy";
  start_html($tag_title);
  stylesheet_link("../../style_sheet");
  start_body("main_background");

  $comment = "Please access this page via the test_copy_search page.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}
?>
