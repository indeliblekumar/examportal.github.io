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

function img_div()
{
  echo <<<EOQ
<DIV ID="div_id1" STYLE="position:absolute; top:0; left:0;">
<img id="img_id1" src='/blank.png' ALT='blank picture'>
<input id="id1" type="hidden">
</DIV>
EOQ;
}


$javascript = <<<EOQ
<SCRIPT LANGUAGE="JavaScript">

var inputA=null;
var old_event_x = 0, old_event_y = 0;
var w_width, w_height;
var max_x=0, max_y=0;
var cur_div_obj_x, cur_div_obj_y;
var cur_div_obj, cur_but_obj;
var cur_div_id;
var unset=0;

function set_undo_value(hidden_id,checkbox_id)
{
  if (document.getElementById)
  {
    if ( document.getElementById(checkbox_id).checked == true)
      document.getElementById(hidden_id).value = 'y';
  }
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");

  return true;
}


function setHandlers()
{
  start_globals();
  window.document.onmousemove = move; 
} 


function coords_to_value()
{
 var str_len = cur_div_id.length - 4;
 var id = cur_div_id.substr(4, str_len);

 if (document.getElementById)
   document.getElementById(id).value = 'v'+cur_div_obj_x+'_'+cur_div_obj_y;
 else
   alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}


function start_globals()
{     
    set_but_obj('img_id1');
    set_div_obj('div_id1');
    set_div_obj_xy();
    max_x = cur_div_obj_x;
    max_y = cur_div_obj_y;
    unset = 1;
} 


function unset_globals()
{ 
    if ((cur_div_obj_x + 150) > max_x)
      max_x = cur_div_obj_x + 150;
    if ((cur_div_obj_y + 100) > max_y)
      max_y = cur_div_obj_y + 100;    
    
    set_but_obj('img_id1');
    set_div_obj('div_id1');
    move_div_obj_xy(max_x, max_y);
    cur_but_obj.blur();
    unset = 1;
} 


function set_globals(but_id, div_id)
{ 
    set_div_obj(div_id);
    set_div_obj_xy();
    set_but_obj(but_id);
    unset = 0;
} 


function set_but_obj(id)
{ 
 if (document.getElementById)
   cur_but_obj = document.getElementById(id);
 else
   alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
} 


function set_div_obj(id)
{ 
 cur_div_id = id;
 if (document.getElementById)
   cur_div_obj = document.getElementById(id).style;
 else
   alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
} 


function set_div_obj_xy()
{ 
  if (document.getElementById)
  { 
    cur_div_obj_x = cur_div_obj.pixelLeft; 
    cur_div_obj_y = cur_div_obj.pixelTop; 
  } 
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}


function move_div_obj_xy(new_x, new_y)
{ 
  if (document.getElementById)
  { 
    cur_div_obj.pixelLeft = new_x; 
    cur_div_obj.pixelTop = new_y; 
  } 
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");

  coords_to_value();
}


function set_height_width()
{
  if( typeof( window.innerWidth ) == 'number' )
  {
    //netscape
    w_width = window.innerWidth;
    w_height = window.innerHeight;
  }
  else if( document.documentElement &&
      ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
  {
    //IE 6+
    w_width = document.documentElement.clientWidth;
    w_height = document.documentElement.clientHeight;
  }
  else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
  {
    //IE 4
    w_width = document.body.clientWidth;
    w_height = document.body.clientHeight;
  }
}


function move()
{ 
    new_event_x = event.x; 
    new_event_y = event.y;

    set_height_width();

    if (unset == 0)
    if ((new_event_x < 3)||(new_event_y < 3)||(new_event_x>w_width)||(new_event_y>w_height))
      unset_globals();

    new_div_x = cur_div_obj_x + (new_event_x - old_event_x);
    new_div_y = cur_div_obj_y + (new_event_y - old_event_y);
             
    if (new_div_x < 0 )
      new_div_x = 0;
 
    if (new_div_y < 0 )
      new_div_y = 0;
 


    if (unset == 0)
    {
      move_div_obj_xy(new_div_x , new_div_y);
      cur_div_obj_x = new_div_x;
      cur_div_obj_y = new_div_y;
    }
    old_event_x = new_event_x;
    old_event_y = new_event_y;
} 


function doneConfirm1() {
var str='You will delete all information associated with this student, including tests taken and attendance information. Do you want to continue?'; var ok=confirm(str);
if (ok) return true; else return false;
}

window.onload = setHandlers; 


function set_bgcolor(my_checkbox, ids_str, number_of_ids)
{

  if (document.getElementById)
  { 
    id_arr = ids_str.split("_");
    if (my_checkbox.checked)
    {
      for( i = 0; i < number_of_ids; i++)
      {
        id = id_arr[i];
        document.getElementById(id).style.backgroundColor = 'red';
      }
    }
    else
    {
      for( i = 0; i < number_of_ids; i++)
      {
        id = id_arr[i];
        document.getElementById(id).style.backgroundColor = 'white';
      }
    }
  } 
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}


function set_global_pos(myobject)
{ if(myobject) inputA = myobject; }


function doneConfirm2() {
var ok=confirm("Did you save your changes?")
if (ok) return true; else return false;
}


function addText( insText )
{
  if (inputA)
  {
    if (inputA.type == "textarea" || inputA.type == "text")
    {
      inputA.focus();
      if( inputA.createTextRange )
      {
        document.selection.createRange().text += insText;
      }
      else
      {
        if( inputA.setSelectionRange )
        {
          var len = inputA.selectionEnd;
          inputA.value=inputA.value.substr(0,len) + insText +
                 inputA.value.substr(len);
          inputA.setSelectionRange(len+insText.length,len+insText.length);
        }
        else { inputA.value += insText; }
      }
    }
  }
}


function doneConfirm3() {
var ok=confirm("Are you done with Test?")
if (ok) return true; else return false;
}


function set_s_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");

  if (document.getElementById)
  { 
    if ( radio.checked == true)
    {
      document.getElementById('s1').value = date_arr[0];
      document.getElementById('s2').value = date_arr[1];
      document.getElementById('s3').value = date_arr[2];
    }
  }
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}

function set_e_values(radio) {
  radio_val = radio.value;
  date_arr = radio_val.split("-");

  if (document.getElementById)
  { 
    if ( radio.checked == true)
    {
      document.getElementById('e1').value = date_arr[0];
      document.getElementById('e2').value = date_arr[1];
      document.getElementById('e3').value = date_arr[2];
    }
  }
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}

function set_t_values(radio) {
  radio_val = radio.value;
  time_arr = radio_val.split("-");

  if (document.getElementById)
  { 
    if ( radio.checked == true)
    {
      document.getElementById('t1').value = time_arr[0];
      document.getElementById('t2').value = time_arr[1];
    }
  }
  else
    alert("Sorry, but the Javascript code did not work. This web page will not work properly.");
}
</SCRIPT>
<noscript>
  <h2>Sorry, but the Javascript code did not work.</h2>
  <p>This web page will not work properly without the Javascript code it uses.
     You may be able to fix the problem, if you enable Javascript in your browser.</p>
</noscript>
EOQ;

?>
