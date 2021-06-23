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

include('./main_menu.php');

echo <<<EOQ
<html>
<head>
<title>gnuschool.org More
</title>
<link rel="STYLESHEET" type="text/css" href="../style_sheet">
</head>
<body class="main_background">
EOQ;

main_menu();

echo <<<EOQ
<div class="page_title"><h2>live</h2>
<pre>
you are special, you feel it in yourself
let this not compel you to feel superior
for each and every one of us is special

there are people
of light complexion
of darker complexion
of various shapes and sizes
that like the same gender
that like the opposite gender
that pray at home
that pray in a large building
that pray outdoors
that are greedy
that are more greedy
that are more considerate
that are less considerate
that are hardworking
that are not inclined to work hard
that call the father allah
that call the father god or by another name

their difference does not make them evil
you know that one from any group can be evil

proud superiority makes you hate those who are different

why are you so proud
do you know how and why your thoughts come to be
did you make your mind
did you make your eye or a hair on your head

be at peace with what you were given
do not condemn others because they are different
their purpose comes from above

we are called to be not selfish but selfless

the best example is jesus christ

</pre>
</div>
</body>
</html>
EOQ;
?>