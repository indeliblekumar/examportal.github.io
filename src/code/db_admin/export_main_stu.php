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

include('./login.php');

$yes_input = 1;

if(empty($proceed))
  $yes_input = 0;


function used_export_cols($def_cols_r)
{
  $col_id_opt = $def_cols_r['col_id_opt'];
  $col_hr_opt = $def_cols_r['col_hr_opt'];
  $col_all_name_opt = $def_cols_r['col_all_name_opt'];
  $col_id_val = $def_cols_r['col_id'];
  $col_fname_val = $def_cols_r['col_fname'];
  $col_mname_val = $def_cols_r['col_mname'];
  $col_lname_val = $def_cols_r['col_lname'];
  $col_hr_val = $def_cols_r['col_hr'];
  $col_all_name_val = $def_cols_r['col_all_name'];

  $col_id_opt = trim($col_id_opt);
  $col_hr_opt = trim($col_hr_opt);
  $col_all_name_opt = trim($col_all_name_opt);
  $col_id_val = trim($col_id_val);
  $col_fname_val = trim($col_fname_val);
  $col_mname_val = trim($col_mname_val);
  $col_lname_val = trim($col_lname_val);
  $col_hr_val = trim($col_hr_val);
  $col_all_name_val = trim($col_all_name_val);
  $col_r = array();
  if($col_all_name_opt == "no")
  {
    $col_r[] = array('col_fname', $col_fname_val);
    $col_r[] = array('col_mname', $col_mname_val);
    $col_r[] = array('col_lname', $col_lname_val);
  }
  else
  {
    $col_r[] = array('col_all_name', $col_all_name_val);
  }    

  if($col_hr_opt == "yes")
  {
    $col_r[] = array('col_hr', $col_hr_val);
  }

  if($col_id_opt == "yes")
  {
    $col_r[] = array('col_id', $col_id_val);
  }
  return $col_r;
}


$tag_title = "gnuschool.org Export Data to Text File";
$page_title = "Export data to a text file";


start_html($tag_title);
stylesheet_link("../style_sheet");
start_body("main_background");
back_2_parentA("setup");

  $comment = "Work in progress.";
  title_comment_div_noend($page_title, $comment);
  back_2_parentA("setup");
  end_html_exit();
?>
