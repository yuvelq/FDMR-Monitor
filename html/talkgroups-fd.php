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
    <legend><b>&nbsp;.: FreeDMR Talk Groups :.&nbsp;</b></legend>
<table style="width:1100px; border: 1px solid black">
<tr>
<td style="width: 98%; background-color:#FFFFFF; border:1px solid black;">
	<img src="http://www.freedmr.uk/wp-content/uploads/2021/04/Free_DMR_logo_02.png" alt="FreeDMR" width="200" height="100">
	<p style="text-align:center;"><b>Talk Groups</b></p>
	<p style="text-align:center;">Talk Groups listed on this page are contained with are Network wide.</p>
<br>
<?php
echo "TalkGroup List Generated at: ".date("d F Y H:i");
?>&nbsp;</p>

<!-- Pull TalkGroup List to include in list -->
<!-- FreeDMR Source - http://downloads.freedmr.uk/downloads/Talkgroups_FreeDMR.csv -->
<!-- FreeDMR Wales - http://freedmr.cymru/talkgroups/Talkgroups_FreeDMR.csv -->
<center>
<?php
$freeDMRlist = file("http://freedmr.cymru/talkgroups/Talkgroups_FreeDMR.csv");

if ($freeDMRlist) {
	$dmrRefList = array();
	$counter = 0;
	foreach($freeDMRlist as $line) {
		if ($counter == 0) {
			$counter++;
			continue;
		}
                if (ctype_alpha(substr($line, 0, 1))) {
                        if  (substr_count($line, ",") >= 2)  { $refData = explode(",", $line);  }
                        elseif  (substr_count($line, "\t") >= 2)  { $refData = explode("\t", $line);  }
                        array_push($dmrRefList, $refData[1].";".$refData[2].";".$refData[0]);
                        $counter++;
		}
	}
	natsort($dmrRefList);

	echo '<table style="width:950px; border: 1px solid black">'."\n";
	echo '  <tr>'."\n";
	echo '    <th style="background-color:#FFA500; border:1px solid black;">TG Number</th><th style="background-color:#FFA500; border:1px solid black; text-align: left;">TG Name</th><th style="background-color:#FFA500; border:1px solid black; text-align: left;">Country</th>'."\n";
	echo '  </tr>'."\n";
	foreach ($dmrRefList as $refData) {
		$fields = explode(";", $refData);
		$tgNum = $fields[0];
		$tgName = $fields[1];
		$tgCountry = $fields[2];
		echo '<tr><td style="background-color:#FFFFFF; border:1px solid black">TG '.$tgNum.'</td><td style="background-color:#FFFFFF; border:1px solid black; text-align: left;">'.$tgName.'</td><td style="background-color:#FFFFFF; border:1px solid black; text-align: left;">'.$tgCountry.'</td></tr>'."\n";
	}
	echo '</table>'."\n";
	echo '<br />'."\n";
	echo "Number of Talkgroups: ".$counter."\n";
}
else {
	echo '<table>'."\n";
	echo '  <tr>'."\n";
	echo '    <td colspan="3">FreeDMR data not available from FreeDMR.uk</td>'."\n";
	echo '  </tr>'."\n";
	echo '</table>'."\n";
	echo '<br />'."\n";
}
?>

<p style="text-align:center;">
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
