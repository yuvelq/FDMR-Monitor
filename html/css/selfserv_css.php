<?php
header("Content-type: text/css");
include_once '../include/config.php';
?>

fieldset.selfserv {
  font-size: 11pt;
  width: 800px;
  height: auto;
  margin: 10px auto;
  text-align: center;
  background-color: #e6e6e6;
}
.selfserv h3 {
  margin-bottom: 6px;
  color: #193d67;
}

/* actual selection */
.selfserv .actual {
border: 0.5px solid grey;
border-radius: 8px;
width: max-content;
padding: 7px 9px;
margin: 6px auto;
background-color: #d7e6f4;
}
.selfserv .error {
  color: red;
  font-weight: bolder;
}
.selfserv .actl-item {
  color: navy;
}
/* Form button */
.selfserv .opt-ttl {
  text-align: center; 
  text-decoration: underline; 
  font-weight: bold;
  font-size: 1.4em;
}
.selfserv .form-button {
  <?php echo THEME_COLOR."\n"?>
  background-image: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);
  border: none;
  padding: 6px 13px;
  text-align: center;
  font-size: 11pt;
  margin: 10px 3px;
  border-radius: 8px;
  box-shadow: 0px 8px 10px rgba(0, 0, 0, 0.1);
}
.form-button:hover {
  background-color: rgb(140, 140, 140);
  background: rgb(140, 140, 140);
  color: white;
}

/* Language */
.selfserv .lang {
  margin-left:640px;
}

/* Show data in Self Service page */
.selfserv .show-data {
  line-height: 1.3em;
  margin: 15px auto;
  padding: 20px 30px;
  background: #f2f2f2;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2),
  0 6px 20px 0 rgba(0, 0, 0, 0.19);
  width: fit-content;
}

/* Tooltip for Self Service */
.tooltip {
  position: relative;
  display: inline-block;
  opacity: 1;
}
.tooltip .tooltiptext {
  visibility: hidden;
  width: 280px;
  background-color: #6E6E6E;
  box-shadow: 4px 4px 6px #3b3b3b;
  color: #FFFFFF;
  text-align: left;
  font-size: 10pt;
  border-radius: 6px;
  padding: 8px 15px;
  left: 100%;
  opacity: 1;
  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}
.tooltip img {
  vertical-align: middle;
}

/* login page */
.selfserv.login {
  height: 400px;
  position: relative;
}
.selfserv .login-gen {
  background: #f2f2f2;
  text-align: left;
  padding: 25px 60px;
  border-radius: 10px;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2),
  0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
.login-gen .login-item {
  line-height: 1.6em;
  margin-top: 10px;
  margin-bottom: 10px;
  margin-left: 2px;
  font-weight: bold;
}

/* Single */
.status-succes {
  color: #009900;
  margin-top: 10px;
}
.status-error {
  color: red;
  margin-top: 10px;
}