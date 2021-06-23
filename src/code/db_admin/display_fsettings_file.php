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

$proceed=$_POST['proceed'];
$parent=$_POST['parent'];

$yes_input = 1;

if(empty($proceed))
  $yes_input = 0;

include('./login.php');


// -=45  .=46  0=48,9=57  A=65,Z=90  _=95  a=97,z=122
function is_ascii_char($char = '')
{
  $char_val = ord($char);
  if ($char_val > 31 && $char_val < 127)
    return 1;
  else 
    return 0;
}


$tag_title = "gnuschool.org Display Default File";
$page_title = "Display Default File";


start_html($tag_title);
stylesheet_link("../style_sheet");
start_body("main_background");
back_2_parentA($parent);

$defcol2val_r = file_settings_assoc_r();

if (empty($defcol2val_r))
{
  $comment= "The file settings are empty.";
  title_comment_div_noend($page_title, $comment);
  back_2_parentA("$parent");
  end_html_exit();
}

$file_name = $defcol2val_r['file_name'];
if(empty($file_name))
{
  $comment = "The file name is empty. There is no file name
                   in the file settings.";
  title_comment_div_noend($page_title, $comment);
  back_2_parentA("$parent");
  end_html_exit();
}

//only ../../data_text_files/ directory has access
//$file_dir = $defcol2val_r['file_dir'];

$file_dir = '../../data_text_files/';

$path_file = "$file_dir" . "$file_name";

if (!$yes_input)
{
  $comment = "Display the file $path_file";
  title_comment_div_noend($page_title, $comment);
  echo "<b>Only text and dbase files are supported.</b>";
  proceed_form($parent);
  back_2_parentA($parent);
  end_html_exit();
}

  
$comment = "Attempting to display the file in file settings...";
title_comment_div_noend($page_title, $comment);


if ( file_exists($path_file) === FALSE )
{
  echo "<b>Error $path_file doesn't exist.</b></br><br>";
  back_2_parentA($parent);
  end_html_exit();    
}

$r_h = fopen($path_file, "r");

if ($r_h === FALSE)
{
  echo "<b>Error opening default file $path_file</b><br><br>";
  back_2_parentA($parent);
  end_html_exit();
}
  
$char1 = fgetc($r_h); 
fclose($r_h);

if (is_ascii_char($char1))
{
  $lines_all_r = file("$path_file");

  if ($lines_all_r === FALSE)
  {
    echo "<b>Error opening text file $path_file</b><br><br>";
    back_2_parentA($parent);
    end_html_exit();
  }
  $lines_size = count($lines_all_r);


  echo "<br><b>This is the text file $path_file</b></br>";

  for($i=0; $i< $lines_size; $i++)
  {
    $line = $lines_all_r[$i];
    echo htmlspecialchars($line);
    echo "<br>";
  }
}
else
{
  $dbf = dbase_open($path_file, 0);

  if ($dbf === FALSE)
  {
    echo "<b>Error opening dbase $path_file</b><br><br>";
    end_html_exit();
  }

  $column_info = dbase_get_header_info($dbf);

  if ($column_info === FALSE)
  {
    echo "<b>Error obtaining header information from the dbase 
         $path_file</b><br><br>";
    end_html_exit();
  }
  
  echo "<br><b>This record is the column/header data for 
        $path_file</b><br>";
  print_r($column_info);
  print "<br><br>";
  $record_numbers = dbase_numrecords($dbf);
  if ($record_numbers === FALSE)
  {
    echo "<b>Error obtaining record numbers from the dbase 
          $path_file</b><br><br>";
    end_html_exit();
  }

  echo "<b>These are the records.</b><br><br>";
  for ($i=1; $i<$record_numbers + 1; $i++)
  {
    $row = dbase_get_record_with_names($dbf, $i);
    if ($row === FALSE)
    {
      echo "<b>Error obtaining record with names from the dbase 
           $path_file</b><br>";
    }
    print_r($row);
    print "<br><br>";
  }
}
echo "<br><br>";
back_2_parentA($parent);
echo "</DIV>";

?>

</body></html>
