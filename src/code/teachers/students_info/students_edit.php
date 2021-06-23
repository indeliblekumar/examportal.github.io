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


$fname=$_POST['fname'];
$fname2=$_POST['fname2'];
$mname=$_POST['mname'];
$lname=$_POST['lname'];
$period=$_POST['period'];
$all_periods=$_POST['all_periods'];
$insert=$_POST['insert'];
$menu_submit=$_POST['menu_submit'];

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


function input_form()
{
  $login = <<<EOQ
<form method=post action="$PHP_SELF">
<b>Period:</b><br>
<input type=text name=period size=4>&nbsp &nbsp
<input type=checkbox name=all_periods value="all_periods"><b>All periods
and all students:</b>
<br><br>

<b>Student's first name:</b><br>
<input type=text name=fname size=30>
<br><br>

<b>Student's preferred first name:</b><br>
<input type=text name=fname2 size=30>
<br><br>

<b>Student's middle name:</b><br>
<input type=text name=mname size=3>
<br><br>

<b>Student's last name:</b><br>
<input type=text name=lname size=30>
<br><br>

<input type=hidden name=menu_submit value="Student">
<input type=submit name=submit value="Search for student(s)">
</form>
EOQ;
  return $login;
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

function get_new_pword() {
  $digits_arr[]="2";
  $digits_arr[]="3";
  $digits_arr[]="4";
  $digits_arr[]="5";
  $digits_arr[]="6";
  $digits_arr[]="7";
  $digits_arr[]="8";
  $digits_arr[]="9";
  $digits_arr[]="A";
  $digits_arr[]="B";
  $digits_arr[]="C";
  $digits_arr[]="D";
  $digits_arr[]="E";
  $digits_arr[]="F";
  $digits_arr[]="G";
  $digits_arr[]="H";
  $digits_arr[]="J";
  $digits_arr[]="K";
  $digits_arr[]="L";
  $digits_arr[]="M";
  $digits_arr[]="N";
  $digits_arr[]="P";
  $digits_arr[]="R";
  $digits_arr[]="S";
  $digits_arr[]="T";
  $digits_arr[]="U";
  $digits_arr[]="V";
  $digits_arr[]="W";
  $digits_arr[]="X";
  $digits_arr[]="Y";
  $digits_arr[]="Z";

  $chars_num_arr[]=5;
  $chars_num_arr[]=6;
  $chars_num_arr[]=7;
  $chars_num_arr[]=8;

  srand ((double) microtime() * 1000000);
  $char_num = array_rand($chars_num_arr, 1);
  $digits = $chars_num_arr[$char_num];

  $arr_rand_keys = array_rand($digits_arr, $digits);
  shuffle($arr_rand_keys);
  
  $pword_str = "";
  for ( $i=0; $i<$digits; $i++)
  {
    $pword_str .= $digits_arr[$arr_rand_keys[$i]];
  }
  return $pword_str;
}


function insert_pword ($tid="", $id=0)
{
  $table = "pwords" . "$tid";
  $pword = "";
  $p_w_exists=1;
  while ($p_w_exists)
  {
    $pword = get_new_pword();
    $p_w_exists = pword_exists ($table, $pword);
  }

  $query = "insert into $table (id,pword)
            values ($id,'$pword')";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
}


function pword_exists ($table, $pword="")
{
  $queryString = "select * from $table where pword =
                  '$pword'";
  $result = mysql_query($queryString);
  // query_outcome_echo($query,$result);

  if (mysql_num_rows($result) == 0)
    return 0;
  else
    return 1;
}


function show_ids_arr($period_idsNames_ass=array())
{
  while (list($period,$ids_names_num) = each($period_idsNames_ass))
  {
    print "period=$period<br>";
    while (list(,$id_name_arr) = each($ids_names_num))
    {
      $fname = $id_name_arr['fname'];
      $fname2 = $id_name_arr['fname2'];
      $mname = $id_name_arr['mname'];
      $lname = $id_name_arr['lname'];
      $id = $id_name_arr['id'];
      print "$fname $fname2 $mname $lname, id=$id <br>";
    }
  }
  print "<br>";
}


function show_table_column_title($period)
{
  echo "<br><br>";
  echo startTable("border");
  $cell_data = tDataSource("Period $period", "center", "",1);
  echo rowPrint($cell_data);
}


function show_periods_info_table($period_idsNames_ass)
{
  while (list($period,$ids_names_num) = each($period_idsNames_ass))
  {
    show_table_column_title($period);
    while (list(,$id_name_arr) = each($ids_names_num))
    {
      $form_data=get_info_form($id_name_arr, $period);
      $cell_data = tDataSource($form_data, "center");          
      echo rowPrint($cell_data);
    }
    echo endTable();
  }
//  echo endTable();
}


function get_info_form ($id_name_arr=array(), $period)
{
  $fname = $id_name_arr['fname'];
  $fname2 = $id_name_arr['fname2'];
  if(!empty($fname2))
    $fname .= " (" . $fname2 . ") ";

  $id = $id_name_arr['id'];
  $name = $fname;
  $name .= " " . $id_name_arr['mname'];
  $name .= " " . $id_name_arr['lname'];

  $page = "./students_e_d.php?id=$id&period=$period";
  $window_description =
"width=300,height=550,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<input type=button name="button" value="$name" class=info_button
onClick="window.open('$page','newwindow','$window_description')">
EOQ;
  return $form_html;  
}

function start_main_div ()
{
  echo '<DIV class=main_table>';
}


function end_main_div ()
{
  echo '</DIV>';
}


$tag_title = "gnuschool.org Student Information";


start_html($tag_title);
stylesheet_link("../../style_sheet");
start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Student";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
main_menu($menu_r);

$page_title = "Edit student information";

if (!$yes_input || !empty($insert))
{
  $comment = "Please complete the following information:";

  title_comment_div_noend($page_title, $comment);
  echo input_form();
  end_html_exit();
}

$query_str = "";

$defaults_r = get_defaults_r($tid);
$order = $defaults_r['order'];

$per_stu_rrr = array();
$stu_rr = array();

if($name_flag)
{
  $query_str = post2_stu_blocks_query($order);
//  print "<br><br>stu_blocks_query = $query_str";
  $stu_rr = stu_blocks2stu_rr($query_str,$period_flag,$period,$tid);
  $per_stu_rrr = add_period_fname2($tid,$stu_rr,$period);
}
else
{
  $query_str = post2_spanish_query($tid,"period");
  $stu_rr = spanish2stu_rr($query_str);
  $per_stu_rrr = add_stu_names($tid, $stu_rr);
}

if(sizeof($per_stu_rrr) == 0)
{
  $comment = "Sorry, no student matches found.";
  title_comment_div_noend($page_title, $comment);
}   
else
{
  $comment = "Please click on the student you wish to edit.";
  title_comment_div_noend($page_title, $comment);
  show_periods_info_table ($per_stu_rrr);
}

echo "</DIV>";

?>
</body></html>
