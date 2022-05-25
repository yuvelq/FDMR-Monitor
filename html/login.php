<?php
session_start();
include_once "include/config.php";
include_once "selfserv/functions.php";
// Open database connection
check_db();

// Set Language variable
if (isset($_GET["lang"]) && !empty($_GET["lang"])) {
  $_SESSION["lang"] = $_GET["lang"];
  if (isset($_SESSION["lang"]) && $_SESSION["lang"] != $_GET["lang"]) {
    echo '<script type="text/javascript"> location.reload(); </script>';
  }
}
// Include Language file
if (isset($_SESSION["lang"])) {
  include "selfserv/lang/lang_".$_SESSION['lang'].".php";
} else {
  include "selfserv/lang/lang_eng.php";
}
// Define empty variables
$callsign = $psswd = "";
$dmr_idErr = $psswdErr = $loginErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Call sign
  if (isset($_POST["callsign"])) {
    $callsign = Check_input($_POST["callsign"]);
    if (strlen($callsign) < 3 or strlen($callsign) > 10 or preg_match("/[^0-9A-Za-z]/", $callsign)) {
      $callsignErr = True;
    }
  }
  // Password
  if (isset($_POST["psswd"])) {
    $psswd = Check_input($_POST["psswd"]);
    if (strlen($psswd) < 6 or strlen($psswd) > 64) {
      $psswdErr = True;
    }
  }

  if ($callsign and $psswd and !$callsigndErr and !$psswdErr) {
    $h_psswd = hash_pbkdf2("sha256", $psswd, "FreeDMR", 2000);

    $stmt = mysqli_prepare($db_conn, "SELECT int_id, options, mode FROM Clients
      WHERE callsign =  ? AND psswd = ? AND logged_in = True AND opt_rcvd = False");
    $stmt -> bind_param("ss", $callsign, $h_psswd);
    $stmt -> execute();
    $result = $stmt -> get_result();
    if ($result->num_rows > 0) {
      $hs_avail = array();
      while($row = $result -> fetch_assoc()) {
        $hs_avail[$row["int_id"]] = array($row["options"], $row["mode"]);
      }
      // Set Session variables
      $_SESSION["auth"] = True;
      $_SESSION["callsign"] = $callsign;
      $_SESSION["h_psswd"] = $h_psswd;
      $_SESSION["time_ref"] = time();
      $_SESSION["hs_avail"] = $hs_avail;
      $_SESSION["origin"] = "devices.php";
      header("Location: devices.php");
    } else {
      $loginErr = _LOGINERR;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FDMR Monitor - Login</title>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <link rel="stylesheet" type="text/css" href="css/selfserv_css.php">
  <meta name="description" content="Copyright (c) 2016-22.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2022 (v230422)">
</head>
<body>
  <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
  <h2><?php echo  REPORT_NAME?></h2>
  <div><?php include_once "buttons.php"?></div>
  <fieldset class="login selfserv">
    <legend><b>.: Login :.</b></legend>
    <script>
    function changeLang(){
      document.getElementById("form_lang").submit();
    }
    </script>
    <!-- Language -->
    <form class="lang" method="get" action="" id="form_lang">
      <?php echo _SELECT_LANG?><select name="lang" onchange="changeLang();">
      <option value="eng" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "eng"){echo "selected";}?> >English</option>
      <option value="esp" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "esp"){echo "selected";}?> >Español</option>
      </select>
    </form>
    <!-- Login form -->
    <div class="login-gen">
    <form method="post" action="" name="signin-form">
      <div class="login-item">
        <label><?php echo _CALLSIGN?><span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?php echo _CS_INFO?></span></span></label><br>
        <input type="text" name="callsign" autocomplete="on" pattern="[0-9]+[a-zA-Z]+{6,10}" title="<?php echo _CS_ONLY?>" required>
      </div>
      <div class="login-item">
        <label><?php echo _PASSWORD?><span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?php echo _PSSWD_INFO?></span></span></label><br>
        <input type="password" name="psswd" autocomplete="current-password" minlength="6" maxlength="64" required>
      </div>
      <div style="color: red;"><?php echo $loginErr?></div>
      <button class="form-button" type="submit" name="login" value="login">Log in</button>
    </form>
    </div>
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