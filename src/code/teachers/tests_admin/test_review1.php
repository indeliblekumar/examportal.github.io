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
$period=$_POST['period'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;
if (empty($period) && empty($test_id)) $yes_input = 0;

function input_form()
{
  echo <<<EOQ
<form method=post action="$PHP_SELF">
<b>Period of test to review:</b><br>
<input type=text name=period size=4>
<br><br>

<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit
value="Review the test set up for the period above">
</form>

<form method=post action="./test_find.php">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Find a test to review">
</form>
EOQ;
}


function get_test_id($tid="", $period="" )
{
  $t_field = "period" . $period;
  $table = "today" . "$tid";
  $query = "select * from $table";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  $row = mysql_fetch_object($result);
  $test_id = $row->$t_field;
  return $test_id;
}


function get_test_arr($tid="", $test_id=0 )
{
  $table = "tests" . "$tid";
  $query = "select * from $table where test_id = $test_id";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  $row = mysql_fetch_object($result);
  $test_arr["test"] = $row->test;
  $test_arr["answers"] = $row->answers;
  $test_arr["description"] = $row->description;
  $test_arr["type"] = $row->type;
  return $test_arr;
}


function get_questions_sequence( $arr = array() )
{
  $new_arr = array();
  while (list ($key, $value) = each($arr))
  {  
     $pos = strpos($key, "|");
     $question_number = substr($key, 0, $pos);
     $new_arr[] = $question_number;
  }
  return $new_arr;
}


function randomize_array( $test_arr )
{
	$arr = array();
        
	while (list( , $value) = each ($test_arr))
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
	$items = count ($arr);
	$new_arr = array();
	$arr_rand_keys = array_rand($arr, $items);
        
      if(is_array($arr_rand_keys))
      {
        shuffle($arr_rand_keys);

	for ( $i=0; $i<$items; $i++)
	{
	  $new_arr[$arr_rand_keys[$i]] = $arr[$arr_rand_keys[$i]];
	}
      }
      else
        $new_arr[$arr_rand_keys] = $arr[$arr_rand_keys];
      return $new_arr;
}


function array_to_html($arr=array(), $source="", $type)
{
  $sequence = get_questions_sequence($arr);
  $index = 1;
  
  while (list ($key, $value) = each($arr))
  {  
     $pos = strpos($key, "|");
     $question = substr($key, $pos + 1);
     $mult_choice_source = "";
     if ($value == "")
     {       
       $quest_num = $sequence[$index - 1];
       $quest_num = trim($quest_num);
       $name = "v" . "$quest_num";
       $text_field = <<<EOQ
\n<input type=text size=34 name="$name" onclick="set_global_pos(this)">
EOQ;
       $question = str_replace("|", " | ", $question);
       $question = str_replace("|", $text_field, $question);
       $special_chars = special_chars($type);
       $question =<<<EOQ
$question <br>
$special_chars
EOQ;
     }
     else
     {
     $ans_arr = explode("~", $value);
     $count1 = 1;
     while (list ($key1, $value1) = each($ans_arr))
     {
       $quest_num = $sequence[$index - 1];
       $quest_num = trim($quest_num);
       $name = "v" . "$quest_num";
       $mult_choice_source .= <<<EOQ
<input type="radio" name='$name' value="$count1">$value1<br>
EOQ;
       $count1++;
     }
     }
     $source .= <<<EOQ
<b>$index .) $question </b><br>
EOQ;
     $source .= $mult_choice_source;
     $source .= "<br>";
     $index++;
  }
return $source;
}


function array_print_html($desc = "", $arr,$test_id="", $heading="", $type)
{
$title = "Title: $desc";
title_comment_div_noend($heading, $title);
$source_top = <<<EOQ
<hr><table width=100%><tr>
<form name=test method=post action="./test_review2.php">
<td>
<input type=hidden name=menu_submit value="Tests">
<input type=hidden name=heading value="$heading">
<input type=hidden name=test_id value="$test_id">\n
EOQ;

$source = array_to_html($arr, $source_top, $type);

$source .= <<<EOQ
<input type=submit name=submit value="I'm done, please compute the grade" 
onClick="return doneConfirm3()"><br></td></form></tr>
<tr><TD><TABLE width=100%><TR>
<form name=test method=post 
action="./test_edit.php">
<td><input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Edit this test">
</td></form>
<form name=test method=post 
action="./test_find.php">
<TD align=left><input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Set this test for a period">
</TD></form></TR></TABLE></TD></tr></table>
</DIV>
EOQ;

print $source;
}

$tag_title = "gnuschool.org Assessment";

start_html($tag_title);
stylesheet_link("../../style_sheet");

if($yes_input)
{
  echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

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


var inputA=null;

function set_global_pos(myobject)
{
  if(myobject) inputA = myobject;
}


function doneConfirm2()
{
  var ok=confirm("Did you save your changes?");
  if (ok) return true;
  else return false;
}


function addText( insText )
{
  if (inputA)
  {
    if (inputA.type == "textarea" || inputA.type == "text")
    {
      inputA.focus();
      if( inputA.createTextRange )
      {
        document.selection.createRange().text += insText;
      }
      else if( inputA.setSelectionRange )
      {
        var len = inputA.selectionEnd;
        inputA.value=inputA.value.substr(0,len) + insText +
        inputA.value.substr(len);
        inputA.setSelectionRange(len+insText.length,len+insText.length);
      } 
      else
      {
        inputA.value += insText;
      }
    }
  }
}
</script>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in your browser.</p>
</noscript>
EOQ;
}

start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Tests";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
main_menu($menu_r);

$page_title = "Assessment Review";
if (!$yes_input)
{
  $comment = "Please fill the search fields as needed.";
  title_comment_div_noend($page_title, $comment);
  input_form();
  end_html_exit();
}

$name = "Test verification";

$heading = "Database test review.";
if(empty($test_id))
{
  $heading = "Period $period test review.";
  $test_id = get_test_id($tid, $period);
}
$all_test_arr = get_test_arr($tid, $test_id);
$desc = $all_test_arr["description"];
$type = $all_test_arr["type"];
$quest_str = str_replace("__________",
                           " __________ ", $all_test_arr['test']);
$quest_str = str_replace("\n", "<br>", $quest_str);
$quest_arr = explode("&&", $quest_str);
$arr = randomize_array($quest_arr);

array_print_html($desc,$arr,$test_id,$heading,$type);
img_div();
?>
</body></html>
