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


$update=$_POST['update'];
$parent=$_POST['parent'];

include('./login.php');

//  0=48,9=57
function all_nums($str = "")
{
  $str = trim($str);
  $length = strlen($str);
  if ($length < 1) return FALSE;

  for ($i=0; $i<$length; $i++)
  { 
    $char = substr($str, $i, 1);
    $char_val = ord($char);
    if (!($char_val > 47 && $char_val < 58) )
        return 0;
  }
  return 1;
}


// A=65,Z=90  a=97,z=122
function all_alpha($str = "")
{
  $length = strlen($str);
  if ($length < 1) return FALSE;

  for ($i=0; $i<$length; $i++)
  { 
    $char = substr($str, $i, 1);
    $char_val = ord($char);
    if (!(($char_val > 64 && $char_val < 91) ||
       ($char_val > 96 && $char_val < 123)) )
       return 0;
  }
  return 1;
}


function is_valid_file_dir($str)
{
  $valid = 1;
  $str=trim($str);

  $char = substr($str, 0, 1);
  if($char == '/') $str = substr($str, 1);

  $char = substr($str, -1);
  if($char == '/') $str = substr($str, 0, -1);

  $dir_r = explode("/", $str);

  if(empty($dir_r))
  {
    $valid = is_valid_file_name($str);
  }
  else
  {
    $r_size = count($dir_r);
    for ($i=0; $i<$r_size; $i++)
    {
      $cur_dir = $dir_r[$i];
      if (!($cur_dir == '.' || $cur_dir == '..'))
      {
        if (empty($cur_dir))
        {
          $valid = 0; $i = $r_size;
        }
        else
        {
          $valid = is_valid_file_name($cur_dir);
          if ( !$valid ) $i = $r_size;
        }
      }
    }
  }
  return $valid;
}


function post2new_file_settings_r(&$def_r)
{
  $file_name = trim($_POST['file_name']);
  $file_dir = trim($_POST['file_dir']);
  $field_delim = trim($_POST['field_delim']);
  $line_delim = trim($_POST['line_delim']);

  $col_all_name = trim($_POST['col_all_name']);
  $col_all_name_opt = trim($_POST['col_all_name_opt']);//output file option
  $col_id = trim($_POST['col_id']);
  $col_id_opt = trim($_POST['col_id_opt']);//output file option
  $col_fname = trim($_POST['col_fname']);
  $col_mname = trim($_POST['col_mname']);
  $col_lname = trim($_POST['col_lname']);
  $col_hr = trim($_POST['col_hr']);
  $col_hr_opt = trim($_POST['col_hr_opt']);//output file option
  $col_pe = trim($_POST['col_pe']);
  $col_pe_opt = trim($_POST['col_pe_opt']);//output file option

if(!empty($file_name))
  if ((strlen($file_name) > 28) || (!is_valid_file_name($file_name)))
  { print"<br><b><font color='#ff0000'>
          Bad file name $file_name </font></b>";
    $file_name = FALSE;
  }

if(!empty($file_dir))
  if ((strlen($file_dir) > 48) || (!is_valid_file_dir($file_dir))) 
  { print"<br><b><font color='#ff0000'>
          Bad file directory $file_dir </font></b>";
    $file_dir = FALSE;
  }

if ((strlen($line_delim) > 5)) 
    $line_delim = FALSE;
if ((strlen($field_delim) > 5)) 
    $field_delim = FALSE;

  if ( $col_all_name_opt != "yes" )
    $col_all_name_opt = "no";

  if ((strlen($col_all_name) > 10) || 
                             (!is_valid_file_name($col_all_name)) )
  { print"<br><b><font color='#ff0000'>
          Bad full name column $col_all_name </font></b>";
    $col_all_name = FALSE;
  }
  else
  {
    if($col_all_name==$col_id || $col_all_name==$col_fname ||
       $col_all_name==$col_mname || $col_all_name==$col_lname ||
       $col_all_name==$col_hr || $col_all_name==$col_pe)
      return -2;
  }

  if ( $col_id_opt != "yes" )
    $col_id_opt = "no";

  if ((strlen($col_id) > 10) || (!is_valid_file_name($col_id)) )
  { print"<br><b><font color='#ff0000'>
          Bad ID column $col_id </font></b>";
    $col_id = FALSE;
  }
  else
  {
    if($col_id==$col_all_name || $col_id==$col_fname ||
       $col_id==$col_mname || $col_id==$col_lname ||
       $col_id==$col_hr || $col_id==$col_pe)
      return -2;
  }

  if ((strlen($col_fname) > 10) || (!is_valid_file_name($col_fname)) )
  { print"<br><b><font color='#ff0000'>
          Bad first name column $col_fname </font></b>";
    $col_fname = FALSE;
  }
  else
  {
    if($col_fname==$col_id || $col_fname==$col_all_name ||
       $col_fname==$col_mname || $col_fname==$col_lname ||
       $col_fname==$col_hr || $col_fname==$col_pe)
      return -2;
  }

  if ((strlen($col_mname) > 10) || (!is_valid_file_name($col_mname)) )
  { print"<br><b><font color='#ff0000'>
          Bad middle name column $col_mname </font></b>";
    $col_mname = FALSE;
  }
  else
  {
    if($col_mname==$col_id || $col_mname==$col_fname ||
       $col_mname==$col_all_name || $col_mname==$col_lname ||
       $col_mname==$col_hr || $col_mname==$col_pe)
      return -2;
  }

  if ((strlen($col_lname) > 10) || (!is_valid_file_name($col_lname)) )
  { print"<br><b><font color='#ff0000'>
          Bad last name column $col_lname </font></b>";
    $col_lname = FALSE;
  }
  else
  {
    if($col_lname==$col_id || $col_lname==$col_fname ||
       $col_lname==$col_mname || $col_lname==$col_all_name ||
       $col_lname==$col_hr || $col_lname==$col_pe)
      return -2;
  }

  if ( $col_hr_opt != "yes" )
    $col_hr_opt = "no";

  if ((strlen($col_hr) > 10) || (!is_valid_file_name($col_hr)) )
  { print"<br><b><font color='#ff0000'>
          Bad home room column $col_hr </font></b>";
    $col_hr = FALSE;
  }
  else
  {
    if($col_hr==$col_id || $col_hr==$col_fname ||
       $col_hr==$col_mname || $col_hr==$col_lname ||
       $col_hr==$col_all_name || $col_hr==$col_pe)
      return -2;
  }

  if ( $col_pe_opt != "yes" )
    $col_pe_opt = "no";

  if ((strlen($col_pe) > 10) || (!is_valid_file_name($col_pe)) )
  { print"<br><b><font color='#ff0000'>
          Bad period column $col_pe </font></b>";
    $col_pe = FALSE;
  }
  else
  {
    if($col_pe==$col_id || $col_pe==$col_fname ||
       $col_pe==$col_mname || $col_pe==$col_lname ||
       $col_pe==$col_hr || $col_pe==$col_all_name)
      return -2;
  }

  if ($file_name !== FALSE) $def_r['file_name'] = $file_name;
  if ($file_dir !== FALSE) $def_r['file_dir'] = $file_dir;
  if ($line_delim !== FALSE) $def_r['line_delim'] = $line_delim;
  if ($field_delim !== FALSE) $def_r['field_delim'] = $field_delim;

  if ($col_all_name_opt !== FALSE) $def_r['col_all_name_opt'] = $col_all_name_opt;
  if ($col_all_name !== FALSE) $def_r['col_all_name'] = $col_all_name;
  if ($col_id !== FALSE) $def_r['col_id'] = $col_id;
  if ($col_id_opt !== FALSE) $def_r['col_id_opt'] = $col_id_opt;
  if ($col_fname !== FALSE) $def_r['col_fname'] = $col_fname;
  if ($col_mname !== FALSE) $def_r['col_mname'] = $col_mname;
  if ($col_lname !== FALSE) $def_r['col_lname'] = $col_lname;
  if ($col_hr !== FALSE) $def_r['col_hr'] = $col_hr;
  if ($col_hr_opt !== FALSE) $def_r['col_hr_opt'] = $col_hr_opt;
  if ($col_pe !== FALSE) $def_r['col_pe'] = $col_pe;
  if ($col_pe_opt !== FALSE) $def_r['col_pe_opt'] = $col_pe_opt;
  return 1;
}


function set_file_settings($def)
{
  $file_name = $def['file_name'];
  $file_dir = $def['file_dir'];
  $line_delim = $def['line_delim'];
  $field_delim = $def['field_delim'];

  $col_all_name_opt  = $def['col_all_name_opt'];
  $col_all_name  = $def['col_all_name'];
  $col_id_opt = $def['col_id_opt'];
  $col_id = $def['col_id'];
  $col_fname = $def['col_fname'];
  $col_mname = $def['col_mname'];
  $col_lname = $def['col_lname'];
  $col_hr_opt = $def['col_hr_opt'];
  $col_hr = $def['col_hr'];
  $col_pe_opt = $def['col_pe_opt'];
  $col_pe = $def['col_pe'];

  $query =<<<EOQ
update file_settings set
file_name='$file_name',
file_dir='$file_dir',
line_delim='$line_delim',
field_delim='$field_delim',
col_all_name_opt='$col_all_name_opt',
col_all_name='$col_all_name',
col_id_opt='$col_id_opt',
col_id='$col_id',
col_fname='$col_fname',
col_mname='$col_mname',
col_lname='$col_lname',
col_hr_opt='$col_hr_opt',
col_hr='$col_hr',
col_pe_opt='$col_pe_opt',
col_pe='$col_pe'
EOQ;
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
}


function data_transfer_form($def, $parent)
{
  $file_name = $def['file_name'];
  $file_dir = $def['file_dir'];
  $line_delim = $def['line_delim'];
  $field_delim = $def['field_delim'];

  $name_opt = $def['col_all_name_opt'];
  $id_checked = "";
  $name_checked = "";
  $hr_checked = "";
  $id_opt = $def['col_id_opt'];
  $name_opt = $def['col_all_name_opt'];
  $hr_opt = $def['col_hr_opt'];
  $pe_opt = $def['col_pe_opt'];
  if ($id_opt == "yes") $id_checked = "checked";
  if ($name_opt == "yes") $name_checked = "checked";
  if ($hr_opt == "yes") $hr_checked = "checked";
  if ($pe_opt == "yes") $pe_checked = "checked";
  $all_name = $def['col_all_name'];
  $id = $def['col_id'];
  $fname = $def['col_fname'];
  $mname = $def['col_mname']; 
  $lname = $def['col_lname'];
  $hr = $def['col_hr'];
  $pe = $def['col_pe'];


  echo <<<EOQ
<form method="post" action="$PHP_SELF">
<br><br><hr>
<b>File name and directory used to import or export data.</b><br>
File name: <input type=text name="file_name"
value="$file_name" size=38 maxlength=28>
<br><br>
File directory: <input type=text name="file_dir"
value="$file_dir" size=84 maxlength=80 disabled>
<br><br><hr>
<b>Delimiters for text files.</b><br>
Field delimiter: <input type=text name="field_delim"
value="$field_delim" size=8 maxlength=5>
<br><br><hr>
<b>Column names used in the import or export file.</b><br>
ID (an identification number) column: <input type=text name="col_id"
value="$id" size=15 maxlength=10>
<br><br>
Full name column: <input type=text name="col_all_name"
value="$all_name" size=15 maxlength=10>
<br><br>
First name column: <input type=text name="col_fname"
value="$fname" size=15 maxlength=10>
<br><br>
Middle name column: <input type=text name="col_mname"
value="$mname" size=15 maxlength=10>
<br><br>
Last name column: <input type=text name="col_lname"
value="$lname" size=15 maxlength=10>
<br><br>
Room name column: <input type=text name="col_hr"
value="$hr" size=15 maxlength=10>
<br><br>
Block name column: <input type=text name="col_pe"
value="$pe" size=15 maxlength=10>
<br><br>
<hr>
<input type=hidden name="parent" value="$parent">
<input type=submit name="update" value="Update Settings">
</form>
EOQ;

/*
The following settings do not work.
Currently there is no code to export data to a file.

<b>Column settings used with the export file:</b><br>
<input type=checkbox name=col_id_opt value="yes" $id_checked>
Use a student ID column.
<br><br>
<input type=checkbox name=col_all_name_opt value="yes" $name_checked>
Use a full name column.
<br><br>
<input type=checkbox name=col_hr_opt value="yes" $hr_checked>
Use a room column.
<br><br>
<input type=checkbox name=col_pe_opt value="yes" $pe_checked>
Use a block or period column.
<br><br>
*/

}


$tag_title = "gnuschool.org Settings";
$page_title = "Settings";


start_html($tag_title);

stylesheet_link("../style_sheet");

start_body("main_background");
back_2_parentA($parent);


$comment = "These are the data input and data output file settings.
   <br>To change them, please edit the form and click the update button.";
title_comment_div_noend($page_title, $comment);

$defaults_r = file_settings_assoc_r();

if(!empty($update))
{
  $success = post2new_file_settings_r($defaults_r);
  if($success==-2)
    print"<br><b><font color='#ff0000'>
          Duplicate column names are not allowed.
          Please try again. </font></b>";
  else
   set_file_settings($defaults_r);
}
data_transfer_form($defaults_r, $parent);
back_2_parentA($parent);

echo "</DIV>";
?>
</body></html>
