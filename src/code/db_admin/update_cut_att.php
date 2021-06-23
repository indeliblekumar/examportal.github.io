
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

$parent=$_POST['parent'];

include('./login.php');


//  $query="delete from cut where date>'2012-10-26'";
//  mysql_query($query);

//  $query="update todate set date='0000-00-00'";
//  mysql_query($query);


function main_cut_insert ($id, $did, $period)
{
  $query="select * from cut where id=$id
          and period='$period' and date=$did";
  $result = mysql_query($query);
  if( mysql_num_rows($result) === 0 )
  {
    $query="insert into cut (id,date,period)
            values ($id,$did,'$period')";
    mysql_query($query);
  }
}


function main_attendance_insert($id, $did, $time_in)
{
  $query = "select is_in from attendance
            where id=$id and date=$did";
  $result = mysql_query($query);
  if (mysql_num_rows($result) == 0)
  {
    $query="insert into attendance (id,date,time_in,is_in)
            values ($id,$did,'$time_in',1)";
    mysql_query($query);
  }
}


// $p_names_rr[0]=in_school_flag [1]=time_in $hr_attendance
function is_in_hr_attendance($id,$tid,$did)
{
 $is_in_time_r=array();
 $is_in_time_r[0] = "";
 $is_in_time_r[1] = "00:00:00";

 $table = "hr_attendance" . "$tid";
 $query = "select time_in, is_in from $table 
           where id=$id and date=$did";
 $result = mysql_query($query);
//  query_outcome_echo($query,$result,$errors);

 if ($row=mysql_fetch_array($result, MYSQL_ASSOC))
 {
   $is_in_time_r[0] = $row['is_in'];
   $is_in_time_r[1] = $row['time_in'];
 }
 return $is_in_time_r;
}


// returns $periods_rr[0]=1(in school) 2(not in school) [1]=$periods_r
function get_periods_attended(
           $id,$cur_period,$period_tid_rr,$did,$cur_is_in)
{
 $periods_rr=array();
 $periods_rr[0]=0;
 $periods_r=array();

 for($i=0;$i<sizeof($period_tid_rr);$i++)
 {
   $is_in = 0;
   $period = $period_tid_rr[$i][0];
   $tid = $period_tid_rr[$i][1];
       
   if($period === $cur_period)
   {
     $is_in = $cur_is_in;
   }
   else
   {
     $table = "attendance" . "$tid";
     $query="select is_in from $table where id=$id
             and period='$period' and date=$did";
     $result = mysql_query($query);
     if ($row = mysql_fetch_row($result))
     {
       $is_in = $row[0];
     }
   }

   if($is_in)
   {
     $periods_r["$period"] = 1;
     $periods_rr[0] = 1;
   }
   else
     $periods_r["$period"] = 0;
 }
 $periods_rr[1]=$periods_r;

 return $periods_rr;
}


function insert_attend_cuts($id,$periods_rr,$did,$in_hr,$time_in)
{
 $in_school = $periods_rr[0];
 $periods_r = $periods_rr[1];
 if ($in_school)
 {
   main_attendance_insert($id, $did, $time_in);
   if (!$in_hr)
     main_cut_insert($id, $did, "HR");
 }
 while (list($period, $is_in) = each($periods_r))
 {
   if (!$is_in)
   { 
     if ($in_school)
     {
       main_cut_insert($id, $did, $period);
     }
   }
 }
}

$tag_title = "gnuschool.org Cut & Attendance Update";
$page_title = "Cut & Attendance Update <br>
               This page was opened $date at $time";


//  $query="update todate set date='0000-00-00'";
//  mysql_query($query);


start_html($tag_title);
stylesheet_link("../style_sheet");
start_body("main_background");
back_2_parentA($parent);
$query = "select date from todate where search='cut'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$todate = $row[0];
//query_outcome_echo($query,$result,0);
if($todate !== $date)
{
  if(empty($todate))
    $date_clause=" date<'$date'";
  else
    $date_clause=" (date>='$todate' and date<'$date')";

  $query = "select id, periods from stu_blocks";
  $result = mysql_query($query);
  //query_outcome_echo($query,$result,0);

print"<br><br>";
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $id = $row['id'];
    $periods = $row['periods'];
    $length = strlen($periods);
    $periods = substr($periods,1,$length-2);
    $periods_r = explode("|",$periods);
    $period_tid_rr = array();
    $hr_tid = "";

    // student periods string into $period_tid_rr excluding HR(homeroom)
    for($i=0;$i<sizeof($periods_r);$i++)
    {
      $period_tid_str = $periods_r[$i];
      $period_tid_r = array();
      $period_tid_r = explode("~",$period_tid_str);
      $period = $period_tid_r[0];
      if ($period == "HR" ) $hr_tid = $period_tid_r[1];
      else
      {
        $period_tid_rr[] = $period_tid_r;
      }
    }

    $dates_done_r = array();
    for($i=0;$i<sizeof($period_tid_rr);$i++)
    {
      $is_in = "2";
      $period = $period_tid_rr[$i][0];
      $tid = $period_tid_rr[$i][1];

      $table = "attendance" . $tid;     
      $query="select date, is_in from $table where id=$id
              and period='$period' and $date_clause";
      $result1 = mysql_query($query);
      $errors=0;
//      query_outcome_echo($query,$result1,$errors);
      while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
      { 
        $cur_date = $row1['date'];
        $done_date = $dates_done_r["$cur_date"];

        if (empty($done_date))
        {
          $is_in = $row1['is_in'];

//print "<br> is_in_hr_attendance(id,hr_tid,cur_date)<br>";
//print "is_in_hr_attendance($id,$hr_tid,$cur_date)";
          $is_in_hr_r = is_in_hr_attendance($id,$hr_tid,$cur_date);
//print "is_in_hr_r= "; print_r($is_in_hr_r); print"<br>";
          $is_in_hr = $is_in_hr_r[0];
          $time_in = $is_in_hr_r[1];

//print "get_periods_attended(id,period,period_tid_rr,cur_date,is_in)<br>";  
//print "get_periods_attended($id,$period,$period_tid_rr,$cur_date,$is_in)";  
          $periods_rr = get_periods_attended( 
               $id,$period,$period_tid_rr,$cur_date,$is_in);  
//print"periods_rr= "; print_r($periods_rr); print"<br>";
          if($is_in_hr)$periods_rr[0]=1;

//print "insert_attend_cuts(id,periods_rr,cur_date,is_in_hr,time_in)<br>";
//print "insert_attend_cuts($id,periods_rr,$cur_date,$is_in_hr,$time_in)";
//print"<br>";
          insert_attend_cuts(
               $id,$periods_rr,$cur_date,$is_in_hr,$time_in);
          $dates_done_r["$cur_date"] = 1;
//print"<br>";
        }
      }
    }
  }
  $comment=
    "Please wait. Script is processing ...................................
     <br><br>Automate this script to run when no users are logged into the system.
     Opening this page updates the database.<br><br>
     .... the database has been updated.";
  title_comment_div_noend($page_title, $comment);

  $query="update todate set date='$date'";
  $result = mysql_query($query);
//  query_outcome_echo($query,$result,$errors);
}
else
{
  $comment=
    "Automate this script to run when no users are logged into the system.
     Opening this page updates the database.<br><br>
     The database was already updated today.";
  title_comment_div_noend($page_title, $comment);
}

back_2_parentA($parent);
echo "</DIV>";

?>
</body></html>
