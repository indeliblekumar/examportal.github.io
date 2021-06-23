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
<title>gnuschool.org Explain </title>
<link rel="STYLESHEET" type="text/css" href="../style_sheet">
</head>
<body class="main_background">
EOQ;

main_menu();

echo <<<EOQ
<div class="page_title"><h2>Explaining gnuschool</h2>

<B>School structure</B><br>
This software is written for a specific type of school.<br>
The school day is broken up into blocks or periods.<br>
Attendance is taken at the beginning of each block.<br>
The homeroom period is the first block of the day.<br>
Homeroom attendance records if a student came to school on time.<br>
Geometry, Art, Biology, etc... are blocks following homeroom.<br>
<br>

<dl>
<B>Input file used in the gnuschool setup</B>
<li>On "Step 3", if the file name in file settings is empty, the default file is "students.txt".</li>
<li>On "Step 4", if the file name in file settings is empty, the default file is "teacher_students.txt".</li>
<li>On "Step 5", if the file name in file settings is empty, the default file is "principals.txt".</li>
</dl>


<B>A real world model</B><br>
We learn best when a real world model is part of the lesson.<br>
A student becomes a much better engine mechanic
when a real engine is part of the lesson.<br>
The engine must be totally open to the students.<br>
Any hidden copyrighted components hurt the learning process.<br><br>
Programming promotes effective logic, and leads to great careers.<br>
In order to teach programming optimally, a real life model is needed.<br>
The model must be free, as in free to share, free to study, and free to improve.<br>
<br>
Copyrighted software hides or restricts its code, and is not the best educational real life model.<br>
Free software lets students study the software without impediments.<br>
The software system that assists a school should be totally open to students.<br>
They should be able to learn from it and contribute to it to their abilites' content.<br>


<dl>
<B>gnuschool tries to</B>
<li>Assist educators and administrators better manage
   their classrooms</li>
<li>Give students accurate and instant assessment feedback</li>
<li>Give students, parents, and educators access to tests
   taken, including right and wrong answers</li>
</dl>

<B>Educational tool</B><br>
Lets make our educational system the best it can be.<br>
Schools already have less advanced and more advanced classes. We need a tool that enhances this idea.<br>
We need a tool that makes it easier to record and evaluate children's progress.<br>
Millions of children loose interest and attend school because they have to.<br>
Give educators a tool that makes it easier and more effective to teach class.<br>
This will reduce educator stress and will make school more rewarding for students.<br>
Educators do a fantastic job. Let's not blame them for our lack of foresight.<br>
All kids have great potential.<br>
We must do better.<br><br>


<B>Why named gnuschool</B><br>
This software was originally written, setup, and tested in a GNU-Linux system.<br>
GNU is a free software system created by the Free Software Foundation.<br>
Free software has the transparency that is essential in educational software.<br>
The General Public License ensures that this software remains free.<br>
Free to use, study, improve, and free to share those improvements with others.<br>


</div></body></html>
EOQ;
?>