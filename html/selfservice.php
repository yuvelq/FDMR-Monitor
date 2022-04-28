<?php
session_start();
include_once "include/config.php";
include_once "selfserv/functions.php";
// Open database connection
check_db();

// Redirect to login page
if (!defined("PRIVATE_NETWORK") or PRIVATE_NETWORK) {
  header("Location: login.php");
  exit();
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $client_addr = $_SERVER["REMOTE_ADDR"];
  if ($client_addr) {
    $stmt = mysqli_prepare($db_conn, "SELECT int_id, options, mode FROM Clients WHERE host = ? and logged_in=True and opt_rcvd = False");
    $stmt->bind_param("s", $client_addr);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      $hs_avail = array();
      while ($row = $result->fetch_assoc()) {
        $hs_avail[$row["int_id"]] = array($row["options"], $row["mode"]);								
      }
      $_SESSION["auth"] = True;
      $_SESSION["time_ref"] = time();
      $_SESSION["client_addr"] = $client_addr;
      $_SESSION["hs_avail"] = $hs_avail;
      $_SESSION["changed"] = False;
      $_SESSION["origin"] = "selfservice.php";

      $show_array = array("<b>"._SELECT_DEVICE."</b>");
      foreach ($hs_avail as $k=>$val) {
        array_push($show_array, '<a href="form.php?dmr_id='.$k.'"> '.$k.' </a>');
      }
      $data2show = implode("<br>", $show_array);

    } else {
      $data2show = _NOT_LOGGED.$client_addr.".";
    }
  } else {
    $data2show = _INVALID_IP;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FDMR Server - Monitor</title>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <link rel="stylesheet" type="text/css" href="css/selfserv_css.php">
  <meta name="description" content="Copyright (c) 2016-22.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2022 (v200422)">
</head>
<body>
  <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
  <h2><?= REPORT_NAME;?></h2>
  <div><?php include_once "buttons.php"; ?></div>
  <fieldset class="selfserv">
    <legend><b>&nbsp;.: Self Service :.&nbsp;</b></legend>
    <script>
    function changeLang(){
      document.getElementById("form_lang").submit();
    }
    </script>
    <!-- Language -->
    <form class="lang" method="get" action="" id="form_lang" >
      <?=_SELECT_LANG?><select name="lang" onchange="changeLang();" >
      <option value="eng" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "eng"){ echo "selected"; } ?> >English</option>
      <option value="esp" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "esp"){ echo "selected"; } ?> >Español</option>
      </select>
    </form>
    <p style="text-align: left; margin: 15px 25px;"><?=_NOTE_MAIN?></p>
    <!-- Logged in devices info -->
    <div class="show-data"><?= $data2show ?></div>
    <div><?= _TRY_LOGIN ?></div>
  </fieldset>
  <footer>
    <p>
      Copyright (c) 2016-2022<br>
      The Regents of the <a target="_blank" href=http://k0usy.mystrikingly.com >K0USY Group</a>. All rights reserved.<br>
      <a title="FDMR Monitor OA4DOA v230422" target="_blank" href=https://github.com/yuvelq/FDMR-Monitor.git>Version OA4DOA 2022</a>
      <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
    </p>
  </footer>
</body>
</html>