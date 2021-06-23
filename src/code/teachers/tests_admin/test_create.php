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

$menu_submit=$HTTP_POST_VARS['menu_submit'];

function input_form()
{
  $type_r = get_subjects_r();
  $select_html = array2select($type_r,"","type");
  $accent_buttons = special_chars($type);

  echo <<<EOQ
<form method=post action="./test_edit.php">
<TABLE width="100%" cellspacing=4 cellpadding=4>
<TR align=left><TD>
<b>Test Description:</b><br>
<input type=text size=115 name="description" maxlength=255>
</TD></TR>
<TR align=left><TD>
<b>Subject:</b><br>
$select_html
</TD></TR>
<TR align=left><TD>
<b>Book Title:</b><br>
<input type=text size=115 name="title" maxlength=125>
</TD></TR>
<TR align=left><TD>
<b>Page number:</b><br>
<input type=text size=6 name="page" maxlength=10>
</TD></TR>
<TR align=left><TD>
<b>Author:</b><br>
<input type=text size=115 name="author" maxlength=125>
</TD></TR>
<TR align=left><TD>
<b>ISBN number:</b><br>
<input type=text size=16 name="isbn" maxlength=10>
</TD></TR>
<TR align=left><TD>
<b>Approximate number of questions in the test:</b><br>
<input type=number size=3 name=lines_approx maxlength=3>
</TD></TR>
<TR align=left><TD>
<input type=hidden name=menu_submit value="Tests">
<input type=submit name="create_new" value="Create test">
</TD></TR>
</TABLE>
</FORM>
EOQ;
}


$tag_title = "gnuschool.org Create a Test";

start_html($tag_title);
stylesheet_link("../../style_sheet");
start_body("main_background");

if(empty($menu_submit)) $menu_submit = "Tests";

$menu_r=array();
$menu_r['active_page']= $menu_submit;
main_menu($menu_r);

$page_title = "Create a Test";

$comment = "Please complete as much information as possible.";
title_comment_div_noend($page_title, $comment);
input_form();
echo "</DIV>"
?>

</body></html>
