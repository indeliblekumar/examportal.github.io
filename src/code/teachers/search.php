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

function post2_stu_blocks_query($order = "lname")
{
  $fname=$_POST['fname'];
  $mname=$_POST['mname'];
  $lname=$_POST['lname'];
  $id=$_POST['id'];
  $active=$_POST['active'];


  $query = "";
  $where = "";
  $and_flag = 0;

  if (!empty($id))if($and_flag)
    { $where .= " and id = $id";}
    else { $where .= " id = $id"; $and_flag=1;}

  if (!empty($fname))if($and_flag)
    { $where .= " and fname = '$fname'";}
    else { $where .= " fname = '$fname'"; $and_flag=1;}

  if (!empty($mname))
  {
    if (strlen($mname)== 1) $mname = $mname . ".";
    if($and_flag)
    { $where .= " and mname = '$mname'";}
    else { $where .= " mname = '$mname'"; $and_flag=1;}
  }

  if (!empty($lname))if($and_flag)
    { $where .= " and lname = '$lname'";}
    else { $where .= " lname = '$lname'"; $and_flag=1;}
    
  if (!empty($active)) if($and_flag)
  { $where_query .= " and active = 'y'";}
  else { $where_query .= " active = 'y'"; $and_flag=1;}

 if(!empty($order))
   $order = "order by $order";

  if ($and_flag)
      $query = "select * from stu_blocks where $where $order";

  return $query;
}


function stu_blocks2_r($stu_query)
{
  $all_rows = array();
  $result = mysql_query($stu_query);
  if(mysql_num_rows($result) == 0) return 0;
 else 
 {
   while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
     $all_rows[] = $row;
   }
 }
 return $all_rows;
}


function post2_spanish_query($tid="", $order = "period" )
{
  $fname2=$_POST['fname2'];
  $period=$_POST['period'];
  $all_periods=$_POST['all_periods'];

  $spanish_query = "";

  if( !empty($order))
    $order = "order by $order";

  $table = "spanish" . "$tid";
  if (empty($all_periods))
  {
    $where = "";
    $and_flag = 0;

    if (!empty($period))
    {
      if($and_flag)
      {
        $where .= " and period = '$period'";
      }
      else
      {
        $where .= " period = '$period'"; $and_flag=1;
      }
    }

    if (!empty($fname2))
    {
      if($and_flag)
      {
        $where .= " and fname2 = '$fname2'";
      }
      else
      {
        $where .= " fname2 = '$fname2'"; $and_flag=1;
      }
    }
     
    if ($and_flag)
      $spanish_query = "select * from $table
                        where $where $order";
  }
  else
  {
     $spanish_query = "select * from $table $order";
  }
  return $spanish_query;
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


function date_search_form($def, $submit_val,$today_date)
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

 echo <<<EOQ
    <TR>
     <TD>
<input type=checkbox name=active value="active" checked='yes'><b>Active Students</b>
     </TD>
     <TD>
<input type=checkbox name=all_periods value="all_pers"><b>All periods</b>
     </TD>
    </TR>
    <TR>
     <TD colspan=2>
<b>Period:</b>
<input type=text name=period size=4 maxlength=4>
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


/*
function date_search_form($def, $submit_val, $today_date)
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
<form method=post action="$PHP_SELF">
<b>Start date:</b><br>

<b>Month</b> (2 digits):
<input id="s2" type=text name="search_start_m" 
value="$date_r[1]" size=3 maxlength=2>
&nbsp&nbsp&nbsp<b>Day</b> (2 digits):
<input id="s3" type=text name="search_start_d"
value="$date_r[2]" size=3 maxlength=2>
&nbsp&nbsp&nbsp<b>Year</b> (4 digits): 
<input id="s1" type=text name="search_start_y"
value="$date_r[0]" size=6 maxlength=4>
<br>
EOQ;

  echo <<<EOQ
<input type=radio name="search_start" value="$today_date" checked onclick="set_s_values(this)">
Today's date
<input type=radio name="search_start" value="$default_start" onclick="set_s_values(this)">
Default start date<br>
<input type=radio name="search_start" value="$q1_start" onclick="set_s_values(this)">
Start of 1st quarter
<input type=radio name="search_start" value="$q2_start" onclick="set_s_values(this)">
Start of 2nd quarter
<input type=radio name="search_start" value="$q3_start" onclick="set_s_values(this)">
Start of 3rd quarter
<input type=radio name="search_start" value="$q4_start" onclick="set_s_values(this)">
Start of 4th quarter <br>
<input type=radio name="search_start" value="$q1_end" onclick="set_s_values(this)">
End of 1st quarter
<input type=radio name="search_start" value="$q2_end" onclick="set_s_values(this)">
End of 2nd quarter
<input type=radio name="search_start" value="$q3_end" onclick="set_s_values(this)">
End of 3rd quarter
<input type=radio name="search_start" value="$q4_end" onclick="set_s_values(this)">
End of 4th quarter
EOQ;

echo <<<EOQ
<br><br>
<b>End date:</b><br>
<b>Month</b> (2 digits):
<input id="e2" type=text name="search_end_m" 
value="$date_r[1]" size=3 maxlength=2>
&nbsp&nbsp&nbsp<b>Day</b> (2 digits):
<input id="e3" type=text name="search_end_d" 
value="$date_r[2]" size=3 maxlength=2>
&nbsp&nbsp&nbsp<b>Year</b> (4 digits): 
<input id="e1" type=text name="search_end_y" 
value="$date_r[0]" size=6 maxlength=4>
<br>
EOQ;

  echo <<<EOQ
<input type=radio name="search_end" value="$today_date" checked onclick="set_e_values(this)">
Today's date
<input type=radio name="search_end" value="$default_end" onclick="set_e_values(this)">
Default end date<br>
<input type=radio name="search_end" value="$q1_start" onclick="set_e_values(this)">
Start of 1st quarter
<input type=radio name="search_end" value="$q2_start" onclick="set_e_values(this)">
Start of 2nd quarter
<input type=radio name="search_end" value="$q3_start" onclick="set_e_values(this)">
Start of 3rd quarter
<input type=radio name="search_end" value="$q4_start" onclick="set_e_values(this)">
Start of 4th quarter <br>
<input type=radio name="search_end" value="$q1_end" onclick="set_e_values(this)">
End of 1st quarter
<input type=radio name="search_end" value="$q2_end" onclick="set_e_values(this)">
End of 2nd quarter
<input type=radio name="search_end" value="$q3_end" onclick="set_e_values(this)">
End of 3rd quarter
<input type=radio name="search_end" value="$q4_end" onclick="set_e_values(this)">
End of 4th quarter
EOQ;

echo <<<EOQ
<br><br>
<input type=checkbox name=active value="active" checked='yes'><b>Active Students:</b><br><br>
<b>Period:</b><br>
<input type=text name=period size=4>&nbsp &nbsp
<input type=checkbox name=all_periods value="all_pers"><b>All periods:</b>
<br><br>

<b>Student first name:</b><br>
<input type=text name=fname size=30>
<br><br>

<b>Student last name:</b><br>
<input type=text name=lname size=30>
<br><br>

<input type=submit name=menu_submit value="$submit_val">
</form>
EOQ;
}
*/

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


function get_date_id ($date)
{
  $query = "select id from dates where
            date = '$date'";
  $result = mysql_query($query);
  if ($row = mysql_fetch_row($result))
    return $row[0];
  else
    return 0;
}


function get_id_date ($did)
{
  $query = "select date from dates where
            id = $did";
  $result = mysql_query($query);
  if ($row = mysql_fetch_row($result))
    return $row[0];
  else
    return 0;
}


function order_date_r($date, $search_r)
{
  $date1 = $search_r['search_start'];
  $date2 = $search_r['search_end'];

  if($date1 > $date2)
  {
    $temp = $date2;
    $date2 = $date1;
    $date1 = $temp;
  }
  if($date < $date2) $date2 = $date;

  return array($date1,$date2);
}


//$did_ends_r[0]=$id lowest date  $did_ends_r[1]=$id greatest date
//$did_date_r[date id]=>actual date
//returns $did_rr[0]=>$did_ends_r  $did_rr[1]=>$did_date_r
function get_2id_iddate_rr( $date1, $lt_e, $date2)
{
  $did_ends_r=array();
  $did_date_r=array();
  $did_rr=array();
  $query = "select * from dates where ( date >= '$date1' and date $lt_e '$date2' )
                     order by id";
  $result = mysql_query($query);
//query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0)
  {
    return 0;
  }
  else
  {
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $id = $row['id'];
    $did_date_r["$id"]=$row['date'];
    $did_ends_r[0]=$id;
    
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
       $id = $row['id'];
       $did_date_r["$id"]=$row['date'];
    }

    $did_ends_r[1]=$id;
    $did_rr[0]=$did_ends_r;
    $did_rr[1]=$did_date_r;
    return $did_rr;
  }
}

?>

