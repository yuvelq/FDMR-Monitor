<?php
session_start();
include_once "include/config.php";
include_once "selfserv/functions.php";
// Open database connection
check_db();

// Define session owner
if (isset($_GET["dmr_id"])){
  $_SESSION["opt_owner"] = $_GET["dmr_id"];
}

if (!isset($_SESSION["auth"], $_SESSION["time_ref"]) or !array_key_exists($_SESSION["opt_owner"], $_SESSION["hs_avail"])
      or !$_SESSION["auth"] or time() - $_SESSION["time_ref"] > 1800) {
  if (isset($_SESSION["origin"])) {
    header("Location: ".$_SESSION["origin"]);
  } elseif (defined("PRIVATE_NETWORK") and PRIVATE_NETWORK) {
    header("Location: devices.php");
  } else {
    header("Location: selfservice.php");
  }
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

// Set variables
$ts1 = $aod_ts1 = $ts2 = $aod_ts2 = $timer = $single = $voice = $status = $class = "";
$ts1Err = $ts2Err = $timerErr = "";
$_SESSION["changed"] = False;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  // Declare default values for options
  $_SESSION["opt_base"] = array("TS1="=>array(), "TS2="=>array(), "TIMER="=>"", "SINGLE="=>"", "VOICE="=>"");
  // If options not empty
  if ($_SESSION["hs_avail"][$_SESSION["opt_owner"]][0] != NULL) {
    $exp_opts = explode(";", $_SESSION["hs_avail"][$_SESSION["opt_owner"]][0]);
    foreach ($exp_opts as $item) {
      $item_exp = explode("=", $item);
      if (count($item_exp) > 1) {
        $type = $item_exp[0];
        if ($type == "TS1") {
          $_SESSION["opt_base"]["TS1="] = explode(",", $item_exp[1]);
        } elseif ($type == "TS2") {
          $_SESSION["opt_base"]["TS2="] = explode(",", $item_exp[1]);
        } elseif ($type == "TIMER") {
          $_SESSION["opt_base"]["TIMER="] = $item_exp[1];
        } elseif ($type == "SINGLE") {
          $_SESSION["opt_base"]["SINGLE="] = $item_exp[1];
        } elseif ($type == "VOICE") {
          $_SESSION["opt_base"]["VOICE="] = $item_exp[1];
        }
      }
    }
  }
// When form is posted //
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate and process the data from the form
  $ts_type = $_SESSION["hs_avail"][$_SESSION["opt_owner"]][1];
  // TS1
  if (isset($_POST["ts1"]) and $_POST["ts1"]) {
    $ts1 = check_input($_POST["ts1"]);
    // Only allow numbers, commas and white spaces
    if (!preg_match("/[^0-9,\s]/", $ts1)) {
      $ts1 = preg_replace(array("/,+\s+/","/\s+,+/","/\s+/","/,+/"), ",", $ts1);
      $ts1_exp = explode(",", $ts1);
      $ts1_array = $_SESSION["opt_base"]["TS1="];
      foreach ($ts1_exp as $item) {
        if ($_POST["aod_ts1"] == "add") {
          if (count($_SESSION["opt_base"]["TS1="]) < 8) {
            if (!in_array($item, $_SESSION["opt_base"]["TS1="]) and $item >= 10 and $item <= 9999999) {
              array_push($_SESSION["opt_base"]["TS1="], $item);
              $_SESSION["changed"] = True;
            } else {continue;}
          } else {break;}
        } elseif ($_POST["aod_ts1"] == "delete") {
          $temp_del = array();
          foreach ($ts1_exp as $item) {
            if (in_array($item, $_SESSION["opt_base"]["TS1="])) {
              array_push($temp_del, $item);
            } else {continue;}
          }
          if ($temp_del) {
            $_SESSION["opt_base"]["TS1="] = array_diff($_SESSION["opt_base"]["TS1="], $temp_del);
            $_SESSION["changed"] = True;
          }
        }
      }
    } else {
      $ts1Err = _TSERR_ONLY;
    }
  }
  // TS2
  if (isset($_POST["ts2"]) and $_POST["ts2"]) {
    $ts2 = check_input($_POST["ts2"]);
    if (!preg_match("/[^0-9,\s]/", $ts2)) {
      $ts2 = preg_replace(array("/,+\s+/","/\s+,+/","/\s+/","/,+/"), ",", $ts2);
      $ts2_exp = explode(",", $ts2);
      foreach ($ts2_exp as $item) {
        if ($_POST["aod_ts2"] == "add") {
          if (count($_SESSION["opt_base"]["TS2="]) <= 8) {
            if (!in_array($item, $_SESSION["opt_base"]["TS2="]) and $item >= 10 and $item <= 9999999) {
              array_push($_SESSION["opt_base"]["TS2="], $item);
              $_SESSION["changed"] = True;
            } else {continue;}
          } else {break;}
        } elseif ($_POST["aod_ts2"] == "delete") {
          $temp_del = array();
          foreach ($ts2_exp as $item) {
            if (in_array($item, $_SESSION["opt_base"]["TS2="])) {
              array_push($temp_del, $item);
            } else {continue;}
          }
          if ($temp_del) {
            $_SESSION["opt_base"]["TS2="] = array_diff($_SESSION["opt_base"]["TS2="], $temp_del);
            $_SESSION["changed"] = True;
          }
        }
      }
    } else {
      $ts2Err = _TSERR_ONLY;
      $opt_err = True;
    }
  }
  // Timer
  if (isset($_POST["timer"])) {
    $timer = check_input($_POST["timer"]);
    if (!preg_match("/[^0-9\s]/", $timer)) {
      if ($timer == "" or ($timer <= 999 and $timer >= 0)) {
        if ($timer !== $_SESSION["opt_base"]["TIMER="]) {
          $_SESSION["opt_base"]["TIMER="] = $timer;
          $_SESSION["changed"] = True;
        }
      } else {
        $timerErr = _TIMERERR_VAL.$timer;
      }
    } else {
      $timerErr = _ONLY_NUMB;
    }
  }
  // Single
  if (isset($_POST["single"])) {
    $single = check_input($_POST["single"]);
    if ($single == "default") {
      $single = "";
    } elseif ($single == "enable") {
      $single = "1";
    } elseif ($single == "disable") {
      $single = "0";
    }
    if ($single != $_SESSION["opt_base"]["SINGLE="]) {
      $_SESSION["opt_base"]["SINGLE="] = $single;
      $_SESSION["changed"] = True;
    }
  }
  // Voice
  if (isset($_POST["voice"])) {
    $voice = check_input($_POST["voice"]);
    if ($voice == "default") {
      $voice = "";
    } elseif ($voice == "enable") {
      $voice = "1";
    } elseif ($voice == "disable") {
      $voice = "0";
    }

    if ($voice != $_SESSION["opt_base"]["VOICE="]) {
      $_SESSION["opt_base"]["VOICE="] = $voice;
      $_SESSION["changed"] = True;
    }
  }
  // Make the options string and send it to the DMR server
  if ($_SESSION["changed"] and !$ts1Err and !$ts2Err and !$timerErr) {
    $final_opt = "";

    foreach ($_SESSION["opt_base"] as $key=>$value) {
      if (gettype($value) == "array") {
        if (count($value) < 1) {
          continue;
        } else {
          $value = implode(",", $value);
        }

      } else {
        if (strlen($value) < 1) {
          continue;
        }
      }
      $final_opt .= $key.$value.";";
    }
    $stmt = mysqli_prepare($db_conn, "UPDATE Clients SET options=?, modified=True WHERE int_id=? and opt_rcvd=False");
    $stmt -> bind_param("si",$final_opt, $_SESSION["opt_owner"]);
    $stmt -> execute();
    if (mysqli_affected_rows($db_conn)) {
      $status = _DATA_UPDT;
      $class = "status-succes";
    } else {
      $status = _DATA_UPDT_ERR;
      $class = "status-error";
    }
    $_SESSION["changed"] = False;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FDMR Monitor - Options</title>
  <link rel="stylesheet" type="text/css" href="css/styles.php">
  <link rel="stylesheet" type="text/css" href="css/selfserv_css.php">
  <meta name="description" content="Copyright (c) 2016-22.The Regents of the K0USY Group. All rights reserved. Version OA4DOA 2022 (v230422)">
</head>
<body>
  <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
  <h2><?=REPORT_NAME?></h2>
  <div><?php include_once "buttons.php"; ?></div>
  <fieldset class="selfserv" >
    <legend><b>.: Options form :.</b></legend>
    <!-- Language -->
    <script>
    function changeLang(){
      document.getElementById("form_lang").submit();
    }
    </script>
    <form class="lang" method="get" action="" id="form_lang" >
      <?=_SELECT_LANG?><select name="lang" onchange="changeLang();">
      <option value="eng" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "eng"){ echo "selected"; } ?> >English</option>
      <option value="esp" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "esp"){ echo "selected"; } ?> >Español</option>
      </select>
    </form>
    <h3 class="opt-ttl"><?php echo _OPTS.$_SESSION["opt_owner"]; ?></h3>
    <!-- Options form -->
    <form action="form.php" method="post">
      <!-- TS1 -->
      <?php if(in_array($_SESSION["hs_avail"][$_SESSION["opt_owner"]][1], array(1,3))){include_once "selfserv/ts1.php";}?>
      <!-- TS2 -->
      <?php if(in_array($_SESSION["hs_avail"][$_SESSION["opt_owner"]][1], array(2,3,4))){include_once "selfserv/ts2.php";}?>
      <!-- Timer -->
      <h3>Timer: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?=_TIMER_INFO?></span></span></h3>
      <div class="actual"><?=_ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["TIMER="]===""){echo _DEFAULT_STS;}else{echo $_SESSION["opt_base"]["TIMER="]._MINUTES;} ?></span></div>
      <input type="text" name="timer" pattern="[0-9\s]+" value="<?php if($_SESSION["opt_base"]["TIMER="] === ""){echo "";}else{echo $_SESSION["opt_base"]["TIMER="];} ?>" title="<?=_TIMER_PATT?>">
      <p class="error"><?=$timerErr?></p>
      <!-- Single mode -->
      <h3>Single:</h3>
      <div class="actual"><?=_ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["SINGLE="]==""){echo _DEFAULT_STS;}elseif($_SESSION["opt_base"]["SINGLE="]=="1"){echo _ENABLED;}else{echo _DISABLED;}?></span></div>
      <select name="single" >
        <option value="default" <?php if($_SESSION["opt_base"]["SINGLE="]==""){echo "selected";}?>> <?=_DEFAULT?> </option>
        <option value="enable" <?php if($_SESSION["opt_base"]["SINGLE="]=="1"){echo "selected";}?>><?=_ENABLE?></option>
        <option value="disable" <?php if($_SESSION["opt_base"]["SINGLE="]=="0"){echo "selected";}?>><?=_DISABLE?></option>
      </select>
      <!-- Voice announcements -->
      <h3>Beacon Voice Announcements</h3>
      <div class="actual"><?=_ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["VOICE="]==""){echo _DEFAULT_STS;}elseif($_SESSION["opt_base"]["VOICE="]=="1"){echo _ENABLED;}else{echo _DISABLED;}?></span></div>
      <select name="voice" >
        <option value="default" <?php if($_SESSION["opt_base"]["VOICE="]==""){echo "selected";}?>><?=_DEFAULT?></option>
        <option value="enable" <?php if($_SESSION["opt_base"]["VOICE="]=="1"){echo "selected";}?>><?=_ENABLE?></option>
        <option value="disable" <?php if($_SESSION["opt_base"]["VOICE="]=="0"){echo "selected";}?>><?=_DISABLE?></option>
      </select>
      <div class="<?=$class?>"><b><?=$status?></b></div>
      <input class="form-button" type="submit" value="<?=_SUBMIT?>">
    </form>

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
