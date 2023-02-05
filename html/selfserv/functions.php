<?php
// Version 1.0.0
/* Attempt to connect to MySQL database */
function check_db() {
  try {
    global $db_conn;
    $db_conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
  } catch (Exception $e) {
    exit("ERROR: Could not connect.<br>".$e -> getMessage());
  }
}

// Sanitize data
function check_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Parse config file that allows special characters
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
