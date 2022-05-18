<?php

// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

$path2config = "/opt/FDMR-monitor/fdmr-mon.cfg";

if (file_exists($path2config)) {
  $config = parse_ini_file($path2config, true);
} else {
  $config = null;
}

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
  if (!str_ends_with($height, "px")) {
    $height = $config["GLOBAL"]["HEIGHT_ACTIVITY"]."px";
  }
  define("HEIGHT_ACTIVITY", $height);
} else {
  define("HEIGHT_ACTIVITY", "45px");
}

// Define THEME_COLOR
if ($config and array_key_exists("THEME_COLOR", $config["GLOBAL"])) {
  $theme = $config["GLOBAL"]["THEME_COLOR"];
  if ($theme == "Green") {
    define("THEME_COLOR","background-color:#4a8f3c;color:white;");
  } elseif ($theme == "Blue1") {
    define("THEME_COLOR","background-color:#2A659A;color:white;");
  } elseif ($theme == "Blue2") {
    define("THEME_COLOR","background-color:#43A6DF;color:white;");
  } elseif ($theme == "BlueGradient1") {
    define("THEME_COLOR","background-image: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);color:white;");
  } elseif ($theme == "BlueGradient2") {
    define("THEME_COLOR","background-image: linear-gradient(to bottom, #3333cc 0%, #265a88 100%);color:white;");
  } elseif ($theme == "RedGradient") {
    define("THEME_COLOR","background-image:linear-gradient(0deg, rgba(251,0,0,1) 0%, rgba(255,131,131,1) 50%, rgba(255,255,255,1) 100%);color:black;");
  } elseif ($theme == "GreyGradient") {
    define("THEME_COLOR","background-image: linear-gradient(to bottom, #3b3b3b 10%, #808080 100%);color:white;");
  } elseif ($theme == "GreenGradient") {
    define("THEME_COLOR","background-image:linear-gradient(to bottom right,#d0e98d, #4e6b00);color:black;");
  } else {
    // Define THEME_COLOR fallback value
    define("THEME_COLOR","background-image: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);color:white;");
  }
} else {
  define("THEME_COLOR","background-image: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);color:white;");
}
