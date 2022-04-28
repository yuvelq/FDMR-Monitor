<?php
// Output CSS and not plain text
header("Content-type: text/css");
include_once '../include/config.php';
?>
.link { <?= THEME_COLOR ?> }
.dropbtn { <?= THEME_COLOR ?> }

table, td, th {
  border: 0.8px solid #d0d0d0; 
  padding: 3px; 
  text-align:center;
  border-collapse: collapse;
}
th {
  <?= THEME_COLOR."\n" ?>
}

#log {
  height: 40em; 
  text-align: left; 
  overflow-y: scroll; 
  font-size:13px; 
  background-color: #000000; 
  color: #b1eee9;
}

/* Server Activity table */
table.active-qso {
  border: 0;
  text-align: center;
  width: 100%;
  border-spacing: 0 7px;
  border-collapse: separate;
}
.active-qso th {
  <?= THEME_COLOR."\n" ?>
  border-radius: 10px;
  font-size : 9pt;
  font-weight: bold;
  height: 25px;
}
.active-qso td {
  line-height: 1.3em;
  height: <?= HEIGHT_ACTIVITY.";\n" ?>
  border-radius: 10px;
  border: 1px solid LightGrey;
  background-color: #f9f9f9f9;
  font-size: 12pt; 
  font-weight:bold;
}

/* Static TG table */
table.stctg {
  table-layout:fixed;
  width:100%;
  margin-top:5px;
  margin-bottom:5px;
  background-color: #f9f9f9f9;
}
.ts1-bkgnd {
  background-color: #e6e6e69d;
}
.stctg .th1wd {width: 120px;}
.stctg .th2wd {width: 160px;}
.stctg .th3wd {width: 90px;}
.stctg .th4wd {width: 40px;}
.stctg .th5wd {width: 50%;}
.stctg .th6wd {width: 65px;}
.stctg .th7wd {width: 65px;}
.stctg .connted-bkgnd {background-color:#cefdce;}
.stctg .location{font-size: 92%; color:#b5651d; font-weight:bold}

/* Master peer table */
table.lnksys {
  table-layout:fixed;
  width:100%;
  margin-top:5px;
  margin-bottom:5px;
  background-color: #f9f9f9f9;
}
.lnksys .th1wd {width: 120px;}
.lnksys .th2wd {width: 160px;}
.lnksys .th3wd {width: 90px;}
.lnksys .th4wd {width: 40px;}
.lnksys .th5wd {width: 50%;}
.lnksys .th6wd {width: 40%;}
.lnksys .connted-bkgnd {background-color:#cefdce;}
.lnksys .location {font-size: 92%; color:#b5651d; font-weight:bold}
.lnksys .peer-conn {font-size: 9pt; background-color: #cefdce;}
.lnksys .peer-disc {font-size: 9pt; background-color: #ff704d;}

/* Last heard table */
table.log {
  background-color: #f9f9f9f9;
  border-collapse: collapse;
  width: 100%;
  text-align: center;
}
.log th {
  height: 30px;
}
.log tr:nth-child(even) {
  background-color: #e6e6e69d;
}

.conn2srv {
  font-size: 10pt;
  font-weight: 600;
  margin-top: 5px;
  margin-bottom: 5px;
  background-image: linear-gradient(to bottom, #e9e9e9 50%, #bcbaba 100%);
  border: 1px solid LightGrey;
  border-radius: 10px;

}
.conn2srv .tittle {
  margin-left: 7px;
  margin-bottom: 5px;
  float:left;
  color:#464646;
  font-weight:600;
  line-height:1.5;
}
.conn2srv .hs-peers {
  clear: left;
  text-align: left;
  font-size: 9.5pt;
  font-weight: bold;
  margin: 10px 25px;
  line-height: 1.4;
  white-space: normal;
}
div.tooltiptext.c2s-pos1 {
  left:115%;
  top:-10px;
  font-size: 9.5pt;
  padding-left: 15px;
}
span.tooltiptext.c2s-pos2 {
  font-size: 9.5pt;
  top:120%;
  left:50%;
  margin-left:-70%;
  width:100px;
  padding: 2px 0;
  text-align: center;
}

a:link {
  text-decoration: none;
  font-size: 9.5pt;
  font-weight:bold;
  color:#0066ff;
  text-shadow: 1px 1px 1px Lightgrey, 0 0 0.5em LightGrey, 0 0 1em whitesmoke;
}

/* visited link */
a:visited {
  color: #0066ff;
  text-decoration: none;
}

/* mouse over link */
a:hover {
  color: hotpink;
  text-decoration: underline;
}
/* selected link */
a:active {
  color: #0066ff;
  text-decoration: none;
}
.tooltip {
  position: relative;
  opacity: 1;
  display: inline-block;
  border-bottom: 1px dotted white;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 280px;
  background-color: #6E6E6E;
  box-shadow: 4px 4px 6px #3b3b3b;
  color: #FFFFFF;
  text-align: left;
  border-radius: 6px;
  padding: 8px 0;
  left: 100%;
  opacity: 1;
  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {
  right: 100%;
  opacity: 1;
  visibility: visible;
}
a.button {
  <?= THEME_COLOR."\n" ?>
  text-shadow: none;
  border: none;
  padding: 8px 10px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  font-weight: 500;
  margin: 7px 3px;
  border-radius: 8px;
  box-shadow: 0px 8px 10px rgba(0,0,0,0.1);
}

.button:hover {background-color:rgb(140,140,140);background: rgb(140,140,140); color:white;}  
.dropdown:hover .dropbtn {background-color:rgb(140,140,140);background: rgb(140,140,140); color:white;} 

.dropbtn {
  border: none;
  padding: 8px 10px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  font-weight: 500;
  margin: 7px 3px;
  border-radius: 8px;
  box-shadow: 0px 8px 10px rgba(0,0,0,0.1);
}

/* The container - needed to position the dropdown content */
.dropdown {
  position: relative;
  display: inline-block;
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 140px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1000;
}

/* Links inside the dropdown */
.dropdown-content a {
  color: black;
  padding: 6px 16px;
  text-decoration: none;
  display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {background-color: #ddd;}

/* Show the dropdown menu on hover */
.dropdown:hover .dropdown-content {display: block;}

/*  NEW CHANGES HERE  */

body {
  margin: auto;
  width: 1100px;
  background-color: #d0d0d0;
  font: 10pt arial, sans-serif;
  text-align: center;
}
/* Top image */
.img-top {
  display: block;
  margin-top: 10px;
  margin-left: auto;
  margin-right: auto;
}
/* Server tittle */
.srv-tittle {
  color: #000;
  font-size: 18px;
  font-weight: bold;
  text-align: center;
  padding: 7px;
}

/* fieldset general */
fieldset {
  background-color: #e6e6e6;
  border-radius: 10px;
  margin: 10px auto;
}

fieldset.big{
  width: 1100;
}
fieldset.med {
  width: 900px;
}
fieldset.small {
  width: 70%;
}
legend {
  margin-left: 20px;
  text-align: left;
  padding: 0 5px;
}

/* Waiting for server data tabble */
.w4data {
  width: 100%;
  background-color:#f9f9f9;
  font: 13pt arial, sans-serif;
  margin-top:4px;
  margin-bottom:4px;
  border:none;
  height:60px;
  text-align:center;
  color: brown;
}
table.opb {
  background-color: #f9f9f9f9;
  table-layout:fixed;
  width:100%;
  margin-top:5px;
  margin-bottom:5px;
}
.opb .col1 {text-align: left; padding-left: 20px;}
.opb .col2 {font-size: 9pt;}
.opb .col3 {font-size: 9pt; font-weight: 600; color:#464646;}

.opb .th1wd {width: 12%;}
.opb .th2wd {width: 12%;}
.opb .th3wd {width: 70%;}

/* sysinfo page */
.images img {display: block; padding-bottom: 10px; margin-left: auto; margin-right: auto; }

.bkgnd-1d1 {background: #1d1;}
.bkgnd-8ecfb4 {background-color: #8ecfb4;}
.bkgnd-cefdce {background-color: #cefdce;}
.bkgnd-ff0000 {background-color: #ff0000;}

.txt-yellow {color: yellow;}
.txt-b70101 {color: #b70101;}
.txt-green {color: green;}
.txt-red {color: red;}
.txt-white {color: white;}
.txt-blue {color: blue;}
.txt-008000 {color: #008000;}
.txt-0065ff {color: #0065ff;}
.txt-b5651d {color: #b5651d;}
.txt-464646 {color: #464646;}
.txt-002d62 {color: #002d62;}
.txt-3a4aa6 {color: #3a4aa6;}

.TX {background-color: #90EE90;color:black;}
.RX {background-color: #ff6600;color:#ffffff;}

.fnt-7pt {font-size: 7pt;}
.fnt-8pt {font-size: 8pt;}
