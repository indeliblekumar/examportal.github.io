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

$description=$_POST['description'];
$isbn=$_POST['isbn'];
$title=$_POST['title'];
$author=$_POST['author'];
$page=$_POST['page'];
$type=$_POST['type'];
$test_id=$_POST['test_id'];
$menu_submit=$_POST['menu_submit'];

$yes_input = 1;

if (empty($description) && empty($title) && empty($isbn) &&
    empty($author) && empty($page) && empty($type) && empty($test_id))
  $yes_input = 0;


function input_form()
{
  $type_r = get_subjects_r();
  $select_html = array2select($type_r,"","type");
    
  echo <<<EOQ
<form method=post action="$PHP_SELF">
<b>Test Description:</b><br>
<input type=text size=115 name=description maxlength=111>
<br><br>

<b>Subject:</b><br>
$select_html
<br><br>

<b>Book Title:</b><br>
<input type=text size=115 name=title maxlength=111>
<br><br>

<b>Page number:</b><br>
<input type=text size=6 name=page maxlength=4>
<br><br>

<b>Author:</b><br>
<input type=text size=115 name=author maxlength=111>
<br><br>

<b>ISBN number:</b><br>
<input type=text size=16 name=isbn maxlength=10>
<br><br>

<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="Find test">
</form>
EOQ;
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


function startTable($border="", $c_padding=0, $c_spacing=0)
{
  $cellpadding = "cellpadding=" . $c_padding;
  $cellspacing = "cellspacing=" . $c_spacing;
  return "<TABLE $border $cellspacing $cellpadding>";
}


function endTable()
{
  return "</TABLE>";
}


function  start_div_table ()
{
  echo "<div class=main_table>";
  echo startTable();
}


function  end_div_table ()
{
  echo "</table></div>";
}


function show_tests_div ($tid="", $tests_arr=array())
{
  $size = sizeof($tests_arr);
  $tests = "tests";
  if ($size == 1) $tests = "test";
  echo "<tr><td>";
  echo "$size $tests found";
  echo "</td></tr>";
  for ($i=0; $i<sizeof($tests_arr); $i++)
  {
    $test_arr = $tests_arr[$i];
    $test_id = $test_arr['test_id'];
    echo "<tr><td>";
    echo startTable("border");
    echo "<tr><td>";
    show_periods_setup_form($tid, $test_id);
    echo "</td></tr><tr>";
    show_form_start("./test_edit.php");
    echo "<td>";
    show_test_table ($test_arr, $i);
    show_test_form_input($tid, $test_id, "Edit this test");
    echo "</td></form></tr><tr>";
    show_form_start("./test_review1.php");
    echo "<td>";
    show_test_form_input($tid, $test_id, "Review this test");
    echo "</td></form></tr>";
    echo endTable();
    echo "</td></tr><tr><td>&nbsp</td></tr>";
  }
}    
  

function show_test_table ($test_arr=array(), $num)
{
  echo startTable("border");
  $description = $test_arr['description'];
  $isbn = $test_arr['isbn'];
  $title = $test_arr['title'];
  $author = $test_arr['author'];
  $page = $test_arr['page'];
  $cell_data = tDataSource("Description", "left");
  $cell_data .= tDataSource($description, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("Author", "left");
  $cell_data .= tDataSource($author, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("Title", "left");
  $cell_data .= tDataSource($title, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("Page", "left");
  $cell_data .= tDataSource($page, "left");
  echo rowPrint($cell_data);
  $cell_data = tDataSource("ISBN", "left");
  $cell_data .= tDataSource($isbn, "left");
  echo rowPrint($cell_data);
  echo endTable();
}


function show_periods_setup_form($tid="", $test_id=0)
{
  echo <<<EOQ
<form method=post action="./test_period_set.php">
<input type=submit name=submit value="Setup this test for the periods checked">
EOQ;
  show_checkboxes_table($tid);
  echo <<<EOQ
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
</form>
EOQ;
}


function show_form_start($action="")
{
  echo <<<EOQ
<form method=post action="$action">
EOQ;
}


function show_test_form_input($tid="", $test_id=0, $button_val)
{
  echo <<<EOQ
<input type=hidden name=test_id value="$test_id">
<input type=hidden name=menu_submit value="Tests">
<input type=submit name=submit value="$button_val">
EOQ;
}


function get_periods_array($tid)
{
  $periods_r = array();
  $table = "spanish" . "$tid";
  $query = "select DISTINCT period from $table order by period";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  while ($row = mysql_fetch_row($result))
  {
    $periods_r[] = $row[0];
  }
  return $periods_r;
}


function get_today_fields($tid, $periods_r)
{
  $today_fields_r = array();
  $table = "today" . "$tid";
  $query = "select names from $table";
  $result = mysql_query($query);
  if($result === FALSE)
  {
    create_today_table($tid, $periods_r);
    $result = mysql_query($query);
  }
//  query_outcome_echo($query,$result);
  $row = mysql_fetch_row($result);
  $fields_str = $row[0];
  $today_fields_r = explode(",", $fields_str);
  return $today_fields_r;
}


function today_columns_ok($tid, $periods_r)
{
  $today_fields_r = get_today_fields($tid, $periods_r);
  if (sizeof($periods_r) != sizeof($today_fields_r)) return 0;
  $flag = 1;
  for ($i=0; $i<sizeof($periods_r); $i++)
  {
    $name = "period" . $periods_r[$i];
    if ($name != $today_fields_r[$i]) $flag = 0;
  }
  return $flag;
}


function create_today_table($tid, $periods_r)
{
  $names_str = "";
  $fields = "";
  $column_type = "varchar(12)"; 
  while (list(,$value) = each($periods_r))
  {
    $fields .= "period$value " . "$column_type,"; 
    $names_str .= "period$value,";
    $values_str .= "1,";
  }
  $values_str = substr($values_str, 0, strlen($values_str) - 1);
  $names_str = substr($names_str, 0, strlen($names_str) - 1);
  $fields = substr($fields, 0, strlen($fields) - 1);
  $names_field = "names varchar(125),";
  $fields="($names_field$fields)";

  $table = "today" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  $fields = "(names,$names_str)";
  $names_str = "'$names_str'";
  $query =
  "insert into $table $fields values($names_str,$values_str)";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
}


function drop_today_table($tid)
{
  $table = "today" . "$tid";
  $query = "drop table $table";
  $result = mysql_query($query);
}


function show_checkboxes_table($tid)
{
  $periods_r = get_periods_array($tid);
  if( !today_columns_ok($tid, $periods_r))
  {
    drop_today_table($tid);  
    create_today_table($tid,$periods_r);  
  }
  $ckbox_html = "";
  for ($i=0; $i<sizeof($periods_r); $i++)
  {
    $period = $periods_r[$i];
    $name = "period" . $period;
    $ckbox_html .=<<<EOQ
<td><input type=checkbox name=$name value="yes">$period
&nbsp &nbsp</td>
EOQ;
  }

  echo <<<EOQ
<table>
<tr>
$ckbox_html
</tr>
</table>
EOQ;
}


function get_where_query()
{
  $description=$_POST['description'];
  $isbn=$_POST['isbn'];
  $title=$_POST['title'];
  $author=$_POST['author'];
  $page=$_POST['page'];
  $type=$_POST['type'];
  $test_id=$_POST['test_id'];

  $and_flag = 0;
  $query = "";
  if (!empty($description))
  {
    if($and_flag)
      $query .= "and description = '$description' ";
    else
      $query .= "description = '$description' ";
    $and_flag = 1;
  }
  if (!empty($type))
  {
    if($and_flag)
      $query .= "and type = '$type' ";
    else
      $query .= "type = '$type' ";
    $and_flag = 1;
  }
  if (!empty($isbn))
  {
    if($and_flag)
      $query .= "and isbn = '$isbn' ";
    else
      $query .= "isbn = '$isbn' ";
    $and_flag = 1;
  }
  if (!empty($title))
  {
    if($and_flag)
      $query .= "and title = '$title' ";
    else
      $query .= "title = '$title' ";
    $and_flag = 1;
  }
  if (!empty($author))
  {
    if($and_flag)
      $query .= "and author = '$author' ";
    else
      $query .= "author = '$author' ";
    $and_flag = 1;
  }
  if (!empty($page))
  {
    if($and_flag)
      $query .= "and page = '$page' ";
    else
      $query .= "page = '$page' ";
    $and_flag = 1;
  }
  if (!empty($test_id))
    $query .= "test_id = $test_id ";
  return $query;
}


function get_where_like_query()
{
  $description=$_POST['description'];
  $isbn=$_POST['isbn'];
  $title=$_POST['title'];
  $author=$_POST['author'];
  $type=$_POST['type'];
  $type=trim($type);
  $page=$_POST['page'];

  $description = str_replace(" ", "%", $description);
  $isbn = str_replace(" ", "%", $isbn);
  $title = str_replace(" ", "%", $title);
  $author = str_replace(" ", "%", $author);
  $page = str_replace(" ", "%", $page);
  
  $and_flag = 0;
  $query = "";
  if (!empty($description))
  {
    if($and_flag)
      $query .= "and description like '%$description%' ";
    else
      $query .= "description like '%$description%' ";
    $and_flag = 1;
  }
  if (!empty($type))
  {
    if($and_flag)
      $query .= "and type = '$type' ";
    else
      $query .= "type = '$type' ";
    $and_flag = 1;
  }
  if (!empty($isbn))
  {
    if($and_flag)
      $query .= "and isbn like '%$isbn%' ";
    else
      $query .= "isbn like '%$isbn%' ";
    $and_flag = 1;
  }
  if (!empty($title))
  {
    if($and_flag)
      $query .= "and title like '%$title%' ";
    else
      $query .= "title like '%$title%' ";
    $and_flag = 1;
  }
  if (!empty($author))
  {
    if($and_flag)
      $query .= "and author like '%$author%' ";
    else
      $query .= "author like '%$author%' ";
    $and_flag = 1;
  }
  if (!empty($page))
  {
    if($and_flag)
      $query .= "and page='$page' ";
    else
      $query .= "page='$page' ";
    $and_flag = 1;
  }
  return $query;
}


function get_tests_arr($tid = "", $where_clause = "")
{
  $tests_arr = array();
  $fields_arr = array();

  $table = "tests" . "$tid";
  $query = "select * from $table where $where_clause";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result);
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $fields_arr["description"]=$row['description'];
    $fields_arr["isbn"]=$row['isbn'];
    $fields_arr["title"]=$row['title'];
    $fields_arr["author"]=$row['author'];
    $fields_arr["page"]=$row['page'];
    $fields_arr["test_id"]=$row['test_id'];
    $tests_arr[]=$fields_arr;
  }
  return $tests_arr;
}


function get_words_array($many_words_str, $delimiter=" ")
{
  $arr = explode($delimiter, $many_words_str);
  $clean_arr = array();
  $count = 0;
  for ($i=0; $i<sizeof($arr); $i++)
  {
    $temp_str = trim($arr[$i]);
    if (!empty($temp_str))
    {
      $clean_arr[$count] = $temp_str;
      $count++;
    }
  }
  return $clean_arr;
}


function permutate($arr=array(), $length=0, $whole_str="", &$permut_arr)
{
  if ($length == 0)
  {
    $permut_arr[] = "$whole_str";
    $whole_str = "";
  }
  for ($i=0; $i< $length; $i++)
  {
    $temp_str = $whole_str . $arr[$i] . " ";
    $next_level = array();
    $count = 0;
    for ($m=0; $m< $length-1; $m++)
    {
      if ($i == $m) $count++;
      $next_level[$m] = $arr[$count];
      $count++;
    }
    permutate($next_level, sizeof($next_level), $temp_str, $permut_arr);
  }
}


$tag_title = "gnuschool.org Find a Test";

start_html($tag_title);
stylesheet_link("../../style_sheet");

if ($yes_input)
{
/*
echo <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

function set_bgcolor(my_checkbox, ids_str, number_of_ids)
{
  id_arr = ids_str.split("_");
  if (my_checkbox.checked)
  {
    if (document.getElementById)
    {
      for( i = 0; i < number_of_ids; i++)
      {
        id = id_arr[i];
        document.getElementById(id).style.backgroundColor = 'red';
      }
    }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
  else
  {
    if (document.getElementById)
    {
      for( i = 0; i < number_of_ids; i++)
      {
        id = id_arr[i];
        document.getElementById(id).style.backgroundColor = 'white';
      }
    }
    else
      alert("Sorry, but the Javascript code did not work. This page will not work properly.");
  }
}


var inputA=null;


function set_global_pos(myobject)
{ if(myobject) inputA = myobject; }


function doneConfirm() {
var ok=confirm("Are you done with Test?")
if (ok) return true; else return false;
}


function addText( insText )
{
if (inputA)
{
if (inputA.type == "textarea" || inputA.type == "text")
{
 inputA.focus();
 if( inputA.createTextRange ) {
   document.selection.createRange().text += insText;
 } else if( inputA.setSelectionRange ) {
   var len = inputA.selectionEnd;
   inputA.value=inputA.value.substr(0,len) + insText +
inputA.value.substr(len);
   inputA.setSelectionRange(len+insText.length,len+insText.length);
 } else { inputA.value += insText; }
}
}
}

</script>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
       You may be able to fix the problem, if you enable Javascript in you browser.</p>
</noscript>
EOQ;
*/
}


start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Tests";

$menu_r=array();
$menu_r['active_page']= $menu_submit;

main_menu($menu_r);

$page_title = "Test finder";

if (!$yes_input)
{
  $comment = "Type as much information as you like in the fields 
    below.<br>Few words means a more general search. Many words
    means a more narrow search.";
  title_comment_div_noend($page_title, $comment);
  input_form();
  end_html_exit();
}

title_comment_div_noend($page_title);

$where_exact = get_where_query();

if(empty($where_exact)) end_html_exit();

$tests_arr = get_tests_arr($tid, $where_exact);

if(sizeof($tests_arr) == 0)
{
  $where_like = get_where_like_query();
  $tests_arr = get_tests_arr($tid, $where_like);
}

if(sizeof($tests_arr) != 0)
{
  start_div_table ();
  show_tests_div ($tid, $tests_arr);
  end_div_table ();
}
else
{
  $clean_arr = get_words_array($description, " ");
 
  $permut_arr = array();
  permutate($clean_arr, sizeof($clean_arr), "", $permut_arr);
 
  start_div_table ();
  $no_result = 1;
  for ($i=0; $i<sizeof($permut_arr); $i++)
  {
    $_POST['description'] = $permut_arr[$i];
    $where_part = get_where_like_query();
    $tests_arr = get_tests_arr($tid, $where_part);
    if(sizeof($tests_arr) != 0)
    {
      show_tests_div ($tid, $tests_arr, sizeof($permut_arr), $i);
      $no_result = 0;
    }
  }
  if ($no_result) echo "0 matches found";
  end_div_table ();
}
?>

</body></html>
