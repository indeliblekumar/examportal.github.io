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

function special_chars($type)
{
  $source = "";
  if($type == "Spanish")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">  
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">  
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">  
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">  
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  " 
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  " 
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
EOQ;
  }
elseif($type == "German")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  " 
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
EOQ;
  }
elseif($type == "French")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">  
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
EOQ;
  }
elseif($type == "Italian")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')">
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
<INPUT TYPE="button" VALUE="  �  "
onclick="addText('�')"> 
EOQ;
  }
  return $source;
}

?>
