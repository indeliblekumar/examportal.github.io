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


function end_html_exit()
{
  echo "</DIV></BODY></HTML>";
  exit(1);
}


function back_2_parentA($parent)
{
  echo "<b>";
  if ($parent == "setup")
  echo <<<EOQ
<a href="./setup_gnuschool.php"
>Go to the main gnuschool setup page.</a>
EOQ;
  elseif ($parent == "admin_options")
  echo <<<EOQ
<a href="./admin_options.php"
>Go to the database administration page.</a>
EOQ;
  elseif ($parent == "setup_steps")
  echo <<<EOQ
<a href="./setup_steps.php"
>Go to the setup steps page.</a>
EOQ;
  elseif ($parent == "display")
  echo <<<EOQ
<a href="./display_files.php"
>Go to the view files page.</a>
EOQ;
  else
    if ($parent == "password")
  echo <<<EOQ
<a href="./new_password.php"
>Go back to the new password page.</a>
EOQ;
  echo "</b>";
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


function start_body($colors="main_background")
{
echo <<<EOQ
</head>
<body class="$colors">
EOQ;
}


function escape_single_quotes(&$post_r)
{
  while (list ($key ,$value) = each($post_r))
  {
    $value = str_replace("'", "\'", $value);
    $post_r["$key"] = $value;
  }
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


function td_get_form($file, $text, $parent="")
{
  if (!empty($parent))
    $parent =<<<EOQ
<input type=hidden name=parent value="$parent">
EOQ;

  echo <<<EOQ
<form method=post action="$file"> 
<td align="left">
$parent
<input type=submit name=submit value="$text">
</td>
</form>
EOQ;
}


// -=45  .=46  0=48,9=57  A=65,Z=90  _=95  a=97,z=122
function is_valid_file_name($str = "")
{
  $str = trim($str);
  $length = strlen($str);
  if ($length < 1) return FALSE;

  $char = substr($str, 0, 1);
  $char_val = ord($char);
  if (!(($char_val > 64 && $char_val < 91) ||
      ($char_val > 96 && $char_val < 123)) )
      return 0;

  for ($i=1; $i<$length; $i++)
  { 
    $char = substr($str, $i, 1);
    $char_val = ord($char);
    if (!(($char_val > 47 && $char_val < 58) || 
        ($char_val > 64 && $char_val < 91) ||
        ($char_val > 96 && $char_val < 123) ||
        $char_val == 95 || $char_val == 45 || $char_val == 46) )
        return 0;  
  }
  return 1;
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


function file_settings_assoc_r()
{
  $query = "select * from file_settings";
  $result = mysql_query($query);
  query_outcome_echo ( $query, $result);

  if($result === FALSE)
    return 0;

  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function file_settings_file_cols_assoc_r()
{
  $query = "select col_id,col_all_name,col_fname,col_mname,col_lname,
            col_pe,col_hr from file_settings";
  $result = mysql_query($query);
  query_outcome_echo ( $query, $result);

  if($result === FALSE)
    return 0;

  $row = mysql_fetch_array($result, MYSQL_ASSOC);

  return $row;
}


//this method of checking file lines used by im_txt_2_main_stu.php
function used_cols_in_line($def, $line, $delim)
{
  $col_r = array();

  $line = "$delim" . $line . "$delim";
  $str = "$delim" . $def['col_id'] . "$delim";
  if(strpos($line, $str) !== FALSE)
  {
    $col_r['id'] = $def['col_id'];
  }

  $str = "$delim" . $def['col_hr'] . "$delim";
  if(strpos($line, $str) !== FALSE)
  {
    $col_r['hr'] = $def['col_hr'];
  }

  $str = "$delim" . $def['col_all_name'] . "$delim";
  if(strpos($line, $str) === FALSE)
  {
    $col_r['fname'] = $def['col_fname'];
    $col_r['mname'] = $def['col_mname'];
    $col_r['lname'] = $def['col_lname'];
  }
  else
  {
    $col_r['all_name'] = $def['col_all_name'];
  }

  $str = "$delim" . $def['col_pe'] . "$delim";
  if(strpos($line, $str) !== FALSE)
  {
    $col_r['pe'] = $def['col_pe'];
  }

  return $col_r;
}


//this method of checking file lines used by im_txt_2_main_stu.php
function match_and_order($used_col_r, $line_r, $line_size)
{
  $match = 0;
  $new_r = array('id'=>FALSE,'all_name'=>FALSE,'fname'=>FALSE,
  'mname'=>FALSE,'lname'=>FALSE,'pe'=>FALSE,'hr'=>FALSE);

  while (list ($col ,$name1) = each($used_col_r))
  {
    $match = 0;
    for ($i=0; $i< $line_size; $i++)
    { 
      $name2 = $line_r[$i];
      if ( $name1 == $name2)
      {
        $match = 1;
        $new_r["$col"]=$i;
        $i = $line_size;
      }
    }
    if (!$match) break;
  }

  if ($match)
  {
    return $new_r;
  }
  else
    return FALSE;
}


//this method of checking file lines used by im_txt_2_main_stu.php
function fsettings_match_line_r($def_r,$line_col_r,$line_cols,$line,$delim)
{
  $line_col_r = explode("$delim", $line);
  $line_col_r = trim_array($line_col_r);
  $line_cols = sizeof($line_col_r);

  $used_col_r = used_cols_in_line($def_r,$line,$delim);

  $used_size = sizeof($used_col_r);

  // does file line match the file_settings column names ?
  // if match, array key=column name, value=position in file line
  // if no match return FALSE
  $col_pos_r = match_and_order($used_col_r, $line_col_r, $line_cols);

  if($col_pos_r === FALSE)
  {
    echo "<b> The default column names do not match with the text file 
        column names.</b><br><br>";    
    back_2_parentA("setup_steps");
    end_html_exit();
  }
  else
  {
    echo "<b> The file settings column names match with the text file column 
            names. Continuing...</b><br><br>";    
  }
  return $col_pos_r;
}


//method used by im_txt_2_teac_stu.php and im_txt_2_teac_pri.php
function match_col_names($def_r,$line_r)
{
  $match = 1;
  while (list ($val,$pos) = each($line_r))
  {
    $str = $def_r["$val"];
    if(empty($str))
    {
      $match = 0;
      break;
    }
  }
  return $match;
}


//method used by im_txt_2_teac_stu.php and im_txt_2_teac_pri.php
function match_and_order_col_names($def_r,$line_r)
{
  $new_r = array();
  $match = 1;
  while (list ($val,$pos) = each($line_r))
  {
    $str = $def_r["$val"];
    if(empty($str))
    {
      $match = 0;
      break;
    }
    else
    {
      $new_r["$str"] = $pos;
    }
  }

  if ($match)
  {
    return $new_r;
  }
  else//this file line does not match file settings column names, must be data
    return FALSE;
}


function user_rand($db)
{
  $user = "";

  $temp = 1;

  while ($temp)
  {
    $user = syllable_rand(1);
    $user .= syllable_rand();
    $user .= syllable_rand();
    $query="select * from $db.users where user='$user'";
    $result = mysql_query($query);
    query_outcome_echo($query,$result);
    if ($result === FALSE) return FALSE;
    if (mysql_num_rows($result) === 0) $temp = 0;
  }
  return $user;
}


function pword_rand()
{
  $pwd = syllable_rand(1);
  $pwd .= syllable_rand();
  $pwd .= rand(1,99);

  if(rand(0,1))
    $pwd .= '-';
  else
    $pwd .= '_';

  $pwd .= syllable_rand();
  $pwd .= syllable_rand();
  $pwd .= rand(1,99);
  return $pwd;
}


function proceed_form($parent="")
{
  echo <<<EOQ
<form method=post action="$PHP_SELF">
<input type=hidden name=parent value="$parent">
<input type=submit name=proceed value="Proceed">
</form>
EOQ;
}


function exit_no_mysql_connect($tries,$mdb_o_udb, $parent)
{
  $comment = "Failure to connect to $mdb_o_udb MySQL server with correct 
              user name and password. Please see your system 
              administrator.";
  get_user_pword_login($tries, $comment, $parent);
  exit();
}


function get_periods_assoc_r()
{
  $query = "select period from periods";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $per_r = array(); $i=0;
  while ($row = mysql_fetch_row($result))
  {
    $period = $row[0];
    $per_r["$period"] = 1; $i++;
  }
  if($i)return $per_r;
  else return FALSE;
}


function get_periods_num_r()
{
  $query = "select period from periods";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $per_r = array(); $i=0;
  while ($row = mysql_fetch_row($result))
  {
    $period = $row[0];
    $per_r[] = "$period";
  }
  if($i)return $per_r;
  else return FALSE;
}


function get_rooms_assoc_r()
{
  $query = "select room from rooms";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $room_r = array(); $i=0;
  while ($row = mysql_fetch_row($result))
  {
    $room = $row[0];
    $room_r["$room"] = 1; $i++;
  }
  if($i)return $room_r;
  else return FALSE;
}


function get_rooms_num_r()
{
  $query = "select room from rooms";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $room_r = array(); $i=0;
  while ($row = mysql_fetch_row($result))
  {
    $room = $row[0];
    $room_r[]="$room"; $i++;
  }
  if($i)return $room_r;
  else return FALSE;
}


function get_hroom_tid_assoc_r()
{
  $query = "select * from teachers";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $teacher_r = array();
  while ($row = mysql_fetch_array($result))
  {
    $tid = $row['tid'];
    $hr = $row['hr'];
    $teacher_r["$hr"]="$tid";
  }
  return $teacher_r;
}


function insert_room($room)
{
  $query="insert into rooms (room) values ('$room')";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if($result) return 1;
  else return 0;
}


function get_hr_dates_num_r($tid, $date_clause)
{
  $query = "select distinct date from hr_attendance$tid where $date_clause";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $date_r = array();
  while ($row = mysql_fetch_row($result))
  {
    $date = $row[0];
    $date_r[]= "$date";
  }
  return $date_r;
}


function insert_period($period)
{
  $query="insert into periods (period) values ('$period')";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if($result) return 1;
  else return 0;
}

?>
