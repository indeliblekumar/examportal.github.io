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

/*
submit1 value="Save the changes"
submit2 value="Add a new fill in question"
submit3 value="Add a new multiple choice question"
*/


$lines_total=$_POST['lines_total'];
$submit=$_POST['submit'];
$submit1=$_POST['submit1'];
$submit2=$_POST['submit2'];
$submit3=$_POST['submit3'];
$create_new=$_POST['create_new'];
$menu_submit=$_POST['menu_submit'];

$title=$_POST['title'];

$submit = "";
if(!empty($submit1)) $submit = "save";
elseif(!empty($submit2)) $submit = "fill";
elseif(!empty($submit3)) $submit = "mult";

$yes_input = 1;

if(empty($menu_submit) && empty($submit) && empty($create_new)) $yes_input = 0;

function answers_to_db($tid,$lines_total=0,
                                 $submit="", $blank_mults= array())
{
  $where_str = get_where_str();
  $answers = array();
  for ( $i=1; $i < $lines_total + 1; $i++)
  {
    $checkbox_name = "v" . $i;
    $checkbox_value= $_POST["$checkbox_name"];
    if (empty($checkbox_value))
    {
      $part_b_name = $checkbox_name . "b"; 
      if (is_array($_POST["$part_b_name"]))
      {
        $current_size = sizeof($_POST["$part_b_name"]);
        $mult_radio_name = $checkbox_name . "radio"; 
        if (empty($_POST["$mult_radio_name"])) $answers[] = "1";
        else
        {
          $blanks_arr = explode(",", $blank_mults["$part_b_name"]);
          $radio_num = (int)($_POST["$mult_radio_name"]);  
          for ($j=($radio_num - 1); $j > -1; $j--)
          {
            if ($blanks_arr[$j] == "1") 
              $radio_num--;
          }
        }
        $answers[]="$radio_num";
      }
      else
      {
        $answer = $_POST["$part_b_name"];
        $answers[] = trim($answer);
      }
    }
  }
  if ($submit == "fill") $answers[] = "edit";
  elseif ($submit == "mult") $answers[] = "1";

  $answers_str = implode(",", $answers);
  $answers_str = stripslashes($answers_str);
  $answers_str = addslashes($answers_str);
  $table = "tests" . "$tid";
  $query = "update $table set answers = '$answers_str'
$where_str";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
}


function lines_to_db_write($tid, $lines_total=0,$submit="")
{
  $blank_mults = array();

  $where_str = get_where_str();
  $line_num = 1;
  for ( $i=1; $i < $lines_total+1; $i++)
  {
    $checkbox_name = "v" . $i;
    $checkbox_value=$_POST["$checkbox_name"];
    if (empty($checkbox_value))
    {         
      $part_a = trim($_POST["$checkbox_name" . "a"]," ");
      $line = "$line_num" . "|" . "$part_a";
      $part_b_name = $checkbox_name . "b"; 
      if (is_array($_POST["$part_b_name"]))
      {
        $line .= "__________";
        $part_c = trim($_POST["$checkbox_name" . "c"], " ");
        $line .= $part_c;
        $part_b_arr = $_POST["$part_b_name"];
        for ( $j=0; $j<sizeof($part_b_arr); $j++)
        {
          $value = $part_b_arr[$j]; $value = trim($value, " ");
          if($value == "")
            $blank_mults["$part_b_name"] .= "1,"; 
          else
          {            
            $blank_mults["$part_b_name"] .= "0,"; 
            $line .= "~" . $value;
          }
        }
        if (sizeof($blank_mults) > 0)
        $blank_mults["$part_b_name"] =
                substr($blank_mults["$part_b_name"], 0, -1);
        $more_choices = $part_b_name . "_more";
        if ($_POST["$more_choices"] == "more")
          $line .=  "~ ~ ~ ~ ~ ";
      }
      else
      {
        $part_c = trim($_POST["$checkbox_name" . "c"], " ");
        $line .= "|" . $part_c;
      }
      $text_str .= $line . "&&";
      $line_num++;
    }
  }
  if ($submit == "fill")
  {
    $line = "$line_num" . "|edit|edit.";
    $text_str .= $line . "&&";
  }
  elseif ($submit == "mult")
  {
    $edit_choices =  "~edit~edit~ ~ ~ ~ ~ ~ ~ ~ ";
    $line = "$line_num" . "|edit__________edit." . $edit_choices;
    $text_str .= $line . "&&";
  }

  $text_str = substr($text_str, 0, -2);
  $text_str = stripslashes($text_str);
  $text_str = addslashes($text_str);
  
  $table = "tests" . "$tid";
  $query = "update $table set test='$text_str' $where_str";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  return $blank_mults;
}


function new_lines_to_db_write($tid)
{
  $description = $_POST["description"];
  $description = stripslashes($description);
  $description = addslashes($description);
  $type = $_POST["type"];
  $isbn = $_POST["isbn"];
  $title = $_POST["title"];
  $title = stripslashes($title);
  $title = addslashes($title);
  $author = $_POST["author"];
  $author = stripslashes($author);
  $author = addslashes($author);
  $page = $_POST["page"];
  $question_lines = $_POST["lines_total"];
  $lines_approx = $_POST["lines_approx"];

  if( empty($description) && empty($isbn) && empty($title) 
                      && empty($author) && empty($page) ) exit(1);

  if(empty($question_lines))$question_lines=$lines_approx;

  $line_num = 1;
  $answers1 = "edit";
  $middle_num = (int)($question_lines/2);
  for ($i=1; $i< $middle_num; $i++)
  {
    $answers1 .= ",edit";
  }
  $answers2 = "";
  for ($i=$middle_num; $i< $question_lines; $i++)
  {
    $answers2 .= ",1";
  }
  $answers = $answers1 . $answers2;
  $answers = stripslashes($answers);
  $answers = addslashes($answers);

  for ( $i=0; $i < $middle_num; $i++)
  {
    $line = "$line_num" . "|" . "edit" . "|" . "edit";
    $text_str .= "$line" . "&&";
    $line_num++;
  }
  for ( $i=$middle_num; $i < $question_lines; $i++)
  {
    $edit_choices =  "~edit~edit~ ~ ~ ~ ~ ~ ";
    $line = "$line_num" . "|edit__________edit." . $edit_choices;
    $text_str .= $line . "&&";
    $line_num++;
  }
  $text_str = substr($text_str, 0, -2);
  $text_str = stripslashes($text_str);
  $text_str = addslashes($text_str);
  

  $table = "tests" . "$tid";
  $query = "insert into $table (description, type, answers, isbn,
title, author, page, test) values ( '$description', '$type', '$answers',
'$isbn', '$title', '$author', '$page', '$text_str')";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);

  $test_id = mysql_insert_id();
  return $test_id;
}


function test_info_to_db_write($tid)
{
  $test_id = $_POST["test_id"];
  $description = $_POST["description"];
  $description = stripslashes($description);
  $description = addslashes($description);
  $type = $_POST["type"];
  $isbn = $_POST["isbn"];
  $title = $_POST["title"];
  $title = stripslashes($title);
  $title = addslashes($title);
  $author = $_POST["author"];
  $author = stripslashes($author);
  $author = addslashes($author);
  $page = $_POST["page"];

  $table = "tests" . "$tid";
  $query = "update $table set description='$description',
            type='$type', isbn='$isbn', title='$title', 
            author='$author', page='$page' where test_id=$test_id";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
}


function get_where_str()
{
  $description=$_POST['description'];
  $isbn=$_POST['isbn'];
  $title=$_POST['title'];
  $author=$_POST['author'];
  $page=$_POST['page'];
  $test_id=$_POST['test_id'];

  $and_flag = 0;
  $query = " where ";
  if (!empty($test_id))
  {
    $query .= "test_id = $test_id";
  }
  else
  {
  if (!empty($description))
  {
    if($and_flag)
      $query .= "and description = '$description' ";
    else
      $query .= "description = '$description' ";
    $and_flag = 1;
  }
  if (!empty($isbn))
  {
    if($and_flag)
      $query .= "and isbn = '$isbn' ";
    else
      $query .= "isbn = '$isbn' ";
    $and_flag = 1;
  }
  if (!empty($title))
  {
    if($and_flag)
      $query .= "and title = '$title' ";
    else
      $query .= "title = '$title' ";
    $and_flag = 1;
  }
  if (!empty($author))
  {
    if($and_flag)
      $query .= "and author = '$author' ";
    else
      $query .= "author = '$author' ";
    $and_flag = 1;
  }
  if (!empty($page))
  {
    if($and_flag)
      $query .= "and page = '$page' ";
    else
      $query .= "page = '$page' ";
    $and_flag = 1;
  }
  }
  return $query;
}


function get_test_arr($tid="", $where_query )
{
  $test_r = array();
  $table = "tests" . "$tid";
  $query = "select * from $table $where_query";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);

  $row = mysql_fetch_object($result);
  $test_r["description"] = $row->description;
  $test_r["isbn"] = $row->isbn;
  $test_r["author"] = $row->author;
  $test_r["title"] = $row->title;
  $test_r["page"] = $row->page;
  $test_r["type"] = $row->type;
  $test_r["test"] = $row->test;
  $test_r["answers"] = $row->answers;
  return $test_r;
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


function start_table1_html()
{
  $question = <<<EOQ
<TR><TD colspan=2>
<TABLE cellspacing=0 cellpadding=0 style="border:2px solid black;"
width="100%">
EOQ;
  return $question;
}


function start_table2_html()
{
   $question = <<<EOQ
<TR><TD colspan=2>&nbsp;</TD></TR><TR><TD colspan=2>
<TABLE cellspacing=0 cellpadding=0 style="border:2px solid black;"
width="100%">
EOQ;
  return $question;
}


function sub_table1_html()
{
       $question = <<<EOQ
<TR><TD colspan=2>
<TABLE cellspacing=0 cellpadding=0 width="100%">
<TR><TD>
<TABLE cellspacing=0 cellpadding=0 width="100%">
EOQ;
  return $question;
}


function sub_table2_html()
{
       $question = <<<EOQ
</TABLE></TD><TD>
<TABLE cellspacing=0 cellpadding=0 width="100%">
EOQ;
  return $question;
}


function sub_tables_end()
{
       $question = <<<EOQ
</TABLE></TD>
</TR></TABLE></TD></TR>
EOQ;
  return $question;
}


// $index= question number
// $name= checkbox name and value
// $id_names= 3 element array v1a,v1b,v1c or v2a,v2b,v2c etc
// $answers= 1 if fill-in, if multiple choice then # of answers
function checkbox_html($index,$name,$id_names=array(),$answers=1,
$more_radios="")
{
  $id_name_b = $id_names[1];
  $id_names_str = "";
  if ($answers > 1)
    for ($i=1; $i<$answers+1; $i++)
    {
      $id_answers_str .= $id_name_b . $i . "_";     
    }
  else $id_answers_str = $id_name_b . "_";  
  $id_all_str = "$id_names[0]_" . $id_answers_str . $id_names[2]; 
  $size = $answers + 2;
  $question =<<<EOQ
<TR align=center>
<TD><b>Number $index </b></TD>
<TD>Delete This Item:
<input type=checkbox name=$name value=$name 
onclick="set_bgcolor(this,'$id_all_str', $size)">&nbsp $more_radios
</TD></TR>
EOQ;
  return $question;
}


function simple_checkbox_html($name)
{
  $question =<<<EOQ
<TR><TD align=center>Add more choices</TD></TR>
<TR><TD align=center>
<input type=checkbox name=$name value="more"></TD></TR>
EOQ;
  return $question;
}


function accent_buttons_html($type)
{
  $buttons = special_chars($type);
  if ($buttons == "")
    $buttons = "&nbsp";
  $question =<<<EOQ
<TR><TD>&nbsp</TD><TD> $buttons </TD></TR>
EOQ;
  return $question;
}


function textarea_html($name1,$question_part="first or last",
$part="Part AorBorC")
{
  $question =<<<EOQ
<TR><TD align=right>$part</TD><TD>
<textarea id=$name1 name="$name1" cols=70 rows=3 onclick="set_global_pos(this)">
$question_part</textarea></TD></TR>
EOQ;
  return $question;
}


// $input_name=v1b v2b etc, name of input, brackets are added if mult
// $id_name=v1b for fill-in or v1b1 v1b2 etc for radio, for unique id
// $value= value of input whether it's multiple choice or not
// $brackets="[]" or "" tells function if it's multiple choice or not,
// $index = question number, to create radio_name="v".$index."radio" 
// $choice_num=radio number, 1 for first 2=2nd etc, radio's value
// $answer_index=answer number to set radio $checked="" or ="checked"
function input_html($input_name, $id_name, $value, $brackets="",
                               $index, $choice_num, $answer_index)
{
  $radio_html = "";
  $input_name .= $brackets;
  $input_label="";
  if ($brackets != "")
  {
    $input_label = "Choice $choice_num";
    $radio_name = "v" . $index . "radio";
    $checked = "";
    if ($choice_num == trim($answer_index)) $checked = "checked";
    $radio_html =<<<EOQ
<input type=radio name="$radio_name" value="$choice_num" $checked>
This is the answer.
EOQ;
  }
  else
  {
    $input_label = "Fill in";
  }
  $input_item =<<<EOQ
<TR>
<TD align=right>$input_label &nbsp</TD>
<TD><input id="$id_name" type=text size=40 name="$input_name"
value="$value" onclick="set_global_pos(this)">$radio_html</td>
</TR>
EOQ;
  return $input_item;
}


function array_to_html($arr=array(), $answer_arr=array(), $type)
{
  $source = "";
  $index = 1;

  while (list ($key, $value) = each($arr))
  {  
    $pos = strpos($key, "|");
    $question = substr($key, $pos + 1);
    $mult_choice_source = "";
    if ($value == "")
    {
      $pos1 = strpos($question, "|");
      $question_part1 = substr($question, 0, $pos1);
      $question_part1 = trim($question_part1, " ");
      $answer = $answer_arr[$index -1];
      $answer = trim($answer);
      $question_part2 = substr($question, $pos1 + 1);
      $question_part2 = trim($question_part2, " ");
      $name = "v" . $index;
      $id_names[0] = "v" . $index . "a";
      $id_names[1] = "v" . $index . "b";
      $id_names[2] = "v" . $index . "c";
      if ($index== 1)
        $question = start_table1_html();
      else
        $question = start_table2_html();
      $question .= checkbox_html($index,$name,$id_names,1);
      $question .= accent_buttons_html($type);
      $question .= textarea_html($id_names[0],$question_part1,"Part A");
      $question.=input_html($id_names[1],$id_names[1],$answer,"",0,0,"");
      $question .= textarea_html($id_names[2],$question_part2,"Part B");
      $question .= "</TABLE></TD></TR>";
    }
    else
    {
      $answer_index = $answer_arr[$index -1];
      $choice_ans_arr = explode("~", $value);
      $pos1 = strpos($question, "_");
      $pos2 = strrpos($question, "_");
      if($pos1 == 0)
      {
        $question_part1 = $question;
        $question_part1 = trim($question_part1, " ");
        $question_part2 = "";
      }
      else
      {
        $question_part1 = substr($question, 0, $pos1);
        $question_part1 = trim($question_part1, " ");
        $question_part2 = substr($question, $pos2 + 1);
        $question_part2 = trim($question_part2, " ");
      }
      $name = "v" . $index;
      $id_names[0] = "v" . $index . "a";
      $id_names[1] = "v" . $index . "b";
      $id_names[2] = "v" . $index . "c";
      if ($index== 1)
        $question = start_table1_html();
      else
        $question = start_table2_html();
      $question .= checkbox_html($index,$name,$id_names,
        sizeof($choice_ans_arr), $more_checkbox);
      $question .= accent_buttons_html($type);
      $question .= textarea_html($id_names[0],$question_part1 ,"Part A");      
      $question .= sub_table1_html();
      $count1 = 1;
      $input_name = $id_names[1];
      while (list ($key1, $value1) = each($choice_ans_arr))
      {
        $id_name = $input_name . $count1;
        $question .= input_html($input_name, $id_name, $value1, "[]",
                                $index, $count1, $answer_index);
	$count1++;
      }
      $question .= sub_table2_html();
      $more_radios = $id_names[1] . "_more";
      $question .= simple_checkbox_html($more_radios);
      $question .= sub_tables_end();

      $question .= textarea_html($id_names[2],$question_part2,"Part B");
      $question .= "</TABLE></TD></TR>";
    }
    $source .= $question;
    $source .= $mult_choice_source;
    $index++;
  }  
  return $source;
}


function test_info_inputs($test_r)
{
  $description = $test_r["description"];
  $type = $test_r["type"];
  $isbn = $test_r["isbn"];
  $title = $test_r["title"];
  $author = $test_r["author"];
  $page = $test_r["page"];
  $type_arr['Art'] = 0;
  $type_arr['English'] = 0;
  $type_arr['Geography'] = 0;
  $type_arr['History'] = 0;
  $type_arr['Mathematics'] = 0;
  $type_arr['Science'] = 0;
  $type_arr['Social Studies'] = 0;
  $type_arr['French'] = 0;
  $type_arr['German'] = 0;
  $type_arr['Italian'] = 0;
  $type_arr['Spanish'] = 0;
  $select_html = "<SELECT name=type><OPTION> </OPTION>";
  $flag = "";
  while (list($key,) = each($type_arr))
  {
    if ($type == $key) $flag = " SELECTED>";
    else $flag = ">";
    $select_html .= "<OPTION" . $flag . $key . "</OPTION>";
  }
  $select_html .= "</SELECT>";

  $accent_buttons = special_chars($type);

  $test_info = <<<EOQ
<TABLE width="100%" cellspacing=4 cellpadding=4>
<TR align=left><TD>
$accent_buttons <br>
<b>Test Description:</b><br>
<textarea name="description" cols=80 rows=3 maxlength=255 
onclick="set_global_pos(this)">$description</textarea>
</TD></TR>
<TR align=left><TD>
<b>Subject:</b><br>
$select_html
</TD></TR>
<TR align=left><TD>
$accent_buttons <br>
<b>Book Title:</b><br>
<textarea name="title" cols=80 rows=3 maxlength=125
onclick="set_global_pos(this)">$title</textarea>
</TD></TR>
<TR align=left><TD>
<b>Page number:</b><br>
<input type=text size=6 name=page value="$page" maxlength=10>
</TD></TR>
<TR align=left><TD>
<b>Author:</b><br>
<input type=text size=115 name=author value="$author" maxlength=125>
</TD></TR>
<TR align=left><TD>
<b>ISBN number:</b><br>
<input type=text size=16 name=isbn value="$isbn" maxlength=10>
</TD></TR>
</TABLE>
EOQ;
  return $test_info;
}


function form_top_bottom_add($html_questions="", $lines_total = 0, $test_info="")
{
  $test_id = $_POST["test_id"];
  $description = $_POST["description"];
  $type = $_POST["type"];
  $isbn = $_POST["isbn"];
  $title = $_POST["title"];
  $author = $_POST["author"];
  $page = $_POST["page"];
  $answers = "edit";
  
  echo <<<EOQ
<DIV class=main_table>
<TABLE width="100%">
<TR align=left>
<form name=test method=post action="$PHP_SELF">
<TD>
<TABLE width="100%">
<TR align=left><TD colspan=2>
$test_info
</TD></TR>
<TR align=left><TD colspan=2>
$html_questions
</TD></TR>
<TR><TD align=left>
<input type=submit name=submit1 value="Save the changes">
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=lines_total value="$lines_total">
<input type=hidden name=menu_submit value="Tests">
</TD><TD align=right>
<input type=submit name=submit3 value="Add a new multiple choice question">
</TD></TR>
<TR><TD>&nbsp
</TD><TD align=right>
<input type=submit name=submit2 value="Add a new fill in question">
</TD></TR>
</TABLE>
</TD>
</form>
</TR>
<TR align="center">
<TD>
<TABLE width="100%">
<TR align=center>
<form name=test method=post 
action="./test_find.php">
<TD align=left>
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Set this test for a period"
onClick="return doneConfirm2();">
</TD></form>
<form name=test method=post 
action="./test_review1.php">
<TD align=right>
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Review this test"
onClick="return doneConfirm2();">
</TD></form></TR>
</TABLE>
</TD>
</TR>
</TABLE>
</div>
EOQ;

}


$tag_title = "gnuschool.org Edit Test";

start_html($tag_title);
stylesheet_link("../../style_sheet");

if ($yes_input)
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

$page_title = "Edit test";

if(!$yes_input)
{
  $comment = "There was a problem please contact your administrator.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

title_comment_div_noend($page_title);

if(!empty($create_new))
{
  $test_id = new_lines_to_db_write($tid);  
  $_POST['test_id'] = $test_id;
}

if (!empty($lines_total)) 
{
  $blank_mults_arr = lines_to_db_write($tid,$lines_total,$submit);
  $answers_str = answers_to_db($tid,$lines_total, $submit,$blank_mults_arr);
  test_info_to_db_write($tid);
}

$where_query = get_where_str();
$test_r = get_test_arr($tid, $where_query);
$type = $test_r['type'];

$questions_arr = explode("&&", $test_r["test"]);
$answers_arr = explode(",", $test_r["answers"]);
$lines_total = sizeof($questions_arr);

$arr = question_in_key_array( $questions_arr );
$test_contents_html = array_to_html($arr, $answers_arr, $type);
$test_info = test_info_inputs($test_r);

form_top_bottom_add($test_contents_html, $lines_total,$test_info);
img_div();
echo "</DIV>";
?>

</body></html>

