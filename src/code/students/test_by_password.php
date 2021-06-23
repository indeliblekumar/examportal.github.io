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

$pword=$_GET['pword'];
$t_name=$_GET['t_name'];

$yes_input=1;
if ( empty($pword) || empty($t_name)) $yes_input=0;


$tag_title = "gnuschool.org Assessment";
start_html($tag_title);

echo <<<EOQ
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


if ($yes_input)
{
echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

var inputA=null;


function set_global_pos(myobject)
{ if(myobject) inputA = myobject; }


function doneConfirm3() {
var ok=confirm("Are you done with Test?")
if (ok) return true; else return false;
}


function addText( insText )
{
if (inputA)
{
if (inputA.type == "textarea" || inputA.type == "text")
{
 inputA.focus();
 if( inputA.createTextRange ) {
   document.selection.createRange().text += insText;
 } else if( inputA.setSelectionRange ) {
   var len = inputA.selectionEnd;
   inputA.value=inputA.value.substr(0,len) + insText +
inputA.value.substr(len);
   inputA.setSelectionRange(len+insText.length,len+insText.length);
 } else { inputA.value += insText; }
}
}
}
</script>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in you browser.</p>
</noscript>
EOQ;
}

start_body();

$page_title = "Assessment";

if (!$yes_input)
{
  $comment = "Please access this page using the home page.";
  title_comment_div_noend($page_title, $comment);
}
else
{

  // access mysql server where the school database is
  $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);

  if($mylink === FALSE)
  {
    $comment = "Failure to connect to MySQL server with correct 
               user name and password. Please see your system 
               administrator.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }  

  mysql_select_db($mdb,$mylink);

  $query="select tid from teachers where t_name = '$t_name'";
  $result = mysql_query($query);
//  $errors = 0;
//  $errors = query_outcome_echo($query,$result,$errors);

  if($result === FALSE)
  {
    mysql_close($mylink);
    $comment = "There was an ERROR in the SQL query.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }

  if (mysql_num_rows($result) == 0)
  {
    mysql_close($mylink);
    $comment = "Bad user name or password.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }

  $row = mysql_fetch_row($result);
  $tid = $row[0];

  $def_r = get_defaults_r($tid);
  $is_locked = $def_r['test_lock'];

  if ($is_locked == 'y')
  {
    $comment = "The database is LOCKED. 
               Students will not be able to start assessments
               at this time. Please close this
               window and inform your instructor.";

    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }

  $test_did = get_date_id ($date);
  $id_r = get_id_period ($tid, $pword);
  $id = $id_r['id'];
  $period = $id_r['period'];

  $test_id = 0;
  $score_in = "";

  if($id == -1)
  {
    $comment = "Your password was incorrect. Please close this
                window and try again.";
    title_comment_div_noend($page_title, $comment);
  }
  elseif($id == -2)
  {
    $comment = "The database is locked, please wait for your 
                instructor to open it.";
    title_comment_div_noend($page_title, $comment);
  }
  else
  {
    $n_r = stu_name_ass_r($id);
    $name = $n_r['fname'] . " ";
    $name .= $n_r['mname'] . " ";
    $name .= $n_r['lname'];
  
    $makeup_testid_date = makeup_in_scores ($tid,$id);
    if ($makeup_testid_date != "no")
    {
      $info_r = explode("_", $makeup_testid_date);
      $test_id = $info_r[0];
      $test_did = $info_r[1];
      $score_in = "no";
    }
    else
    {
      $is_in = is_in_period_attendance($id,$test_did,$tid,$period);

      if($is_in != 1)
      {
        $comment = "Your attendance was not taken for this class.
              Please inform your instructor that you're in class.";
        title_comment_div_noend($page_title, $comment);
        exit(1);      
      }

      $test_id = get_test_id($tid, $period);
      if(!empty($test_id))
      $score_in=isin_scores_table 
                            ($tid,$id,$test_id,$test_did,$period);
    }
     

    if ($test_id == 1)
    {      
      print "<b>No Test</b></body></html>";
      exit(1);      
    }

    if ( $score_in == "yes" )
    {
      $comment = "You already have a grade for this test.";
      title_comment_div_noend($page_title, $comment);
    }
    else
    {
      $all_test_arr = get_test_arr($tid, $test_id);
      $desc = $all_test_arr["description"];
      $type = $all_test_arr["type"];
      $quest_str = str_replace("__________",
                           " __________ ", $all_test_arr['test']);
      $quest_arr = explode("&&", $quest_str);
      srand ((double) microtime() * 1000000);
      $arr = randomize_array($quest_arr);
    
      array_print_html($desc, $arr, $tid, $id, $test_id,$period,  
                        $name, $test_did, $type);
    }
  }
}
echo "<DIV>";
?>
</body></html>

