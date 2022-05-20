<?php

$path2file = "C:/Users/chris/Documents/GitHUB/FDMR-Monitor/config_sample.cfg";

// $config = parse_ini_file($path2file, true);
$config = null;

if ($config and array_key_exists("TGCOUNT_INC", $config["GLOBAL"])) {
echo "asi es mi bro";
//  ;
}

// // $test = $config["GLOBAL"]["TGCOUNT_INC"];
// define("TEST", $config["GLOBAL"]["TGCOUNT_INC"]);
// $jeta = array_key_exists("TGCOUNT_INC", $config["GLOBAL"]);
// $jeta = in_array("TGCOUNT_INC", $config["GLOBAL"]);
// var_dump($jeta);
// var_dump($config);
