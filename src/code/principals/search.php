<?php 
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


function post2_stu_blocks_query()
{
  $fname=$_POST['fname'];
  $mname=$_POST['mname'];
  $lname=$_POST['lname'];
  $hr_num=$_POST['hr_num'];
  $all_hr=$_POST['all_hr'];
  $active=$_POST['active'];

  $query = "";
  $where_query = "";
  $and_flag = 0;
  if (!empty($fname)) if($and_flag)
  { $where_query .= " and fname = '$fname'";}
  else { $where_query .= " fname = '$fname'"; $and_flag=1;}

  if (!empty($mname))
  {
    if (strlen($mname)== 1) $mname = $mname . ".";
    if($and_flag)
    { $where_query .= " and mname = '$mname'";}
    else { $where_query .= " mname = '$mname'"; $and_flag=1;}
  }

  if (!empty($lname)) if($and_flag)
  { $where_query .= " and lname = '$lname'";}
  else { $where_query .= " lname = '$lname'"; $and_flag=1;}

  if (!empty($hr_num)) if($and_flag)
  { $where_query .= " and hr = '$hr_num'";}
  else { $where_query .= " hr = '$hr_num'"; $and_flag=1;}

  if(!$and_flag && empty($all_hr))return "";

  if (!empty($active)) if($and_flag)
  { $where_query .= " and active = 'y'";}
  else { $where_query .= " active = 'y'"; $and_flag=1;}

  if($and_flag)
  {
    $query = "select * from stu_blocks where $where_query  
              order by hr,lname";
  }
  else
   if ( !empty($all_hr) )
    $query = "select * from stu_blocks order by hr,lname";

  return $query;
}


function glue_search_inputs()
{
  $search_r = array();
  $search_r['ok']=1;
  $search_r['errors'];

  $str_y = $_POST['search_start_y'];
  $len = chars_are_nums($str_y);
  if ( $len != 4  || !$len )
  {
    $search_r['ok']=0;
    $search_r['errors'].="start year '$str_y' <br>";
  }

  $str_m = $_POST['search_start_m'];
  $len = chars_are_nums($str_m);
  if ( $len > 2  || !$len )
  {
    $search_r['ok']=0;
    $search_r['errors'].="start month '$str_m' <br>";
  }
  else
    if ( $len == 1 ) $str_m = "0" . $str_m;

  $str_d = $_POST['search_start_d'];
  $len = chars_are_nums($str_d);
  if ( $len > 2  || !$len )
  {
    $search_r['ok']=0;
    $search_r['errors'].="start day '$str_d' <br>";
  }
  else
    if ( $len == 1 ) $str_d = "0" . $str_d;

  $search_start="$str_y"."-"."$str_m"."-"."$str_d";

  $str_y = $_POST['search_end_y'];
  $len = chars_are_nums($str_y);
  if ( $len != 4  || !$len )
  {
    $search_r['ok']=0;
    $search_r['errors'].="end year '$str_y' <br>";
  }

  $str_m = $_POST['search_end_m'];
  $len = chars_are_nums($str_m);
  if ( $len > 2  || !$len )
  {
    $search_r['ok']=0;
    $search_r['errors'].="end month '$str_m' <br>";
  }
  else
    if ( $len == 1 ) $str_m = "0" . $str_m;

  $str_d = $_POST['search_end_d'];
  $len = chars_are_nums($str_d);
  if ( $len > 2  || !$len )
  {
    $search_r['ok']=0;
    $search_r['errors'].="end day '$str_d' <br>";
  }
  else
    if ( $len == 1 ) $str_d = "0" . $str_d;

  $search_end="$str_y"."-"."$str_m"."-"."$str_d";    


  $search_r['search_start'] = $search_start;
  $search_r['search_end'] = $search_end;

  return $search_r;
}


function date_search_form($def, $submit_val,$today_date,$only_cut_hr=0)
{
  $default_start = $def['search_start'];
  $default_end = $def['search_end'];
  $q1_start = $def['q1_start'];
  $q2_start = $def['q2_start'];
  $q3_start = $def['q3_start'];
  $q4_start = $def['q4_start'];
  $q1_end = $def['q1_end'];
  $q2_end = $def['q2_end'];
  $q3_end = $def['q3_end'];
  $q4_end = $def['q4_end'];

  $date_r = explode("-",$today_date);

  echo <<<EOQ
<TABLE  CLASS=SEARCH>
 <form method=post action="$PHP_SELF">
 <TR>
  <TD>
   <b>Start date:</b>
  </TD>
 </TR>
 <TR>
  <TD>
   <TABLE class=search1>
    <TR>
     <TD>
      <b>Month</b> (2 digits):
      <input id="s2" type=text name="search_start_m" 
      value="$date_r[1]" size=3 maxlength=2>
     </TD>
     <TD>
      <b>Day</b> (2 digits):
      <input id="s3" type=text name="search_start_d"
      value="$date_r[2]" size=3 maxlength=2>
     </TD>
     <TD>
      <b>Year</b> (4 digits): 
      <input id="s1" type=text name="search_start_y"
      value="$date_r[0]" size=6 maxlength=4>
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
EOQ;

  echo <<<EOQ
 <TR>
  <TD>
   <TABLE class=search2>
    <TR>
     <TD>
<input type=radio name="search_start" value="$today_date" checked onclick="set_s_values(this)">
Today's date
     </TD>
     <TD colspan=3>
<input type=radio name="search_start" value="$default_start" onclick="set_s_values(this)">
Default start date
     </TD>
    </TR>
    <TR>
     <TD>
<input type=radio name="search_start" value="$q1_start" onclick="set_s_values(this)">
Start of 1st quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_start" value="$q2_start" onclick="set_s_values(this)">
Start of 2nd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_start" value="$q3_start" onclick="set_s_values(this)">
Start of 3rd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_start" value="$q4_start" onclick="set_s_values(this)">
Start of 4th quarter &nbsp
     </TD>
    </TR>
    <TR>
     <TD>
<input type=radio name="search_start" value="$q1_end" onclick="set_s_values(this)">
End of 1st quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_start" value="$q2_end" onclick="set_s_values(this)">
End of 2nd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_start" value="$q3_end" onclick="set_s_values(this)">
End of 3rd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_start" value="$q4_end" onclick="set_s_values(this)">
End of 4th quarter &nbsp
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
EOQ;

  echo <<<EOQ
 <TR>
  <TD>&nbsp
  </TD>
 <TR>
 </TR>
  <TD>
   <b>End date:</b>
  </TD>
 </TR>
 <TR>
  <TD>
   <TABLE class=search1>
    <TR>
     <TD>
      <b>Month</b> (2 digits):
      <input id="e2" type=text name="search_end_m" 
      value="$date_r[1]" size=3 maxlength=2>
     </TD>
     <TD>
      <b>Day</b> (2 digits):
      <input id="e3" type=text name="search_end_d" 
      value="$date_r[2]" size=3 maxlength=2>
     </TD>
     <TD>
      <b>Year</b> (4 digits): 
      <input id="e1" type=text name="search_end_y" 
      value="$date_r[0]" size=6 maxlength=4>
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
EOQ;

  echo <<<EOQ
 <TR>
  <TD>
   <TABLE class=search2>
    <TR>
     <TD>
<input type=radio name="search_end" value="$today_date" checked onclick="set_e_values(this)">
Today's date
     </TD>
     <TD colspan=3>
<input type=radio name="search_end" value="$default_end" onclick="set_e_values(this)">
Default end date
     </TD>
    </TR>
    <TR>
     <TD>
<input type=radio name="search_end" value="$q1_start" onclick="set_e_values(this)">
Start of 1st quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_end" value="$q2_start" onclick="set_e_values(this)">
Start of 2nd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_end" value="$q3_start" onclick="set_e_values(this)">
Start of 3rd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_end" value="$q4_start" onclick="set_e_values(this)">
Start of 4th quarter &nbsp
     </TD>
    </TR>
    <TR>
     <TD>
<input type=radio name="search_end" value="$q1_end" onclick="set_e_values(this)">
End of 1st quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_end" value="$q2_end" onclick="set_e_values(this)">
End of 2nd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_end" value="$q3_end" onclick="set_e_values(this)">
End of 3rd quarter &nbsp
     </TD>
     <TD>
<input type=radio name="search_end" value="$q4_end" onclick="set_e_values(this)">
End of 4th quarter &nbsp
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
EOQ;

 echo <<<EOQ
 <TR>
  <TD>
   <TABLE class=search1>
EOQ;

if($only_cut_hr)
 echo <<<EOQ
    <TR>
     <TD colspan=2>
<input type=checkbox name=hr_cut value="notdefault">
<b>Only students that may have cut homeroom</b>
     </TD>
    </TR>
EOQ;

 echo <<<EOQ
    <TR>
     <TD>
<input type=checkbox name=active value="active" checked='yes'><b>Active Students</b>
     </TD>
     <TD>
<input type=checkbox name=all_hr value="absents"><b>All homerooms</b>
     </TD>
    </TR>
    <TR>
     <TD colspan=2>
<b>Homeroom:</b>
<input type=text name=hr_num size=10 maxlength=10>
     </TD>
    </TR>
    <TR>
     <TD colspan=2>
<b>Student first name:</b>
<input type=text name=fname size=30 maxlength=30>
     </TD>
    </TR>
    <TR>
     <TD colspan=2>
<b>Student middle name:</b>
<input type=text name=mname size=30 maxlength=30>
     </TD>
    </TR>
    <TR>
     <TD colspan=2>
<b>Student last name:</b>
<input type=text name=lname size=30 maxlength=30>
     </TD>
    </TR>
    <TR>
     <TD>
<input type=submit name=menu_submit value="$submit_val">
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
 </form>
</TABLE>
EOQ;
}


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


?>
