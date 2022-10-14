<?php
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
include_once 'include/config.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
<head>
<meta charset="UTF-8">
<title>OZ-DMR Networks</title>
<script type="text/javascript" src="scripts/hbmon.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.php" />
<meta name="description" content="Copyright &copy; 2016-2022.The Regents of the K0USY Group. All rights reserved. Version SP2ONG 2019-2022" />
</head>
<body style="background-color:powderblue; font: 10pt arial, sans-serif;">
<center><div style="width:1100px; text-align: center; margin-top:5px;">
<img src="https://www.oz-dmr.uk/wp-content/uploads/2022/05/logo1.png" alt="" />
</div>
<div style="width: 1100px;">
<p style="text-align:center;"><span style="color:#000;font-size: 18px; font-weight:bold;"><?php echo REPORT_NAME;?></span></p>
<p></p>
</div>
<?php include_once 'buttons.php'; ?>
<div style="width: 1100px;">
<noscript>You must enable JavaScript</noscript>
        <p id="Server Status"></p>
<center>
<fieldset style="box-shadow:0 0 10px #999;background-color:#e0e0e0e0; width:1050px;margin-left:15px;margin-right:15px;font-size:14px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
    <legend><b>&nbsp;.: FreeDMR Server Status :.&nbsp;</b></legend>
<table style="width:1100px; border: 1px solid black">
<tr>
<td style="width: 98%; background-color:#FFFFFF; border:1px solid black;">
	<p style="text-align:center;"></p>
	<img src="http://www.freedmr.uk/wp-content/uploads/2021/04/Free_DMR_logo_02.png" alt="FreeDMR" width="200" height="100">
	<p style="text-align:center;"><b>Server Status</b></p>
<p style="text-align: center;"><span style="text-align: center;">
<p style="text-align: center;">Updated Every 15 Mins<span style="text-align: center;">
<br>
<br>
<?php

$filename = '/var/www/html/server_ids.tsv';

if (file_exists($filename)) {
    echo "Servers Last Checked: " . date ("d F Y @ H:i", filemtime($filename));
}
?>
<p style="text-align:center;"></p>
<p style="text-align: center;">&#x1F7E2; - ONLINE&nbsp;&nbsp;&nbsp;&#x1F534; - OFFLINE<span style="text-align: center;">
<center>

<?php

$freeDMRserver = file("https://dash.oz-dmr.uk/server_ids.tsv");

if ($freeDMRserver) {
	$dmrRefList = array();
	$counter = 0;
	foreach($freeDMRserver as $line) {
		if ($counter == 0) {
			$counter++;
			continue;
		}
                if (ctype_alpha(substr($line, 0, 1))) {
                        if  (substr_count($line, ",") >= 4)  { $refData = explode(",", $line);  }
                        elseif  (substr_count($line, "\t") >= 4)  { $refData = explode("\t", $line);  }
                        array_push($dmrRefList, $refData[1].";".$refData[2].";".$refData[0].";".$refData[4].";".$refData[5]);
                        $counter++;
		}
	}
	natsort($refData[2]);

	echo '<table style="width:950px; border: 1px solid black">'."\n";
	echo '  <tr>'."\n";
	echo '    <th style="background-color:#FFA500; border:1px solid black; width: 250px; text-align: left;">&nbsp;Server Name</th><th style="background-color:#FFA500; border:1px solid black; width: 80px;text-align: center;">&nbsp;Server ID</th><th style="background-color:#FFA500; border:1px solid black; text-align: left;">&nbsp;Server (IP)Address</th><th style="background-color:#FFA500; border:1px solid black; width: 50px">Port</th><th style="background-color:#FFA500; border:1px solid black; width: 150px">Server Status</th>'."\n";
	echo '  </tr>'."\n";
	foreach ($dmrRefList as $refData) {
		$fields = explode(";", $refData);
		$Id = $fields[0];
		$Server = $fields[1];
		$Country = $fields[2];
		$Port = $fields[3];
		$Status = $fields[4];

//		if ($Status =="ONLINE") {

//			$Ss = "'&#x1F7E2;'&nbsp;&nbsp;&nbsp;'.date("d M Y h:i").'&nbsp;"; // Operational

//		} elseif ($Status == "OFFLINE") {

//			$Ss = "'&#x1F534;'&nbsp;&nbsp;&nbsp;'.date("d M Y h:i").'&nbsp;"; // Not Operational

//		} else {

//			$Ss = "'&#x1F7E0;'&nbsp;&nbsp;&nbsp;'.date("d M Y h:i").'&nbsp;"; // Not Operational

//		}


		echo '<tr><td style="background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;'.$Country.'</td><td style="background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;'.$Id.'</td><td style="background-color:#FFFFFF; border:1px solid black; text-align: left;">&nbsp;'.$Server.'</td><td style="background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;'.$Port.'</td><td style="background-color:#FFFFFF; border:1px solid black; text-align: center;">&nbsp;'.$Status.'</td></tr>'."\n";
	}
	echo '</table>'."\n";
	echo '<br />'."\n";
	echo "Number of Registered Servers: ".$counter."\n";
}
else {
	echo '<table>'."\n";
	echo '  <tr>'."\n";
	echo '    <td colspan="3">FreeDMR Server Data Not Available from FreeDMR.uk</td>'."\n";
	echo '  </tr>'."\n";
	echo '</table>'."\n";
	echo '<br />'."\n";
}
?>

<p style="text-align:center;"></p>
<p style="text-align: center;">&#x1F7E2; - ONLINE&nbsp;&nbsp;&nbsp;&#x1F534; - OFFLINE<span style="text-align: center;">
<p style="text-align:center;"></p>
</center>
</td>
</tr>
</table>
</fieldset>
</center>
</div>
<p style="text-align: center;"><span style="text-align: center;">
Copyright &copy; 2016-2022<br>The Regents of the <a href=http://k0usy.mystrikingly.com/>K0USY Group</a>. All rights reserved.<br><a href=https://github.com/sp2ong/HBMonv2>Version SP2ONG 2019-2022</a><br><br>
    <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
    <!-- This is version of HBMonitor SP2ONG 2019-2022 -->
</p>
</center>
</body>
</html>
