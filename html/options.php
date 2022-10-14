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
}

.p5 {
   font-family: Arial, Helvetica, sans-serif;
  font-size: 10px;
  color: #ff0000;
}

.p6 {
   font-family: Arial, Helvetica, sans-serif;
  font-size: 13px;
  color: #ff0000;
}

</style>
<script type="text/javascript">
   document.getElementById("timeslot1-1").disabled = true;
   document.getElementById("timeslot1-2").disabled = true;
   document.getElementById("timeslot1-3").disabled = true;
   document.getElementById("timeslot1-4").disabled = true;
   document.getElementById("timeslot1-5").disabled = true;
   document.getElementById("timeslot1-6").disabled = true;
   document.getElementById("timeslot1-7").disabled = true;
   document.getElementById("timeslot1-8").disabled = true;
   document.getElementById("timeslot1-9").disabled = true;

   function ShowHideDiv () {
      var chkDuplex = document.getElementById("duplex")
      var timeslot11 = document.getElementById("timeslot1-1");
      var timeslot12 = document.getElementById("timeslot1-2");
      var timeslot13 = document.getElementById("timeslot1-3");
      var timeslot14 = document.getElementById("timeslot1-4");
      var timeslot15 = document.getElementById("timeslot1-5");
      var timeslot16 = document.getElementById("timeslot1-6");
      var timeslot17 = document.getElementById("timeslot1-7");
      var timeslot18 = document.getElementById("timeslot1-8");
      var timeslot19 = document.getElementById("timeslot1-9");

      timeslot11.disabled = chkDuplex.checked ? false: true
      timeslot12.disabled = chkDuplex.checked ? false: true
      timeslot13.disabled = chkDuplex.checked ? false: true
      timeslot14.disabled = chkDuplex.checked ? false: true
      timeslot15.disabled = chkDuplex.checked ? false: true
      timeslot16.disabled = chkDuplex.checked ? false: true
      timeslot17.disabled = chkDuplex.checked ? false: true
      timeslot18.disabled = chkDuplex.checked ? false: true
      timeslot19.disabled = chkDuplex.checked ? false: true
      }

</script>
<script src="scripts/hbmon.js"></script>
<body>
<center>
<style>
body {
  background-color: #FFFFFF;
}
</style>
<div style="width: 1100px; margin-left:0px;">
<?php include_once 'buttons.php'; ?></div>
<fieldset style="box-shadow:0 0 10px #999;background-color:#e0e0e0e0; width:1050px;margin-left:15px;margin-right:15px;font-size:14px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
<legend><b><font color="#000">&nbsp;.: Options Calculator :.&nbsp;</font></b></legend>
<table style="width:1100px; border: 1px solid white; background-color:white;">
<tr>
<td style="background-color: white;">
<img src="https://www.oz-dmr.uk/wp-content/uploads/2022/05/RQxX4uGfmtNQAAAABJRU5ErkJggg.png" alt="OZ-DMR Networks"</img><h2>OPTIONS Calculator</h2>
<br>
<p class="p2">This tool is used to help generate the "string" needed for the options field for Pi-Star, MMDVM and DMRGateway.</p>
<p class="p4">Duplex Hotspots / Repeaters can use TS1 and/or TS2</p>
<p class="p4">Simplex Hotspots can only use TS2</p>
<p class="p2">Type in the Talk Groups and Settings you want. <br>
Click "Generate Pi-Star Options"</p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="hidden" name="generate" value="yes">
<p class="p2">Duplex: <input type="radio" id="duplex" name="hotspot" value="duplex" onclick="ShowHideDiv()"/> Simplex: <input type="radio" id="simplex" name="hotspot" value="simplex" onclick="ShowHideDiv()" checked/></p>
<center>
<p class="p6">If you plan on using&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Dial-a-TG</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Do not set any Static Talk Groups on Slot 2</p>
<table border="1" style="background-color: #F8F0E3;">
<tbody><td class="p2"><center><b>Timeslot 1</center></b></td><td class="p2"><center><b>Timeslot 2</center></b></td></tr></p>
<tr><td class="p2">&nbsp; TG 1 (leave 0 for none): <input type="text" id="timeslot1-1" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 1 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 2 (leave 0 for none): <input type="text" id="timeslot1-2" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 2 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 3 (leave 0 for none): <input type="text" id="timeslot1-3" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 3 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 4 (leave 0 for none): <input type="text" id="timeslot1-4" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 4 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 5 (leave 0 for none): <input type="text" id="timeslot1-5" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 5 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 6 (leave 0 for none): <input type="text" id="timeslot1-6" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 6 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 7 (leave 0 for none): <input type="text" id="timeslot1-7" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 7 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 8 (leave 0 for none): <input type="text" id="timeslot1-8" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 8 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG 9 (leave 0 for none): <input type="text" id="timeslot1-9" name="timeslot1[]" size="10" value="0" disabled=""></td><td class="p2">&nbsp; TG 9 (leave 0 for none): <input type="text" id="timeslot2" name="timeslot2[]" size="10" value="0"></td></tr>
</table><script>
   document.getElementById("timeslot1-1").disabled = true;
   document.getElementById("timeslot1-2").disabled = true;
   document.getElementById("timeslot1-3").disabled = true;
   document.getElementById("timeslot1-4").disabled = true;
   document.getElementById("timeslot1-5").disabled = true;
   document.getElementById("timeslot1-6").disabled = true;
   document.getElementById("timeslot1-7").disabled = true;
   document.getElementById("timeslot1-8").disabled = true;
   document.getElementById("timeslot1-9").disabled = true;
</script>
<br>
<center><table style="width:350px; border: 1px solid color: black; background-color: #F8F0E3;>
<tr><td class="p2" colspan="3"><br><a title="" href="options-explination.php" rel="noopener"><span style="color: #20bd67;">Click here to See Options Explained in more details</a><br><br></td></tr>
<tr><td class="p2">&nbsp; Dial-a-TG</td><td class="p2">&nbsp; 0 or 23595</td><td><input type="text" name="dial" size="10" value="23595"></td></tr>
<tr><td class="p2">&nbsp; Voice Identification &nbsp;</td><td class="p2">&nbsp; 0 or 1</td><td><input type="text" name="voice" size="2" value="0"></td></tr>
<tr><td class="p2">&nbsp; Language</td><td class="p2">&nbsp; 0 or en_GB &nbsp;</td><td><input type="text" name="lang" size="7" value="0"></td></tr>
<tr><td class="p2">&nbsp; Single Mode</td><td class="p2">&nbsp; 0 or 1</td><td><input type="text" name="single" size="2" value="0"></td></tr>
<tr><td class="p2">&nbsp; TG Timeout</td><td class="p2">&nbsp; Minutes</td><td><input type="text" name="timeout" size="5" value="15"></td></tr>
</table>
<br>
<p>Results Will Be Displayed Below</p>
<input type="submit" value="Generate Options">
<br>
<br>
<hr width="80%">
<?php
  function myFilter($var) {
     return ($var !== NULL && $var !== FALSE && $var !== "" && $var !== "0");
  }

   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($_POST['generate'] == "yes") {
         $dmrgateway = "Options=\"";
         if (!empty($_POST["timeslot1"])) {
            $ts1result = array_filter($_POST["timeslot1"], "myFilter");
            for ($i=0; $i < count($ts1result)+3; $i++) {
               $ts1 .= $ts1result[$i];
               $next = $i+1;
               if (isset($ts1result[$next])) {
                  $ts1 .= ",";
               }
            }
            if($ts1 !== "") {
               $ts1out = "TS1=$ts1;";
            }
         }

         if (!empty($_POST["timeslot2"])) {
            $ts2result = array_filter($_POST["timeslot2"], "myFilter");
            for ($i=0; $i < count($ts2result)+3; $i++) {
               if ($ts2result[$i] !== "0") {
                  $ts2 .= $ts2result[$i];
               }
               $prev = $i-1;
               $next = $i+1;
               if (isset($ts2result[$next])) {
                  $ts2 .= ",";
               }
            }
            if($ts2 !== "") {
               $ts2out = "TS2=$ts2;";
            }
         }

	 echo "<p class=\"p2\">Results:</p>\n";
         $rest = "DIAL=".$_POST["dial"].";VOICE=".$_POST["voice"].";LANG=".$_POST["lang"].";SINGLE=".$_POST["single"].";TIMER=".$_POST["timeout"];
         ## Display the lines.
         echo "<p class=\"p4\">DMRGateway:&nbsp;&nbsp;&nbsp;&nbsp;".$dmrgateway.$ts1out.$ts2out.$rest.";\"</p>";
         echo "<p class=\"p2\">DMR Options=:&nbsp;&nbsp;&nbsp;&nbsp;".$ts1out.$ts2out.$rest.";</p>";
      }
   }
?>
<br>
</td>
</tr>
</table>
</fieldset></div><br>
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
