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

$proceed = $_POST['proceed'];

include('./login.php');

$yes_input = 1;

if(empty($proceed))
  $yes_input = 0;


function trim_array($old_r)
{
  $length = sizeof($old_r);
  $new_r = array();
  for($i=0; $i<$length; $i++)
  {
    $val = $old_r[$i];
    $val = trim($val);
    if (!empty($val)) $new_r[] = $val;
  }
  return $new_r;
}


function get_name_r($name)
{
  $name_r = array();
  $temp_r = explode(" ", $name);
  for ($i=0; $i<sizeof($temp_r); $i++)
  { 
    $name_part = $temp_r[$i];
    $name_part = trim($name_part);
    if(!empty($name_part))
      $name_r[] = $name_part;
  }
  if(sizeof($name_r) == 2) 
  {
    $name_r[2] = $name_r[1];
    $name_r[1] = "";
  }
  if(sizeof($name_r) != 3)
    return FALSE;
  else
    return $name_r;
}


function query_id($id)
{
  $query = "select id from stu_blocks where id = $id";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  $num = mysql_num_rows($result);
  return $num;
}


function query_name($fname, $mname="", $lname)
{
  $mname_str = "";
  if (!empty($mname)) $mname_str = " and mname='$mname' ";
  $query = "select id from stu_blocks where
            fname='$fname' $mname_str and lname='$lname'";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  $num = mysql_num_rows($result);
  return $num;
}


$tag_title = "gnuschool.org Edit Database With Text File";
$page_title = "Edit database with a text file";


start_html($tag_title);
stylesheet_link("../style_sheet");
start_body("main_background");
back_2_parentA("setup_steps");

$def_col_r = file_settings_assoc_r();

if (empty($def_col_r))
{
  echo "<br>no default settings found";
  back_2_parentA("setup_steps");
  end_html_exit();
}

$file_name = $def_col_r['file_name'];
if(empty($file_name))$file_name = "students.txt";

$file_dir = $def_col_r['file_dir'];
$path_file = "$file_dir" . "$file_name";
$delim = $def_col_r['field_delim'];

if (!$yes_input)
{
  $comment = "Edit the database using file $path_file
        <br>The file must contain student information only.";
  title_comment_div_noend($page_title, $comment);
  echo "<ul><b>
       <LI>The first line will have the student column names.
       <LI>Student data must follow the student column names.</b>
       <LI>See sample below.</ul>";

echo "<TABLE>
<TR><TD>FIRSTNAME&nbsp</TD><TD>MIDDLENAME&nbsp</TD><TD>LASTNAME&nbsp</TD>
<TD>HOMEROOM</TD></TR>
<TR><TD>Toloro</TD><TD>Zorro</TD><TD>Senoro</TD><TD>A193</TD></TR>
<TR><TD>Flanito</TD><TD>X.</TD><TD>Pamplos</TD><TD>A193</TD></TR>
<TR><TD>John</TD><TD>H.</TD><TD>Smith</TD><TD>A193</TD></TR>
<TR><TD>Mary</TD><TD>B.</TD><TD>Doe</TD><TD>R462</TD></TR>
<TR><TD>Jean</TD><TD>Lucid</TD><TD>Estudiante</TD><TD>R462</TD></TR>
</TABLE>";

  proceed_form();
  back_2_parentA("setup_steps");
  end_html_exit();
}

$comment = "Attempting to edit the database using the default text file...";
title_comment_div_noend($page_title, $comment);


if ( file_exists($path_file) === FALSE )
{
  echo "<b>Error $path_file doesn't exist!</b></br><br>";
  back_2_parentA("setup_steps");
  end_html_exit();    
}

$lines_all_r = file("$path_file");
if ($lines_all_r === FALSE)
{
  echo "<b>Error opening text file!</b><br><br>";
  back_2_parentA("setup_steps");
  end_html_exit();
}

$line = "";
$index = 0;
$lines_size = sizeof($lines_all_r);

for($i=0; $i< $lines_size; $i++)
{
  $line = $lines_all_r[$i];
  $line = trim($line);
  if (!empty($line))
  {
    $index = $i;
    $i = $lines_size;
  }
}
$index++;

if (empty($line))
{
  echo "<b>Empty file!</b><br><br>";
  back_2_parentA("setup_steps");
  end_html_exit();
}

$line_col_r = explode("$delim", $line);
$line_col_r = trim_array($line_col_r);
$line_cols = sizeof($line_col_r);
//print"<br>def_col_r "; print_r($def_col_r);print"<br>";
//print"<br>line_col_r "; print_r($line_col_r);print"<br>";

$col_pos_r=fsettings_match_line_r($def_col_r,$line_col_r,$line_cols,$line,$delim);
//print"<br>col_pos_r ";print_r($col_pos_r);print"<br>";

$pos_id = $col_pos_r['id'];
$pos_all_name = $col_pos_r['all_name'];
$pos_fname = $col_pos_r['fname'];
$pos_mname = $col_pos_r['mname'];
$pos_lname = $col_pos_r['lname'];
$pos_pe = $col_pos_r['pe'];
$pos_hr = $col_pos_r['hr'];


$yes_id = 0;
$yes_all_name = 0;
$yes_fname = 0;
$yes_mname = 0;
$yes_lname = 0;
$yes_pe = 0;
$yes_hr = 0;

if ( is_int($pos_id) )   $yes_id = 1;
if ( is_int($pos_all_name) )  $yes_all_name = 1;
if ( is_int($pos_fname) )  $yes_fname = 1;
if ( is_int($pos_mname) )  $yes_mname = 1;
if ( is_int($pos_lname) )  $yes_lname = 1;
if ( is_int($pos_pe) )  $yes_pe = 1;
if ( is_int($pos_hr) )  $yes_hr = 1;

if ( !$yes_all_name )
{
  if ( !($yes_fname && $yes_lname) )
  {
    echo "<b>The required first and last name fields are not 
             present.</b><br><br>";
    back_2_parentA("setup_steps");
    end_html_exit();
  }
}
else
{
  if ($yes_fname || $yes_lname)
  {
    echo "<b>There are too many name columns present.</b><br><br>";
    back_2_parentA("setup_steps");
    end_html_exit();
  }
}

$rooms_ass_r = array();
$rooms_ass_r = get_rooms_assoc_r();
if(!is_array($rooms_ass_r))
  $rooms_ass_r = array();

$edited = 0;
for ($i=$index; $i< $lines_size; $i++)
{
  $line = $lines_all_r[$i];
  $line_r = explode($delim, $line);
  $line_r = trim_array($line_r);
  $line_size = sizeof($line_r);

  if ($line_size != $line_cols)
  {
    echo "<b>SKIPPING:The number of default columns is not equal 
      to the number of columns in this line.</b><br>$line<br><br>";
  }
  else
  {
    $ok_line = 1;
    $id = "";
    $fname = "";
    $mname = "";
    $lname = "";
    $hr = "";
  
    if ($yes_all_name)
    {
      $all_name = $line_r[$pos_all_name];
      $name_r = get_name_r($all_name);
      if ($name_r === FALSE) 
      {
        $ok_line = 0;
        echo "<b>SKIPPING: Bad name $all_name.</b><br><br>";
      }
      else
      {
        $fname = $name_r[0];
        $mname = $name_r[1];
        $lname = $name_r[2];
      }
    }
    else
    {
      $fname = $line_r[$pos_fname];
      $mname = $line_r[$pos_mname];
      $lname = $line_r[$pos_lname];
      if (empty($fname) || empty($lname))
      {
        $ok_line = 0;
        echo "<b>SKIPPING: Bad name $fname $mname $lname.</b><br><br>";
      }
    }
      
    if ($ok_line && !is_alpha_enddot_quote($fname))
    {
        echo "<b>SKIPPING: Bad character found in the first name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
    }

    if ($ok_line && !is_alpha_enddot_quote($mname, 1))
    {
        echo "<b>SKIPPING: Bad character found in the middle name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
    }

    $lname_quote = is_alpha_enddot_quote($lname,0,1);
    if ($ok_line && $lname_quote == 0)
    {
        echo "<b>SKIPPING: Bad character found in the last name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
    }

    $ok_lname_quote = 0;
    if($lname_quote == 39)
    {
      $ok_lname_quote = 1;
      $lname = addslashes($lname);
    }

    if ($yes_id && $ok_line)
    {
      $id = $line_r[$pos_id];
      $temp = query_id($id);
      if ($temp)
      { 
          echo "<b>SKIPPING: ID $id already exists.</b><br><br>";
          $ok_line = 0;
      }
      else
      { 
        if($temp === FALSE) 
        {
          echo "<b>SKIPPING: MySQL was unable to process id 
               query.</b><br><br>";
          $ok_line = 0;
        }
        else
          echo "<b>CONTINUING: ID $id does not exist.</b><br>";
      }
    }

    $name_warn = "";
    if ($ok_line)
    {
      $temp = query_name($fname, "", $lname);
      if ($temp)
      {
        $name_warn = "<b>WARNING: $fname $lname exists.</b><br>";
        $temp = query_name($fname, $mname, $lname);
        if ($temp)
        {
          echo "<b>SKIPPING: $fname $mname $lname already 
                exists.</b><br><br>";
          $ok_line = 0;
        }
        else
        {
          if ($temp === FALSE)
          {
            echo "<b>SKIPPING: MySQL was unable to process names 
                  query.</b><br><br>";
            $ok_line = 0;
          }
          else
          {
            echo $name_warn;
            echo "<b>CONTINUING: $fname $mname $lname doesn't 
                exist.</b><br>";
          }
        }
      }
      else
      {
        echo "<b>CONTINUING: $fname $mname $lname doesn't 
            exist.</b><br>";
      }
    }

    if ($ok_line)
    {
      $query = "insert into stu_blocks (";
      $values = " values (";
      $comma = 0;
      
      if ($yes_id)
      {
        $query .= "id,";
        $values .= "$line_r[$pos_id],";
      }

      if ($ok_lname_quote)
      {
        $query .= "quote,";
        $values .= "'y',";
      }

      if ($yes_hr)
      {
        $room = $line_r[$pos_hr];
        $query .= "hr,";
        $values .= "'$room',";
        if($rooms_ass_r["$room"] != 1)
        {
          if(insert_room($room))
            $rooms_ass_r["$room"] = 1;
        }
      }       
      $query .= "fname,mname,lname)";

      $values .= "'$fname','$mname','$lname')";
      $query .= $values;
      $result = mysql_query($query);
      query_outcome_echo($query,$result);
      $edited = 1;
      echo "<br>";
    } 
  }
}
if ($edited)
  echo "<b>You have updated the database.</b><br><br>";
else
  echo "<b>No changes have been made to the database.</b><br><br>";

back_2_parentA("setup_steps");
echo "</DIV>";

?>
</body></html>
