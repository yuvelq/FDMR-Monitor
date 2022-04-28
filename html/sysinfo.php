<?php include_once 'include/config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="300">
  <title>FreeDMR Monitor - System Info</title>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <meta name="description" content="Copyright (c) 2016-22.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2022 (v270422)">
</head>
<body>
  <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
  <h2><?php echo REPORT_NAME; ?></h2>
  <div><?php include_once 'buttons.php'; ?></div>

  <fieldset class="med">
    <legend><b>.: System Info :.</b></legend>
    <div class="images">
      <!-- Temp CPU -->
      <img alt="" src="img/tempC.png">
      <!-- Disk usage -->
      <img alt="" src="img/hdd.png">
      <!-- Memory usage -->
      <img alt="" src="img/mem.png">
      <!-- CPU loads -->
      <img alt="" src="img/cpu.png">
      <!-- Network traffic -->
      <img alt="" src="img/mrtg/localhost_2-day.png">
    </div>
    <p><span class="txt-blue"><b>BLUE</b></span> Outgoing Traffic in Bits per Second | <span class="txt-green"><b>GREEN</b></span> Incoming Traffic in Bits per Second</p>
  </fieldset>
  <footer>
    <p>
      Copyright (c) 2016-2022<br>
      The Regents of the <a target="_blank" href=http://k0usy.mystrikingly.com >K0USY Group</a>. All rights reserved.<br>
      <a title="FDMR Monitor OA4DOA v270422" target="_blank" href=https://github.com/yuvelq/FDMR-Monitor.git>Version OA4DOA 2022</a>
      <!-- Credits: SP2ONG 2019-2022 -->
      <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
    </p>
  </footer>
  </div>
</body>
</html>
