<?php include_once 'include/config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FDMR Server - Monitor</title>
  <script src="scripts/hbmon.js"></script>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <meta name="description" content="Copyright (c) 2016-22.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2022 (v270422)">
</head>
<body>
  <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
  <h2><?php echo REPORT_NAME; ?></h2>
  <div><?php include_once 'buttons.php'; ?></div>
  <noscript>You must enable JavaScript</noscript>
  <p id="moni"></p>
  <!-- LOG monitor -->
  <fieldset class="big">
    <legend><b>.: Call log window :.</b></legend>
    <pre id="log"></pre>
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
</body>
</html>
