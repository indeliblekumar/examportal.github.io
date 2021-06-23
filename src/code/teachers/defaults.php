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
include('./functions.php');
include ('./login.php');

$yes_input = 1;
$menu_submit=$_POST['menu_submit'];
if(empty($menu_submit)) $yes_input = 0;
$update_names=$_POST['update_names'];
$update_pwords=$_POST['update_pwords'];
$update_search=$_POST['update_search'];


function chars_are_nums($str = "")
{
  $str = trim($str);
  $char_r = array(1,1,1,1,1,1,1,1,1,1);
  $length = strlen($str);
  for ($i=0; $i<$length; $i++)
  { 
    $char = substr($str, $i, 1);
    if ( $char_r[$char] != 1 )
      $length = 0;
  }
  return $length;
}


function def_r_search_edit($search_r, &$def)
{
  $q1_start = $search_r['q1_start'];
  $q1_end = $search_r['q1_end'];
  $q2_start = $search_r['q2_start'];
  $q2_end = $search_r['q2_end'];
  $q3_start = $search_r['q3_start'];
  $q3_end = $search_r['q3_end'];
  $q4_start = $search_r['q4_start'];
  $q4_end = $search_r['q4_end'];
  $search_start = $search_r['search_start'];
  $search_end = $search_r['search_end'];


  if (!empty($q1_start)) $def['q1_start'] = $q1_start;
  if (!empty($q1_end)) $def['q1_end'] = $q1_end;
  if (!empty($q2_start)) $def['q2_start'] = $q2_start;
  if (!empty($q2_end)) $def['q2_end'] = $q2_end;
  if (!empty($q3_start)) $def['q3_start'] = $q3_start;
  if (!empty($q3_end)) $def['q3_end'] = $q3_end;
  if (!empty($q4_start)) $def['q4_start'] = $q4_start;
  if (!empty($q4_end)) $def['q4_end'] = $q4_end;
  if (!empty($search_start)) $def['search_start'] = $search_start;
  if (!empty($search_end)) $def['search_end'] = $search_end;
}


function def_r_post2name(&$def)
{
  $fname = $_POST['fname'];
  $fname_on = $_POST['fname_on'];
  $fname2 = $_POST['fname2'];
  $fname2_on = $_POST['fname2_on'];
  $mname = $_POST['mname'];
  $lname = $_POST['lname'];
  $lname_on = $_POST['lname_on'];
  $nline_f = $_POST['name_nline_f'];
  $nline_p = $_POST['name_nline_p'];
  $nline_m = $_POST['name_nline_m'];
  if (empty($nline_f)) $nline_f = "-";
  else $nline_f = "f";
  if (empty($nline_p)) $nline_p = "-";
  else $nline_p = "p";
  if (empty($nline_m)) $nline_m = "-";
  else $nline_m = "m";

  $name_lines  = $nline_f . $nline_p . $nline_m;

  if (empty($fname) && !empty($mname)) $def['seating_fname'] = 'n';
  else if(!(strlen($fname) > 2) && chars_are_nums($fname))
         $def['seating_fname'] = $fname;

  if (!empty($mname)) $def['seating_mname'] = $mname;

  if (empty($lname) && !empty($mname)) $def['seating_lname'] = 'n';
  else if(!(strlen($lname) > 2) && chars_are_nums($lname))
         $def['seating_lname'] = $lname;

  if (empty($fname2)) $def['seating_fname2'] = 'n';
  else if(!(strlen($fname2) > 2) && chars_are_nums($fname2))
         $def['seating_fname2'] = $fname2;

  if (empty($fname_on)) $def['seating_fname_on'] = 'n';
  else $def['seating_fname_on'] = $fname_on;

  if (empty($fname2_on)) $def['seating_fname2_on'] = 'n';
  else $def['seating_fname2_on'] = $fname2_on;

  if (empty($lname_on)) $def['seating_lname_on'] = 'n';
  else $def['seating_lname_on'] = $lname_on;

  if (!empty($name_lines)) $def['seating_name_lines'] = $name_lines;
}


function def_r_post2pwords(&$def)
{
  $tables_across = $_POST['tables_across'];
  $row_space = $_POST['row_space'];
  $column_space = $_POST['column_space'];
  $order = $_POST['order'];

  if (!empty($tables_across)) $def['pwords_tables_across'] = $tables_across;
  if (!empty($row_space)) $def['pwords_row_space'] = $row_space;
  if (!empty($column_space)) $def['pwords_column_space'] = $column_space;
  if (!empty($order)) $def['pwords_order'] = $order;
}


function set_defaults($tid, &$def)
{
  $fname = $def['seating_fname'];
  $fname_on = $def['seating_fname_on'];
  $fname2 = $def['seating_fname2'];
  $fname2_on = $def['seating_fname2_on'];
  $mname = $def['seating_mname'];
  $lname = $def['seating_lname'];
  $lname_on = $def['seating_lname_on'];
  $name_lines  = $def['seating_name_lines'];
  $search_start = $def['search_start'];
  $search_end = $def['search_end'];
  $q1_start = $def['q1_start'];
  $q1_end = $def['q1_end'];
  $q2_start = $def['q2_start'];
  $q2_end = $def['q2_end'];
  $q3_start = $def['q3_start'];
  $q3_end = $def['q3_end'];
  $q4_start = $def['q4_start'];
  $q4_end = $def['q4_end'];
  $tables_across = $def['pwords_tables_across'];
  $row_space = $def['pwords_row_space'];
  $column_space = $def['pwords_column_space'];
  $order = $def['pwords_order'];

  $table = "defaults" . "$tid";

  $query =<<<EOQ
update $table set
seating_fname='$fname',
seating_fname_on='$fname_on',
seating_fname2='$fname2',
seating_fname2_on='$fname2_on',
seating_mname='$mname',
seating_lname='$lname',
seating_lname_on='$lname_on',
seating_name_lines='$name_lines',
pwords_tables_across='$tables_across',
pwords_column_space='$column_space',
pwords_row_space='$row_space',
pwords_order='$order',
q1_start = '$q1_start',
q1_end = '$q1_end',
q2_start = '$q2_start',
q2_end = '$q2_end',
q3_start = '$q3_start',
q3_end = '$q3_end',
q4_start = '$q4_start',
q4_end = '$q4_end',
search_start = '$search_start',
search_end = '$search_end'
EOQ;
  mysql_query($query);
}


function get_pwords_form( $def, $menu_submit)
{
  $tables_across = $def['pwords_tables_across'];
  $row_space = $def['pwords_row_space'];
  $column_space = $def['pwords_column_space'];
  $order = $def['pwords_order'];

  $order_str = "";
  if ($order=="seat_x")
    $order_str =<<<EOQ
Order by: <select name=order>
<option value="lname">Last Name
<option value="fname">First Name
<option selected value="seat_x">Room Width
<option value="seat_y">Room Height
</select>
EOQ;
  elseif ($order=="seat_y")
    $order_str =<<<EOQ
Order by: <select name=order>
<option value="lname">Last Name
<option value="fname">First Name
<option value="seat_x">Room Width
<option selected value="seat_y">Room Height
</select>
EOQ;
  elseif ($order=="fname")
    $order_str =<<<EOQ
Order by: <select name=order>
<option value="lname">Last Name
<option selected value="fname">First Name
<option value="seat_x">Room Width
<option value="seat_y">Room Height
</select>
EOQ;
  elseif ($order=="lname")
    $order_str =<<<EOQ
Order by: <select name=order>
<option selected value="lname">Last Name
<option value="fname">First Name
<option value="seat_x">Room Width
<option value="seat_y">Room Height
</select>
EOQ;
  $submit = <<<EOQ
<br><br>
Tables across: <input type=text name="tables_across"
value="$tables_across" size=3 maxlength=2>
<br><br>
Row space: <input type=text name="row_space"
value="$row_space" size=3 maxlength=2>
<br><br>
Column space: <input type=text name="column_space"
value="$column_space" size=3 maxlength=2>
<br><br>
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="$menu_submit">
$order_str
<br>
<input type=submit name=update value="Update defaults">
EOQ;
  return $submit;
}


function glue_search_def_inputs()
{
  $search_start = "y";
  $str_y = $_POST['search_start_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $search_start="";
  $str_m = $_POST['search_start_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $search_start ="";
  $str_d = $_POST['search_start_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $search_start ="";
  if ($search_start == "y")
    $search_start="$str_y"."-"."$str_m"."-"."$str_d";

  $search_end = "y";
  $str_y = $_POST['search_end_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $search_end = "";
  $str_m = $_POST['search_end_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $search_end = "";
  $str_d = $_POST['search_end_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $search_end = "";
  if ($search_end == "y")
    $search_end="$str_y"."-"."$str_m"."-"."$str_d";    

  $q1_start = "y";
  $str_y = $_POST['q1_start_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q1_start = "";
  $str_m = $_POST['q1_start_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q1_start = "";
  $str_d = $_POST['q1_start_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q1_start = "";
  if ($q1_start == "y") $q1_start="$str_y"."-"."$str_m"."-"."$str_d";

  $q1_end = "y";
  $str_y = $_POST['q1_end_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q1_end = "";
  $str_m = $_POST['q1_end_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q1_end = "";
  $str_d = $_POST['q1_end_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q1_end = "";
  if ($q1_end == "y") $q1_end="$str_y"."-"."$str_m"."-"."$str_d";

  $q2_start = "y";
  $str_y = $_POST['q2_start_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q2_start = "";
  $str_m = $_POST['q2_start_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q2_start = "";
  $str_d = $_POST['q2_start_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q2_start = "";
  if ($q2_start == "y") $q2_start="$str_y"."-"."$str_m"."-"."$str_d";

  $q2_end = "y";
  $str_y = $_POST['q2_end_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q2_end = "";
  $str_m = $_POST['q2_end_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q2_end = "";
  $str_d = $_POST['q2_end_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q2_end = "";
  if ($q2_end == "y") $q2_end="$str_y"."-"."$str_m"."-"."$str_d";

  $q3_start = "y";
  $str_y = $_POST['q3_start_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q3_start = "";
  $str_m = $_POST['q3_start_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q3_start = "";
  $str_d = $_POST['q3_start_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q3_start = "";
  if ($q3_start == "y") $q3_start="$str_y"."-"."$str_m"."-"."$str_d";

  $q3_end = "y";
  $str_y = $_POST['q3_end_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q3_end = "";
  $str_m = $_POST['q3_end_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q3_end = "";
  $str_d = $_POST['q3_end_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q3_end = "";
  if ($q3_end == "y") $q3_end="$str_y"."-"."$str_m"."-"."$str_d";

  $q4_start = "y";
  $str_y = $_POST['q4_start_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q4_start = "";
  $str_m = $_POST['q4_start_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q4_start = "";
  $str_d = $_POST['q4_start_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q4_start = "";
  if ($q4_start == "y") $q4_start="$str_y"."-"."$str_m"."-"."$str_d";

  $q4_end = "y";
  $str_y = $_POST['q4_end_y'];
  if ((strlen($str_y) > 4) || !chars_are_nums($str_y)) $q4_end = "";
  $str_m = $_POST['q4_end_m'];
  if ((strlen($str_m) > 2) || !chars_are_nums($str_m)) $q4_end = "";
  $str_d = $_POST['q4_end_d'];
  if ((strlen($str_d) > 2) || !chars_are_nums($str_d)) $q4_end = "";
  if ($q4_end == "y") $q4_end="$str_y"."-"."$str_m"."-"."$str_d";

  $search = array();
  $search['q1_start'] = $q1_start;
  $search['q1_end'] = $q1_end;
  $search['q2_start'] = $q2_start;
  $search['q2_end'] = $q2_end;
  $search['q3_start'] = $q3_start;
  $search['q3_end'] = $q3_end;
  $search['q4_start'] = $q4_start;
  $search['q4_end'] = $q4_end;
  $search['search_start'] = $search_start;
  $search['search_end'] = $search_end;

  return $search;
}


function make_search_inputs($def)
{
  $search = array();
  $str = $def['q1_start'];
  $arr = explode("-", $str);
  $search['q1_start_y'] = $arr[0];
  $search['q1_start_m'] = $arr[1];
  $search['q1_start_d'] = $arr[2];
  $str = $def['q1_end'];
  $arr = explode("-", $str);
  $search['q1_end_y'] = $arr[0];
  $search['q1_end_m'] = $arr[1];
  $search['q1_end_d'] = $arr[2];

  $str = $def['q2_start'];
  $arr = explode("-", $str);
  $search['q2_start_y'] = $arr[0];
  $search['q2_start_m'] = $arr[1];
  $search['q2_start_d'] = $arr[2];
  $str = $def['q2_end'];
  $arr = explode("-", $str);
  $search['q2_end_y'] = $arr[0];
  $search['q2_end_m'] = $arr[1];
  $search['q2_end_d'] = $arr[2];

  $str = $def['q3_start'];
  $arr = explode("-", $str);
  $search['q3_start_y'] = $arr[0];
  $search['q3_start_m'] = $arr[1];
  $search['q3_start_d'] = $arr[2];
  $str = $def['q3_end'];
  $arr = explode("-", $str);
  $search['q3_end_y'] = $arr[0];
  $search['q3_end_m'] = $arr[1];
  $search['q3_end_d'] = $arr[2];

  $str = $def['q4_start'];
  $arr = explode("-", $str);
  $search['q4_start_y'] = $arr[0];
  $search['q4_start_m'] = $arr[1];
  $search['q4_start_d'] = $arr[2];
  $str = $def['q4_end'];
  $arr = explode("-", $str);
  $search['q4_end_y'] = $arr[0];
  $search['q4_end_m'] = $arr[1];
  $search['q4_end_d'] = $arr[2];

  $str = $def['search_start'];
  $arr = explode("-", $str);
  $search['search_start_y'] = $arr[0];
  $search['search_start_m'] = $arr[1];
  $search['search_start_d'] = $arr[2];
  $str = $def['search_end'];
  $arr = explode("-", $str);
  $search['search_end_y'] = $arr[0];
  $search['search_end_m'] = $arr[1];
  $search['search_end_d'] = $arr[2];

  return $search;  
}


function get_search_form($search, $menu_submit)
{
  $q1_start_y = $search['q1_start_y'];
  $q1_start_m = $search['q1_start_m'];
  $q1_start_d = $search['q1_start_d'];
  $q1_end_y = $search['q1_end_y'];
  $q1_end_m = $search['q1_end_m'];
  $q1_end_d = $search['q1_end_d'];
  $q2_start_y = $search['q2_start_y'];
  $q2_start_m = $search['q2_start_m'];
  $q2_start_d = $search['q2_start_d'];
  $q2_end_y = $search['q2_end_y'];
  $q2_end_m = $search['q2_end_m'];
  $q2_end_d = $search['q2_end_d'];
  $q3_start_y = $search['q3_start_y'];
  $q3_start_m = $search['q3_start_m'];
  $q3_start_d = $search['q3_start_d'];
  $q3_end_y = $search['q3_end_y'];
  $q3_end_m = $search['q3_end_m'];
  $q3_end_d = $search['q3_end_d'];
  $q4_start_y = $search['q4_start_y'];
  $q4_start_m = $search['q4_start_m'];
  $q4_start_d = $search['q4_start_d'];
  $q4_end_y = $search['q4_end_y'];
  $q4_end_m = $search['q4_end_m'];
  $q4_end_d = $search['q4_end_d'];
  $search_start_y = $search['search_start_y'];
  $search_start_m = $search['search_start_m'];
  $search_start_d = $search['search_start_d'];
  $search_end_y = $search['search_end_y'];
  $search_end_m = $search['search_end_m'];
  $search_end_d = $search['search_end_d'];

  $submit = <<<EOQ
<b>Start search using:</b><br>
Month (2 digits):
<input type=text name="search_start_m"
value="$search_start_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="search_start_d"
value="$search_start_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="search_start_y"
value="$search_start_y" size=6 maxlength=4>
<br>
<b>End search using:</b><br>
Month (2 digits):
<input type=text name="search_end_m" 
value="$search_end_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="search_end_d" 
value="$search_end_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="search_end_y" 
value="$search_end_y" size=6 maxlength=4>
<br><br>

<b>First quarter start:</b><br>
Month (2 digits):
<input type=text name="q1_start_m"
value="$q1_start_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q1_start_d"
value="$q1_start_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q1_start_y" 
value="$q1_start_y" size=6 maxlength=4>
<br>
<b>First quarter end:</b><br>
Month (2 digits):
<input type=text name="q1_end_m"
value="$q1_end_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q1_end_d"
value="$q1_end_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q1_end_y"
value="$q1_end_y" size=6 maxlength=4>
<br><br>

<b>Second quarter start:</b><br>
Month (2 digits):
<input type=text name="q2_start_m"
value="$q2_start_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q2_start_d"
value="$q2_start_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q2_start_y" 
value="$q2_start_y" size=6 maxlength=4>
<br>
<b>Second quarter end:</b><br>
Month (2 digits):
<input type=text name="q2_end_m"
value="$q2_end_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q2_end_d"
value="$q2_end_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q2_end_y"
value="$q2_end_y" size=6 maxlength=4>
<br><br>
<b>Third quarter start:</b><br>
Month (2 digits):
<input type=text name="q3_start_m"
value="$q3_start_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q3_start_d"
value="$q3_start_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q3_start_y" 
value="$q3_start_y" size=6 maxlength=4>
<br>
<b>Third quarter end:</b><br>
Month (2 digits):
<input type=text name="q3_end_m"
value="$q3_end_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q3_end_d"
value="$q3_end_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q3_end_y"
value="$q3_end_y" size=6 maxlength=4>
<br><br>

<b>Fourth quarter start:</b><br>
Month (2 digits):
<input type=text name="q4_start_m"
value="$q4_start_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q4_start_d"
value="$q4_start_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q4_start_y" 
value="$q4_start_y" size=6 maxlength=4>
<br>
<b>Fourth quarter end:</b><br>
Month (2 digits):
<input type=text name="q4_end_m"
value="$q4_end_m" size=3 maxlength=2>
&nbsp&nbsp&nbspDay (2 digits):
<input type=text name="q4_end_d"
value="$q4_end_d" size=3 maxlength=2>
&nbsp&nbsp&nbspYear (4 digits): 
<input type=text name="q4_end_y"
value="$q4_end_y" size=6 maxlength=4>
<br>
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="$menu_submit">
<input type=submit name=update_search value="Update defaults">
EOQ;
  return $submit;
}


function get_attendance_form($def, $menu_submit)
{
  $fname = $def['seating_fname'];
  $fname_on = $def['seating_fname_on'];
  $fname2 = $def['seating_fname2'];
  $fname2_on = $def['seating_fname2_on'];
  $mname = $def['seating_mname']; 
  $lname = $def['seating_lname'];
  $lname_on = $def['seating_lname_on'];
  $name_lines = $def['seating_name_lines'];

  if ($fname == 'n') $fname = '0';
  if ($fname2 == 'n')$fname2 = '0';
  if ($lname == 'n') $lname = '0';

  $checked_y = "";
  $checked_1 = "";
  $checked_n = "";

  if($fname_on == 'y') $checked_y = "checked";
  if($fname_on == '1') $checked_1 = "checked";
  if($fname_on == 'n') $checked_n = "checked";

  $fname_str = <<<EOQ
<br>
<input type="radio" name="fname_on" value="y" $checked_y>
      Show the first name.
<br>
<input type="radio" name="fname_on" value="1" $checked_1>
      Show the first name, if not showing the preferred name.
<br>
<input type="radio" name="fname_on" value="n" $checked_n>
      Do not show the first name.
EOQ;

$fname2_str = "";

  if($fname2_on == 'n')
    $fname2_str .= <<<EOQ
<br>
<input type=checkbox name="fname2_on"
value="y">Show the preferred name.
EOQ;
  else
    $fname2_str .= <<<EOQ
<br>
<input type=checkbox name="fname2_on"
value="y" checked="y">Show the preferred name.
EOQ;

$mname_str = "";

  if ($mname == "y")
    $mname_str .=<<<EOQ
Show middle name: <select name="mname">
<option selected value="y">Yes
<option value="n">No
</select>
EOQ;
  else
    $mname_str .=<<<EOQ
Show middle name: <select name="mname">
<option value="y">Yes
<option selected value="n">No
</select>
EOQ;

$lname_str = "";

  if($lname_on == 'n')
    $lname_str .= <<<EOQ
<br>
<input type=checkbox name="lname_on"
value="y">Show the last name.
EOQ;
  else
    $lname_str .= <<<EOQ
<br>
<input type=checkbox name="lname_on"
value="y" checked="y">Show the last name.
EOQ;


$val_f = "";
$val_p = "";
$val_m = "";

// $name_lines = new lines when attendance buttons are displayed
// "fpm" would display a new line after first, preferred, and middle name
// "-p-" would display a new line only after the preferred name
// "f" means there's a new line after the first name
// "p" means there's a new line after the preferred first name
// "m" means there's a new line after the middle name

$name_lines_r = str_split($name_lines);
$val = $name_lines_r[0];
if ($val != "-")$val_f = "checked=\"y\"";
$val = $name_lines_r[1];
if ($val != "-")$val_p = "checked=\"y\"";
$val = $name_lines_r[2];
if ($val != "-")$val_m = "checked=\"y\"";

  $name_lines_str = <<<EOQ
<input type=checkbox name="name_nline_f"
value="y" $val_f>Show a new line after the first name.
<br>
<input type=checkbox name="name_nline_p"
value="y" $val_p>Show a new line after the preferred name.
<br>
<input type=checkbox name="name_nline_m"
value="y" $val_m>Show a new line after the middle name.
EOQ;


  $submit = <<<EOQ
<br><br>
First name length: <input type=text name="fname"
value="$fname" size=3 maxlength=2>(0-99)
$fname_str
<br><br>
Preferred first name length: <input type=text name="fname2"
value="$fname2" size=3 maxlength=2>(0-99)
$fname2_str
<br><br>
$mname_str
<br><br>
Last name length: <input type=text name="lname"
value="$lname" size=3 maxlength=2>(0-99)
$lname_str
<br><br>
<input type=hidden name=period value="$period">
<input type=hidden name=menu_submit value="$menu_submit">
$name_lines_str
<br>
<input type=submit name="update_names" value="Update defaults">
EOQ;
  return $submit;
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
  $t_start =<<<EOQ
<TABLE $border $cellspacing $cellpadding width="100%">
EOQ;
  return $t_start;
}


function endTable()
{
  return "</TABLE>";
}


function show_form_table ($defaults_form)
{
  echo startTable("border");
  echo <<<EOQ
<form method=post action="$PHP_SELF">
EOQ;
  $cell_data = tDataSource("$defaults_form", "left");
  echo rowPrint($cell_data);
  echo '</form>';
  echo endTable();
}


$tag_title = "gnuschool.org Defaults";
$page_title = "Defaults";

start_html($tag_title);
stylesheet_link("../style_sheet");
start_body("main_background");


if (!$yes_input)
{
  $comment = "Please access this page using the site menu.";
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

title_comment_div_noend($page_title);

$def_r = get_defaults_r($tid);

if ($menu_submit == "Attendance" || $menu_submit == "Seats" ||
                                 $menu_submit == "Monitor a Test")
{
  if(!empty($update_names))
  {
    def_r_post2name($def_r);
    set_defaults($tid, $def_r);
  }
  $defaults_form = get_attendance_form($def_r, $menu_submit);
}
elseif ($menu_submit == "Passwords")
{
  if(!empty($update_pwords))
  {
    def_r_post2pwords($def_r);
    set_defaults($tid, $def_r);
  }
  $defaults_form = get_pwords_form($def_r, $menu_submit);
}
else
  if ($menu_submit == "Absent Search" || 
                                     $menu_submit=="Grades Search")
  {
    if(!empty($update_search))
    {
      $search_r = glue_search_def_inputs();
      def_r_search_edit($search_r, $def_r);
      set_defaults($tid, $def_r);
    }
    $search_inputs_r = make_search_inputs($def_r);
    $defaults_form = get_search_form($search_inputs_r, $menu_submit);
  }

if(!empty($update_search))
  set_defaults($tid, $def_r);

echo startTable("",10,0);
echo "<tr><td>";
show_form_table ($defaults_form);
echo "</td></tr>";
echo endTable();

echo "</DIV>";

?>

</body></html>
