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
include ('../login.php');

$period=$_POST['period'];
$new_seats=$_POST['new_seats'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;
if (empty($period) && empty($new_seats))
  $yes_input = 0;


function input_form()
{
  $login = <<<EOQ
<form method=post action="$PHP_SELF">
<b>Period:</b><br>
<input type=text size=4 name=period>
<br><br>

<input type=hidden name=menu_submit value="Seats">
<input type=submit name=submit value="Edit seating plan">
</form>
EOQ;
  return $login;
}


function get_students_arr( $tid="", $period="", $def_r)
{
  $def_fname = $def_r['seating_fname'];
  $def_fname_on = $def_r['seating_fname_on'];
  $def_fname2 = $def_r['seating_fname2'];
  $def_fname2_on = $def_r['seating_fname2_on'];
  $def_mname = $def_r['seating_mname']; //'n' or 'y'
  $def_lname = $def_r['seating_lname'];
  $def_lname_on = $def_r['seating_lname_on'];
  $def_name_lines = $def_r['seating_name_lines'];
  $students_arr = array();

  $table = "spanish" . "$tid";
  $query = "select * from $table where period = '$period'";
  $result = mysql_query($query);

  $nline_f = "  ";
  $nline_p = "  ";
  $nline_m = "  ";

  // $name_lines = new lines when attendance buttons are displayed
  // "fpm" would display a new line after first, preferred, and middle name
  // "-p-" would display a new line only after the preferred name
  // "f" means there's a new line after the first name
  // "p" means there's a new line after the preferred first name
  // "m" means there's a new line after the middle name

  $name_lines_r = str_split($def_name_lines);
  $val = $name_lines_r[0];
  if ($val != "-")$nline_f = "\n";
  $val = $name_lines_r[1];
  if ($val != "-")$nline_p = "\n";
  $val = $name_lines_r[2];
  if ($val != "-")$nline_m = "\n";

  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $fname2 = $row['fname2'];
    $fname2_is = $row['fname2_is'];
    $id = $row['id'];

    $name_r = stu_name_ass_r($id);
    $fname = $name_r['fname'];
    $mname = $name_r['mname'];  
    $lname = $name_r['lname'];

    $n_f = 0;
    $n_p = 0;
    $n_m = 0;
    $n_l = 0;

    $no_fname = 0;
    if($def_fname_on == '1' && $def_fname2_on == 'y' &&
        $fname2_is == 'y')$no_fname = 1;

    $continues = "";
    if($no_fname || $def_fname == 'n' || $def_fname_on == 'n') $fname = "";
    else
    {
      $length = strlen($fname);

      if ($length > (int)$def_fname)
        {$length = (int)$def_fname; $continues = "~";}
      $fname = substr($fname, 0, $length);
      $fname .= $continues;
      if(!empty($fname))$n_f = 1;
    }

    $continues = "";
    if ($def_fname2 == 'n' || $fname2_is == 'n' ||
        $def_fname2_on == 'n') $fname2 = "";
    else
    {
      $length = strlen($fname2);

      if ($length > (int)$def_fname2)
        {$length = (int)$def_fname2; $continues = "~";}
      $fname2 = substr($fname2, 0, $length);
      $fname2 .= $continues;
      if(!empty($fname2))
      {
        $fname2 = "(" . $fname2 . ")";
        $n_p = 1;
        if($n_f)
        {
          $fname2 = "$nline_f$fname2";
          $n_f = 0;
        }
      }
    }

    if ($def_mname == 'n') $mname = "";
    else
      if(!empty($mname))
      {
        $n_m = 1;
        if($n_p)
        {
          $mname = "$nline_p$mname";
          $n_p = 0;
        }
        else
          if($n_f)
          {
            $mname = "$nline_f$mname";
            $n_f = 0;
          }
      }

    $continues = "";
    if ($def_lname == 'n' || $def_lname_on == 'n') $lname = "";
    else
    {
      $length = strlen($lname);
      if ($length > (int)$def_lname)
        { $length = (int)$def_lname; $continues = "~";}
      $lname = substr($lname, 0, $length);
      $lname .= $continues;
      if(!empty($lname))
      {
        $n_l = 1;
        if($n_m)
        {
          $lname = "$nline_m$lname";
          $n_m = 0;
        }
        elseif($n_p)
        {
          $lname = "$nline_p$lname";
          $n_p = 0;
        }
        else
          if($n_f)
          {
            $lname = "$nline_f$lname";
            $n_f = 0;
          }
      }
    }

    $all_name = "$fname" . "$fname2" . "$mname" . "$lname";

    $students_arr["$id"] = "$all_name";
  }
  return $students_arr;
}


function get_seating_data($tid, $id="", $period="")
{
  $table = "seating" . "$tid";
  $id_num = (int)($id);
  $query = "select * from $table where id = $id_num
            and period = '$period'";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  $row = mysql_fetch_object($result);
  return $row;
}


function get_img_coords($tid, $period = "")
{
  $table = "seating" . "$tid";
  $biggest_coords = array();
  $biggest_x = 10;
  $biggest_y = 10;

  $query = "select * from $table where period = '$period'";
  $result = mysql_query($query);
// query_outcome_echo($query,$result);
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    if ($row['seat_x'] > $biggest_x) $biggest_x = $row['seat_x'];
    if ($row['seat_y'] > $biggest_y) $biggest_y = $row['seat_y'];
  }
  $biggest_coords['seat_x'] = $biggest_x;
  $biggest_coords['seat_y'] = $biggest_y;
  return $biggest_coords;
}


function update_seating($tid, $post_arr = array())
{
  $table1 = "spanish" . "$tid";
  $table2 = "seating" . "$tid";
  $period = $post_arr['period'];
  $query = "select id from $table1 where period = '$period'";
  $result = mysql_query($query);
  // query_outcome_echo($query,$result);
  while ($row = mysql_fetch_row($result))
  {
    $id = $row[0];
    $id_num = (int)($id);
    $v_id = "v" . $id;
    $coords = $post_arr["$v_id"];
    if (!empty($coords))
    {
      $pos = strpos($coords, "_");
      $x_val = substr($coords, 1, $pos-1);
      $y_val = substr($coords, $pos+1);
      $query1 = "update $table2 ";
      $query1 .= "set seat_x='$x_val', seat_y='$y_val' where id=$id_num
                  and period = '$period'";
      mysql_query($query1);
  // query_outcome_echo($query,$result);
    }
  }
}


function array_to_html($tid, $students_arr=array(), $period)
{
  $source = "";
  $index = 2;
  while (list ($id , $name) = each($students_arr))
  {
    $seating_arr = get_seating_data( $tid, $id, $period);
    $seat_x = $seating_arr->seat_x;
    $seat_y = $seating_arr->seat_y;
    $div_id = "div_" . $id;
    $but_id = "but_" . $id;
    $v_id = "v" . $id;
    $index++;
echo <<<EOQ
<DIV ID="$div_id" STYLE="position:absolute; top:$seat_y; left:$seat_x; 
z-index: $index">
<input id="$but_id" type="button" class="gray_button" name="$but_id"
value="$name" 
onMouseDown="set_globals('$but_id', '$div_id');"
onMouseUp="unset_globals();">
</DIV>
EOQ;
    $source .= <<<EOQ
<input id="$id" type="hidden" name="$v_id">
EOQ;
  }
  return $source;
}

function img_div_html($coords_r)
{
  $seat_x = $coords_r['seat_x'] + 151;
  $seat_y = $coords_r['seat_y'] + 101;
  echo <<<EOQ
<DIV ID="div_id1" STYLE="position:absolute; top:$seat_y; left:$seat_x;">
<img id="img_id1" src='../blank.png' ALT='blank picture'>
<input id="id1" type="hidden">
</DIV>
EOQ;
}


function get_update_seats_inputs($hidden_divs="", $period)
{
  $inputs = <<<EOQ
$hidden_divs
<input type=hidden name="menu_submit" value="Seats">
<input type=submit class=green_button name="new_seats"
value='Save Seat Changes for "PERIOD $period"'>
EOQ;
  return $inputs;
}



function update_seats_div($hidden_divs="")
{
  echo <<<EOQ
<DIV STYLE="position:absolute; top:30; left:0; z-index: 2">
<form method=post action="$PHP_SELF">
$hidden_divs
<input type=submit class=green_button name="new_seats"
value="Save changes">
</form>
</DIV>
EOQ;
}


$tag_title = "gnuschool.org Edit seating plan";

start_html($tag_title);
stylesheet_link("../../style_sheet");

if ($yes_input)
{
  echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

var old_event_x = 0, old_event_y = 0;
var w_width, w_height;
var max_x=0, max_y=0;
var cur_div_obj_x, cur_div_obj_y;
var cur_div_obj, cur_but_obj;
var cur_div_id;
var unset=0;


function setHandlers()
{
  start_globals();
  window.document.onmousemove = move; 
} 


function coords_to_value()
{
  var str_len = cur_div_id.length - 4;
  var id = cur_div_id.substr(4, str_len);
  document.getElementById(id).value = 'v'+cur_div_obj_x+'_'+cur_div_obj_y;
}


function start_globals()
{     
    set_but_obj('img_id1');
    set_div_obj('div_id1');
    set_div_obj_xy();
    max_x = cur_div_obj_x;
    max_y = cur_div_obj_y;
    unset = 1;
} 


function unset_globals()
{ 
    if ((cur_div_obj_x + 150) > max_x)
      max_x = cur_div_obj_x + 150;
    if ((cur_div_obj_y + 100) > max_y)
      max_y = cur_div_obj_y + 100;
    
    set_but_obj('img_id1');
    set_div_obj('div_id1');
    move_div_obj_xy(max_x, max_y);
    cur_but_obj.blur();
    unset = 1;
} 


function set_globals(but_id, div_id)
{ 
    set_div_obj(div_id);
    set_div_obj_xy();
    set_but_obj(but_id);
    unset = 0;
} 


function set_but_obj(id)
{ 
  cur_but_obj = document.getElementById(id);
} 


function set_div_obj(id)
{ 
  cur_div_id = id;
  cur_div_obj = document.getElementById(id).style;
} 


function set_div_obj_xy()
{ 
  if (document.getElementById)
  {
    cur_div_obj_x = cur_div_obj.pixelLeft; 
    cur_div_obj_y = cur_div_obj.pixelTop; 
  } 
  else
    alert("Sorry, but the Javascript code did not work. This page will not work properly.");
}


function move_div_obj_xy(new_x, new_y)
{ 
  if (document.getElementById)
  {
    cur_div_obj.pixelLeft = new_x; 
    cur_div_obj.pixelTop = new_y; 
  } 
  else
    alert("Sorry, but the Javascript code did not work. This page will not work properly.");

  coords_to_value();
}


function set_height_width()
{
  if( typeof( window.innerWidth ) == 'number' )
  {
    //netscape
    w_width = window.innerWidth;
    w_height = window.innerHeight;
  }
  else if( document.documentElement &&
      ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
  {
    //IE 6+
    w_width = document.documentElement.clientWidth;
    w_height = document.documentElement.clientHeight;
  }
  else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
  {
    //IE 4
    w_width = document.body.clientWidth;
    w_height = document.body.clientHeight;
  }
}


function move()
{ 
    new_event_x = event.x; 
    new_event_y = event.y;

    set_height_width();

    if (unset == 0)
    if ((new_event_x < 3)||(new_event_y < 3)||(new_event_x>w_width)||(new_event_y>w_height))
      unset_globals();

    new_div_x = cur_div_obj_x + (new_event_x - old_event_x);
    new_div_y = cur_div_obj_y + (new_event_y - old_event_y);
             
    if (new_div_x < 0 )
      new_div_x = 0;
 
    if (new_div_y < 0 )
      new_div_y = 0;

    if (unset == 0)
    {
      move_div_obj_xy(new_div_x , new_div_y);
      cur_div_obj_x = new_div_x;
      cur_div_obj_y = new_div_y;
    }
    old_event_x = new_event_x;
    old_event_y = new_event_y;
} 

window.onload = setHandlers; 
</script>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in your browser.</p>
</noscript>
EOQ;
}

start_body("main_background");

$menu_submit = "Seats";

$def_r = get_defaults_r($tid);

$comment = "";

if($yes_input)
{
  $students_arr = get_students_arr( $tid, $period, $def_r);  
  if (empty($students_arr)) 
  {
    $comment = "There are no students in period $period.<br>";
    $yes_input = 0;
    $menu_submit = "Main";
  }
}

$menu_r=array();
$menu_r['active_page']= $menu_submit;
$menu_r['period'] = "$period";
main_menu($menu_r);

$page_title = "Edit seating plan";

if (!$yes_input)
{
  main_menu($menu_r);
  $comment .= "Please complete the following information:";
  title_comment_div_noend($page_title, $comment);
  echo input_form();
  end_html_exit();
}

if (!empty($new_seats))
  update_seating($tid,$_POST);

$img_coords = get_img_coords($tid, $period );
img_div_html($img_coords);

$hidden_inputs_html = array_to_html($tid, $students_arr, $period);

$hidden_inputs_html .= <<<EOQ
<input type="hidden" name="period" value="$period">
EOQ;
$update_inputs = get_update_seats_inputs($hidden_inputs_html, $period);
$menu_r['update_inputs'] = $update_inputs;
main_menu($menu_r);


?>
</body></html>
