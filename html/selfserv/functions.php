<?php

/* Attempt to connect to MySQL database */
function check_db(){
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