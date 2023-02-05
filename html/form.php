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
$ts1Err = $ts2Err = $timerErr = $dialErr = "";
$_SESSION["changed"] = False;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  // Declare default values for options
  $_SESSION["opt_base"] = array("TS1="=>array(), "TS2="=>array(), "TIMER="=>"", "SINGLE="=>"", "VOICE="=>"", "LANG="=>"", "DIAL="=>"");
  // If options not empty
  if ($_SESSION["hs_avail"][$_SESSION["opt_owner"]][0] != NULL) {
    $exp_opts = explode(";", $_SESSION["hs_avail"][$_SESSION["opt_owner"]][0]);
    foreach ($exp_opts as $item) {
      $item_exp = explode("=", $item);
      if (count($item_exp) > 1) {
        $key = $item_exp[0];
        $value = $item_exp[1];
        if ($key == "TS1") {
          foreach(explode(",", $value) as $tg) {
            if ($tg != ""){
              array_push($_SESSION["opt_base"]["TS1="], $tg);
            }
          }
        } elseif ($key == "TS2") {
          foreach(explode(",", $value) as $tg) {
            if ($tg != ""){
              array_push($_SESSION["opt_base"]["TS2="], $tg);
            }
          }
        } elseif ($key == "TIMER") {
          $_SESSION["opt_base"]["TIMER="] = $value;
        } elseif ($key == "SINGLE") {
          $_SESSION["opt_base"]["SINGLE="] = $value;
        } elseif ($key == "VOICE") {
          $_SESSION["opt_base"]["VOICE="] = $value;
        }elseif ($key == "LANG") {
          $_SESSION["opt_base"]["LANG="] = $value;
        }elseif ($key == "DIAL") {
          $_SESSION["opt_base"]["DIAL="] = $value;
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
          if (count($_SESSION["opt_base"]["TS1="]) <= 12) {
            if (!in_array($item, $_SESSION["opt_base"]["TS1="]) and $item >= 10 and $item <= 16777215) {
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
          if (count($_SESSION["opt_base"]["TS2="]) <= 12) {
            if (!in_array($item, $_SESSION["opt_base"]["TS2="]) and $item >= 10 and $item <= 16777215) {
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
   //lang
   if (isset($_POST["lang"])) {
    $lang = check_input($_POST["lang"]);

    if ($lang != $_SESSION["opt_base"]["LANG="]) {
      $_SESSION["opt_base"]["LANG="] = $lang;
      $_SESSION["changed"] = True;
    }
  }
  //dial
  if (isset($_POST["dial"])) {
    $dial = check_input($_POST["dial"]);

    if ($dial != $_SESSION["opt_base"]["DIAL="]) {
      if ($dial === "") {
        $_SESSION["opt_base"]["DIAL="] = $dial;
        $_SESSION["changed"] = True;
      } elseif ($dial != "" and is_numeric($dial) and intval($dial) >= 0 and intval($dial) <= 16777215) {
        $_SESSION["opt_base"]["DIAL="] = intval($dial);
        $_SESSION["changed"] = True;
      } else {
        $dialErr = _TIMERERR_VAL.$dial;
      }
    }
  }
  // Make the options string and send it to the DMR server
  if ($_SESSION["changed"]) {
    $final_opt = "";
    foreach ($_SESSION["opt_base"] as $key => $value) {
      if ($key == "TS1=" and $ts1Err or $key == "TS2=" and $ts2Err or $key == "TIMER=" and $timerErr or $key == "DIAL=" and $dialErr) {
        continue;
      }
      if (gettype($value) == "array") {
        if (count($value) < 1) {
          $value = "";
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
  <meta name="description" content="Copyright (c) 2016-23.The Regents of the K0USY Group. All rights reserved. Version OA4DOA">
</head>
<body>
  <img class="img-top" src="img/logo.png?random=323527528432525.24234" alt="">
  <h2><?php echo REPORT_NAME?></h2>
  <div><?php include_once "buttons.php"?></div>
  <fieldset class="selfserv" >
    <legend><b>.: Options form :.</b></legend>
    <!-- Language -->
    <script>
    function changeLang() {
      document.getElementById("form_lang").submit();
    }
    </script>
    <form class="lang" method="get" action="" id="form_lang" >
      <?php echo _SELECT_LANG?><select name="lang" onchange="changeLang();">
      <option value="eng" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "eng"){echo "selected";}?> >English</option>
      <option value="ita" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "ita"){echo "selected";}?> >Italiano</option>
      <option value="esp" <?php if(isset($_SESSION["lang"]) and $_SESSION["lang"] == "esp"){echo "selected";}?> >Español</option>
      </select>
    </form>
    <h3 class="opt-ttl"><?php echo _OPTS.$_SESSION["opt_owner"]?></h3>
    <!-- Options form -->
    <form action="form.php" method="post">
      <!-- TS1 -->
      <?php if(in_array($_SESSION["hs_avail"][$_SESSION["opt_owner"]][1], array(1,3))){include_once "selfserv/ts1.php";}?>
      <!-- TS2 -->
      <?php if(in_array($_SESSION["hs_avail"][$_SESSION["opt_owner"]][1], array(2,3,4))){include_once "selfserv/ts2.php";}?>
      <!-- Timer -->
      <h3>Timer: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?php echo _TIMER_INFO?></span></span></h3>
      <div class="actual"><?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["TIMER="]===""){echo _DEFAULT_STS;}else{echo $_SESSION["opt_base"]["TIMER="]._MINUTES;}?></span></div>
      <input type="text" name="timer" pattern="[0-9\s]+" value="<?php if($_SESSION["opt_base"]["TIMER="] === ""){echo "";}else{echo $_SESSION["opt_base"]["TIMER="];}?>" title="<?php echo _TIMER_PATT?>">
      <p class="error"><?php echo $timerErr?></p>
      <!-- Single mode -->
      <h3>Single:</h3>
      <div class="actual"><?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["SINGLE="]==""){echo _DEFAULT_STS;}elseif($_SESSION["opt_base"]["SINGLE="]=="1"){echo _ENABLED;}else{echo _DISABLED;}?></span></div>
      <select name="single" >
        <option value="default" <?php if($_SESSION["opt_base"]["SINGLE="]==""){echo "selected";}?>> <?php echo _DEFAULT?> </option>
        <option value="enable" <?php if($_SESSION["opt_base"]["SINGLE="]=="1"){echo "selected";}?>><?php echo _ENABLE?></option>
        <option value="disable" <?php if($_SESSION["opt_base"]["SINGLE="]=="0"){echo "selected";}?>><?php echo _DISABLE?></option>
      </select>
      <!-- Voice announcements -->
      <h3>Beacon Voice Announcements</h3>
      <div class="actual"><?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["VOICE="]==""){echo _DEFAULT_STS;}elseif($_SESSION["opt_base"]["VOICE="]=="1"){echo _ENABLED;}else{echo _DISABLED;}?></span></div>
      <select name="voice" >
        <option value="default" <?php if($_SESSION["opt_base"]["VOICE="]==""){echo "selected";}?>><?php echo _DEFAULT?></option>
        <option value="enable" <?php if($_SESSION["opt_base"]["VOICE="]=="1"){echo "selected";}?>><?php echo _ENABLE?></option>
        <option value="disable" <?php if($_SESSION["opt_base"]["VOICE="]=="0"){echo "selected";}?>><?php echo _DISABLE?></option>
      </select>
      <!-- Lang -->
      <h3>Server Voice Language</h3>
      <div class="actual"><?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["LANG="]==""){echo _DEFAULT_STS;}else{echo $_SESSION["opt_base"]["LANG="];}?></span></div>
      <select name="lang" >
        <option value="" <?php if($_SESSION["opt_base"]["LANG="]==""){echo "selected";}?>><?php echo _DEFAULT?></option>
        <option value="cy_GB" <?php if($_SESSION["opt_base"]["LANG="]=="cy_GB"){echo "selected";}?>><?php echo "cy_GB"?></option>
        <option value="de_DE" <?php if($_SESSION["opt_base"]["LANG="]=="de_DE"){echo "selected";}?>><?php echo "de_DE"?></option>
        <option value="el_GR" <?php if($_SESSION["opt_base"]["LANG="]=="el_GR"){echo "selected";}?>><?php echo "el_GR"?></option>
        <option value="en_GB" <?php if($_SESSION["opt_base"]["LANG="]=="en_GB"){echo "selected";}?>><?php echo "en_GB"?></option>
        <option value="en_GB_2" <?php if($_SESSION["opt_base"]["LANG="]=="en_GB_2"){echo "selected";}?>><?php echo "en_GB_2"?></option>
        <option value="es_ES" <?php if($_SESSION["opt_base"]["LANG="]=="es_ES"){echo "selected";}?>><?php echo "es_ES"?></option>
        <option value="es_ES_2" <?php if($_SESSION["opt_base"]["LANG="]=="es_ES_2"){echo "selected";}?>><?php echo "es_ES_2"?></option>
        <option value="fr_FR" <?php if($_SESSION["opt_base"]["LANG="]=="fr_FR"){echo "selected";}?>><?php echo "fr_FR"?></option>
        <option value="pt_PT" <?php if($_SESSION["opt_base"]["LANG="]=="pt_PT"){echo "selected";}?>><?php echo "pt_PT"?></option>
        <option value="th_TH" <?php if($_SESSION["opt_base"]["LANG="]=="th_TH"){echo "selected";}?>><?php echo "th_TH"?></option>
        <option value="CW" <?php if($_SESSION["opt_base"]["LANG="]=="CW"){echo "selected";}?>><?php echo "CW"?></option>
      </select>
      <!-- Dial -->
      <h3>Dial: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?php echo _DIALINFO?></span></span></h3>
      <div class="actual"> <?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["DIAL="]===""){echo _DEFAULT_STS;} else{echo $_SESSION["opt_base"]["DIAL="];}?></span></div>
      <input type="text" name="dial" pattern="[0-9\s]+" value="<?php if($_SESSION["opt_base"]["DIAL="] === ""){echo "";} else{echo $_SESSION["opt_base"]["DIAL="];}?>" title="<?php echo _TIMER_PATT?>">
      <p class="error"><?php echo $dialErr?></p>

      <div class="<?php echo $class?>"><b><?php echo $status?></b></div>
      <input class="form-button" type="submit" value="<?php echo _SUBMIT?>">
    </form>

  </fieldset>
  <footer>
    <p>
      Copyright (c) 2016-<?php echo date("Y");?><br>
      The Regents of the <a target="_blank" href=http://k0usy.mystrikingly.com >K0USY Group</a>. All rights reserved.<br>
      <a title="FDMR Monitor OA4DOA v1.0.0" target="_blank" href=https://github.com/yuvelq/FDMR-Monitor.git>Version OA4DOA</a>
      <!-- THIS COPYRIGHT NOTICE MUST BE DISPLAYED AS A CONDITION OF THE LICENCE GRANT FOR THIS SOFTWARE. ALL DERIVATEIVES WORKS MUST CARRY THIS NOTICE -->
    </p>
  </footer>
</body>
</html>
