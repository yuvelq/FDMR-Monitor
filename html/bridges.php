<?php
include_once 'include/config.php';
session_start()
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FDMR Monitor - Status</title>
<script src="scripts/hbmon.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.php">
<meta name="description" content="Copyright (c) 2016-23.The Regents of the K0USY Group. All rights reserved. Version OA4DOA">
</head>
<body>
<img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="" >
<h2><?php echo REPORT_NAME?></h2>
<div><?php include_once 'buttons.php'?></div>
<noscript>You must enable JavaScript</noscript>
<p id="bridge"></p>  
<footer>
  <p>
    Copyright (c) 2016-<?php echo date("Y");?><br>
    The Regents of the <a href=http://k0usy.mystrikingly.com>K0USY Group</a>. All rights reserved.<br>
    <a title="FDMR Monitor OA4DOA v1.0.0" href=https://github.com/yuvelq/FDMR-Monitor.git>Version OA4DOA</a>
    <!-- Credits: SP2ONG 2019-2022 -->
    <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
  </p>
</footer>
</body>
</html>
