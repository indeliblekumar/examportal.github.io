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

$print=$_POST['print'];
$menu_submit=$_POST['menu_submit'];
$all_periods=$_POST['all_periods'];
$period=$_POST['period'];
$fname=$_POST['fname'];
$lname=$_POST['lname'];

$periods_flag = 0;
$period_flag = 0;
$name_flag = 0;
if (!empty($all_periods)) $periods_flag = 1;
if (!empty($period)) $period_flag = 1;
if (!empty($fname) || !empty($mname) || !empty($lname)) $name_flag = 1;

$yes_input = 0;
if ($periods_flag || $period_flag || $name_flag)
 $yes_input = 1;

if($period_flag)
 if ($period == "hr" || $period == "Hr" || $period == "hR")
 {
   $period = "HR";
   $_POST['period'] = $period;
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


function tFormDataSource($data="", $align="", $width="", $colspan="")
{
  if ($align != "") $align = "align=" . $align;
  if ($colspan != "") $colspan = "colspan=" . $colspan;
  if ($width != "") $width = "width=" . $width;
$data_html = <<<EOQ
<FORM method="post" action="$PHP_SELF">
<TD $align $colspan $width>
$data
</TD>
</form>
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


function get_ids_arr ($spanish_query)
{
  $periods_idsNames_ass = array();
  $result = mysql_query($spanish_query);

  $period_idName_num=array();
  $id_name_arr=array();

  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $period = $row['period'];

  $name = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
  $id_name_arr['name'] = $name;
  $id_name_arr['id'] = $row['id'];
  $period_idName_num[]=$id_name_arr;

  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $next_period = $row['period'];
    $name = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
    $id_name_arr['name'] = $name;
    $id_name_arr['id'] = $row['id'];
    if ($period == $next_period)
      $period_idName_num[]=$id_name_arr;
    else
    {
      $periods_idsNames_ass["$period"] = $period_idName_num;
      $period_idName_num=array();
      $period_idName_num[]=$id_name_arr;
      $period = $next_period;
    }
  }
  $periods_idsNames_ass["$period"] = $period_idName_num;
// show_ids_arr($periods_idsNames_ass);
  return $periods_idsNames_ass;
}


function show_ids_arr($period_idsNames_ass=array())
{
  while (list($period,$ids_names_num) = each($period_idsNames_ass))
  {
    print "period=$period<br>";
    while (list(,$id_name_arr) = each($ids_names_num))
    {
      $name = $id_name_arr['name'];
      $id = $id_name_arr['id'];
      print "name=$name, id=$id <br>";
    }
  }
  print "<br>";
}


function get_test_ids_arr ($tid="", $period="", $date_clause="")
{
  $table = "taken" . "$tid";
  $query = "select test_id,date from $table where
           period = '$period' and $date_clause order by date";
  $test_ids_arr = array();
  $result = mysql_query($query);

  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $test_id = $row['test_id'];
    $date = $row['date'];
    $test_id_date = $test_id . "|" . $date;
    $test_ids_arr[] = $test_id_date;
  }

// show_test_ids_arr($test_ids_arr, $period);
  return $test_ids_arr;
}


function show_test_ids_arr($test_ids_arr=array(), $period)
{
  print "Period:$period<br>";
  while (list(,$test_id_date) = each($test_ids_arr))
  {
    print "test_id_date=$test_id_date<br>";
  }
  print "<br>";
}


function get_scores_arr ($tid="", $id=0, $period="", $date_clause)
{
  $table = "scores" . "$tid"; 
  $test_id_scores = array();
  $query = "select * from $table where id = $id and
             period='$period' and $date_clause";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $test_id_date = $row['test_id'] . "|" . $row['date'];    
    $score = $row['score'];
    $makeup = $row['makeup'];
    if($score === "0") $score = "zero";
    
    if(!empty($score))
    {
      $test_id_scores["$test_id_date"] = array("$score",$makeup); 
      $score_arr=array();
      $score_arr['score']=$score;
      $score_arr['makeup']=$makeup;
      $test_id_scores["$test_id_date"] = $score_arr; 
    }
  }
// show_test_id_scores_arr($test_id_scores);
  return $test_id_scores;
}


function show_test_id_scores_arr($test_id_scores=array())
{
  while (list($test_idDate,$score_r) = each($test_id_scores))
  {
    print_r($score_r);
    print "test_idDate=$test_idDate <br>";
  }
  print "<br>";
}


function show_table_column_titles($testid_date_num=array(), $period)
{
  echo "<br><br>";
  echo startTable("border");
  $cell_data = tDataSource("Period $period grades", "center", "",
                            sizeof($testid_date_num) +1);
  echo rowPrint($cell_data);
  $cell_data = tDataSource("&nbsp");

  for ($i=0; $i<sizeof($testid_date_num); $i++)
  {
    $temp_str = $testid_date_num[$i];
    $pos = strpos($temp_str, "|"); 
    $test_id = substr($temp_str, 0, $pos); 
    $did = substr($temp_str, $pos + 1); 
    $date = get_id_date ($did);
    $window_button = test_id_button($test_id, $date);
    $cell_data .= tDataSource($window_button, "right");
  }
  echo rowPrint($cell_data);
}


function show_periods_scores_table
            ($tid="",$period_idsNames_ass,$date_clause="")
{
  while (list($period,$ids_names_num) = each($period_idsNames_ass))
  {
    $testid_date_num=get_test_ids_arr ($tid, $period, $date_clause);
    show_table_column_titles($testid_date_num, $period);
    while (list(,$id_name_arr) = each($ids_names_num))
    {
      $fname = $id_name_arr['fname'];
      $fname2 = $id_name_arr['fname2'];
      if(!empty($fname2)) $fname .= " (" . $fname2 . ") ";

      $name = $fname;
      $name .= " " . $id_name_arr['mname'];
      $name .= " " . $id_name_arr['lname'];
      $id = $id_name_arr['id'];
      $cell_data = tDataSource($name, "left");
      $testidDate_score_ass=
                  get_scores_arr($tid,$id,$period,$date_clause);
      for ($i=0; $i<sizeof($testid_date_num); $i++)
      {
        $test_id_date = $testid_date_num[$i];
        $score_arr = $testidDate_score_ass["$test_id_date"];
        $score = $score_arr['score'];
        $makeup = $score_arr['makeup'];
        if (!empty($score))
        {
          if ($score == "zero")
$form_data=get_test_form($id,$test_id_date,"0", $period);
          else
$form_data=get_test_form($id,$test_id_date,$score, $period);
          $cell_data .= tFormDataSource("$form_data", "center");          
        }
        else
        {
          $cell_data .= tDataSource("&nbsp");
        }
      }
      echo rowPrint($cell_data);
    }
    echo endTable();
  }
}


function get_test_form ($id=0,$testid_date="",$score="", $period="")
{
  $temp_str = $testid_date;
  $pos = strpos($temp_str, "|"); 
  $test_id = substr($temp_str, 0, $pos); 
  $date = substr($temp_str, $pos + 1); 

  $search_start_y=$_POST['search_start_y'];
  $search_start_m=$_POST['search_start_m'];
  $search_start_d=$_POST['search_start_d'];
  $search_end_y=$_POST['search_end_y'];
  $search_end_m=$_POST['search_end_m'];
  $search_end_d=$_POST['search_end_d'];
  $fname=$_POST['fname'];
  $lname=$_POST['lname'];
  $color=$_POST['color'];
  $all_periods=$_POST['all_periods'];

  $form_html = <<<EOQ
<input type=hidden name="id" value="$id">
<input type=hidden name="testid" value="$test_id">
<input type=hidden name="date" value="$date">
<input type=hidden name="search_start_y" value="$search_start_y">
<input type=hidden name="search_start_m" value="$search_start_m">
<input type=hidden name="search_start_d" value="$search_start_d">
<input type=hidden name="search_end_y" value="$search_end_y">
<input type=hidden name="search_end_m" value="$search_end_m">
<input type=hidden name="search_end_d" value="$search_end_d">
<input type=hidden name="fname" value="$fname">
<input type=hidden name="lname" value="$lname">
<input type=hidden name="menu_submit" value="Grades">
<input type=hidden name="period" value="$period">
<input type=hidden name="all_periods" value="$all_periods">
EOQ;

  $page =
"./test_copy.php?id=$id&date=$date&test_id=$test_id&period=$period";
  $window_description =
      "width=600,height=400,scrollbars=yes,resizable=yes,status=yes";

  $form_html .= <<<EOQ
<input type=submit class="gray_button" name=submit value="$score"
onClick="window.open('$page','copywindow','$window_description')">
EOQ;

  return $form_html;  
}


function test_id_button ($test_id, $date)
{
  $page = "./test_info.php?test_id=$test_id";
  $window_description =
       "width=600,height=400,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<input type=submit name=submit value="$date"
onClick="window.open('$page','gradeswindow','$window_description')">
EOQ;
  return $form_html;  
}


$tag_title = "gnuschool.org hardcopy search";
$page_title = "Hardcopy search";

start_html($tag_title);
stylesheet_link("../../style_sheet");

echo <<<EOQ

<SCRIPT LANGUAGE="JavaScript">

function set_s_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");

  if ( radio.checked == true)
  {
    if (document.getElementById)
    {
      document.getElementById('s1').value = date_arr[0];
      document.getElementById('s2').value = date_arr[1];
      document.getElementById('s3').value = date_arr[2];
    }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
}

function set_e_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");

  if ( radio.checked == true)
  {
     if (document.getElementById)
     {
        document.getElementById('e1').value = date_arr[0];
        document.getElementById('e2').value = date_arr[1];
        document.getElementById('e3').value = date_arr[2];
     }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
}
</SCRIPT>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in your browser.</p>
</noscript>
EOQ;

start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Make Up Grades";
if($menu_submit=="Make Up Grades" || $menu_submit=="Grades Search" 
|| $menu_submit=="Hardcopy Search")
  $defaults="yes";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
$menu_r['defaults']= $defaults;
main_menu($menu_r);

img_div();
if (!$yes_input)
{
  $submit_val = "Hardcopy Search";
  $def_r = get_defaults_r($tid);
  $comment = "Please fill the search fields as needed.";
  title_comment_div_noend($page_title, $comment);
  date_search_form($def_r, $submit_val, $date);
  end_html_exit();
}

$comment = "Please click on a score to view its corresponding hardcopy.";
title_comment_div_noend($page_title, $comment);

$spa_ta = "spanish" . "$tid";
$tests_ta = "tests" . "$tid";

$query_str = "";

$defaults_r = get_defaults_r($tid);
$order = $defaults_r['order'];

$per_stu_rrr = array();
$stu_rr = array();

if($name_flag)
{
  $query_str = post2_stu_blocks_query($order);
  $stu_rr = stu_blocks2stu_rr($query_str,$period_flag,$period,$tid);
  $per_stu_rrr = add_period_fname2($tid,$stu_rr,$period);
}
else
{
  $query_str = post2_spanish_query($tid,"period");
  $stu_rr = spanish2stu_rr($query_str);
  $per_stu_rrr = add_stu_names($tid, $stu_rr);
}

$search_r = glue_search_inputs();
if (!$search_r['ok'])
{
  $errors = $search_r['errors'];
  $comment = "There was a search date error.<br>$errors";
  title_comment_div_noend("", $comment);
  end_html_exit();
}

$search_r=order_date_r($date, $search_r);
$date1=$search_r[0];
$date2=$search_r[1];

$id_rr = get_2id_iddate_rr($date1, "<=", $date2);
$id_ends_r = $id_rr[0];
$id_date_r = $id_rr[1];
$date_id1 = $id_ends_r[0];
$date_id2 = $id_ends_r[1];
if(empty($id_ends_r))
{ echo "No matches were found";
  end_html_exit();
}

$date_query= get_date_query($date_id1, $date_id2);
show_periods_scores_table ($tid, $per_stu_rrr, $date_query);

echo "</DIV>";

?>

</body></html>
