<?php
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
include_once 'include/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OZ-DMR Networks</title>
  <script src="scripts/hbmon.js"></script>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <meta name="description" content="Copyright (c) 2016-21.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2021 (v202111)">
</head>
<body style="background-color:powderblue;">
<center><img class="img-top" src="https://www.oz-dmr.uk/wp-content/uploads/2022/05/logo1.png" alt="" /></center>
  <h2><?php echo REPORT_NAME;?></h2>
  <div><?php include_once 'buttons.php'; ?></div>
  <noscript>You must enable JavaScript</noscript>

<html>
<style>
table {
  border-collapse: collapse;
  border: 2px solid black;
}

.p1 {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 28px;
  color: #000000;
}

.p2 {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 14px;
  color: #000000;
}

.p3 {
  font-family: "Lucida Console", "Courier New", monospace;
  font-size: 13px;
  color: #000000;
}

.p4 {
   font-family: Arial, Helvetica, sans-serif;
  font-size: 14px;
  color: #20bd67;
.p5 {
   font-family: Arial, Helvetica, sans-serif;
  font-size: 10px;
  color: #ff0000;
}}

</style>
<body>
<center>
<style>
body {
  background-color: #FFFFFF;
}
</style>
<div style="width: 1100px; margin-left:0px;">
<?php include_once 'buttons.php'; ?></div>
<div>
<fieldset style="box-shadow:0 0 10px #999;background-color:#e0e0e0e0; width:1050px;margin-left:15px;margin-right:15px;font-size:14px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
<legend><b><font color="#000">&nbsp;.: Options Calculator :.&nbsp;</font></b></legend>
<table style="width:1100px; border: 1px solid white; background-color:white;">
<tr>
<td style="background-color: white;">
<img src="https://www.oz-dmr.uk/wp-content/uploads/2022/05/RQxX4uGfmtNQAAAABJRU5ErkJggg.png" alt="OZ-DMR Networks"</img><h2>OPTIONS - Explained</h2>
<br>
<p class="p2">Below is an example OPTIONS line.<br>We will use this for the exercise which you can use to custom build you own or use the OPTIONS calculator<br><br><p class="p4"><b>Options=TS1=2350;TS2=2350;DIAL=0;TIMER=10;VOICE=0;SINGLE=0;LANG=0;</b></p>
<br>
<hr width="80%"><br>
<p class="p4"><b>TS1=2350,235;</b></p>
<p class="p2">Set Statics you want on Slot 1<br>Separate each Talk Group with a comma</p>
<br>
<p class="p4"><b>TS2=2350,2351;</b></p>
<p class="p2">Set Statics you want on Slot 2<br>Separate each Talk Group with a comma</p>
<br>
<p class="p4"><b>DIAL=2350;</b></p>
<p class="p2">0=None.<br><br>Set default Enhanced Talk Groups (Dial-a-TG) TG 9 Slot 2<br>Only One Talk Group can be entered in this section<br><br>You should not have the same Talk Group set as static on Slot 1 or Slot 2</p>
<br>
<p class="p4"><b>TIMER=10;</b></p>
<p class="p2">Time a Talk Group is held active for.  Pressing your PTT on a Talk Group sets this timer running.<br>You select any number of minutes you like here. But the recommended time is 10 or 5 mins<br><br>If setting more than two static to a slot, you might wish to set Timer to  1 or 2 min</p>
<br>
<p class="p4"><b>VOICE=0;</b></p>
<p class="p2">0=OFF  1=ON.<br><br>15 minute Voice Ident can be set ON or OFF on Time Slot 2.<br><br>Your radio will RX from the network&nbsp;&nbsp;&nbsp;&nbsp;"<b>This is &#60;YOUR CALL-SIGN&#62; FreeDMR</b>"</p>
<br>
<p class="p4"><b>SINGLE=0;</b></p>
<p class="p2">ON=0 OFF=1<br><br>Single Mode can be either ON or OFF. Network default is: OFF</p>
<br>
<p class="p4"><b>LANG=0;</b></p>
<p class="p2">Set the Language the SYSTEM VOICE comes back to you as.  0=Servers Default<br><br>Use the list below to select the language you wish to use. Then use the VOICE CODE in the next column</p>
<br>
<center>
<style>
table, td, th {
  border: 1px solid black;
}

table {
  border-collapse: collapse;
  width: 500px;
}

th {
  height: 30px; width: 200px;
}
</style>
<table style="border-collapse: collapse; border: none; border-spacing: 0px; width=400px">
	<tr><th style="padding-right: 3pt; padding-left: 3pt; width=200px">Voice Language</th><th style="padding-right: 3pt; padding-left: 3pt; width=200px">Voice Code</th></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">CW</td><td style="padding-right: 3pt; padding-left: 3pt;">CW</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">English 1</td><td style="padding-right: 3pt; padding-left: 3pt;">en_GB</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">English 2</td><td style="padding-right: 3pt; padding-left: 3pt;">en_GB_2</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">English 3</td><td style="padding-right: 3pt; padding-left: 3pt;">en_US</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Danish</td><td style="padding-right: 3pt; padding-left: 3pt;">dk_DK</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">French</td><td style="padding-right: 3pt; padding-left: 3pt;">fr_FR</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">German</td><td style="padding-right: 3pt; padding-left: 3pt;">de_DE</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Greek</td><td style="padding-right: 3pt; padding-left: 3pt;">el_GR</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Italian</td><td style="padding-right: 3pt; padding-left: 3pt;">it_IT</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Norwegian</td><td style="padding-right: 3pt; padding-left: 3pt;">no_NO</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Polish</td><td style="padding-right: 3pt; padding-left: 3pt;">pl_PL</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Portuguese</td><td style="padding-right: 3pt; padding-left: 3pt;">pt_PT</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Spanish 1</td><td style="padding-right: 3pt; padding-left: 3pt;">es_ES</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Spanish 2</td><td style="padding-right: 3pt; padding-left: 3pt;">es_ES_2</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Swedish</td><td style="padding-right: 3pt; padding-left: 3pt;">se_SE</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Thai</td><td style="padding-right: 3pt; padding-left: 3pt;">th_TH</td></tr>
	<tr><td style="padding-right: 3pt; padding-left: 3pt;">Welsh</td><td style="padding-right: 3pt; padding-left: 3pt;">cy_GB</td></tr>
</table>
</center>
<br>
<h2 style="text-transform: uppercase; color: red;"><b>Each section is separated with a Semicolon<br>Does not matter what order the above codes are in</b></h2>
<p class="p2">More information can be found on the <a href="http://www.freedmr.uk/index.php/contact/how-to/" target="_blank">FreeDMR website</a>
</fieldset>
</div>
<br>
<br>
<hr width="80%">
<br>
  <footer>
    <p>
      Copyright (c) 2016-2021<br>
      The Regents of the <a target="_blank" href=http://k0usy.mystrikingly.com >K0USY Group</a>. All rights reserved.<br>
      <a title="FDMR Monitor OA4DOA v2021-11" target="_blank" href=https://github.com/yuvelq/FDMR-Monitor.git>Version OA4DOA
        2021</a><br>
        <a title="Modified by OZ-DMR © 2022" target="_blank" href=https://www.oz-dmr.uk/>Modified by OZ-DMR © 2022</a>
      <!-- Credits: SP2ONG 2019-2021 (v20212012)-->
      <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
    </p>
  </footer>
</body>
</html>
