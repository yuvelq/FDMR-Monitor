<?php
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
include_once 'include/config.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="300">
    <title>FDMR Monitor - System Info</title>
    <script type="text/javascript" src="scripts/hbmon.js"></script>
    <link rel="stylesheet" type="text/css" href="css/styles.php">
    <style>
    .images img {display: block; padding-bottom: 10px; margin-left: auto; margin-right: auto; }
    </style>
  </head>
  <body>
    <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
    <h2><?php echo REPORT_NAME;?></h2>
    <?php echo'<div>'; include_once 'buttons.html'; echo"</div>";?>
    <noscript>You must enable JavaScript</noscript>
    <!--
    <div>
    <a target="_blank" href="esm/"><button class="button link">&nbsp;eZ Server Monitor&nbsp;</button></a>
    </div>
    -->
    <fieldset style="width: 900px;margin: auto; " class="big">
    <legend><b>&nbsp;.: System Info :.&nbsp;</b></legend>
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
          Copyright (c) 2016-2021<br>
          The Regents of the <a target="_blank" href=http://k0usy.mystrikingly.com />K0USY Group</a>. All rights reserved.<br>
          <a title="FDMR Monitor OA4DOA v2021-11" target="_blank" href=https://github.com/yuvelq/HBMonv2>Version OA4DOA 2021</a>
          <!-- Credits: SP2ONG 2019-2021 (v20212012)-->
          <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
        </p>
      </footer>
    </div>
  </body>
</html>
