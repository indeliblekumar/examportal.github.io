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
include ('./functions.php');
include ('./login.php');
include ('./search.php');

$fname=$_POST['fname'];
$mname=$_POST['mname'];
$lname=$_POST['lname'];
$hr_num=$_POST['hr_num'];
$all_hr=$_POST['all_hr'];
$insert=$_POST['insert'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;
if (empty($fname) && empty($mname) && empty($lname) && empty($hr_num) &&
    empty($all_hr))
  $yes_input = 0;


function input_form()
{
  echo <<<EOQ
<form method=post action="$PHP_SELF">
<b>Homeroom:</b><br>
<input type=text name="hr_num" size=10 maxlength=10>&nbsp &nbsp
<input type=checkbox name=all_hr value="all_hr"><b>All homerooms:</b>
<br><br>

<b>Student first name:</b><br>
<input type=text name=fname size=30 maxlength=30>
<br><br>

<b>Student middle name:</b><br>
<input type=text name=mname size=3 maxlength=3>
<br><br>

<b>Student last name:</b><br>
<input type=text name=lname size=30 maxlength=30>
<br><br>

<input type=hidden name=menu_submit value="Student">
<input type=submit name=submit value="Search for student(s)">
<input type=submit name=insert value="Insert this new student">
</form>
EOQ;
}


function insert_new_student($fname,$mname,$lname,$period)
{
  $comments = "";
  $ok_line = 1;
  $quote = 'n';

  if(empty($fname)||empty($lname)||empty($period))
  {
    $comments = "Sorry, you need to complete the form to insert a new
                student.";
  }
  else
  {
    if (!is_alpha_enddot_quote($fname))
    {
        echo "<b>SKIPPING: Bad character found in the first name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
    }

    if (!is_alpha_enddot_quote($mname, 1))
    {
        echo "<b>SKIPPING: Bad character found in the middle name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
    }

    $lname_quote = is_alpha_enddot_quote($lname,0,1);
    if ($lname_quote == 0)
    {
        echo "<b>SKIPPING: Bad character found in the last name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
    }

    if($lname_quote == 39)
    {
      $lname = addslashes($lname);
      $quote = 'y';
    }

    $query = "select * from stu_blocks where fname='$fname' and
              mname='$mname' and lname='$lname' and hr='$period'";
    $result = mysql_query($query);
// query_outcome_echo($query, $result, 0);
    if( mysql_num_rows($result) == 0 )
    {
      $query = "insert into stu_blocks (fname,mname,lname,quote,hr)
                values ('$fname','$mname','$lname',
                        '$quote','$period')";
      mysql_query($query);
      $comments ="You have inserted a new student.";
    }
    else
      $comments ="The student already exists.";    
  }
  return $comments;
}


function endTable()
{
  return "</TABLE>";
}


function get_ids_rr ($spanish_query,$date)
{
  $periods_idsNames_ass = array();
  $result = mysql_query($spanish_query);
// print "<br><br><br><br>";
// query_outcome_echo($spanish_query, $result, 0);
  if (mysql_num_rows($result) == 0) return array(); 
  $period_idName_num=array();
  $id_name_r=array();

  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $hr_num = $row['hr'];
  $id_name_r['fname'] = $row['fname'];
  $id_name_r['mname'] = $row['mname'];
  $id_name_r['lname'] = $row['lname'];
  $id = $row['id'];
  $id_name_r['id'] = $id;
  $id_name_r['active'] = $row['active'];
  $id_name_r['gone']=is_dismissed($id,$date);
  $period_idName_num[]=$id_name_r;

  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $next_hr_num = $row['hr'];
    $id_name_r['fname'] = $row['fname'];
    $id_name_r['mname'] = $row['mname'];
    $id_name_r['lname'] = $row['lname'];
    $id = $row['id'];
    $id_name_r['id'] = $id;
    $id_name_r['active'] = $row['active'];
    $id_name_r['gone']=is_dismissed($id,$date);
    if ($hr_num == $next_hr_num)
      $period_idName_num[]=$id_name_r;
    else
    {
      $periods_idsNames_ass["$hr_num"] = $period_idName_num;
      $period_idName_num=array();
      $period_idName_num[]=$id_name_r;
      $hr_num = $next_hr_num;
    }
  }
  $periods_idsNames_ass["$hr_num"] = $period_idName_num;
// show_ids_arr($periods_idsNames_ass);
  return $periods_idsNames_ass;
}


function show_ids_arr($period_idsNames_ass=array())
{
  while (list($hr_num,$ids_names_num) = each($period_idsNames_ass))
  {
    print "Homeroom=$hr_num<br>";
    while (list(,$id_name_r) = each($ids_names_num))
    {
      $fname = $id_name_r['fname'];
      $mname = $id_name_r['mname'];
      $lname = $id_name_r['lname'];
      $id = $id_name_r['id'];
      print "$fname $mname $lname, id=$id <br>";
    }
  }
  print "<br>";
}


function show_table_column_title($hr_num)
{
  echo "<br><br>";
  echo startTable("border");
  $cell_data = tDataSource("Homeroom $hr_num", "center", "",3);
  echo rowPrint($cell_data);
}


function show_periods_info_table($period_idsNames_ass)
{
  while (list($hr_num,$ids_names_num) = each($period_idsNames_ass))
  {
    show_table_column_title($hr_num);
    while (list(,$id_name_r) = each($ids_names_num))
    {
      $form_data=get_info_form($id_name_r, $hr_num);
      $cell_data = tDataSource($form_data, "right"); 
      $form_data=get_inactivate_form($id_name_r, $hr_num);
      $cell_data .= tDataSource($form_data, "center");          
      $form_data=get_dismiss_form($id_name_r, $hr_num);
      $cell_data .= tDataSource($form_data, "center");                   
      echo rowPrint($cell_data);
    }
    echo endTable();
  }
}


function get_info_form ($id_name_r=array(), $hr_num)
{
  $id = $id_name_r['id'];
  $name = $id_name_r['fname'];
  $name .= " " . $id_name_r['mname'];
  $name .= " " . $id_name_r['lname'];

  $page = "./student_edit.php?id=$id&hr_num=$hr_num";
  $window_description =
"width=300,height=600,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<input type=button name="button" value="$name" class=info_button
onClick="window.open('$page','stuinfo','$window_description')">
EOQ;
  return $form_html;  
}


function get_inactivate_form ($id_name_r=array(), $hr_num)
{
  $id = $id_name_r['id'];
  $active = $id_name_r['active'];
  $active_str = "Deactivate";
  if ($active == 'n') $active_str = "Activate"; 

  $page = "./student_deactivate.php?id=$id&hr_num=$hr_num";
  $window_description =
"width=600,height=400,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<input type=button name="button" value="$active_str" class=info_button
onClick="window.open('$page','inactive','$window_description')">
EOQ;
  return $form_html;  
}


function get_dismiss_form ($id_name_r=array(), $hr_num)
{
  $id = $id_name_r['id'];
  $dismissed = $id_name_r['gone'];
  $dismiss_str = "Dismiss";
  if ($dismissed) $dismiss_str = "Undismiss"; 

  $page = "./student_dismiss.php?id=$id&hr_num=$hr_num";
  $window_description =
"width=600,height=400,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<input type=button name="button" value="$dismiss_str" class=info_button
onClick="window.open('$page','dismiss','$window_description')">
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

$tag_title = "gnuschool.org Grades search";
start_html($tag_title);

stylesheet_link("../style_sheet");

start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Student";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
main_menu($menu_r);

$page_title = "Student information";

if (!$yes_input)
{
  $comments = "Please complete the following information:";
  title_comment_div_noend($page_title, $comments);
  input_form();
  end_html_exit();
}

if(!empty($insert))
{
  $comments=insert_new_student($fname,$mname,$lname,$hr_num);
  title_comment_div_noend($page_title, $comments);
  input_form();
  end_html_exit();
}

$stu_blocks_query = post2_stu_blocks_query();
//print "<br><br>stu_blocks_query = $stu_blocks_query";
$period_idsNames_ass = get_ids_rr ($stu_blocks_query, $date);
if(sizeof($period_idsNames_ass) == 0)
{
  $comment = "Sorry, no student matches found.";
  title_comment_div_noend($page_title, $comment);
}   
else
{
  $page_title = "Edit student information";
  $comment = "Please click on the student you wish to edit.";
  title_comment_div_noend($page_title, $comment);
  show_periods_info_table ($period_idsNames_ass);
}
echo "</DIV>";
?>
</body></html>
