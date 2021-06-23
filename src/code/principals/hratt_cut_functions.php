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


function get_period_tid_r($periods)
{
 $p_t_r = array();
 $period = "";
 $length = strlen($periods);
 $periods = substr($periods,1,$length-2);
 $p_r = explode("|",$periods);
  for($i=0;$i<count($p_r);$i++)
 {
   $p_tid_str = $p_r[$i];
   $per_tid_r = explode("~",$p_tid_str);
   $period = $per_tid_r[0];
   $tid = $per_tid_r[1];
   $p_t_r["$period"]=$tid;
 }
 return $p_t_r;
}


//$did_ends_r[0]=$date_id lowest date  $did_ends_r[1]=$date_id greatest date
//$did_date_r[date id]=>actual date (all dates between low and high date inclusive)
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


function get_date_query($start=0, $end=0)
{
   if($start > $end)
  {
    $temp = $end;
    $end = $start;
    $start = $temp;
  }

  $where = "";

  if ($start)
  {
    if($end)
      $where = "(date >= $start and date <= $end)";
    else
      $where = "date = $start";
  }
  else
    if ($end)
      $where = "date = $end";

  return $where;
}


function get_todate()
{
  $query = "select date from todate where search='cut'";
  $result = mysql_query($query);
  //query_outcome_echo($query,$result,0);
  $row = mysql_fetch_row($result);
  $todate = $row[0]; 
  return $todate;
}


function main_attendance_insert($id, $did, $time_in, $is_in=1)
{
  $query = "select is_in from attendance
            where id=$id and date=$did";
  $result = mysql_query($query);
  if (mysql_num_rows($result) == 0)
  {
    $query="insert into attendance (id,date,time_in,is_in)
            values ($id,$did,'$time_in',$is_in)";
    mysql_query($query);
  }
}


function main_cut_insert ($id, $did, $period)
{
  $query="select * from cut where id=$id
          and period='$period' and date=$did";
  $result = mysql_query($query);
//query_outcome_echo($query,$result,0);
  if( mysql_num_rows($result) === 0 )
  {
    $query="insert into cut (id,date,period)
            values ($id,$did,'$period')";
    mysql_query($query);
//query_outcome_echo($query,$result,0);
  }
}


function in_non_HR_period_r($id,$p_tid_r,$did)
{
  $per_in_r = array();
  $in_r = array();
  $inper = 0;
  while (list($period,$tid) = each($p_tid_r))
  {
    $table = "attendance" . $tid;
    $query="select is_in from $table where id=$id
            and period='$period' and date=$did";
    $result = mysql_query($query);
//query_outcome_echo($query,$result,0);
    if ($row = mysql_fetch_row($result))
    {
      $is_in = $row[0];
      $in_r["$period"] = $is_in;
      if($is_in)$inper = 1;
    }
  }
  $per_in_r[0]=$inper;
  $per_in_r[1]=$in_r;
  return $per_in_r;
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


function in_hrattendance_r($id,$did,$hr_tid)
{
  $table = "hr_attendance" . $hr_tid;
  $query = "select time_in,is_in from $table where
            id=$id and date=$did";
  $result = mysql_query($query);
//query_outcome_echo($query,$result,0);
  if ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    return $row;
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


// for now record time_in for each and every student
// later record only students that are late to homeroom or have cut homeroom class
// and instead of saving in time column, save in tinyint column the minutes
// student is late
function update_cut_att($date)
{
$todate = get_todate();
$hr_tid_r = get_hroom_teacherID_assoc_r();
//print"hr_tid_r "; print_r($hr_tid_r);print"<br>";

$id_rr = get_2id_iddate_rr($todate, "<" , $date);
if(!empty($id_rr))
{
  $query="update todate set date='$date'";
  mysql_query($query);
//  query_outcome_echo($query,$result,$errors);

  $id_date_r = $id_rr[1];

  $stu_query = "select id,periods,hr from stu_blocks";
  $result = mysql_query($stu_query);

  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  { 
    $hr = $row['hr'];
    $periods = $row['periods'];
    $p_tid_r = get_period_tid_r($periods);
    $id = $row['id'];  
    $hr_tid = $hr_tid_r["$hr"];
    while (list($did,$ndate) = each($id_date_r))
    {
      $hr_r = in_hrattendance_r($id,$did,$hr_tid);
      $in_per_in_rr = in_non_HR_period_r($id,$p_tid_r,$did);
      $in_periods = $in_per_in_rr[0];
      $per_in_r = $in_per_in_rr[1];

      if(!empty($hr_r))  // this homeroom took attendance
      {
        $timein = $hr_r['time_in'];
        $in_hr = $hr_r['is_in'];
        if($in_hr)
        {
// in homeroom  = 1
          main_attendance_insert($id, $did, $timein, 1);
          if($in_periods)
            while (list($period,$in_p) = each($per_in_r))
            {
              if(!$in_p) main_cut_insert($id,$did,$period);
            }
          else
          {
            while (list($period,) = each($p_tid_r))
            {
              main_cut_insert($id,$did,$period);
            }
            reset($p_tid_r);
          }
        }
        else
        {
          if($in_periods)
          {
// not in homeroom but in periods, possible cut = 2
            main_attendance_insert($id, $did, "", 2);
            while (list($period,$in_p) = each($per_in_r))
            {
              if(!$in_p) main_cut_insert($id,$did,$period);
            }
          }
          else
          {
// not in School = 0
            main_attendance_insert($id, $did, "",0);
          }
        } 
      }
      else  //this homeroom didn't take attendance
      {
        if($in_periods)
        {
// in school but there was no homeroom class = 3
          main_attendance_insert($id, $did, "",3);
          while (list($period,$in_p) = each($per_in_r))
          {
            if(!$in_p) main_cut_insert($id,$did,$period);
          }
        }
        else
        {
// not in School = 0
          main_attendance_insert($id, $did, "",0);
        }
      }
    }
    reset($id_date_r);
  }
 }
}

?>
