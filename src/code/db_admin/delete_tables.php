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


//DELETE SPECIFIC USER TABLES
//*****************************************************

mysql_connect($Muhost,$Muuser,$Mupword);

$sql = "DROP TABLE $udb.users";
if($result = mysql_query($sql))
{ echo "Success - table $table_name deleted. <br>";
}
else
{ echo "Error deleting $table_name. MySQL Error: " . mysql_error() . "";
}


//DELETE SPECIFIC MAIN TABLES
//*****************************************************
$tables_r = array(
'stu_blocks',
'periods',
'rooms',
'teachers',
'todate',
'dates',
'gone',
'dismiss',
'cut',
'excused',
'attendance',
'date_settings',
'file_settings',
'attendance1',
'attendance2',
'attendance3',
'defaults1',
'defaults2',
'defaults3',
'forced_score1',
'forced_score2',
'forced_score3',
'hr_attendance1',
'hr_attendance2',
'hr_attendance3',
'pwords1',
'pwords2',
'pwords3',
'scores1',
'scores2',
'scores3',
'seating1',
'seating2',
'seating3',
'spanish1',
'spanish2',
'spanish3',
'taken1',
'taken2',
'taken3',
'tests1',
'tests2',
'tests3',
'today1',
'today2',
'today3'
);

mysql_connect($Mmhost,$Mmuser,$Mmpword);

foreach($tables_r as $table_name)
{ $sql = "DROP TABLE $mdb.$table_name";
  if($result = mysql_query($sql))
  { echo "Success - table $table_name deleted. <br>";
  }
  else
  { echo "Error deleting $table_name. MySQL Error: " . mysql_error() . "";
  }
}

/*
//DELETE ALL TABLES IN USER DATABASE ***CAUTION YOU MAY HAVE NON GNUSCHOOL USERS OR TABLES YOU NEED***
//*****************************************************
$found_tables = array();

if (!$mylink)
{
  die("Could not connect: " . mysql_error());
}

$sql = "SHOW TABLES FROM $mdb";
if($result = mysql_query($sql))
{
  while($row = mysql_fetch_row($result))
  { $found_tables[]=$row[0];
  }
}
else
{ die("Error, could not list tables. MySQL Error: " . mysql_error());
}
 
foreach($found_tables as $table_name)
{ $sql = "DROP TABLE $mdb.$table_name";
  if($result = mysql_query($sql))
  { echo "Success - table $table_name deleted. <br>";
  }
  else
  { echo "Error deleting $table_name. MySQL Error: " . mysql_error() . "";
  }
}

//DELETE ALL USER TABLES IN MAIN DATABASE ***CAUTION YOU MAY HAVE NON GNUSCHOOL TABLES YOU NEED***
//**********************************************

$found_tables = array();

$mylink = mysql_connect($Muhost,$Muuser,$Mupword);
if (!$mylink)
{
  die("Could not connect: " . mysql_error());
}

$sql = "SHOW TABLES FROM $udb";
if($result = mysql_query($sql))
{
  while($row = mysql_fetch_row($result))
  { $found_tables[]=$row[0];
  }
}
else
{ die("Error, could not list tables. MySQL Error: " . mysql_error());
}
 
foreach($found_tables as $table_name)
{ $sql = "DROP TABLE $udb.$table_name";
  if($result = mysql_query($sql))
  { echo "Success - table $table_name deleted. <br>";
  }
  else
  { echo "Error deleting $table_name. MySQL Error: " . mysql_error() . "";
  }
}
*/



?>