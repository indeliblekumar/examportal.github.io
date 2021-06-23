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

$proceed = $_POST['proceed'];
$delim = $_POST['delim'];
//$mail_up = $_POST['mail_up'];
//$show_up = $_POST['show_up'];
//$e_mail = $_POST['e_mail'];

$show_up = 1;

//**testing**
/*
if(empty($mail_up)) $mail_up = 0;
else $mail_up = 1;
if(empty($show_up)) $show_up = 0;
else $show_up = 1;
if(empty($e_mail)) $e_mail = 0;
else $e_mail = 1;
*/

include('./login.php');

$yes_input = 1;

if(empty($proceed))
  $yes_input = 0;


/* this proceed function gives more options
// the email option needs more testing
// to simplify, we use function proceed_form()
function proceed_delim_form()
{
  echo <<<EOQ
<form method=post action="$PHP_SELF">
<input type=checkbox name=e_mail value="yes"><b>Teacher column name 
TEMAIL exists. It holds the teacher's email address.</b>
<BR><BR>
<input type=checkbox name=mail_up value="yes"><b>Mail each user name
and password to that teacher's e-mail address.</b>
<BR><BR>
<input type=checkbox name=show_up value="yes"><b>Display all user name
and password data.</b>
<BR><BR><B>Field delimiter</B>
<BR>
<input type=text size=8 name=delim maxlength=6>
<BR>Use a different delimiter than the file settings delimiter.
<BR> 
It can be a single character such as -
<BR>Or a combination of characters such as &&&
<BR><BR>
<input type=submit name=proceed value="Proceed">
</form>
EOQ;
}
*/

function syllable_rand($start=0)
{
  $str = "";
  $vow = array('a','e','i','o','u');
  $vow_p = array('ai','au','ay','ea','ee','ia','ie'
                 ,'oa','oi','oy','oo','ou');
  $con = array('b','c','d','f','g','h','j','k','l','m','n','p'
               ,'r','s','t','v','w','z');
  $con_p = array('bl','br','ch','cl','cr','dr','fl','fr','gl'
                 ,'gr','ph','pl','pr','sc','sh','sl','sm','sn'
                 ,'sp','st','sw','th','tr','tw');

  $v = rand(0,sizeof($vow) - 1);
  $vp = rand(0,sizeof($vow_p) - 1);
  $c = rand(0,sizeof($con) - 1); 
  $cp = rand(0,sizeof($con_p) - 1); 

  $odds = rand(0,20);
  if($odds < 3 && $start)
  {
    $str = $vow[$v];
    return $str;
  }
  
  $odds = rand(0,20);
  if ($odds < 7)
    $str = $con_p[$cp];
  else
    $str = $con[$c];

  $odds = rand(0,20);
  if ($odds > 8)
    $str .= $vow[$v];
  elseif ($odds < 2)
    $str .= 'y';
  else
    $str .= $vow_p[$vp];
  
  return $str;
}


function grant_access_S($stu_pword)
{
  $errors = 0;
  $query = "grant select on * to $stu_pword";
  $result = mysql_query($query);
  query_outcome_echo($query,$result,$errors);

  $query = "grant update, insert on scores to $stu_pword";
  $result = mysql_query($query);
  query_outcome_echo($query,$result,$errors);

  $query = "grant update, insert on taken to $stu_pword";
  $result = mysql_query($query);
  query_outcome_echo($query,$result,$errors);
  return $errors;
}


function create_tables($tid)
{
  $fields = "(date smallint unsigned, score tinyint(4),
              test_id mediumint(8) unsigned, id int(11) unsigned,
              period varchar(12),makeup char(1),test text)";
  $table = "scores" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields = "(date smallint unsigned, test_id mediumint(8) unsigned,
      id int(11) unsigned,period varchar(12),reason varchar(64))";
  $table = "forced_score" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields="(fname2 varchar(30) default '' not null
           ,fname2_is char(1) default 'n' not null
           ,quote char(1) default 'n' not null
           ,period varchar(12) default '' not null
           ,id int(11) unsigned)";
  $table = "spanish" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields = "(description varchar(255), type varchar(30), answers text, 
    test_id mediumint(8) unsigned auto_increment, isbn char(10),  
    title varchar(125), author varchar(125), page varchar(10), test text,
    primary key(test_id))";
  $table = "tests" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  if ($result !== FALSE)
  {
    $query = "insert into $table (description, type
              , answers, isbn,  title, author, page, test) 
              values ( 'empty test','','','','','','','')";
    $result = mysql_query($query);
    $errors = query_outcome_echo($query,$result,$errors);
  }

  $fields="(date smallint unsigned,time_in time,
            is_in tinyint(1) default 0 not null,id int(11) unsigned)";
  $table = "hr_attendance" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields="(period varchar(12),date smallint unsigned,time_in time,
            is_in tinyint(1) default 0 not null,id int(11) unsigned)";
  $table = "attendance" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields = "(period varchar(12), seat_x mediumint(8) unsigned,
       seat_y mediumint(8) unsigned, id int(11) unsigned)";
  $table = "seating" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields = "(id int(11) unsigned, pword varchar(8), period varchar(12))";
  $table = "pwords" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields = "(test_id mediumint(8) unsigned, period varchar(12),
             date smallint unsigned, count tinyint(3) unsigned)";
  $table = "taken" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  $fields = "(test_lock char(1) default 'y' not null,
              seating_fname char(2) default '10' not null,
              seating_fname_on char(1) default 'y' not null,
              seating_fname2 char(2) default '10' not null,
              seating_fname2_on char(1) default 'y' not null,
              seating_mname char(1) default 'n' not null,
              seating_lname char(2) default '10' not null,
              seating_lname_on char(1) default 'y' not null,
              seating_name_lines varchar(6) default 'fpm' not null,
              search_start date default '2013-02-02' not null,
              search_end date default '2013-06-22' not null,
              q1_start date default '2012-09-01' not null,
              q1_end date default '2012-11-13' not null,
              q2_start date default '2012-11-14' not null,
              q2_end date default '2013-02-01' not null,
              q3_start date default '2013-02-02' not null,
              q3_end date default '2013-04-15' not null,
              q4_start date default '2013-04-16' not null,
              q4_end date default '2013-06-22' not null,
              pwords_tables_across char(2) default '2' not null,
              pwords_row_space char(2) default '4' not null,
              pwords_column_space char(2) default '7' not null,
              pwords_order varchar(12) default 'seat_y' not null)";
  $table = "defaults" . "$tid";
  $query = "create table $table " . $fields;
  $result = mysql_query($query);
  $errors = query_outcome_echo($query,$result,$errors);

  if ($result !== FALSE)
  {
    $query = "insert into $table () values()";
    $result = mysql_query($query);
    $errors = query_outcome_echo($query,$result,$errors);
  }

  return $errors;
}


function trim_array($old_r)
{
  $length = count($old_r);
  $new_r = array();
  for($i=0; $i<$length; $i++)
  {
    $val = $old_r[$i];
    $val = trim($val);
    if (!empty($val)) $new_r[] = $val;
  }
  return $new_r;
}


function teacher_col_match($def_rr, $line_r, $def_size, $line_size)
{ 
  $match = 0;

  for ($i=0; $i< $def_size; $i++)
  {
    $match = 0;
    $name1 = $def_rr[$i][1];
    for ($j=0; $j< $line_size; $j++)
    { 
      $name2 = $line_r[$j];
      if ( $name1 == $name2) $match = 1;
    }
    if (!$match) $i=$def_size;
  }
  return $match;
}


function teacher_t_name_rand()
{
  $t_name = "";

  for ($i=0; $i < 50; $i++)
  {
    $temp = syllable_rand(1);
    $temp .= syllable_rand();
    $temp .= syllable_rand();
    $query = "select * from teachers where t_name = '$temp'";
    $result = mysql_query($query);
    query_outcome_echo($query,$result);
    if($result === FALSE) return FALSE;
    if (mysql_num_rows($result) === 0)
    {
      $i=50;
      $t_name = $temp;
    }
  }
  return $t_name;
}


function teacher_id_select($fname,$mname,$lname)
{
  $query = "select tid from teachers where fname='$fname' and 
            mname='$mname' and lname='$lname'";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  $row =  mysql_fetch_row($result);
  return $row[0];
}


function user_tid_insert( $tid, $user, $pwd, $access, $db)
{
  $query = "insert into $db.users (tid,user,pword,access)
            values ($tid,'$user',password('$pwd'),'$access')";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
}


function teacher_insert($t_name,$fname,
                                 $mname,$lname,$hr, $email)
{
  $query =<<<EOQ
insert into teachers (t_name,fname,mname,lname,hr,email)
values ('$t_name','$fname','$mname','$lname','$hr','$email')
EOQ;
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
}


function get_main_id_periods($fname,$mname,$lname)
{
  $query = "select id, periods from stu_blocks where fname='$fname' 
            and mname='$mname' and lname='$lname'";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result) === 0) return 0;

  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  return $row;
}


function id_is_in_teacher_db($id, $period, $tid)
{
  $table = "spanish" . "$tid";
  $query = "select id from $table where id=$id
             and period = '$period'";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
  if (mysql_num_rows($result)) return 1;
  else return 0;
}


function update_main_spanish($tid,$id,$period,$hr_num,$periods)
{
  $per_ta = "|$period~$tid|";

  if ($period == "HR" || $period == "hr" || $period == "Hr" || $period == "Homeroom")
  {
    $query = "select hr from teachers where tid = '$tid'";
    $result = mysql_query($query);
    query_outcome_echo($query,$result);
    $row = mysql_fetch_row($result);
    $thr = $row[0];
    if ($thr != $hr_num)
    {
      echo "This student has a different homeroom number.<br>";
    }
  }
  else
    if (strpos($periods,$per_ta) === FALSE)
    {
      $periods .= "$period~$tid|";
      $query = "update stu_blocks set periods='$periods' where
              id=$id";
      $result = mysql_query($query);
      query_outcome_echo($query,$result);
    }
}


function email_teachers_up($up_r)
{
  $size = sizeof($up_r);

  $subject = "System access information";

  $body2 =<<<EOQ

Welcome to our school.
To access your class information you will need a user name and password.
A user name and password have been created for you. Please use these to 
log into the system. Once you're logged in you may change both your 
user name and your password to something more familiar to you.

EOQ;


  $body4 =<<<EOQ

For security reasons, please write the information on a piece of paper,
and delete this email from your inbox and trash. Please keep the user 
name and password in a safe place.

Thank you,
Pedro E. Rios
EOQ;

  $from = "From: administrator@gnuschool.org\n";

  for ($i=0; $i < $size; $i++)
  {
    $hr = $up_r[$i][0];
    $name = $up_r[$i][1];
    $user = $up_r[$i][2];
    $pwd = $up_r[$i][3];
    $to = $up_r[$i][4];
    
    $body1 = "Dear $name,";

    $body3 =<<<EOQ
Your user name is: $user
Your password is: $pwd
EOQ;

    $body = $body1 . $body2 . $body3 . $body4;

    mail($to, $subject, $body, $from);
  }
}


function up_r_2_table($up_r)
{
  $size = sizeof($up_r);
  if ($size)
  {
    echo "<table border><td colspan=4>You will not be able to
    see this table again. This data is only displayed so that each
    educator may be supplied with a user name and password.
    Educators require privacy, so please destroy the output once
    educators have their user name and password.
    Saving this data is also a security liability.
    If an educator forgets a user name and or password, these can
    be easily reset later by the administrator.</td>
     <tr align=left><td><b>HOMEROOM</b></td><td><b>TEACHER'S NAME
     </b></td><td><b>USER NAME</b></td><td><b>PASSWORD</b></td></tr>";
  }

  for ($i=0; $i < $size; $i++)
  {
    $hr = $up_r[$i][0];
    $name = $up_r[$i][1];
    $user = $up_r[$i][2];
    $pwd = $up_r[$i][3];
    echo "<tr align=left><td>$hr</td><td>$name</td><td>$user</td>
          <td>$pwd</td></tr>";
  }

  if ($size)
    echo "</table><br><br>";
}


function get_tid_r()
{
  $tid_r = array();
  $query = "select tid from teachers";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;
   
  while ($row = mysql_fetch_row($result))
  {  
    $tea_id = $row[0];
    $tid_r[] = $tea_id; 
  }
  return $tid_r;
}


function insert_all_pwords( $tid )
{
  $table = "spanish" . "$tid";
  $query = "select id,period from $table order by period";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;

  $table = "pwords" . "$tid";
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $id = $row['id'];
    $period = $row['period'];

    $query = "select * from $table where id=$id
              and period='$period'";
    $result1 = mysql_query($query);
    query_outcome_echo($query,$result1);
    if ($result1 === FALSE) return FALSE;
    if (mysql_num_rows($result1) === 0)
    {
      $query = "insert into $table 
              (id,pword,period)
              values ($id,'$id','$period')";
      $result2 = mysql_query($query);
      query_outcome_echo($query,$result2);
    }
  }
}


function insert_all_seats( $tid )
{
  $students_r = array();
  $table = "spanish" . "$tid";
  $query = "select * from $table order by period";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
  if ($result === FALSE) return FALSE;

  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $id = $row['id'];
  $period = $row['period'];
  $students_r[] = array($id, "$period");
  $period_cur = $period;
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $id = $row['id'];
    $period = $row['period'];
    if ($period != $period_cur)
    {
      insert_period_seats($students_r, $tid);
      $period_cur = $period;
      $students_r = array();
    }
    $students_r[] = array($id, "$period");
  }
  insert_period_seats($students_r, $tid);
}


function insert_period_seats($students_r, $tid)
{
  $table = "seating" . "$tid";
  $x_start = 10; $y_start = 55; $x_val = 0; $y_val = 0;
  $col_max = 5; $col_cur = 0; $row_cur = 0;
  $x_over = 150; $y_over = 70;
  for ($i=0;$i<sizeof($students_r);$i++)
  {
    $x_val = $col_cur * $x_over + $x_start;
    $y_val = $row_cur * $y_over + $y_start;
    $id = $students_r[$i][0];
    $period = $students_r[$i][1];
    if($col_cur == ($col_max-1))
    {
      $col_cur = 0;
      $row_cur++;
    }
    else $col_cur++;

    $query = "select * from $table where 
              id=$id and period='$period'";
    $result = mysql_query($query);
    query_outcome_echo($query,$result);
    if ($result === FALSE) return FALSE;
    if (mysql_num_rows($result) === 0)
    {
      $query = "insert into $table 
              (id,period,seat_x, seat_y)
              values ($id,'$period','$x_val','$y_val')";
      $result = mysql_query($query);
      query_outcome_echo($query,$result);
    }
  }
}


function seats_pwords()
{
  echo "<b>End of file reached.</b><br><br>
      <b>Arranging student seats and setting passwords...</b><br>";

  $tid_r = get_tid_r();
  $r_size = sizeof($tid_r);
  for ($i=0;$i<$r_size;$i++)
  {
    $tea_id = $tid_r[$i];
    insert_all_seats($tea_id); 
    insert_all_pwords($tea_id); 
  }
  echo "<br><br>";
}

$tag_title = "gnuschool.org Edit Database With Text File";
$page_title = "Edit database with a text file";


start_html($tag_title);
stylesheet_link("../style_sheet");
start_body("main_background");
back_2_parentA("setup_steps");


$defcol2val_r = file_settings_assoc_r();
$defval2col_r = array_flip($defcol2val_r);


if (empty($defcol2val_r))
{
  echo "<br>no default settings found";
  back_2_parentA("setup_steps");
  end_html_exit();
}

$file_name = $defcol2val_r['file_name'];
if(empty($file_name))$file_name = "teacher_students.txt";

$file_dir = $defcol2val_r['file_dir'];
$path_file = "$file_dir" . "$file_name";

if(empty($delim))
  $delim = $defcol2val_r['field_delim'];

if (!$yes_input)
{
  $comment = "Edit the database using file $path_file
        <br>The file must contain teacher and student information.";
  title_comment_div_noend($page_title, $comment);
  echo "<ul><b>
       <LI>The first row must have the teacher column names.
       <LI>The second row must have the teacher data.
       <LI>The third row must have the student column names.
       <LI>The fourth row must have student data.
       <LI>The student data continues until the next line with teacher column names.
       <LI>Repeat the instructions above as often as needed.</b>
       <LI>See sample below.</ul>";

echo "<TABLE>
<TR><TD>FIRSTNAME&nbsp</TD><TD>MIDDLENAME&nbsp</TD><TD>LASTNAME&nbsp</TD>
<TD>HOMEROOM</TD></TR>
<TR><TD>Pedro</TD><TD>Español</TD><TD>Rios</TD><TD>D432</TD></TR>
<TR><TD>FIRSTNAME</TD><TD>MIDDLENAME</TD><TD>LASTNAME</TD><TD>PERIOD</TD></TR>
<TR><TD>Rafael</TD><TD>M.</TD><TD>Rios</TD><TD>HR</TD></TR>
<TR><TD>Flanito</TD><TD>X.</TD><TD>Pamplos</TD><TD>HR</TD></TR>
<TR><TD>John</TD><TD>H.</TD><TD>Smith</TD><TD>5</TD></TR>
<TR><TD>Mary</TD><TD>B.</TD><TD>Doe</TD><TD>5</TD></TR>
</TABLE>
<TABLE>
<TR><TD>FIRSTNAME&nbsp</TD><TD>MIDDLENAME&nbsp</TD><TD>LASTNAME&nbsp</TD>
<TD>HOMEROOM</TD></TR>
<TR><TD>Raymond</TD><TD>Walter</TD><TD>Maestro</TD>
<TD>D121</TD></TR>
<TR><TD>FIRSTNAME</TD><TD>MIDDLENAME</TD><TD>LASTNAME</TD><TD>PERIOD</TD></TR>
<TR><TD>Masca</TD><TD>Bien</TD><TD>Dentista</TD><TD>HR</TD></TR>
<TR><TD>Juan</TD><TD>Dulce</TD><TD>Smithers</TD><TD>HR</TD></TR>
<TR><TD>Maria</TD><TD>C.</TD><TD>Libro</TD><TD>3</TD></TR>
<TR><TD>Marta</TD><TD>L.</TD><TD>Studious</TD><TD>3</TD></TR>
</TABLE>";

  proceed_form();
  back_2_parentA("setup_steps");
  end_html_exit();
}

$comment = "Attempting to edit the database using file $path_file ...";
title_comment_div_noend($page_title, $comment);


if ( file_exists($path_file) === FALSE )
{
  echo "<b>Error $path_file doesn't exist!</b></br><br>";
  back_2_parentA("setup_steps");
  end_html_exit();    
}

$lines_all_r = file("$path_file");
if ($lines_all_r === FALSE)
{
  echo "<b>Error opening text file!</b><br><br>";
  back_2_parentA("setup_steps");
  end_html_exit();
}

$lines_size = count($lines_all_r);

$rooms_ass_r = array();
$rooms_ass_r = get_rooms_assoc_r();
if(empty($rooms_ass_r))
  $rooms_ass_r = array();

$periods_ass_r = get_periods_assoc_r();
if(empty($periods_ass_r))
  $periods_ass_r = array();

$edited = 0;

$line = "";
$up_r = array();
$tea_cols = 1; //teacher column names
$tea_data = 0; //teacher data
$stu_cols = 0; //student column names
$stu_data = 0; //student data
$tid = "";

$defcol2pos_r = array();
$d_size=0;

for ( $i=0; $i < $lines_size; $i++)
{
  $line_num = $i+1;
  $line = $lines_all_r[$i];
  $line = trim($line);

  if (!empty($line))
  {
    $line_r = explode($delim, $line);
    if($delim == " ")
      $line_r = trim_array($line_r);
    $line_assoc_r = array_flip($line_r);

    if ($tea_cols)
    {
      echo "<br>LINE:$line_num<br>$line<br>";
      $defcol2pos_r=match_and_order_col_names($defval2col_r,$line_assoc_r);
      if($defcol2pos_r === FALSE)
      {
        echo "<b> The teacher column names do not match with the text 
            file's teacher column names.</b><br><br>";    

        seats_pwords();
        if($show_up) up_r_2_table($up_r);
//        if($mail_up) email_teachers_up($up_r); **testing**
        back_2_parentA("setup_steps");
        end_html_exit();
      }
      else
      {
        echo "<b> The teacher column names match with the text file's 
              column names. Continuing...</b><br><br>";    
        $tea_cols=0; $tea_data=1;
      }
    }  
    elseif ($tea_data)
    {
      echo "<br>LINE:$line_num<br>$line<br>";
      if(count($line_r) != count($defcol2pos_r))
      {
        echo "<b>The number of teacher data columns is not equal to the 
            column names.</b><br><br>";
      
        seats_pwords();
        if($show_up) up_r_2_table($up_r);
//        if($mail_up) email_teachers_up($up_r); **testing**
        back_2_parentA("setup_steps");
        end_html_exit();
      }

      $k_f = $defcol2pos_r['col_fname'];
      $k_m = $defcol2pos_r['col_mname'];
      $k_l = $defcol2pos_r['col_lname'];
      $k_h = $defcol2pos_r['col_hr'];
//      $k_e = $defcol2pos_r['email']; **testing**

      $fname = $line_r[$k_f];
      $mname = $line_r[$k_m];
      $lname = $line_r[$k_l];
      $hr = $line_r[$k_h];
//      $email = $line_r[$k_e]; **testing**

      $ok_line = 1;

      if (!is_alpha_enddot_quote($fname))
      {
        echo "<b>SKIPPING: Bad character found in the first name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
      }

      if (!is_alpha_enddot_quote($mname, 1))
      {
        echo "<b>SKIPPING: Bad character found in the middle name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
      }

      $lname_quote = is_alpha_enddot_quote($lname,0,1);
      if ($lname_quote == 0)
      {
        echo "<b>SKIPPING: Bad character found in the last name of 
              $fname $mname $lname.</b><br><br>";
        $ok_line = 0;
      }

      if($lname_quote == 39)
      {
        $lname = addslashes($lname);
      }

      $t_exists = 0;
      $t_name = "";
      $tid = teacher_id_select($fname,$mname,$lname);
      if (!empty($tid))
      {
        echo "<br>LINE:$line_num<br>$line<br>";
        echo "<b>SKIPPING: Teacher $fname $mname $lname already 
          exists.</b><br><br>";
        $t_exists = 1;
      }
      else
      {
        $t_name = teacher_t_name_rand();
      }

      if (!$t_exists  && $ok_line)
      {
        teacher_insert($t_name,$fname,$mname,$lname,$hr,$email);
        if($rooms_ass_r["$hr"] != 1)
        {
          if(insert_room($hr))
            $rooms_ass_r["$hr"] = 1;
        }
        $edited = 1;

        $mylink = mysql_connect($Muhost,$Muuser,$Mupword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"USER","setup_steps");
        $user = user_rand($udb);

        $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","setup_steps");
        mysql_select_db($mdb,$mylink);
        $pwd = pword_rand();
        $name = "$fname $mname $lname";
        $up_r[] = array($hr, $name, $user, $pwd, $email);
        $tid = teacher_id_select($fname,$mname,$lname);

        $mylink = mysql_connect($Muhost,$Muuser,$Mupword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"USER","setup_steps");
        user_tid_insert( $tid, $user, $pwd, "T", $udb);

        $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","setup_steps");
        mysql_select_db($mdb,$mylink);
        create_tables($tid);
      } 
      $tea_data=0; $stu_cols=1; 
    }
    elseif ($stu_cols)
    {
      echo "<br>LINE:$line_num<br>$line<br>";
      $defcol2pos_r=match_and_order_col_names($defval2col_r,$line_assoc_r);
      $d_size = count($defcol2pos_r);
      if($defcol2pos_r === FALSE)
      {
        echo "<b> The student column names do not match with the text 
            file's student column names.</b><br><br>";    
        seats_pwords();
        if($show_up) up_r_2_table($up_r);
//        if($mail_up) email_teachers_up($up_r); **testing**
        back_2_parentA("setup_steps");
        end_html_exit();
      }
      else
      {
        echo "<b> The student column names match with the text file's 
            column names. Continuing...</b><br><br>";    
        $stu_cols=0; $stu_data=1;
      }
    }
    elseif ($stu_data)
    {
      if (match_col_names($defval2col_r,$line_assoc_r))
      { $i--; $stu_data=0; $tea_cols=1;}
      else
      {
        echo "<br>LINE:$line_num<br>$line<br>";
        if(count($line_r) != $d_size)
        {
          echo "<b>SKIPPING: The number of student data columns
            is not equal to the number of column names.</b><br><br>";    
        }
        else
        {      
          $k_f = $defcol2pos_r['col_fname'];
          $k_m = $defcol2pos_r['col_mname'];
          $k_l = $defcol2pos_r['col_lname'];
          $k_p = $defcol2pos_r['col_pe'];

          $fname = $line_r[$k_f];
          $mname = $line_r[$k_m];
          $lname = $line_r[$k_l];
          $period = $line_r[$k_p];

          $ok_line = 1;
  
          if (!is_alpha_enddot_quote($fname))
          {
            echo "<b>SKIPPING: Bad character found in the first name of 
                $fname $mname $lname.</b><br><br>";
            $ok_line = 0;
          }

          if (!is_alpha_enddot_quote($mname, 1))
          {
            echo "<b>SKIPPING: Bad character found in the middle name of 
               $fname $mname $lname.</b><br><br>";
            $ok_line = 0;
          }

          $lname_quote = is_alpha_enddot_quote($lname,0,1);
          if ($lname_quote == 0)
          {
            echo "<b>SKIPPING: Bad character found in the last name of 
               $fname $mname $lname.</b><br><br>";
            $ok_line = 0;
          }
          else
          {
            if($lname_quote == 39)
            {
              $lname = addslashes($lname);
            }
          }
 
          if($ok_line)
          {
            $id_r = get_main_id_periods($fname,$mname,$lname);
            $id = $id_r['id'];
            $periods = $id_r['periods'];
            if (empty($id))
            {
              echo "<b>SKIPPING: $fname $mname $lname
                    is not in stu_blocks table</b><br>";
            }
            else
            {
              if($periods_ass_r["$period"] != 1)
              {
                $edited = 1;
                if(insert_period($period))
                  $periods_ass_r["$period"] = 1;
              }
           
              if (!id_is_in_teacher_db($id, $period, $tid))
              {
                $edited = 1;
                update_main_spanish($tid,$id,$period,$hr,$periods);
                $ta = "spanish" . "$tid";
                $query = "insert into $ta 
                    (id,period) values
                    ($id,'$period')";
                $result = mysql_query($query);
                $errors = query_outcome_echo($query,$result,$errors);
              }
              else echo "<b>SKIPPING: $fname $mname $lname
                     is already in spanish table</b><br>";
            }
          }
        }
      } 
    }
  }
}
if ($edited)
  echo "<b>You have updated the database.</b><br><br>";
else
  echo "<b>No changes have been made to the database.</><br><br>";


seats_pwords();
if($show_up) up_r_2_table($up_r);
//if($mail_up) email_teachers_up($up_r); **testing**

back_2_parentA("setup_steps");
echo "</DIV>";

?>
</body></html>
