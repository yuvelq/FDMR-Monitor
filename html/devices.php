<?php
session_start();
include_once "include/config.php";
include_once "selfserv/functions.php";
// Open database connection
check_db();

// Verify session and refresh data if session has more than 5 minutes
if (!isset($_SESSION["auth"]) or !$_SESSION["auth"]) {
  header("Location: login.php");
  exit();
} else {
  if (isset($_SESSION["time_ref"], $_SESSION["w_dmr_id"], $_SESSION["h_psswd"]) and time()-$_SESSION["time_ref"] > 0.5) {
    $stmt = mysqli_prepare($db_conn, "SELECT int_id, options, mode FROM Clients
      WHERE int_id LIKE ? AND psswd = ? AND logged_in = True AND opt_rcvd = False");
    $stmt -> bind_param("ss", $_SESSION["w_dmr_id"], $_SESSION["h_psswd"]);
    $stmt -> execute();
    $result = $stmt -> get_result();
    if ($result -> num_rows > 0) {
      $hs_avail = array();
      while($row = $result -> fetch_assoc()) {
        $hs_avail[$row["int_id"]] = array($row["options"], $row["mode"]);
      }
      $_SESSION["hs_avail"] = $hs_avail;
      $_SESSION["time_ref"] = time();
    }
  }
}

// Set Language variable
if (isset($_GET["lang"]) && !empty($_GET["lang"])) {
  $_SESSION["lang"] = $_GET["lang"];

  if (isset($_SESSION["lang"]) && $_SESSION["lang"] != $_GET["lang"]) {
    echo '<script type="text/javascript"> location.reload(); </script>';
  }
}
// Include Language file
if (isset($_SESSION["lang"])) {
  include "selfserv/lang/lang_".$_SESSION["lang"].".php";
} else {
  include "selfserv/lang/lang_eng.php";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FDMR Monitor - Devices</title>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <link rel="stylesheet" type="text/css" href="css/selfserv_css.php">
  <meta name="description" content="Copyright (c) 2016-22.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2022 (v230422)">
</head>
<body>
  <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
  <h2><?php echo REPORT_NAME; ?></h2>
  <div><?php include_once "buttons.php"; ?></div>
  <fieldset class="selfserv">
    <legend><b>.: Devices :.</b></legend>
    <script>
    function changeLang(){
      document.getElementById("form_lang").submit();
    }
    </script>
    <!-- Language -->
    <form class="lang" method="get" action="" id="form_lang">
      <?=_SELECT_LANG?><select name="lang" onchange="changeLang();">
      <option value="eng" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "eng"){ echo "selected"; } ?> >English</option>
      <option value="esp" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "esp"){ echo "selected"; } ?> >Español</option>
      </select>
    </form>
    
    <!-- Logged in devices info -->
    <div class="show-data">
      <div><b><?=_SELECT_DEVICE?></b></div>
      <?php
        foreach($_SESSION["hs_avail"] as $key=>$val){
          echo '<a href="form.php?dmr_id='.$key.'"> '.$key.' </a><br>';
        }
      ?>
    </div>
  </fieldset>
  <footer>
    <p>
      Copyright (c) 2016-2022<br>
      The Regents of the <a target="_blank" href=http://k0usy.mystrikingly.com>K0USY Group</a>. All rights reserved.<br>
      <a title="FDMR Monitor OA4DOA v230422" target="_blank" href=https://github.com/yuvelq/FDMR-Monitor.git>Version OA4DOA 2022</a>
      <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
    </p>
  </footer>
</body>
</html>