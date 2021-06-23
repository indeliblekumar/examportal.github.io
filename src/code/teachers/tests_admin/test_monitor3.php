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

$name=$_POST['name'];
$id=$_POST['id'];
$test_id=$_POST['test_id'];
$period=$_POST['period'];
$set_score=$_POST['set_score'];
$reason=$_POST['reason'];

$yes_input = 1;
if(empty($id) || empty($test_id) || empty($period))
  $yes_input = 0;


function get_edit_inputs($id=0,$test_id=0,$name="",$period="")
{
  $edit_form = <<<EOQ

<b>Student's name:</b><br>
<input type=text name=name size=30 value="$name" READONLY><br><br>

<b>Period:</b>
<input type=text name=period size=3 value="$period" READONLY>
<br>

<b>Score given for this test:</b>
<input type=number name=set_score size=3 maxlength=3>
<br>

<br>
<b>Reason for forcing a score:</b>
<br><input type=text name=reason size=50 maxlength=50>
<br>

<input type=hidden name=id value="$id">
<input type=hidden name=test_id value="$test_id">

<input type=submit name=grade_in value="Set student's score">
</TD></TR>
EOQ;
  return $edit_form;
}


function check_scores_table ($tid="",$id="",$test_id="",$date="",$period)
{
  $table = "scores" . "$tid";
  $queryString = "select * from $table where id = $id and
         test_id = $test_id and date = '$date' and period = '$period'";
  $result = mysql_query($queryString);
//  query_outcome_echo($query,$result);

  if (mysql_num_rows($result) == 0)
    return "no";
  else
  {
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    if ( $row['makeup'] == 'y') return "makeup";
    else return "yes";
  }
}


function update_data
 ($tid="",$id=0,$test_id=0,$period="",$score=0,$date="",$reason="")
{
  $table = "scores" . "$tid";
  $queryString="update $table set score='$score', makeup='n' 
            where id = $id and test_id = $test_id and date = '$date'";
  $result = mysql_query($queryString);
//  query_outcome_echo($query,$result);
  $count = in_taken_table($tid, $test_id, $period, $date);
  $table = "taken" . "$tid";
  if ($count == 0)
  {
    $query = "insert into $table (test_id,period,date,count)
                          values ($test_id, '$period', '$date', 1)";
    $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  }
  else
  {
    $count++;
    $query = "update $table set count = $count where 
            test_id=$test_id and date='$date' and period='$period'";
    $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  }
}


function insert_data
   ($tid="",$id=0,$test_id=0,$period="",$score=0,$date="",$reason="")
{
  $table = "forced_score" . "$tid";
  if (!empty($reason))
  {
    $query = "insert into $table (test_id,id,period,date,reason) "
        . "values ($test_id, $id, '$period', '$date', '$reason')";
    $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  }

  $table = "scores" . "$tid";
  $query="insert into $table (date,id,score,test_id,period,makeup)
          values ('$date', $id, $score, $test_id, '$period', 'n')";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  $count = in_taken_table($tid, $test_id, $period, $date);

  $table = "taken" . "$tid";
  if ($count == 0)
  {
    $query = "insert into $table (test_id,period,date,count) "
               . "values ($test_id, '$period', '$date', 1)";
    $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  }
  else
  {
    $count++;
    $query = "update $table set count = $count where
              test_id=$test_id and date='$date' and period='$period'";
    $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  }
}


function in_taken_table ($tid="", $test_id=0, $period="",$date="")
{
  $table = "taken" . "$tid";
  $query = "select count from $table where
            test_id = $test_id and date = '$date' and period ='$period'";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);

  if (mysql_num_rows($result) == 0)
    return 0;
  else
  {
    $row = mysql_fetch_row($result);
    return $row[0];
  }
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


function start_main_div ()
{
  echo '<DIV class=main_table>';
}


function end_main_div ()
{
  echo '</DIV>';
}


function show_form_table ($score_form)
{
  echo startTable("border");
  echo '<form method=post action="./test_monitor3.php">';
  $cell_data = tDataSource("$score_form", "left");
  echo rowPrint($cell_data);
  echo '</form>';
  echo endTable();
}


function show_test_table ($test_arr)
{
  echo startTable("border");
  $description = $test_arr['description'];
  $isbn = $test_arr['isbn'];
  $title = $test_arr['title'];
  $author = $test_arr['author'];
  $page = $test_arr['page'];
  $cell_data = tDataSource("<b>Test Information</b>", "center","",2);
  echo rowPrint($cell_data);
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


function get_test_arr($tid = "", $test_id = 0)
{
  $fields_arr = array();
  $table = "tests" . "$tid";
  $query = "select * from $table where test_id=$test_id";
  $result = mysql_query($query);  
//  query_outcome_echo($query,$result);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $fields_arr["description"]=$row['description'];
  $fields_arr["isbn"]=$row['isbn'];
  $fields_arr["title"]=$row['title'];
  $fields_arr["author"]=$row['author'];
  $fields_arr["page"]=$row['page'];
  return $fields_arr;
}


//  .=46,0=48,9=57 
function is_num($str = "")
{
  $num_r = array();
  $num_r[0] = 1;
  $num_r[1] = 0;
  $length = strlen($str);
  if ($length < 1) $num_r[0] = 0;

  for ($i=0; $i<$length; $i++)
  { 
    $char = substr($str, $i, 1);
    $char_val = ord($char);
    if($char_val == 46)
      $num_r[1] = 1;
    else
      if (!($char_val > 47 && $char_val < 58))
      {
        $num_r[0] = 0;
        $i=$length;
      }
  }
  return $num_r;
}


$tag_title = "gnuschool.org Test Monitor";
$page_title = "Monitor a test";

start_html($tag_title);
stylesheet_link("../../style_sheet");
start_body("main_background");

if (!$yes_input)
{
  $comment = "Please access this page using the site menu.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

$name_r = stu_name_ass_r($id);
$name = $name_r['fname'] . " ";
$name .= $name_r['mname'] . " ";
$name .= $name_r['lname'];

$num_r = is_num($set_score);
$ok_num = $num_r[0];
$ok_dec = $num_r[1];

if(!$ok_num)
{
  $page_title = "Set a test score";
  if(!empty($set_score)) $comment = "Bad input!<br>";
  $comment .= "Please enter the score you wish to give for this test.";
  $comment .= "<br>The score must be between 0 and 100 inclusive.";
  title_comment_div_noend($page_title, $comment);  

  $score_form = get_edit_inputs($id,$test_id,$name,$period);
  $test_arr = get_test_arr($tid, $test_id);
  start_main_div ();
  echo startTable("",10,0);
  echo "<tr><td>";
  show_form_table ($score_form);
  echo "</td><td>";
  show_test_table ($test_arr);
  echo "</td></tr>";
  echo endTable();
  end_main_div();
}
else
{
  if($ok_dec)
  { $int_score = (int) $set_score;
    if($int_score < $set_score) $set_score = $int_score + 1;
    else $set_score = $int_score;
  }
  
  if ($set_score >= 0 && $set_score <= 100 )
  {
    $score_in=check_scores_table($tid,$id,$test_id,$date,$period);

    if ( $score_in == "makeup")
      update_data($tid, $id, $test_id, $period, $set_score, $date);
    else
      insert_data($tid,$id,$test_id,$period,$set_score,$date,$reason);

    $page_title = "Score is set";
    $comment = "$name has a score of $set_score for this test.";
    title_comment_div_noend($page_title, $comment);  
  }
  else
  {
    $page_title = "Score is not set";
    $set_score = "\"$set_score\"";
    $comment = "The score $set_score is not a valid entry.";
    title_comment_div_noend($page_title, $comment);  
  }
}

?>
</body></html>

