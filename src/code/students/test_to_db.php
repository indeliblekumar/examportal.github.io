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
include('../../up/up.php');
include('./test_functions.php');
include('./test_type.php');

$id=$_POST['id'];
$test_id=$_POST['test_id'];
$tid=$_POST['tid'];
$name=$_POST['name'];
$period=$_POST['period'];
$did=$_POST['did'];


if (empty($id) || empty($test_id) || empty($tid))
{
  $comment = "Access denied.";
  title_comment_login("", $comment);
  end_html_exit();
}


function get_quest_and_answers($tid, $test_id )
{
  $table = "tests" . "$tid";
  $query = "select * from $table where
            test_id = '$test_id'";
  $result = mysql_query($query);
  $row = mysql_fetch_object($result);
  $quest_answer_arr["test"] = $row->test;
  $quest_answer_arr["answers"] = $row->answers;
  $quest_answer_arr["description"] = $row->description;
  return $quest_answer_arr;
}


// key_arr holds the correct answers
function correct_test($key_arr=array(),&$bad_r)
{
  $items = count($key_arr);
  $right = 0;
  $index = 1;

  while (list ($key, $value) = each($key_arr))
  {  
	$name = "v" . $index++;
	$value2 = trim($_POST[$name]);
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
      $name = "v" . $index;
      $question = substr($key, $pos + 1);
      $mult_choice_source = "";
      if ($value == "")
      {
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
          $answer = $_POST[$name];
          if($answer == $count1)
          {
            $mult_choice_source .= <<<EOQ
<input type="radio" name="$name" value="$count1" checked> $value1 <br>
EOQ;
          }
          else
          {
            $mult_choice_source .= "<b>O</b> $value1<br>";
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
<b> $index .) $question </b><br>
EOQ;
      $source .= $mult_choice_source;
      $source .= "<br>";
      $index++;
    }
  }
  return $source;
}


function check_scores_table
            ($tid,$id="",$test_id="",$did="",$period="")
{
  $table = "scores" . "$tid";
  $query = "select * from $table where id = $id and
         test_id = $test_id and period='$period' and date = $did";
  $result = mysql_query($query);

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
  ($tid,$id=0,$test_id=0,$period="",$score=0,$did="",$test="")
{
  $table = "scores" . "$tid";
  $queryString="update $table set score='$score', makeup='n',  
    test='$test' where id=$id and test_id=$test_id and date=$did
    and period='$period'";
  mysql_query($queryString);
  $count = in_taken_table($tid, $test_id, $period, $did);
  $table = "taken" . "$tid";
  if ($count == 0)
  {
    $query = "insert into $table (test_id,period,date,count)
                          values ($test_id, '$period', $did, 1)";
    mysql_query($query);
  }
  else
  {
    $count++;
    $query = "update $table set count = $count where 
            test_id=$test_id and date=$did and period='$period'";
    mysql_query($query);
  }
}


function insert_data
  ($tid,$id=0,$test_id=0,$period="",$score=0,$did="",$test="")
{
  $table = "scores" . "$tid";  
  $query="insert into $table
                            (date,id,score,test_id,period,makeup,test)
          values ($did,$id,$score,$test_id,'$period','n','$test')";
  mysql_query($query);
  $count = in_taken_table($tid, $test_id, $period, $did);
  $table = "taken" . "$tid";
  if ($count == 0)
  {
    $query = "insert into $table (test_id,period,date,count) "
               . "values ($test_id, '$period', $did, 1)";
    mysql_query($query);
  }
  else
  {
    $count++;
    $query = "update $table set count = $count where
              test_id=$test_id and date=$did and period='$period'";
    mysql_query($query);
  }
}


function in_taken_table ($tid, $test_id=0, $period="",$did="")
{
  $table = "taken" . "$tid";
  $query = "select count from $table where
            test_id = $test_id and date = $did and period ='$period'";
  $result = mysql_query($query);

  if (mysql_num_rows($result) == 0)
    return 0;
  else
  {
    $row = mysql_fetch_row($result);
    return $row[0];
  }
}


function in_copy_table($tid,$id=0,$test_id=0,$period="",$did="")
{
  $table = "test_copy" . "$tid";
  $query = "select * from $table where id=$id and
            test_id = $test_id and date = $did and period ='$period'";
  $result = mysql_query($query);

  if (mysql_num_rows($result) == 0)
    return 0;
  else
    return 1;
}


function add_table_html($test_html="")
{
  $source =<<<EOQ
<table width=100%>
<tr><td>
$test_html
</td></tr>
</table>
EOQ;
  return $source;
}


function show_main_div($source)
{
  echo <<<EOQ
<DIV class=student_test_table>
$source
</DIV>
EOQ;
}


function html_top_bottom_add($source="")
{
  $top = "<html><head></head><body>";
  $bottom = "</body></html>";
  $source = $top . "$source";
  $source .= $bottom;
  return $source;
}


function title_comment_login($page_title="", $comment="", $login="")
{
if (!empty($comment))
  $comment="<h3>$comment</h3>";
echo <<<EOQ
<DIV class=page_title>
<h2>$page_title</h2>
$comment
$login
EOQ;
}


function get_title_comment_login($page_title="", $comment="", $login="")
{
if (!empty($comment))
  $comment="<h3>$comment</h3>";
$source = <<<EOQ
<DIV class=page_title>
<h2>$page_title</h2>
$comment
$login
</DIV>
EOQ;
  return $source;
}


function get_start_html($title)
{
  $start_html =<<<EOQ
<html>
<head>
<title> $title </title>
EOQ;
  return $start_html;
}


function get_start_body($color="main_background")
{
  $start_body =<<<EOQ
</head>
<body class="$color">
EOQ;
  return $start_body;
}


$tag_title = "gnuschool.org Assessment grade";
start_html($tag_title);

$style =<<<EOQ
<style type="text/css">

DIV.page_title {
  position: absolute;
  top: 50;
  left: 10;
}

DIV.main_table {
  position: absolute;
  top: 120;
  left: 10;
}

DIV.student_test_table {
  position: absolute;
  top: 160;
  left: 10;
}

.main_background {
  background: rgb(200,200,255)
}
</style>
EOQ;
echo $style;

?>

<?php
start_body();

$page_title = "Assessment";

$mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);

if($mylink === FALSE)
{
  $comment = "Failure to connect to MySQL server with correct 
             user name and password. Please see your system 
             administrator.";
  title_comment_login("", $comment);
  end_html_exit();
}  

mysql_select_db($mdb,$mylink);

if($mylink === FALSE)
{
  $comment = "Failure to connect to MySQL server.";
  title_comment_login("", $comment);
  end_html_exit();
}  

$all_test_arr = get_quest_and_answers($tid, $test_id);
$quest_arr = explode("&&", $all_test_arr['test']);
$description = $all_test_arr["description"];
$answers_str = $all_test_arr["answers"];
$answers_arr = explode(",", $answers_str);

$score_in = check_scores_table ($tid,$id,$test_id,$did,$period);
if ( $score_in == "yes" )
{
  $comment = "You already have a grade for this test.";
  title_comment_login($page_title, $comment);
  echo "</DIV>";
}
else
{
  $bad_r = array();
  $score_r = correct_test($answers_arr, $bad_r);
  $score = $score_r[1];

  $arr = question_in_key_array( $quest_arr );
  $page_title = "$name";
  $comment = "Test Title: $description";
  $title_comment = get_title_comment_login($page_title,$comment);
  $test_source = array_to_html($arr, $bad_r);
  $test_source .= $score_r[0];
  $test_html = add_table_html($test_source);
  echo $title_comment;
  show_main_div($test_html);

  $source = get_start_html($tag_title);
  $source .= $style;
  $source .= get_start_body();
  $source .= $title_comment;
  $source .= "<div class=student_test_table>";
  $source .= $test_source;
  $source .= "</div></body></html>";

  if ( $score_in == "makeup")
    update_data($tid, $id, $test_id, $period, $score, $did, $source);
  else
    insert_data($tid, $id, $test_id, $period, $score, $did, $source);
}
?>
</body></html>
