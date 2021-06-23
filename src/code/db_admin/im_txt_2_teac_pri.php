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

if(empty($delim)) $delim = " ";

/* this proceed function gives more options
// the email option needs more testing
// to simplify, we use function proceed_form()
function proceed_delim_form()
{
  echo <<<EOQ
<form method=post action="$PHP_SELF">
<input type=checkbox name=e_mail value="yes"><b>Principal column name 
EMAIL exists. It holds the principal's email address.</b>
<BR><BR>
<input type=checkbox name=mail_up value="yes"><b>Mail each user name
and password to that user's e-mail address.</b>
<BR><BR>
<input type=checkbox name=show_up value="yes"><b>Display all user name
and password data.</b>
<BR><BR><B>Field delimiter (the default is the tab or space).
<BR>
<input type=text size=8 name=delim maxlength=6>
<BR>If the delimiter is not a space(s) or a tab(s) you need to type in 
what it is.
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


function trim_array($old_r)
{
  $length = sizeof($old_r);
  $new_r = array();
  for($i=0; $i<$length; $i++)
  {
    $val = $old_r[$i];
    $val = trim($val);
    if (!empty($val)) $new_r[] = $val;
  }
  return $new_r;
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


function user_tid_insert($tid,$user,$pwd,$access,$db)
{
  $query = "insert into $db.users (tid,user,pword,access)
            values ($tid,'$user',password('$pwd'),'$access')";
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
}


function teacher_insert($t_name,$fname,$mname,$lname,$hr, $email)
{
  $query =<<<EOQ
insert into teachers (t_name,fname,mname,lname,hr,email)
values ('$t_name','$fname','$mname','$lname','$hr','$email')
EOQ;
  $result = mysql_query($query);
  query_outcome_echo($query,$result);
}


function email_principals_up($up_r)
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

  $from = "From: admin@mybooktrade.com\n";

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
    principal may be supplied with a user name and password.
    Principals require privacy, so please destroy the output once
    the principals have their user name and password.
    Saving this data is also a security liability.
    If a principal forgets a user name and or password, these can
    be easily reset later by the administrator.</td>
     <tr align=left><td><b>HOMEROOM</b></td><td><b>PRINCIPAL'S NAME
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
if(empty($file_name))$file_name = "principals.txt";

$file_dir = $defcol2val_r['file_dir'];
$path_file = "$file_dir" . "$file_name";

if(empty($delim))
  $delim = $defcol2val_r['field_delim'];


if (!$yes_input)
{
  $comment = "Edit the database using $path_file
        <br>The file must contain principal information only.";
  title_comment_div_noend($page_title, $comment);
  echo "<ul><b>
       <LI>The first line will have the principal column names.
       <LI>Principal data must follow the principal column names.</b>
       <LI>See sample below.</ul>";

echo "<TABLE>
<TR><TD>FIRSTNAME&nbsp</TD><TD>MIDDLENAME&nbsp</TD><TD>LASTNAME&nbsp</TD>
<TD>HOMEROOM</TD></TR>
<TR><TD>William</TD><TD>K.</TD><TD>Thorough</TD><TD>B235</TD></TR>
<TR><TD>Maria</TD><TD>R.</TD><TD>Dedicada</TD><TD>A132</TD></TR>
<TR><TD>Jacobo</TD><TD>D.</TD><TD>Formal</TD><TD>D411</TD></TR>
</TABLE>";

  proceed_form();
  back_2_parentA("setup_steps");
  end_html_exit();
}

$comment = "Attempting to edit the database using the text file...";
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
$prin_cols = 1; //principal column names
$prin_data = 0; //principal data
$tid = "";

$defcol2pos_r = array();
$d_size=0;

for ( $i = 0; $i < $lines_size; $i++)
{
  $line_num = $i+1;
  $line = $lines_all_r[$i];
  $line = trim($line);

  if (!empty($line))
  {
    $line_r = explode($delim, $line);
    $line_r = trim_array($line_r);
    $line_assoc_r = array_flip($line_r);

    if ($prin_cols)
    {
      echo "<br>LINE:$line_num<br>$line<br>";
      $defcol2pos_r=match_and_order_col_names($defval2col_r,$line_assoc_r);
      if($defcol2pos_r === FALSE)
      {
        echo "<b> The principal column names do not match with the text 
            file's principal column names.</b><br><br>";    

        if($show_up) up_r_2_table($up_r);
//        if($mail_up) email_principals_up($up_r); **testing**
        back_2_parentA("setup_steps");
        end_html_exit();
      }
      else
      {
        echo "<b> The principal column names match with the text file's 
              column names. Continuing...</b><br><br>";    
        $prin_cols=0; $prin_data=1;
      }  
    }
    else if($prin_data)
    {
      echo "<br>LINE:$line_num<br>$line<br>";
      if(count($line_r) != count($defcol2pos_r))
      {
        echo "<b>The number of principal data columns is not equal to the 
            column names.</b><br><br>";    
      
        if($show_up) up_r_2_table($up_r);
//        if($mail_up) email_principals_up($up_r); **testing**
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

      $p_exists = 0;
      $p_name = "";
      $tid = teacher_id_select($fname,$mname,$lname);
      if (!empty($tid))
      {
        echo "<br>LINE:$line_num<br>$line<br>";
        echo "<b>SKIPPING: Principal $fname $mname $lname already 
            exists.</b><br><br>";
        $p_exists = 1;
      }
      else
      {
        $p_name = teacher_t_name_rand();
      }

      if (!$p_exists && $ok_line)
      {
        teacher_insert($p_name,$fname,$mname,$lname,$hr,$email);
        $edited = 1;
      
        $mylink = mysql_connect($Muhost,$Muuser,$Mupword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"USER","setup_steps");
        $u_rand = user_rand($udb);

        $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","setup_steps");
        mysql_select_db($mdb,$mylink);
        $pwd = pword_rand();
        $name = "$fname $mname $lname";
        $up_r[] = array($hr, $name, $u_rand, $pwd, $email);
        $tid = teacher_id_select($fname,$mname,$lname);

        $mylink = mysql_connect($Muhost,$Muuser,$Mupword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"USER","setup_steps");
        user_tid_insert( $tid, $u_rand, $pwd, "P", $udb);

        $mylink = mysql_connect($Mmhost,$Mmuser,$Mmpword);
        if($mylink === FALSE) exit_no_mysql_connect($tries,"MAIN","setup_steps");
        mysql_select_db($mdb,$mylink);
      }    
    }
  }
}
if ($edited)
  echo "<b>You have updated the database.</b><br><br>";
else
  echo "<b>No changes have been made to the database.</><br><br>";

if($show_up) up_r_2_table($up_r);
if($mail_up) email_principals_up($up_r);

back_2_parentA("setup_steps");
echo "</DIV>";

?>
</body></html>
