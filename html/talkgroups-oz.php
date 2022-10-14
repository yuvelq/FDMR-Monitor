<?php include_once 'include/config.php';?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OZ-DMR Networks</title>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <meta name="description" content="Copyright (c) 2016-22.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2022 (v270422)">
</head>
<body style="background-color:powderblue;">
  <img class="img-top" src="https://www.oz-dmr.uk/wp-content/uploads/2022/05/logo1.png" alt="">
  <h2><?php echo REPORT_NAME;?></h2>
  <div><?php include_once 'buttons.php';?></div>
  <br>
<center>
<fieldset style="box-shadow:0 0 10px #999;background-color:#e0e0e0e0; width:1050px;margin-left:15px;margin-right:15px;font-size:14px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
    <legend><b>&nbsp;.: OZ-DMR Talk Groups :.&nbsp;</b></legend>
<table style="width:1100px; border: 1px solid black">
<tr>
<td style="width: 98%; background-color:#FFFFFF; border:1px solid black;">
	<img src="https://www.oz-dmr.uk/wp-content/uploads/2022/05/IMG_0058.webp" alt="FreeDMR" height="100">
	<p style="text-align:center;"><b>Talk Groups</b></p>
	<?php
	echo "TalkGroup List Generated at: ".date("d F Y H:i");
	?>&nbsp;</p>
	<p style="text-align:center;">Talk Groups listed on this page are contained with OZ-DMR ONLY unless stated.</p>
<center>
<table style="width:950px; border: 1px solid black">
<tr>
	<th colspan=3 style="width: 100%; background-color:#FFA500; border:1px solid black; text-align: center;">OZ-DMR Talk Group Legend</th>
</tr>
<tr>
	<td style="width: 33%; background-color:#FFFFFF; border:1px solid black; text-align: center;"> <span style="color: #FF0000;"><b>Requested</b></span></td>
	<td style="width: 33%; background-color:#FFFFFF; border:1px solid black; text-align: center;"> <span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 33%; background-color:#FFFFFF; border:1px solid black; text-align: center;"> <span style="color: green;"><b>Local Only</b></span></td>
</tr>
<tr>
	<td style="width: 33%; background-color:#FFFFFF; border:1px solid black; text-align: center; padding: 10px" valign="top"><p align="justify">A request has been submitted and we await a response from the Upstream Network Administrators. Please be patient. Their Administrators are busy working with their own network.</p></td>
	<td style="width: 33%; background-color:#FFFFFF; border:1px solid black; text-align: center; padding: 10px" valign="top"><p align="justify">This Talk Group bridged to another system. Please understand that your traffic goes to another network and requires you to leave a gap left for the other network to reset between overs.</p></td>
	<td style="width: 33%; background-color:#FFFFFF; border:1px solid black; text-align: center; padding: 10px" valign="top"><p align="justify">This Talk Group is only available on the our OZ-DMR Network server. It is not passed to any other system</p></td>
</tr>
</table>
<br>
<table style="width:950px; border: 1px solid black">
<tr>
	<th style="width: 10%; background-color:#FFA500; border:1px solid black;">TG Number</th>
	<th style="width:  5%; background-color:#FFA500; border:1px solid black; text-align: center;">Flag</th>
	<th style="width: 45%; background-color:#FFA500; border:1px solid black; text-align: left;">TG Name</th>
	<th style="width: 30%; background-color:#FFA500; border:1px solid black; text-align: left;">Bridged To</th>
	<th style="width: 35%; background-color:#FFA500; border:1px solid black; text-align: center;">Status</th>
	<th style="width: 40%; background-color:#FFA500; border:1px solid black; text-align: center;">Active</th>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 9</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/234.png" /></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;System Wide - Dial A Talk Group&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(User Activated)</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: justify;">&nbsp;For Accessing ALL Talk Groups</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 10</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/234.png" /></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR Calling</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;YSF 97339</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 11</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/234.png"></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR Chat 1</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR ONLY&nbsp;</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: green;"><b>Local Only</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 12</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/234.png"></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR Chat 2</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR ONLY&nbsp;</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: green;"><b>Local Only</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 13</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/234.png"></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR Chat 3</td>
	<td style="width: 20%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR ONLY&nbsp;</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: green;"><b>Local Only</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 16</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/310.png"</td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;AMSAT</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;XLX606 C</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 17</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/310.png"</td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;M17 Project</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;XLXM17 D</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 19</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/310.png"</td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;HBNet</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;HBNet DMR (TG 9)</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 26</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/505.png"</td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;Central Coast Amateur Radio Club (Australia)</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;XLX500 C</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<!--
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 30</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/310.png"></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;QuadNet Array&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(NOT ACTIVE)</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;QuadNet Array (TG 320)</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: Center;">&nbsp;<span style="color: #FF0000;"><b>Requested</b></span></td>
	<td style="width: 40%; background-color:#FFFFFF; border:1px solid black; text-align: Center;"><b>&#10060;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 31</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/310.png"></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;QuadNet Tech Chat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(NOT ACTIVE)</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;QuadNet Tech Chat (TG 321)</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: #FF0000;"><b>Requested</b></span></td>
	<td style="width: 40%; background-color:#FFFFFF; border:1px solid black; text-align: Center;"><b>&#10060;</b></td>
</tr>
-->
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 9990</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/234.png" /></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR (ECHO) Parrot&nbsp;</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR ONLY</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: green";"><b>Local Only</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
<tr>
	<td style="width: 10%; background-color:#FFFFFF; border:1px solid black">TG 23595</td>
	<td style="width:  5%; background-color:#FFFFFF; border:1px solid black; text-align: center;"><img src="https://freedmr.cymru/talkgroups/flags/234.png" /></td>
	<td style="width: 45%; background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;OZ-DMR Chat & Audio News</td>
	<td style="width: 30%; background-color:#FFFFFF; border:1px solid black; text-align: left;">
<style>
table.GeneratedTable {
  width: 100%;
  background-color: #ffffff;
  border-collapse: collapse;
  border-width: 0px;
  border-color: #ffcc00;
  border-style: solid;
  color: #000000;
}

table.GeneratedTable td, table.GeneratedTable th {
  border-width: 0px;
  border-color: #ffcc00;
  border-style: solid;
  padding: 3px;
}

table.GeneratedTable thead {
  background-color: #ffcc00;
}
</style>
<table class="GeneratedTable">
  <thead>
    <tr>
      <th>MODE</th>
      <th>Connected To</th>
      <th>Connection Method</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>YSF</td>
      <td># 23595</td>
      <td><p style="color:green">MMDVM</p></td>
    </tr>
    <tr>
      <td>NXDN</td>
      <td>TG 23595</td>
      <td><p style="color:green">MMDVM</p></td>
    </tr>
    <tr>
      <td>P25</td>
      <td>TG 23595</td>
      <td><p style="color:red">(Planned)</p></td>
    </tr>
    <tr>
      <td>M17</td>
      <td>M17-OZD</td>
      <td><p style="color:red">(Planned)</p></td>
    </tr>
    <tr>
      <td>XLX</td>
      <td>XLX858-O</td>
      <td><p style="color:green">MMDVM</p></td>
    </tr>
    <tr>
      <td>EchoLink</td>
      <td>M0GLJ-L</td>
      <td><p style="color:green">MMDVM</p></td>
    </tr>
    <tr>
      <td>FreeDMR</td>
      <td>TG 23595</td>
      <td><p style="color:green">OpenBridge</style></td>
    </tr>
  </tbody>
</table>
</td>
	<td style="width: 35%; background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;<span style="color: orange;"><b>Bridged</b></span></td>
	<td style="width: 40%; background-color:#0BDA51; border:1px solid black; text-align: center;"><b>&#10004;</b></td>
</tr>
</table>
</center>
<br>
	<?php
	echo "TalkGroup List Generated at: ".date("d F Y H:i");
	?>&nbsp;</p>
</td>
</tr>
</table>
  </fieldset>
  </center>
 <footer>
   <p>
     Copyright (c) 2016-2022<br>
     The Regents of the <a target="_blank" href=http://k0usy.mystrikingly.com>K0USY Group</a>. All rights reserved.<br>
     <a title="FDMR Monitor OA4DOA v270422" target="_blank" href=https://github.com/yuvelq/FDMR-Monitor.git>Version OA4DOA 2022</a>
     <!-- Credits: SP2ONG 2019-2022 -->
     <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
   </p>
 </footer>
</body>
</html>
