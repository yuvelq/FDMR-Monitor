<?php
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
include_once 'include/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FDMR Monitor - STATUS</title>
<script type="text/javascript" src="scripts/hbmon.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.php" />
<meta name="description" content="Copyright (c) 2016-2021.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2021 (v202111)" />
</head>
<body>
<img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="" />
<h2><?php echo REPORT_NAME;?></h2>
<?php echo'<div>'; include_once 'buttons.html'; echo"</div>";?>
<noscript>You must enable JavaScript</noscript>
		<p id="bridge"></p>  
<footer>
  <p>
    Copyright (c) 2016-2021<br>
    The Regents of the <a href=http://k0usy.mystrikingly.com/>K0USY Group</a>. All rights reserved.<br>
    <a href=https://github.com/yuvelq/HBMonv2>Version OA4DOA 2021</a>
    <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
    <!-- This is version of HBMonitor OA4DOA 2021 (v202111)-->
  </p>
</footer>
</body>
</html>
