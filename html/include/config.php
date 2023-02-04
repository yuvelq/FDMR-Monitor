<?php
// Version 1.0.0
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

// Parse config file allowing special characters
function conf_parser($conf_file) {
  if (file_exists($conf_file)) {
    $file = new SplFileObject($conf_file);
    $conf = array();
    $stanza = "DEFAULT";
    while (!$file -> eof()) {
      $line = trim($file -> fgets());
      $first = substr($line, 0, 1);
      $last = substr($line, -1);
      if (strlen($line) <= 2 or $first == "#" or $first == ";") {
        continue;
      } 
      if ($first == '[' and $last == ']') {
        $stanza = substr($line, 1, -1);
      } else {
        $line_exp = explode('=', $line);
        $key = trim($line_exp[0]);
        $value = trim($line_exp[1]);
        if (in_array(strtolower($value), array("yes", "true", "on", "1"))) {
          $value = true;
        } elseif (in_array(strtolower($value), array("no", "false", "off", "0"))) {
          $value = false;
        }
        $conf[$stanza][$key] = $value;
      }
    }
    return $conf;
  } else {
    return array();
  }
}

$path2config = "/opt/FDMR-Monitor/fdmr-mon.cfg";
// Parse config file
$config = conf_parser($path2config);

// Define TGCOUNT_INC
if ($config and array_key_exists("TGCOUNT_INC", $config["GLOBAL"])) {
  $tgc = $config["GLOBAL"]["TGCOUNT_INC"];
  if ($tgc == "1") {
    $tgc = true;
  } else {
    $tgc = false;
  }
  define("TGCOUNT_INC", $tgc);
} else {
  define("TGCOUNT_INC", true);
}

// Define REPORT_NAME
if ($config and array_key_exists("REPORT_NAME", $config["GLOBAL"])) {
  define("REPORT_NAME", $config["GLOBAL"]["REPORT_NAME"]);
} else {
  define("REPORT_NAME", "Dashboard of local DMR Network");
}

// Define HEIGHT_ACTIVITY
if ($config and array_key_exists("HEIGHT_ACTIVITY", $config["GLOBAL"])) {
  $height = $config["GLOBAL"]["HEIGHT_ACTIVITY"];
  if (!strstr($height, "px")) {
    $height = $config["GLOBAL"]["HEIGHT_ACTIVITY"]."px";
  }
  define("HEIGHT_ACTIVITY", $height);
} else {
  define("HEIGHT_ACTIVITY", "45px");
}

// Define THEME_COLOR
if ($config and array_key_exists("THEME_COLOR", $config["GLOBAL"])) {
  $theme = strtolower($config["GLOBAL"]["THEME_COLOR"]);
  $tc = null;
  if ($theme == "green") {
    $tc = "background-color:#4a8f3c;color:white;";
  } elseif ($theme == "blue1") {
    $tc = "background-color:#2A659A;color:white;";
  } elseif ($theme == "blue2") {
    $tc = "background-color:#43A6DF;color:white;";
  } elseif ($theme == "bluegradient1") {
    $tc = "background-image: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);color:white;";
  } elseif ($theme == "bluegradient2") {
    $tc = "background-image: linear-gradient(to bottom, #3333cc 0%, #265a88 100%);color:white;";
  } elseif ($theme == "redgradient") {
    $tc = "background-image:linear-gradient(0deg, rgba(251,0,0,1) 0%, rgba(255,131,131,1) 50%, rgba(255,255,255,1) 100%);color:black;";
  } elseif ($theme == "greygradient") {
    $tc = "background-image: linear-gradient(to bottom, #3b3b3b 10%, #808080 100%);color:white;";
  } elseif ($theme == "greengradient") {
    $tc = "background-image:linear-gradient(to bottom right,#d0e98d, #4e6b00);color:black;";
  }
  if ($tc) {
    define("THEME_COLOR", $tc);
  } else {
    // Define THEME_COLOR fallback value
    define("THEME_COLOR","background-image: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);color:white;");
  }
} else {
   define("THEME_COLOR","background-image: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);color:white;");
}
