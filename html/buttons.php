<!-- HBMonitor buttons HTML code Version 1.0.0 -->
<a class="button" href="index.php">Home</a>

<a class="button" href="linkedsys.php">Linked Systems</a>

<a class="button" href="statictg.php">Static TG</a>

<a class="button" href="opb.php">OpenBridge</a>

<?php if(TGCOUNT_INC){echo '<a class="button" href="tgcount.php">TG Count</a>';}?>

<div class="dropdown">
  <button class="dropbtn">Self Service</button>
  <div class="dropdown-content">
    <?php if(!PRIVATE_NETWORK){echo '<a href="selfservice.php">SelfService</a>';}?>
    <a href="login.php">Login</a>
    <?php 
    if(isset($_SESSION["auth"], $_SESSION["callsign"], $_SESSION["h_psswd"]) and $_SESSION["auth"]){
      echo '<a href="devices.php">Devices</a>';
    }
    ?>
  </div>
</div>

<div class="dropdown">
  <button class="dropbtn">Server Stats</button>
  <div class="dropdown-content">
    <a href="moni.php">Monitor</a>
    <a href="sysinfo.php">System Info</a>
    <a href="log.php">Lastheard</a>
  </div>
</div>

<a class="button" href="info.php">Info</a>

<!--
<a class="button" href="bridges.php">Bridges</a>
-->

<!-- Example of buttons dropdown HTML code -->
<!--
<div class="dropdown">
  <button class="dropbtn">Admin Area</button>
  <div class="dropdown-content">
    <a href="masters.php">Master&Peer</a>
    <a href="opb.php">OpenBridge</a>
    <a href="moni.php">Monitor</a>
  </div>
</div>

<div class="dropdown">
  <button class="dropbtn">Reflectors</button>
  <div class="dropdown-content">
    <a target='_blank' href="#">YSF Reflector</a>
    <a target='_blank' href="#">XLX950</a>
  </div>
</div>
-->
