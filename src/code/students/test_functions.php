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


function get_date_id ($date)
{
  $query = "select id from dates where
            date = '$date'";
  $result = mysql_query($query);
  if ($row = mysql_fetch_row($result))
    return $row[0];
  else
    return 0;
}


function stu_name_ass_r($id)
{
    $query = "select fname,mname,lname from stu_blocks where id = $id";
    $result = mysql_query($query);
//    query_outcome_echo($query,$result);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    return $row;
}


function query_outcome_echo($query,$result,$errors=0)
{
  if($result === FALSE)
  {
    echo "ERROR " . mysql_errno() . ": " . mysql_error() .
          "<br>$query<br>";
    $errors++;
  }
  else echo "SUCCESS:$query<br>";
  return $errors;
}


function get_id_period ($tid, $pword="")
{
  $table = "pwords" . "$tid";
  $query= "select id,period from $table 
           where pword = '$pword'";
  $result = mysql_query($query);
  if($result === FALSE) return 0;
  if (empty($result)) return -2;
  if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    return $row;
  }
  else
    return -1;
}


function makeup_in_scores ($tid,$id="")
{
  $table = "scores" . "$tid";
  $queryString = "select * from $table where id = $id and
         makeup = 'y'";
  $result = mysql_query($queryString);

  if (mysql_num_rows($result) == 0)
    return "no";
  else
  {
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $info = $row['test_id'];
    $info .= "_" . $row['date'];
    return $info;
  }
}


function get_test_id($tid, $period="" )
{
  $table = "today" . "$tid";
  $t_field = "period" . $period;
  $query = "select * from $table";
  $result = mysql_query($query);
  if($result === FALSE) return 0;
  $row = mysql_fetch_object($result);
  $test_id = $row->$t_field;
  return $test_id;
}


function get_test_arr($tid, $test_id=0 )
{
  $table = "tests" . "$tid";
  $query = "select * from $table where test_id = $test_id";
  $result = mysql_query($query);
  if($result === FALSE) return array();
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


function isin_scores_table 
             ($tid, $id="", $test_id="", $date="", $period="")
{
  $table = "scores" . "$tid";
  $queryString = "select makeup from $table where id = $id and
         test_id = $test_id and period='$period' and date = '$date'";

  $result = mysql_query($queryString);
  if($result === FALSE) return "no";
  if (mysql_num_rows($result) == 0)
    return "no";
  else
    return "yes";
}


function array_2_html($arr=array(), $source="", $type)
{
  $sequence = get_questions_sequence($arr);
  $index = 1;
  $source .= "<hr>";
  
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
&nbsp<input type=text size=34 name="$name" onclick="set_global_pos(this)">
EOQ;
       $question = str_replace("|", " | ", $question);
       $question = str_replace("|", $text_field, $question);
       $question .= "<br>" . special_chars($type);
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


function array_print_html($desc = "", $arr = array(), $tid="",
$id="", $test_id="", $period="", $name="", $did=0, $type)
{
$date=get_id_date ($did);
$name = "Date: $date &nbsp &nbsp &nbsp &nbsp Period: $period<br>Name:
$name";
$title = "Test Title: $desc";
title_comment_div_noend($name, $title);
echo "<DIV>";
$source_top = <<<EOQ
<DIV class=student_test_table>
<table><tr><td>
<form name=test method=post action="./test_to_db.php">
<input type=hidden name=tid value="$tid">
<input type=hidden name=id value="$id">
<input type=hidden name=name value="$name">
<input type=hidden name=did value="$did">
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=period value="$period">
EOQ;

$source = array_2_html($arr, $source_top, $type);

$source .= <<<EOQ
<input type=submit name=submit value="I'm done, please compute the grade" 
onClick="return doneConfirm3()">
<br>
</form>
</td></tr></table>
</div>
EOQ;

print $source;
}


function title_comment_div_noend($page_title="", $comment="")
{
if (!empty($comment))
  $comment="<h3>$comment</h3>";
echo "<DIV class=page_title><h2>$page_title</h2>";
echo $comment;
}


function end_html_exit()
{
  echo "</DIV></BODY></HTML>";
  exit(1);
}


function start_html($title)
{
echo <<<EOQ
<html>
<head>
<title> $title </title>
EOQ;
}


function start_body($color="main_background")
{
echo <<<EOQ
</head>
<body class="$color">
EOQ;
}


function escape_single_quotes()
{
  while (list ($key ,$value) = each($_POST))
  {
    $value = str_replace("'", "\'", $value);
    $post_r["$key"] = $value;
  }
}


function get_defaults_r($tid)
{
  $table = "defaults" . "$tid";
  $defaults_r = array();
  $query = "select * from $table";
  $result = mysql_query($query);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function get_id_date ($did)
{
  $query = "select date from dates where
            id = $did";
  $result = mysql_query($query);
  if ($row = mysql_fetch_row($result))
    return $row[0];
  else
    return 0;
}


function is_in_period_attendance($id,$did,$tid,$period)
{
  $table = "attendance" . "$tid";
  $query = "select is_in from $table where
            id=$id and date=$did and period='$period'";
  $result = mysql_query($query);
//query_outcome_echo($query,$result,0);
  if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    return $row['is_in'];
  else
    return FALSE;
}
?>
