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
$heading=$_POST['heading'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;
if ( empty($test_id) || empty($heading))
  { $yes_input = 0;}


function get_quest_and_answers($tid="", $test_id )
{
  $table = "tests" . "$tid";
  $query = "select * from $table where
            test_id = '$test_id'";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  $row = mysql_fetch_object($result);
  $quest_answer_arr["test"] = $row->test;
  $quest_answer_arr["answers"] = $row->answers;
  $quest_answer_arr["description"] = $row->description;
  return $quest_answer_arr;
}

// key_arr hold the correct answers
function correct_test($key_arr=array(),$ans_arr=array(),&$bad_r)
{
  $items = count($key_arr);
  $right = 0;
  $index = 1;

  while (list ($key, $value) = each($key_arr))
  {  
	$name = "v" . $index++;
	$value2 = trim($ans_arr[$name]);
	$value = trim($value);
	if ($value == $value2)
	{
	   $right++;
	}
        else $bad_r["$name"]=1;
  }
$score = $right/$items*100;
//$score = number_format($score,2);

$int_score = (int) $score;
if($int_score < $score) $score = $int_score + 1;
else $score = $int_score;

$ans_str = "answers";
$q_str = "questions";
if ($right == 1) $ans_str = "answer";
if ($items == 1) $q_str = "question";
$test_info = <<<EOQ
You have $right correct $ans_str out of $items $q_str.<br>
Your score is $score %
EOQ;
$score_r[0]=$test_info;
$score_r[1]=$score;
return $score_r;
}


function question_in_key_array( $text_arr = array())
{
  $arr = array();
       
  while (list( , $value) = each ($text_arr))
  {
    $pos = strpos($value, "~");
    if ($pos)
    {
      $question = substr($value, 0, $pos);
      $answers = substr($value, $pos + 1);
      $arr[$question] = $answers;
    }
    else
    {
      $arr[$value] = "";
    }
  }
  return $arr;
}


function array_to_html($questions_arr, $bad_r)
{
  $source = "<hr>";
  $index = 1;
  
  while (list ($key, $value) = each($questions_arr))
  {  
    $pos = strpos($key, "|");
    if ($pos)
    {
      $question = substr($key, $pos + 1);
      $mult_choice_source = "";
      if ($value == "")
      {
        $name = "v" . $index;
        $answer = $_POST[$name];
        $text_field = <<<EOQ
<input type=text size=34 name=$name value="$answer" READONLY>

EOQ;
        $question = str_replace("|", " | ", $question);
        $question = str_replace("|", $text_field, $question);
      }
      else
      {
        $ans_arr = explode("~", $value);
        $count1 = 1;
        while (list ($key1, $value1) = each($ans_arr))
        {
          $name = "v" . $index;
          $answer = $_POST[$name];
          if($answer == $count1)
          {
            $mult_choice_source .= <<<EOQ
<input type="radio" name="$name" value="$count1" checked> $value1 <br>

EOQ;
          }
          else
          {
            $mult_choice_source .= "<b>O</b>  $value1 <br>";
          }
          $count1++;
        }
      }
      if ($bad_r["$name"])
        $source .= <<<EOQ
<b><font color="#ff0000">$index.) $question </font></b><br>
EOQ;
      else
        $source .= <<<EOQ
<b>$index .) $question </b><br>
EOQ;
      $source .= $mult_choice_source;
      $source .= "<br>";
      $index++;
    }
  }
  return $source;
}


function add_submits_html($test_id="", $test_html="")
{
  $source =<<<EOQ
<table width=100%>
<tr><td>
$test_html
</td></tr>
<tr><TD>
<TABLE width=100%>
<TR>
<form name=test method=post 
action="./test_edit.php">
<td>
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Edit this test">
</td>
</form>
<form name=test method=post 
action="./test_find.php">
<TD align=left>
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Set this test for a period">
</TD></form>
</TR>
</TABLE>
</TD></tr>
</table>
EOQ;
  return $source;
}


function show_main_div($source)
{
  echo <<<EOQ
<DIV class=main_table>
$source
</DIV>
EOQ;
}


$tag_title = "gnuschool.org Insert Test Score";

start_html($tag_title);
stylesheet_link("../../style_sheet");
start_body("main_background");


if(!$yes_input)
{
  $comment = "Please access this page using the site menu.";
  title_comment_div_noend("Test review", $comment);
  end_html_exit();
}

if(empty($menu_submit)) if($menu_submit == "Tests")

$menu_r=array();
$menu_r['active_page']= $menu_submit;
main_menu($menu_r);

$all_test_arr = get_quest_and_answers($tid, $test_id);
$quest_arr = explode("&&", $all_test_arr['test']);
$description = $all_test_arr["description"];
$answers_str = $all_test_arr["answers"];
$answers_arr = explode(",", $answers_str);

$bad_r = array();
$score_r = correct_test($answers_arr, $_POST, $bad_r);

$arr = question_in_key_array( $quest_arr );

$comment = "Title: $description";
$title_comment = get_title_comment_login($heading,$comment);
$test_source = array_to_html($arr, $bad_r);
$test_source .= $score_r[0];
$test_plus = add_submits_html($test_id, $test_source);
echo $title_comment;
show_main_div($test_plus);

$source = get_start_html($tag_title);
$source .= get_start_body();
$source .= $title_comment;
$source .= $test_source;
$source .= "</body></html>";

?>
</body></html>
