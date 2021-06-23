<?php
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

function stu_blocks2stu_rr($query_str,$period_flag,$period,$tid)
{
  $found = 0;
  $result = mysql_query($query_str);
  if (mysql_num_rows($result) == 0)
  { 
    $comment = "Sorry, no matches found.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }

  $stu_rr = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $found = TRUE;
    if($period_flag)
    {
      $periods = $row['periods'];
      $look = "|" . "$period" . "~" . "$tid" . "|";
      $found = strstr($periods,$look);
    }
    if($found !== FALSE) $stu_rr[] = $row;
  }
  if (count($stu_rr) == 0)
  {
    $comment = "Sorry, no matches found.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }
  return $stu_rr;
}


function spanish2stu_rr($query_str)
{
  $found = 0;
  $result = mysql_query($query_str);
  if (mysql_num_rows($result) == 0)
  {
    $comment = "Sorry, no matches found.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }
  $stu_rr = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $stu_rr[] = $row;
  }
  if (count($stu_rr) == 0)
  {
    $comment = "Sorry, no matches found.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }
  return $stu_rr;
}


function get_dateID($date)
{
  $query = "select id from dates where date='$date'";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0)
  {
    return 0;
  }
  else
  {
    $row = mysql_fetch_row($result);
    return $row[0];
  }
}


function insert_get_dateID($date)
{
  $query="insert into dates (date) values ('$date')";
  mysql_query($query);
  $query = "select id from dates where date='$date'";
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  return $row[0];
}


function get_date_query($start=0, $end=0)
{
   if($start > $end)
  {
    $temp = $end;
    $end = $start;
    $start = $temp;
  }

  $where = "";

  if ($start)
  {
    if($end)
      $where = "(date >= $start and date <= $end)";
    else
      $where = "date = $start";
  }
  else
    if ($end)
      $where = "date = $end";

  return $where;
}


function get_id_iddate_rr($date1,$date2)
{
  $id_ends_r=array();
  $id_date_r=array();
  $id_rr=array();
  $query = "select * from dates where ( date >= '$date1' and date <= '$date2' )
                     order by id";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0)
  {
    return 0;
  }
  else
  {
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $id = $row['id'];
    $id_date_r["$id"]=$row['date'];
    $id_ends_r[0]=$id;
    
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
       $id = $row['id'];
       $id_date_r["$id"]=$row['date'];
    }
    $id_ends_r[1]=$id;
    $id_rr[0]=$id_ends_r;
    $id_rr[1]=$id_date_r;
    return $id_rr;
  }
}


function add_period_fname2($tid,$stu_rr,$period="")
{
  $per_stu_rrr = array();
  if(isset($stu_rr))
  {
    $andperiod = "";
    if(!empty($period))$andperiod = " and period='$period'";
    $table = "spanish" . "$tid";
    for ($i=0; $i<count($stu_rr); $i++)
    {
      $id = $stu_rr[$i]['id'];
      $query = "select period, fname2 from $table where id=$id" . "$andperiod";
      $result = mysql_query($query);
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
      {
        $period = $row['period'];
        $fname2 = $row['fname2'];
        $stu_rr[$i]['fname2'] = $fname2;
        $per_stu_rrr["$period"][] = $stu_rr[$i];
      }
    }
  }
  return $per_stu_rrr;
}


function add_stu_names($tid,$stu_rr)
{
  $per_stu_rrr = array();
  if(isset($stu_rr))
  {
    $table = "stu_blocks";
    for ($i=0; $i<count($stu_rr); $i++)
    {
      $id = $stu_rr[$i]['id'];
      $query = "select * from $table where id=$id";
      $result = mysql_query($query);
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
      {
        $name = $row['fname'];
        $stu_rr[$i]['fname'] = $name;
        $name = $row['mname'];
        $stu_rr[$i]['mname'] = $name;
        $name = $row['lname'];
        $stu_rr[$i]['lname'] = $name;
        $period = $stu_rr[$i]['period'];
        $per_stu_rrr["$period"][] = $stu_rr[$i];
      }
    }
  }
  return $per_stu_rrr;
}


function stu_name_ass_r($id)
{
    $query = "select fname,mname,lname from stu_blocks where id = $id";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    return $row;
}


function title_comment_div_noend($page_title="", $comment="")
{
if (!empty($comment))
  $comment="<h3>$comment</h3>";
echo "<DIV class=page_title><h2>$page_title</h2>";
echo $comment;
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


function td_anchor($file, $text)
{
  $data = <<<EOQ
<td align="center">
  <a href="$file?=SID">$text</a>
</td>
EOQ;
  return $data;
}


function td_button($file, $text="")
{
  $data = <<<EOQ
<form method=post action="$file">
<td align="center">
<input class=green_button type=submit name=menu_submit value="$text">
</td>
</form>
EOQ;
  return $data;
}


function td_general_form($text="", $action="")
{
  if ($action=="") $action = $PHP_SELF;
  $data = <<<EOQ
<form method=post action="$action">
<td align="center">
$text
</td>
</form>
EOQ;
  return $data;
}


function td_no_form($text)
{
  $data = <<<EOQ
<td align="center">
$text
</td>
EOQ;
  return $data;
}


function get_attendance_row($period="", $defaults = "")
{
  
  $t_data = "<tr>";
  $t_data .= 
td_button("../index.html","Main");
  $t_data .=
  td_button("../attendance/attendance.php", "Take Attendance");
  $t_data .=
  td_button("../attendance/absent_search.php", "Absent Search");
  if(!empty($defaults))
  {
    $t_data .= get_menu_defaults_form("Absent Search");
  }
  if(!empty($period))
  {
    $temp_str = <<<EOQ
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="Monitor">
<input class=green_button type=submit name=""
 value='Monitor "PERIOD $period"'>
EOQ;
    $t_data .= td_general_form($temp_str,
                         "../tests_admin/test_monitor2.php");
  }
  $t_data .= "</tr>";
  if(!empty($period))
    $t_data .= get_attendance2_row($period);
  return $t_data;
}


function get_attendance2_row($period="")
{
  
  $t_data = "<tr>";
  $temp_str = <<<EOQ
<input id="check_undo" type=checkbox name="undo_attend"
value="yes"><FONT class="green_button">Absent</FONT>
EOQ;
  $t_data .= td_no_form($temp_str);
  
  $temp_str = <<<EOQ
<input type=hidden name=period value="$period">
<input class=green_button type=submit name="undo_all"
 value='Undo attendance for "PERIOD $period"'>
EOQ;
  $t_data .= td_general_form($temp_str);
  $temp_str = <<<EOQ
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="Seats">
<input class=green_button type=submit name=""
 value='Change seats for "PERIOD $period"'>
EOQ;
  $t_data .= td_general_form($temp_str, "../seats/seating_edit.php");
  $t_data .= get_menu_defaults_form("Attendance");
  $t_data .= "</tr>";

  return $t_data;
}


function get_menu_defaults_form ($parent)
{
  
  $page = "../defaults.php";
  $window_description =
"width=600,height=500,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<form method=post target="defwindow" action="../defaults.php"> 
<td align="center">
<input type=hidden name=menu_submit value="$parent">
<input type=submit class=green_button name=defaults value="Defaults"
onClick="window.open('$page','defwindow','$window_description')">
</td>
</form>
EOQ;
  return $form_html;  
}


function get_grades_row($defaults="")
{
  
  $t_data = "<tr>";
  $t_data .= 
td_button("../index.html","Main");
  $t_data .=
    td_button("../grades/grades_search.php", "Grades Search");
  $t_data .=
    td_button("../grades/grades_makeup.php", "Make Up Grades");
  $t_data .= 
    td_button("../grades/test_copy_search.php","Hardcopy Search");
  if(!empty($defaults))
  {
    $t_data .= get_menu_defaults_form("Grades Search");
  }
  $t_data .= "</tr>";
  return $t_data;
}


function get_seats_row($update_inputs, $period)
{
  
  $t_data = "<tr>";
  $t_data .= 
td_button("../index.html","Main");
  $t_data .=
  td_button("../seats/seating_edit.php", "Edit Seats");
  if (!empty($update_inputs))
    $t_data .= td_no_form("&nbsp");
  $t_data .= "</tr>";
  if (!empty($update_inputs))
  {
    $t_data .= "<tr>";
    $temp_str = <<<EOQ
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="Attendance">
<input class=green_button type=submit name=""
 value='Take attendance for "PERIOD $period"'>
EOQ;
  $t_data .= td_general_form($temp_str, 
                            "../attendance/attendance.php");
    $t_data .= td_general_form($update_inputs);
    $t_data .= get_menu_defaults_form("Seats");
    $t_data .= "</tr>";
  }
  return $t_data;
}


function get_tests_row($period="")
{
  
  $t_data = "<tr>";
  $t_data .= 
td_button("../index.html","Main");
  $t_data .=
td_button("../tests_admin/lock_unlock_db.php","Lock or Unlock Tests");
  $t_data .=
td_button("../tests_admin/new_passwords.php","View Test Passwords");
  $t_data .=
td_button("../tests_admin/test_find.php","Set Test for a Period");
  $t_data.=
td_button("../tests_admin/test_monitor1.php", "Monitor a Test");
  $t_data .= "</tr><tr>";
  $t_data .=  
td_button("../tests_admin/test_find.php", "Edit a Test");
  $t_data .=  
td_button("../tests_admin/test_find.php", "Find a Test");
  $t_data .=  
td_button("../tests_admin/test_review1.php", "Review a Test");
  $t_data .=  
td_button("../tests_admin/test_create.php","Create a Test");
  if(empty($period))
    $t_data .= td_no_form("&nbsp");
  else
    $t_data .= get_menu_defaults_form("Passwords");
  $t_data .= "</tr>";
  return $t_data;
}


function get_main_row()
{
  
  $t_data = "<tr>";
  $t_data .=
  td_button("../tests_admin/lock_unlock_db.php","Tests");
  $t_data .=
  td_button("../attendance/attendance.php", "Attendance");
  $t_data .=
  td_button("../grades/grades_search.php", "Grades");
  $t_data .=
  td_button("../seats/seating_edit.php", "Seats");
  $t_data .=
  td_button("../students_info/students_edit.php", "Student");
  $t_data .= "</tr>";
  return $t_data;
}


function get_monitor_row($period="")
{
  
  $t_data = "<tr>";
  $t_data .= 
td_button("../index.html","Main");
  $t_data .=
  td_button("../tests_admin/test_monitor1.php","Monitor a Test");
  $t_data .= 
td_button("../tests_admin/lock_unlock_db.php","Tests");
  $t_data .= "</tr>";
  $t_data .= "<tr>";
  $temp_str = <<<EOQ
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="Monitor">
<input class=green_button type=submit name="monitor_update"
 value='Update "PERIOD $period" students not finished'>
EOQ;
  $t_data .= td_general_form($temp_str);
  $temp_str = <<<EOQ
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="Attendance">
<input class=green_button type=submit name="attendance"
 value='Take attendance for "PERIOD $period"'>
EOQ;
  $t_data .= 
td_general_form($temp_str, "../attendance/attendance.php");
  if(empty($period))
    $t_data .= td_no_form("&nbsp");
  else
    $t_data .=
    get_menu_defaults_form("Monitor a Test");
  $t_data .= "</tr>";

  return $t_data;
}


function get_student_info_row()
{
  
  $t_data = "<tr>";
  $t_data .= 
td_button("../index.html","Main");
  $t_data .=
td_button("../students_info/students_edit.php",
                                         "Edit Student Information");
  $t_data .= "</tr>";
  return $t_data;
}


function get_menu_array()
{
  $submit_r = array();
  $submit_r["Take Attendance"] = "attendance";
  $submit_r["Absent Search"] = "attendance";
  $submit_r["Absent search"] = "attendance";
  $submit_r["Absent"] = "attendance";
  $submit_r["Undo attendance for this period"] = "attendance";
  $submit_r["Grades Search"] = "grade";
  $submit_r["Grades search"] = "grade";
  $submit_r["Hardcopy Search"] = "grade";
  $submit_r["Make Up Grades"] = "grade";
  $submit_r["Make up search"] = "grade";
  $submit_r["Passwords"] = "test";
  $submit_r["Lock or Unlock Tests"] = "test";
  $submit_r["View Test Passwords"] = "test";
  $submit_r["Set Test for a Period"] = "test";
  $submit_r["Monitor a Test"] = "test";
  $submit_r["Edit a Test"] = "test";
  $submit_r["Create a Test"] = "test";
  $submit_r["Find a Test"] = "test";
  $submit_r["Review a Test"] = "test";
  $submit_r["Edit Student Information"] = "student";
  $submit_r["Save Changes"] = "seat";
  $submit_r["Edit Seats"] = "seat";
  $submit_r["Monitor"] = "monitor";

  $submit_r["Tests"] = "test";
  $submit_r["Attendance"] = "attendance";
  $submit_r["Grades"] = "grade";
  $submit_r["Seats"] = "seat";
  $submit_r["Student"] = "student";
  $submit_r["Main"] = "main";
  return $submit_r;
}


function main_menu($var_arr)
{
  $bottom = "";
  $period = $var_arr['period'];
  $act = $var_arr['active_page'];
  $defaults = $var_arr['defaults'];
  $update_inputs = $var_arr['update_inputs'];

  $menu_r = get_menu_array();

  if($menu_r["$act"] == "main")
    $bottom = get_main_row();
  elseif($menu_r["$act"] == "attendance")
    $bottom = get_attendance_row($period, $defaults);
  elseif($menu_r["$act"] == "grade")
    $bottom = get_grades_row($defaults);
  elseif($menu_r["$act"] == "test")
    $bottom = get_tests_row($period);
  elseif($menu_r["$act"] == "seat")
    $bottom = get_seats_row($update_inputs, $period);
  elseif($menu_r["$act"] == "student")
    $bottom = get_student_info_row();
  elseif($menu_r["$act"] == "monitor")
    $bottom = get_monitor_row($period);

echo <<<EOQ
<DIV id=menu_div class=main_menu>
    <table border cellpadding="0" cellspacing="0" width="100%" 
bordercolor="#000000" bgcolor="#008000">
$bottom
    </table>
</div>
EOQ;
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


function special_chars($type)
{
  $source = "";
  if($type == "Spanish")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  á  "
onclick="addText('á')">  
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('é')">  
<INPUT TYPE="button" VALUE="  í  "
onclick="addText('í')">  
<INPUT TYPE="button" VALUE="  ó  "
onclick="addText('ó')">  
<INPUT TYPE="button" VALUE="  ú  "
onclick="addText('ú')">
<INPUT TYPE="button" VALUE="  ü  " 
onclick="addText('ü')"> 
<INPUT TYPE="button" VALUE="  ñ  "
onclick="addText('ñ')">
<INPUT TYPE="button" VALUE="  Á  "
onclick="addText('Á')">
<INPUT TYPE="button" VALUE="  É  "
onclick="addText('É')">
<INPUT TYPE="button" VALUE="  Í  "
onclick="addText('Í')">
<INPUT TYPE="button" VALUE="  Ó  "
onclick="addText('Ó')">
<INPUT TYPE="button" VALUE="  Ú  "
onclick="addText('Ú')">
<INPUT TYPE="button" VALUE="  Ü  " 
onclick="addText('Ü')"> 
<INPUT TYPE="button" VALUE="  Ñ  "
onclick="addText('Ñ')">
<INPUT TYPE="button" VALUE="  ¡  "
onclick="addText('¡')">
<INPUT TYPE="button" VALUE="  ¿  "
onclick="addText('¿')">
EOQ;
  }
elseif($type == "German")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  ä  "
onclick="addText('ä')"> 
<INPUT TYPE="button" VALUE="  ö  "
onclick="addText('ö')"> 
<INPUT TYPE="button" VALUE="  ü  "
onclick="addText('ü')"> 
<INPUT TYPE="button" VALUE="  Ä  " 
onclick="addText('Ä')"> 
<INPUT TYPE="button" VALUE="  Ö  "
onclick="addText('Ö')"> 
<INPUT TYPE="button" VALUE="  Ü  "
onclick="addText('Ü')"> 
<INPUT TYPE="button" VALUE="  ß  "
onclick="addText('ß')">
<INPUT TYPE="button" VALUE="  €  "
onclick="addText('€')">
EOQ;
  }
elseif($type == "French")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  À  "
onclick="addText('À')"> 
<INPUT TYPE="button" VALUE="  à  "
onclick="addText('à')"> 
<INPUT TYPE="button" VALUE="  Á  "
onclick="addText('Á')"> 
<INPUT TYPE="button" VALUE="  á  "
onclick="addText('á')"> 
<INPUT TYPE="button" VALUE="  Â  "
onclick="addText('Â')"> 
<INPUT TYPE="button" VALUE="  â  "
onclick="addText('â')"> 
<INPUT TYPE="button" VALUE="  Ä  "
onclick="addText('Ä')"> 
<INPUT TYPE="button" VALUE="  ä  "
onclick="addText('ä')"> 
<INPUT TYPE="button" VALUE="  É  "
onclick="addText('É')">  
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('é')">
<INPUT TYPE="button" VALUE="  È  "
onclick="addText('È')">
<INPUT TYPE="button" VALUE="  è  "
onclick="addText('è')">
<INPUT TYPE="button" VALUE="  Ê  "
onclick="addText('Ê')"> 
<INPUT TYPE="button" VALUE="  ê  "
onclick="addText('ê')"> 
<INPUT TYPE="button" VALUE="  Ë  "
onclick="addText('Ë')"> 
<INPUT TYPE="button" VALUE="  ë  "
onclick="addText('ë')"> 
<INPUT TYPE="button" VALUE="  Î  "
onclick="addText('Î')"> 
<INPUT TYPE="button" VALUE="  î  "
onclick="addText('î')"> 
<INPUT TYPE="button" VALUE="  Ï  "
onclick="addText('Ï')"> 
<INPUT TYPE="button" VALUE="  ï  "
onclick="addText('ï')">
<INPUT TYPE="button" VALUE="  Ô  "
onclick="addText('Ô')">
<INPUT TYPE="button" VALUE="  ô  "
onclick="addText('ô')">
<INPUT TYPE="button" VALUE="  Œ  "
onclick="addText('Œ')">
<INPUT TYPE="button" VALUE="  œ  "
onclick="addText('œ')">
<INPUT TYPE="button" VALUE="  Ò  "
onclick="addText('Ò')"> 
<INPUT TYPE="button" VALUE="  ò  "
onclick="addText('ò')">
<INPUT TYPE="button" VALUE="  Ô  "
onclick="addText('Ô')"> 
<INPUT TYPE="button" VALUE="  ô  "
onclick="addText('ô')"> 
<INPUT TYPE="button" VALUE="  Ö  "
onclick="addText('Ö')"> 
<INPUT TYPE="button" VALUE="  ö  "
onclick="addText('ö')">
<INPUT TYPE="button" VALUE="  Ÿ  "
onclick="addText('Ÿ')">
<INPUT TYPE="button" VALUE="  ÿ  "
onclick="addText('ÿ')"> 
<INPUT TYPE="button" VALUE="  Ç  "
onclick="addText('Ç')">
<INPUT TYPE="button" VALUE="  ç  "
onclick="addText('ç')">
<INPUT TYPE="button" VALUE="  €  "
onclick="addText('€')">
EOQ;
  }
elseif($type == "Italian")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  á  "
onclick="addText('á')">
<INPUT TYPE="button" VALUE="  à  "
onclick="addText('à')">
<INPUT TYPE="button" VALUE="  â  "
onclick="addText('â')"> 
<INPUT TYPE="button" VALUE="  ã  "
onclick="addText('ã')"> 
<INPUT TYPE="button" VALUE="  ä  "
onclick="addText('ä')"> 
<INPUT TYPE="button" VALUE="  å  "
onclick="addText('å')"> 
<INPUT TYPE="button" VALUE="  æ  "
onclick="addText('æ')"> 
<INPUT TYPE="button" VALUE="  À  "
onclick="addText('À')"> 
<INPUT TYPE="button" VALUE="  Á  "
onclick="addText('Á')"> 
<INPUT TYPE="button" VALUE="  Â  "
onclick="addText('Â')"> 
<INPUT TYPE="button" VALUE="  Ã  "
onclick="addText('Ã')"> 
<INPUT TYPE="button" VALUE="  Ä  "
onclick="addText('Ä')"> 
<INPUT TYPE="button" VALUE="  Å  "
onclick="addText('Å')"> 
<INPUT TYPE="button" VALUE="  Æ  "
onclick="addText('Æ')"> 
<INPUT TYPE="button" VALUE="  è  "
onclick="addText('è')"> 
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('ê')"> 
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('ê')"> 
<INPUT TYPE="button" VALUE="  ë  "
onclick="addText('ë')"> 
<INPUT TYPE="button" VALUE="  È  "
onclick="addText('È')"> 
<INPUT TYPE="button" VALUE="  É  "
onclick="addText('É')"> 
<INPUT TYPE="button" VALUE="  Ê  "
onclick="addText('Ê')"> 
<INPUT TYPE="button" VALUE="  ì  "
onclick="addText('ì')"> 
<INPUT TYPE="button" VALUE="  í  "
onclick="addText('í')"> 
<INPUT TYPE="button" VALUE="  î  "
onclick="addText('î')"> 
<INPUT TYPE="button" VALUE="  ï  "
onclick="addText('ï')"> 
<INPUT TYPE="button" VALUE="  Ì  "
onclick="addText('Ì')"> 
<INPUT TYPE="button" VALUE="  Í  "
onclick="addText('Í')"> 
<INPUT TYPE="button" VALUE="  Î  "
onclick="addText('Î')"> 
<INPUT TYPE="button" VALUE="  Ï  "
onclick="addText('Ï')"> 
<INPUT TYPE="button" VALUE="  ò  "
onclick="addText('ò')"> 
<INPUT TYPE="button" VALUE="  ó  "
onclick="addText('ó')"> 
<INPUT TYPE="button" VALUE="  ô  "
onclick="addText('ô')"> 
<INPUT TYPE="button" VALUE="  õ  "
onclick="addText('õ')"> 
<INPUT TYPE="button" VALUE="  ö  "
onclick="addText('ö')"> 
<INPUT TYPE="button" VALUE="  Ò  "
onclick="addText('Ò')"> 
<INPUT TYPE="button" VALUE="  Ó  "
onclick="addText('Ó')"> 
<INPUT TYPE="button" VALUE="  Ô  "
onclick="addText('Ô')"> 
<INPUT TYPE="button" VALUE="  Õ  "
onclick="addText('Õ')"> 
<INPUT TYPE="button" VALUE="  Ö  "
onclick="addText('Ö')"> 
<INPUT TYPE="button" VALUE="  ù  "
onclick="addText('ù')"> 
<INPUT TYPE="button" VALUE="  ú  "
onclick="addText('ú')"> 
<INPUT TYPE="button" VALUE="  û  "
onclick="addText('û')"> 
<INPUT TYPE="button" VALUE="  ü  "
onclick="addText('ü')"> 
<INPUT TYPE="button" VALUE="  Ù  "
onclick="addText('Ù')"> 
<INPUT TYPE="button" VALUE="  Ú  "
onclick="addText('Ú')"> 
<INPUT TYPE="button" VALUE="  Û  "
onclick="addText('Û')"> 
<INPUT TYPE="button" VALUE="  Ü  "
onclick="addText('Ü')"> 
<INPUT TYPE="button" VALUE="  ç  "
onclick="addText('ç')"> 
<INPUT TYPE="button" VALUE="  Ç  "
onclick="addText('Ç')"> 
<INPUT TYPE="button" VALUE="  ñ  "
onclick="addText('ñ')"> 
<INPUT TYPE="button" VALUE="  Ñ  "
onclick="addText('Ñ')"> 
<INPUT TYPE="button" VALUE="  ß  "
onclick="addText('ß')"> 
<INPUT TYPE="button" VALUE="  ý  "
onclick="addText('ý')"> 
<INPUT TYPE="button" VALUE="  Ý  "
onclick="addText('Ý')"> 
<INPUT TYPE="button" VALUE="  ¿  "
onclick="addText('¿')"> 
<INPUT TYPE="button" VALUE="  ¡  "
onclick="addText('¡')"> 
EOQ;
  }
  return $source;
}


function get_subjects_r()
{
  $type_r['Art'] = 0;
  $type_r['English'] = 0;
  $type_r['Geography'] = 0;
  $type_r['History'] = 0;
  $type_r['Mathematics'] = 0;
  $type_r['Science'] = 0;
  $type_r['Social Studies'] = 0;
  $type_r['French'] = 0;
  $type_r['German'] = 0;
  $type_r['Italian'] = 0;
  $type_r['Spanish'] = 0;
  return $type_r;
}

function array2select($select_r,$selected="",$name)
{
  $select_html = "<SELECT name=$name><OPTION SELECTED>$selected </OPTION>";
  while (list($key,) = each($select_r))
  {
    $select_html .= "<OPTION>" . $key . "</OPTION>";
  }
  $select_html .= "</SELECT>";
  return $select_html;
}

function escape_single_quotes(&$post_r)
{
  while (list ($key ,$value) = each($post_r))
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


function stylesheet_link($file)
{
echo <<<EOQ
<LINK REL="STYLESHEET" TYPE="text/css" HREF="$file">
EOQ;
}


function start_body($color="main_background")
{
echo <<<EOQ
</head>
<body class="$color">
EOQ;
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


function img_div()
{
  
  echo <<<EOQ
<DIV ID="div_id1" STYLE="position:absolute; top:0; left:0;">
<img id="img_id1" src='../blank.png' ALT='blank picture'>
<input id="id1" type="hidden" >
</DIV>
EOQ;
}

?>
