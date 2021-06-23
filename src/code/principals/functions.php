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


function startTable($border="", $cellpadding=0, $cellspacing=0)
{
  $cellpadding = "cellpadding=" . "$cellpadding";
  $cellspacing = "cellspacing=" . "$cellspacing";
return "<TABLE $border $cellspacing $cellpadding>";
}
/* defaults.php
function startTable($border="", $cellpadding=0, $cellspacing=0)
{
  $cellpadding = "cellpadding=" . "$cellpadding";
  $cellspacing = "cellspacing=" . "$cellspacing";
  $t_start =<<<EOQ
<TABLE $border $cellspacing $cellpadding width="100%">
EOQ;
  return $t_start;
}
*/


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


function rowPrint($t_data="", $align="", $more="")
{
  if ($align != "") $align = "align=" . $align;
return "<TR $align $more> $t_data </TR>";
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


function td_button($file, $text="", $getpost="post")
{
  $data = <<<EOQ
<form method=$getpost action="$file">
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


function get_main_row($period="", $defaults = "")
{
  $t_data = "<tr>";
  $t_data .=
  td_button("./hr_attendance_search.php",
                                  "Attendance Search");
  $t_data .=
  td_button("./cut_search.php", "Cut Search");
  $t_data .=
  td_button("./students_info.php","Student Info");
  $t_data .= get_menu_defaults_form("Attendance Search");
  $t_data .= "</tr>";
  return $t_data;
}


function get_menu_defaults_form ($parent)
{
  $page = "./defaults.php";
  $window_description =
"width=600,height=400,scrollbars=yes,resizable=yes,status=yes";

  $form_html = <<<EOQ
<form method=post target="maindefwindow" 
action="./defaults.php"> 
<td align="center">
<input type=hidden name=menu_submit value="$parent">
<input type=submit class=green_button name=defaults value="Defaults"
onClick="window.open('$page','maindefwindow','$window_description')">
</td>
</form>
EOQ;
  return $form_html;  
}


function get_menu_array()
{
  $submit_arr["Main"] = "main";
  $submit_arr["Student"] = "main";
  $submit_arr["Student Info"] = "main";
  $submit_arr["Edit student information"] = "main";
  $submit_arr["Dismiss"] = "main";
  $submit_arr["Attendance Search"] = "main";
  $submit_arr["Attendance search"] = "main";
  $submit_arr["Cut Search"] = "main";
  $submit_arr["Cut search"] = "main";
  return $submit_arr;
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
    $bottom = get_main_row($period, $defaults);

echo <<<EOQ
<DIV id=menu_div class=main_menu>
    <table border cellpadding="0" cellspacing="0" width="100%" 
bordercolor="#000000" bgcolor="#008000">
$bottom
    </table>
</div>
EOQ;
}


function get_rooms_assoc_r()
{
  $query = "select * from rooms";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $room_r = array();
  while ($row = mysql_fetch_row($result))
  {
    $room = $row[0];
    $room_r["$room"] = 1;
  }
  return $room_r;
}


function get_rooms_num_r()
{
  $query = "select * from rooms";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $room_r = array();
  while ($row = mysql_fetch_row($result))
  {
    $room_r[] = $row[0];
  }
  return $room_r;
}


function is_dismissed($id, $did)
{
  $query = "select * from dismiss where id = $id and date = $did";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result,0);
  $last_time = "0";
  $last_gone = "0";
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $time = $row['time'];
    $gone = $row['gone'];
    if ($time > $last_time)
    {
      $last_time = $time;
      $last_gone = $gone;
    }
  }
  return $last_gone;
}


function get_periods_num_r()
{
  $query = "select * from periods";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $p_r = array();
  while ($row = mysql_fetch_row($result))
  {
    $p_r[] = $row[0];
  }
  return $p_r;
}


function get_max_date_assoc_r()
{
  $query = "select date, MAX($id) from dates";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $row =  mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function get_teacherID_num_r()
{
  $query = "select tid from teachers";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $tid_r = array();
  while ($row = mysql_fetch_row($result))
  {
    $tid_r[] = $row[0];
  }
  return $tid_r;
}


function get_hroom_teacherID_assoc_r()
{
  $query = "select hr, tid from teachers";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $room_tid_r = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $room = $row['hr'];
    $room_tid_r["$room"] =  $row['tid'];
  }
  return $room_tid_r;
}


function get_teacherID_hroom_assoc_r()
{
  $query = "select hr, tid from teachers";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $tid_room_r = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $tid =  $row['tid'];
    $tid_room_r["$tid"] =  $row['hr'];
  }
  return $tid_room_r;
}


function array2select($select_r,$selected="",$name)
{
  $select_html  = <<<EOQ
<SELECT name="$name"><OPTION SELECTED>$selected </OPTION>
EOQ;
  while (list(,$val) = each($select_r))
  {
    if($selected != $val)
      $select_html .= "<OPTION>" . $val . "</OPTION>";
  }
  $select_html .= "</SELECT>";
  return $select_html;
}


function change_periodsHR($periods)
{
   $pos = strpos($periods, "|HR~");
   $str1 = substr($periods, 0, $pos);
   $str2 = substr($periods, $pos + 4);
   $pos = strpos($str2, "|");
   $str2 = substr($str2, $pos);
   $periods = $str1 . "|HR~$newtid" . $str2;
   return $periods;
}


function get_date_settings_r()
{
  $defaults_r = array();
  $query = "select * from date_settings";
  $result = mysql_query($query);
// query_outcome_echo($query,$result,0);
  $row = mysql_fetch_array($result, MYSQL_ASSOC);

  $defaults_r['q1_start'] = $row['q1_start'];
  $defaults_r['q1_end'] = $row['q1_end'];
  $defaults_r['q2_start'] = $row['q2_start'];
  $defaults_r['q2_end'] = $row['q2_end'];
  $defaults_r['q3_start'] = $row['q3_start'];
  $defaults_r['q3_end'] = $row['q3_end'];
  $defaults_r['q4_start'] = $row['q4_start'];
  $defaults_r['q4_end'] = $row['q4_end'];

  $defaults_r['search_start'] = $row['search_start'];
  $defaults_r['search_end'] = $row['search_end'];

  return $defaults_r;
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


// A=65,Z=90  a=97,z=122, '=39, .=46
function is_alpha_enddot_quote($str = "", $end_dot=0, $quote=0)
{
  $length = strlen($str);
  if ($length < 1) return FALSE;
  if ($end_dot) $length--;
  $i = 0;

  for ($i; $i<$length; $i++)
  { 
    $char = substr($str, $i, 1);
    $char_val = ord($char);
    if($quote)
    {
      if (!(($char_val > 64 && $char_val < 91) ||
         ($char_val > 96 && $char_val < 123)))
      {
        if ($char_val == 39)
            return 39;
        else return 0;
      }
    }
    else
    {
      if (!(($char_val > 64 && $char_val < 91) ||
         ($char_val > 96 && $char_val < 123)) )
         return 0;
    }
  }

  if ($end_dot) 
  {
    $char = substr($str, $i, 1);
    $char_val = ord($char);
    if($quote)
    {
      if (!(($char_val > 64 && $char_val < 91) ||
         ($char_val > 96 && $char_val < 123) || $char_val == 46))
      {
        if ($char_val == 39)
            return 39;
        else return 0;
      }
    }
    else
    {
      if (!(($char_val > 64 && $char_val < 91) ||
         ($char_val > 96 && $char_val < 123) || $char_val == 46))
         return 0;
    }
  }
  return 1;
}

?>
