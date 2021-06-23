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

$print=$_POST['print'];
$period=$_POST['period'];
$get_period=$_GET['period'];
$fname=$_POST['fname'];
$lname=$_POST['lname'];
$update_pws=$_POST['update_pws'];
$menu_submit=$_POST['menu_submit'];
$id=$_POST['id'];
$get_id=$_GET['id'];
$get_menu_submit=$_GET['menu_submit'];

if(empty($id))
{ $id = $get_id; $_POST['id'] = $id;}
if(empty($period))
{ $period = $get_period; $_POST['period'] = $period;}

$id_flag = 0;
$period_flag = 0;
$name_flag = 0;
if (!empty($id)) $id_flag = 1;
if (!empty($period)) $period_flag = 1;
if (!empty($fname) || !empty($lname)) $name_flag = 1;

$yes_input = 0;
if ($id_flag || $period_flag || $name_flag)
 $yes_input = 1;


function syllable_rand($start=0)
{
  $str = "";
  $vow = array('a','e','i','o','u');
  $vow_p = array('ai','au','ay','ea','ee','ia','ie'
                 ,'oa','oi','oy','oo','ou');
  $con = array('b','c','d','f','g','h','j','k','l','m','n','p'
               ,'r','s','t','v','w','z');
  $con_p = array('bl','br','ch','cl','cr','dr','fl','fr','gl'
                 ,'gr','ph','pl','pr','sc','sh','sl','sm','sn'
                 ,'sp','st','sw','th','tr','tw');

  $v = rand(0,sizeof($vow) - 1);
  $vp = rand(0,sizeof($vow_p) - 1);
  $c = rand(0,sizeof($con) - 1); 
  $cp = rand(0,sizeof($con_p) - 1); 

  $odds = rand(0,20);
  if($odds < 3 && $start)
  {
    $str = $vow[$v];
    return $str;
  }
  
  $odds = rand(0,20);
  if ($odds < 7)
    $str = $con_p[$cp];
  else
    $str = $con[$c];

  $odds = rand(0,20);
  if ($odds > 8)
    $str .= $vow[$v];
  elseif ($odds < 2)
    $str .= 'y';
  else
    $str .= $vow_p[$vp];
  
  return $str;
}


function add_period_pword_seatxy($tid,$stu_rr,$period="")
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
      $query = "select period from $table where id=$id" . "$andperiod";
      $result = mysql_query($query);
      while ($row = mysql_fetch_row($result))
      {
        $period = $row[0];
        $pword = get_pword($tid,$period,$id);
        $stu_rr[$i]['pword'] = $pword;
        $seat_coord_r = get_seat_xy($tid,$period,$id);
        $x = $seat_coords_r['seat_x'];
        $stu_rr[$i]['x'] = $x;
        $y = $seat_coords_r['seat_y'];
        $stu_rr[$i]['y'] = $y;
        $per_stu_rrr["$period"][] = $stu_rr[$i];
      }
    }
  }
  return $per_stu_rrr;
}


function add_stu_names_pword_seatxy($tid,$stu_rr)
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
        $pword = get_pword($tid,$period,$id);
        $stu_rr[$i]['pword'] = $pword;
        $seat_coord_r = get_seat_xy($tid,$period,$id);
        $x = $seat_coords_r['seat_x'];
        $stu_rr[$i]['x'] = $x;
        $y = $seat_coords_r['seat_y'];
        $stu_rr[$i]['y'] = $y;
        $per_stu_rrr["$period"][] = $stu_rr[$i];
      }
    }
  }
  return $per_stu_rrr;
}


function get_pword($tid, $period, $id)
{
  $table = "pwords" . "$tid";
  $query = "select pword from $table where id = $id
            and period='$period'";
  $result = mysql_query($query);
  // echo "<br><br><br>";
  // query_outcome_echo($query,$result);

  $row = mysql_fetch_row($result);//lala
  return $row[0];
}


function get_seat_xy($tid, $period, $id)
{
  $table = "seating" . "$tid";
  $query = "select seat_x, seat_y from $table where id = $id
            and period='$period'";
  $result = mysql_query($query);

  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function id_period2_where($id=0, $period="", $id_r)
{
  $query = "";

  if (!empty($id)) if($and_flag)
  { $query .= " and id = $id";}
  else { $query .= "where id = $id"; $and_flag=1;}

  for ($i=0; $i<count($id_r); $i++)
  {
    $id = $id_r[$i];
    if($and_flag)
    {
      $query .= " or id = $id";
    }
    else
    {
      $query .= "where (id = $id"; $and_flag=1;
    }
  }
  if($and_flag)  $query .= ")";

  if (!empty($period)) if($and_flag)
  { $query .= " and period = '$period'";}
  else { $query .= "where period = '$period'"; $and_flag=1;}

  return $query;
}


function get_input_form()
{
  
  $login = <<<EOQ
<form method=post action="$PHP_SELF">
<b>Period:</b><br>
<input type=text name=period size=4 maxlength=4>
<br><br>

<b>Student first name:</b><br>
<input type=text name=fname size=30 maxlength=30>
<br><br>

<b>Student last name:</b><br>
<input type=text name=lname size=30 maxlength=30>
<br><br>

<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Get passwords">
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


function get_TR($t_data="", $align="")
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


function get_new_pword()
{
  $digits_arr= array('2','3','4','5','6','7','8','9','A','B','C','D','E'
   ,'F','G','H','J','K','L','M','N','P','R','S','T','W','X','Y','Z');

  $chars_num_arr= array('4','5','6','7','8');

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


function update_pword ($tid="", $id=0, $period="")
{
  $pword = "";
  $p_w_exists=1;
  while ($p_w_exists)
  {
    $pword = get_new_pword();
    $p_w_exists = pword_exists ($tid, $pword);
  }
  $table = "pwords" . "$tid";

  $query = "update $table set pword = '$pword'
             where id = $id and period='$period'";
  $result = mysql_query($query);
//  echo "<br><br>";
//  query_outcome_echo($query,$result);
  return $pword; 
}


function pword_exists ($tid, $pword="")
{
  $table = "pwords" . "$tid";
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
      $name = $id_name_arr['name'];
      $id = $id_name_arr['id'];
      $pword = $id_name_arr['pword'];
      print "name=$name, id=$id, pword=$pword<br>";
    }
  }
  print "<br>";
}


function get_closest1($s_r, $size)
{
  $closest = 5000;
  $index = 0;
  for ($i=0; $i< $size; $i++)
  {
    $a = (int)$s_r[$i]["x"];
    $b = (int)$s_r[$i]["y"];
    $hypo=(int)(sqrt(pow($a,2)+pow($b,2)));
    if($hypo < $closest) {$closest = $hypo; $index = $i;} 
  }
  return $index;
}


function get_closest2($s_r, $size, $start)
{
  $newest = $start - 1;
  $x = (int) $s_r[$newest]["x"];
  $y = (int) $s_r[$newest]["y"];
  $closest = 5000;
  $index = $start;

  for ($i=$start; $i< $size; $i++)
  {
    $a = (int)$s_r[$i]["x"];
    $b = (int)$s_r[$i]["y"];
    $dif_x = abs($x - $a); 
    $dif_y = abs($y - $b); 
    $x_sq = pow($dif_x,2);
    $y_sq = pow($dif_y,2);

    $hypo=(int)(sqrt($x_sq + $y_sq));
    if($hypo < $closest) {$closest = $hypo; $index = $i;} 
  }
  return $index;
}


function order_r(&$per_stu_rrr, $per)
{
  $ids_r = $per_stu_rrr["$per"];
  $size = sizeof($ids_r);

  $index = get_closest1($ids_r, $size);

  $cur_pos = 0;

  $l_name = $per_stu_rrr["$per"][$index]["name"];
  $l_id = $per_stu_rrr["$per"][$index]["id"];
  $l_x = $per_stu_rrr["$per"][$index]["x"];
  $l_y = $per_stu_rrr["$per"][$index]["y"];
  $l_pword = $per_stu_rrr["$per"][$index]["pword"];

  $h_name = $per_stu_rrr["$per"][$cur_pos]["name"];
  $h_id = $per_stu_rrr["$per"][$cur_pos]["id"];
  $h_x = $per_stu_rrr["$per"][$cur_pos]["x"];
  $h_y = $per_stu_rrr["$per"][$cur_pos]["y"];
  $h_pword = $per_stu_rrr["$per"][$cur_pos]["pword"];

  $per_stu_rrr["$per"][$index]["name"] = $h_name;
  $per_stu_rrr["$per"][$index]["id"] = $h_id;
  $per_stu_rrr["$per"][$index]["x"] = $h_x;
  $per_stu_rrr["$per"][$index]["y"] = $h_y;
  $per_stu_rrr["$per"][$index]["pword"] = $h_pword;

  $per_stu_rrr["$per"][$cur_pos]["name"] = $l_name;
  $per_stu_rrr["$per"][$cur_pos]["id"] = $l_id;
  $per_stu_rrr["$per"][$cur_pos]["x"] = $l_x;
  $per_stu_rrr["$per"][$cur_pos]["y"] = $l_y;
  $per_stu_rrr["$per"][$cur_pos]["pword"] = $l_pword;

  $cur_pos++;

  for($cur_pos; $cur_pos<$size; $cur_pos++)
  {
    $ids_r = $per_stu_rrr["$per"];
    $index = get_closest2($ids_r, $size, $cur_pos);
  
    $l_name = $per_stu_rrr["$per"][$index]["name"];
    $l_id = $per_stu_rrr["$per"][$index]["id"];
    $l_x = $per_stu_rrr["$per"][$index]["x"];
    $l_y = $per_stu_rrr["$per"][$index]["y"];
    $l_pword = $per_stu_rrr["$per"][$index]["pword"];

    $h_name = $per_stu_rrr["$per"][$cur_pos]["name"];
    $h_id = $per_stu_rrr["$per"][$cur_pos]["id"];
    $h_x = $per_stu_rrr["$per"][$cur_pos]["x"];
    $h_y = $per_stu_rrr["$per"][$cur_pos]["y"];
    $h_pword = $per_stu_rrr["$per"][$cur_pos]["pword"];

    $per_stu_rrr["$per"][$index]["name"] = $h_name;
    $per_stu_rrr["$per"][$index]["id"] = $h_id;
    $per_stu_rrr["$per"][$index]["x"] = $h_x;
    $per_stu_rrr["$per"][$index]["y"] = $h_y;
    $per_stu_rrr["$per"][$index]["pword"] = $h_pword;

    $per_stu_rrr["$per"][$cur_pos]["name"] = $l_name;
    $per_stu_rrr["$per"][$cur_pos]["id"] = $l_id;
    $per_stu_rrr["$per"][$cur_pos]["x"] = $l_x;
    $per_stu_rrr["$per"][$cur_pos]["y"] = $l_y;
    $per_stu_rrr["$per"][$cur_pos]["pword"] = $l_pword;
  }
}


function get_table_column_titles($period, $br)
{
  $source = startTable("border");
  $cell_data = tDataSource("Period $period passwords","center", "",2);
  $source .= get_TR($cell_data);
  $cell_data = tDataSource("Name&nbsp$br", "right");
  $cell_data .= tDataSource("&nbspPassword$br", "left");
  $source .= get_TR($cell_data);
  return $source;
}


function show_tables_array
             ($tid,$period_idsNames_ass,$defaults_r,$new_pws)
{
  if(empty($new_pws)) $new_pws = 0;
  else $new_pws = 1;
  $periods_arr=array();
  $tables_num = $defaults_r['pwords_tables_across'];
  $col_space = $defaults_r['pwords_column_space'];
  $row_space = $defaults_r['pwords_row_space'];
  $br = "";
  for($i=0; $i<$row_space; $i++) $br .= "<br>";
  $max_rows = 0;
  $row_nums = 0;

  while (list($period,$ids_names_num) = each($period_idsNames_ass))
  {
    $sizeof_arr = sizeof($ids_names_num);
    if ($sizeof_arr < $tables_num) $tables_num = $sizeof_arr;

    $tables_arr=array();
    for ($i=0; $i<$tables_num; $i++)
    {
      $tables_arr[$i]=get_table_column_titles($period, $br);
    }
    $index = 0;
    for ($i=0; $i<$sizeof_arr; $i++)
    {
      $name=$ids_names_num[$i]['fname'];
      $name .= " ";
      $name .= $ids_names_num[$i]['mname'];
      $name .= " ";
      $name .= $ids_names_num[$i]['lname'];

      $id = $ids_names_num[$i]['id'];
      if($new_pws)
      {
        $pword = update_pword ($tid, $id, $period);
      }
      else
        $pword=$ids_names_num[$i]['pword'];
      $cell_data = tDataSource("$name&nbsp$br", "right");
      $cell_data .= tDataSource("&nbsp$pword$br", "left");
      $tables_arr[$index] .= get_TR($cell_data);
      $index++;
      if($index == $tables_num) $index = 0;
    }
    for ($i=0; $i<$tables_num; $i++)
      $tables_arr[$i] .= endTable();
    $periods_html[]=$tables_arr;
  }
  display_tables_array($periods_html, $tables_num, $col_space);
}


function display_tables_array ($periods_html, $cells_across, $col_space=0)
{
  $cell_num = 0; $cell_data = ""; $nbsp_str="";
  for($i=0;$i<$col_space;$i++)
    $nbsp_str .= "&nbsp";
  echo startTable("");
  while (list(,$table_row) = each($periods_html))
  {
    $row_size = sizeof($table_row);
    for ($i=0; $i<$row_size; $i++)
    {      
      $cell_data .= tDataSource($table_row[$i], "center");
      if ($i != $row_size - 1)
        $cell_data .= tDataSource("$nbsp_str","center");
      $cell_num++;      
    }
    if($cell_num < $cells_across)
    {
      for($i=$cell_num;$i<$cells_across;$i++)
      {
        $cell_data .= tDataSource("&nbsp", "center");
        $cell_data .= tDataSource("$nbsp_str", "center");
      }
    }
    echo get_TR($cell_data, "top");
    $cell_data = ""; 
  }
  echo endTable();
}


function teacher_info_select($tid)
{
  $query = "select * from teachers where tid = $tid";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  if($result === FALSE) return FALSE;
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $name = $row['fname'] . " " . $row['lname'];
  $t_name = $row['t_name'];
  return array($t_name,$name);
}


function teacher_t_name_rand($tid)
{
  $t_name = "";

  for ($i=0; $i < 50; $i++)
  {
    $temp = syllable_rand(1);
    $temp .= syllable_rand();
    $temp .= syllable_rand();
    $query = "select * from teachers where t_name = '$temp'";
    $result = mysql_query($query);
    // query_outcome_echo($query,$result);
    if($result === FALSE) return FALSE;
    if (mysql_num_rows($result) === 0)
    {
      $i=50;
      $t_name = $temp;
      $query = "update teachers set t_name = '$t_name' where 
                tid = $tid";
      $result = mysql_query($query);
      // query_outcome_echo($query,$result);
    }
  }
  return $t_name;
}


//function show_submit_form($user="", $period="", $fname="", $lname="")
function show_submit_form($period="", $fname="", $lname="")
{
//<input type=hidden name="user" value="$user">
  $submit = <<<EOQ
<form method=post action="$PHP_SELF">
<input type=hidden name="fname" value="$fname">
<input type=hidden name="lname" value="$lname">
<input type=hidden name="period" value="$period">
<input type=hidden name="menu_submit" value="Tests">
<input type=submit name="update_pws" value="Get new passwords">
EOQ;
  $submit .= "</form>";
  echo $submit;
}


function new_window_form ($period, $fname, $lname, $id)
{
  $page = "./new_passwords.php";
  $window_description =
"width=600,height=400,scrollbars=yes,resizable=yes,status=yes,menubar=yes";

  $form_html = <<<EOQ
<form method=post target="window23" action="./new_passwords.php"> 
<input type=hidden name=print value="yes">
<input type=hidden name=period value="$period">
<input type=hidden name=fname value="$fname">
<input type=hidden name=lname value="$lname">
<input type=hidden name=id value="$id">
<input type=submit name=submit value="Printer view"
onClick="window.open('$page','window23','$window_description')">
</form>
EOQ;
  echo $form_html;  
}


function simple_body()
{
  echo "</head><body>";
}


function start_div()
{
  echo "<DIV class=main_table>";
}


function end_div()
{
  echo "</DIV>";
}

$tag_title = "gnuschool.org Grades search";
$page_title = "";

start_html($tag_title);
stylesheet_link("../../style_sheet");

if(empty($menu_submit)) $menu_submit = "Tests";

if (empty($get_id))
{
  $menu_r=array();
  $menu_r['active_page']= $menu_submit;
  $menu_r['period']= "$period";
}

$temp_user = "";
$teacher_r = teacher_info_select($tid);
$teacher_name = $teacher_r[1];
if(empty($update_pws))
  $temp_user = $teacher_r[0];
else
  $temp_user = teacher_t_name_rand($tid);

$temp_u_str = "";
if(empty($temp_user))
  $temp_u_str = "The database is LOCKED.";
else
  $temp_u_str = "Temporary user name for students: $temp_user";

if(empty($print))
{
  start_body("main_background");
  main_menu($menu_r);
  $page_title = "Password search";
}
else
{
  simple_body();
  echo "<b>Name: $teacher_name<br>$temp_u_str</b>";
}


if (!$yes_input)
{
  $comment = "Please fill the search fields as needed.";
  $comment .= get_input_form();
  title_comment_div_noend($page_title, $comment);
  end_html_exit();
}

$query_str = "";

$defaults_r = get_defaults_r($tid);
$order = $defaults_r['order'];

$per_stu_rrr = array();
$stu_rr = array();

if($name_flag || $id_flag)
{
  $query_str = post2_stu_blocks_query($order);
  $found = 0;
  $result = mysql_query($query_str);
  if (mysql_num_rows($result) == 0)
  { 
    $comment = "Sorry, no matches found.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }

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
  $per_stu_rrr = add_period_pword_seatxy($tid,$stu_rr,$period);
}
else
{
  $stu_rr = array();
  $query_str = post2_spanish_query($tid,"period");
  $result = mysql_query($query_str);
  if (mysql_num_rows($result) == 0)
  {
    $comment = "Sorry, no matches found.";
    title_comment_div_noend($page_title, $comment);
    end_html_exit();
  }
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
  $per_stu_rrr = add_stu_names_pword_seatxy($tid, $stu_rr);
}

/*
print"<br><br><br><br>";
show_ids_arr($per_stu_rrr);
print_r($per_stu_rrr);
exit();
*/

if($period_flag)
{
  order_r($per_stu_rrr, $period);
}
if(empty($print))
{
  $comment = "<b>Name: $teacher_name<br>$temp_u_str
                             <br><br>Matches found:";
  title_comment_div_noend($page_title, $comment);
  show_tables_array($tid,$per_stu_rrr, $defaults_r,$update_pws);
  show_submit_form($period, $fname, $lname, $defaults_r);
  new_window_form($period, $fname, $lname, $id);
  end_div();
}
else
  show_tables_array($tid,$per_stu_rrr, $defaults_r,$update_pws);

?>

</body></html>
