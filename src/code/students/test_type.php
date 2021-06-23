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
<INPUT TYPE="button" VALUE="  á  "
onclick="addText('á')">  
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('é')">  
<INPUT TYPE="button" VALUE="  í  "
onclick="addText('í')">  
<INPUT TYPE="button" VALUE="  ó  "
onclick="addText('ó')">  
<INPUT TYPE="button" VALUE="  ú  "
onclick="addText('ú')">
<INPUT TYPE="button" VALUE="  ü  " 
onclick="addText('ü')"> 
<INPUT TYPE="button" VALUE="  ñ  "
onclick="addText('ñ')">
<INPUT TYPE="button" VALUE="  Á  "
onclick="addText('Á')">
<INPUT TYPE="button" VALUE="  É  "
onclick="addText('É')">
<INPUT TYPE="button" VALUE="  Í  "
onclick="addText('Í')">
<INPUT TYPE="button" VALUE="  Ó  "
onclick="addText('Ó')">
<INPUT TYPE="button" VALUE="  Ú  "
onclick="addText('Ú')">
<INPUT TYPE="button" VALUE="  Ü  " 
onclick="addText('Ü')"> 
<INPUT TYPE="button" VALUE="  Ñ  "
onclick="addText('Ñ')">
<INPUT TYPE="button" VALUE="  ¡  "
onclick="addText('¡')">
<INPUT TYPE="button" VALUE="  ¿  "
onclick="addText('¿')">
EOQ;
  }
elseif($type == "German")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  ä  "
onclick="addText('ä')"> 
<INPUT TYPE="button" VALUE="  ö  "
onclick="addText('ö')"> 
<INPUT TYPE="button" VALUE="  ü  "
onclick="addText('ü')"> 
<INPUT TYPE="button" VALUE="  Ä  " 
onclick="addText('Ä')"> 
<INPUT TYPE="button" VALUE="  Ö  "
onclick="addText('Ö')"> 
<INPUT TYPE="button" VALUE="  Ü  "
onclick="addText('Ü')"> 
<INPUT TYPE="button" VALUE="  ß  "
onclick="addText('ß')">
<INPUT TYPE="button" VALUE="  €  "
onclick="addText('€')">
EOQ;
  }
elseif($type == "French")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  À  "
onclick="addText('À')"> 
<INPUT TYPE="button" VALUE="  à  "
onclick="addText('à')"> 
<INPUT TYPE="button" VALUE="  Á  "
onclick="addText('Á')"> 
<INPUT TYPE="button" VALUE="  á  "
onclick="addText('á')"> 
<INPUT TYPE="button" VALUE="  Â  "
onclick="addText('Â')"> 
<INPUT TYPE="button" VALUE="  â  "
onclick="addText('â')"> 
<INPUT TYPE="button" VALUE="  Ä  "
onclick="addText('Ä')"> 
<INPUT TYPE="button" VALUE="  ä  "
onclick="addText('ä')"> 
<INPUT TYPE="button" VALUE="  É  "
onclick="addText('É')">  
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('é')">
<INPUT TYPE="button" VALUE="  È  "
onclick="addText('È')">
<INPUT TYPE="button" VALUE="  è  "
onclick="addText('è')">
<INPUT TYPE="button" VALUE="  Ê  "
onclick="addText('Ê')"> 
<INPUT TYPE="button" VALUE="  ê  "
onclick="addText('ê')"> 
<INPUT TYPE="button" VALUE="  Ë  "
onclick="addText('Ë')"> 
<INPUT TYPE="button" VALUE="  ë  "
onclick="addText('ë')"> 
<INPUT TYPE="button" VALUE="  Î  "
onclick="addText('Î')"> 
<INPUT TYPE="button" VALUE="  î  "
onclick="addText('î')"> 
<INPUT TYPE="button" VALUE="  Ï  "
onclick="addText('Ï')"> 
<INPUT TYPE="button" VALUE="  ï  "
onclick="addText('ï')">
<INPUT TYPE="button" VALUE="  Ô  "
onclick="addText('Ô')">
<INPUT TYPE="button" VALUE="  ô  "
onclick="addText('ô')">
<INPUT TYPE="button" VALUE="  Œ  "
onclick="addText('Œ')">
<INPUT TYPE="button" VALUE="  œ  "
onclick="addText('œ')">
<INPUT TYPE="button" VALUE="  Ò  "
onclick="addText('Ò')"> 
<INPUT TYPE="button" VALUE="  ò  "
onclick="addText('ò')">
<INPUT TYPE="button" VALUE="  Ô  "
onclick="addText('Ô')"> 
<INPUT TYPE="button" VALUE="  ô  "
onclick="addText('ô')"> 
<INPUT TYPE="button" VALUE="  Ö  "
onclick="addText('Ö')"> 
<INPUT TYPE="button" VALUE="  ö  "
onclick="addText('ö')">
<INPUT TYPE="button" VALUE="  Ÿ  "
onclick="addText('Ÿ')">
<INPUT TYPE="button" VALUE="  ÿ  "
onclick="addText('ÿ')"> 
<INPUT TYPE="button" VALUE="  Ç  "
onclick="addText('Ç')">
<INPUT TYPE="button" VALUE="  ç  "
onclick="addText('ç')">
<INPUT TYPE="button" VALUE="  €  "
onclick="addText('€')">
EOQ;
  }
elseif($type == "Italian")
  {
    $source = <<<EOQ
<INPUT TYPE="button" VALUE="  á  "
onclick="addText('á')">
<INPUT TYPE="button" VALUE="  à  "
onclick="addText('à')">
<INPUT TYPE="button" VALUE="  â  "
onclick="addText('â')"> 
<INPUT TYPE="button" VALUE="  ã  "
onclick="addText('ã')"> 
<INPUT TYPE="button" VALUE="  ä  "
onclick="addText('ä')"> 
<INPUT TYPE="button" VALUE="  å  "
onclick="addText('å')"> 
<INPUT TYPE="button" VALUE="  æ  "
onclick="addText('æ')"> 
<INPUT TYPE="button" VALUE="  À  "
onclick="addText('À')"> 
<INPUT TYPE="button" VALUE="  Á  "
onclick="addText('Á')"> 
<INPUT TYPE="button" VALUE="  Â  "
onclick="addText('Â')"> 
<INPUT TYPE="button" VALUE="  Ã  "
onclick="addText('Ã')"> 
<INPUT TYPE="button" VALUE="  Ä  "
onclick="addText('Ä')"> 
<INPUT TYPE="button" VALUE="  Å  "
onclick="addText('Å')"> 
<INPUT TYPE="button" VALUE="  Æ  "
onclick="addText('Æ')"> 
<INPUT TYPE="button" VALUE="  è  "
onclick="addText('è')"> 
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('ê')"> 
<INPUT TYPE="button" VALUE="  é  "
onclick="addText('ê')"> 
<INPUT TYPE="button" VALUE="  ë  "
onclick="addText('ë')"> 
<INPUT TYPE="button" VALUE="  È  "
onclick="addText('È')"> 
<INPUT TYPE="button" VALUE="  É  "
onclick="addText('É')"> 
<INPUT TYPE="button" VALUE="  Ê  "
onclick="addText('Ê')"> 
<INPUT TYPE="button" VALUE="  ì  "
onclick="addText('ì')"> 
<INPUT TYPE="button" VALUE="  í  "
onclick="addText('í')"> 
<INPUT TYPE="button" VALUE="  î  "
onclick="addText('î')"> 
<INPUT TYPE="button" VALUE="  ï  "
onclick="addText('ï')"> 
<INPUT TYPE="button" VALUE="  Ì  "
onclick="addText('Ì')"> 
<INPUT TYPE="button" VALUE="  Í  "
onclick="addText('Í')"> 
<INPUT TYPE="button" VALUE="  Î  "
onclick="addText('Î')"> 
<INPUT TYPE="button" VALUE="  Ï  "
onclick="addText('Ï')"> 
<INPUT TYPE="button" VALUE="  ò  "
onclick="addText('ò')"> 
<INPUT TYPE="button" VALUE="  ó  "
onclick="addText('ó')"> 
<INPUT TYPE="button" VALUE="  ô  "
onclick="addText('ô')"> 
<INPUT TYPE="button" VALUE="  õ  "
onclick="addText('õ')"> 
<INPUT TYPE="button" VALUE="  ö  "
onclick="addText('ö')"> 
<INPUT TYPE="button" VALUE="  Ò  "
onclick="addText('Ò')"> 
<INPUT TYPE="button" VALUE="  Ó  "
onclick="addText('Ó')"> 
<INPUT TYPE="button" VALUE="  Ô  "
onclick="addText('Ô')"> 
<INPUT TYPE="button" VALUE="  Õ  "
onclick="addText('Õ')"> 
<INPUT TYPE="button" VALUE="  Ö  "
onclick="addText('Ö')"> 
<INPUT TYPE="button" VALUE="  ù  "
onclick="addText('ù')"> 
<INPUT TYPE="button" VALUE="  ú  "
onclick="addText('ú')"> 
<INPUT TYPE="button" VALUE="  û  "
onclick="addText('û')"> 
<INPUT TYPE="button" VALUE="  ü  "
onclick="addText('ü')"> 
<INPUT TYPE="button" VALUE="  Ù  "
onclick="addText('Ù')"> 
<INPUT TYPE="button" VALUE="  Ú  "
onclick="addText('Ú')"> 
<INPUT TYPE="button" VALUE="  Û  "
onclick="addText('Û')"> 
<INPUT TYPE="button" VALUE="  Ü  "
onclick="addText('Ü')"> 
<INPUT TYPE="button" VALUE="  ç  "
onclick="addText('ç')"> 
<INPUT TYPE="button" VALUE="  Ç  "
onclick="addText('Ç')"> 
<INPUT TYPE="button" VALUE="  ñ  "
onclick="addText('ñ')"> 
<INPUT TYPE="button" VALUE="  Ñ  "
onclick="addText('Ñ')"> 
<INPUT TYPE="button" VALUE="  ß  "
onclick="addText('ß')"> 
<INPUT TYPE="button" VALUE="  ý  "
onclick="addText('ý')"> 
<INPUT TYPE="button" VALUE="  Ý  "
onclick="addText('Ý')"> 
<INPUT TYPE="button" VALUE="  ¿  "
onclick="addText('¿')"> 
<INPUT TYPE="button" VALUE="  ¡  "
onclick="addText('¡')"> 
EOQ;
  }
  return $source;
}

?>
