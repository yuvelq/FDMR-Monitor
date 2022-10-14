<!-- POPUP HTML code -->
<script type="text/javascript">
// Popup window code
function newPopup(url) {
	popupWindow = window.open(
		url,'popUpWindow','height=600,width=750,left=375,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes')
}
</script>

<!-- HBMonitor buttons HTML code -->
<a class="button"  href="./">Home</a>

<div class="dropdown">
  <button class="dropbtn">Connected Users</button>
  <div class="dropdown-content">
	<a class="button" style="color: white" href="linkedsys.php">User Linked Systems</a>
	<a class="button" style="color: white" href="statictg.php">User Static<br>Talk Groups</a>
  </div>
</div>

<div class="dropdown">
  <button class="dropbtn">&nbsp;User Options&nbsp;</button>
  <div class="dropdown-content">
	<a class="button" style="color: white" href="options.php">&nbsp;User Options Calculator&nbsp;</a>
	<a class="button" style="color: white" href="optionse.php"><font color: white>&nbsp;User Options Explained&nbsp;</a>
  </div>
</div>

<div class="dropdown">
  <button class="dropbtn">Talk Groups</button>
  <div class="dropdown-content">
	<?php if(TGCOUNT_INC){echo '<a class="button" style="color: white" href="tgcount.php">Popular<br>Talk Groups</a>';}?>
	<a class="button" style="color: white" href="talkgroups-oz.php">OZ-DMR<br>Talk Groups</a>
	<a class="button" style="color: white" href="talkgroups-fd.php">FreeDMR<br>Talk Groups</a>
  </div>
</div>

<a class="button" style="color: white" href="opb.php">OpenBridge</a>

<a class="button" style="color: white" href="moni.php">Log Monitor</a>

<a class="button" style="color: white" href="log.php">Lastheard</a>

<div class="dropdown">
  <button class="dropbtn">&nbsp;System&nbsp;</button>
  <div class="dropdown-content">
	<a class="button" style="color: white" href="sysinfo.php">System Info</a>
	<a class="button" style="color: white" href="https://www.oz-dmr.uk/network-status/">Network</a>
  </div>
</div>


<div class="dropdown">
  <button class="dropbtn">&nbsp;Links&nbsp;</button>
  <div class="dropdown-content">
	<a class="button" style="color: white" href="https://www.oz-dmr.uk/">&nbsp;OZ-DMR Website&nbsp;</a>
	<a class="button" style="color: white" target='_blank'href="http://xlx.oz-dmr.uk/">&nbsp;OZ-DMR XLX858 Server&nbsp;</a>
	<a class="button" style="color: white" href="JavaScript:newPopup('http://dash.oz-dmr.uk/json');">&nbsp;OZ-DMR<br>JSON files&nbsp;</a>
	<a class="button" style="color: white" target='_blank' href="https://github.com/ContactLists">&nbsp;Radio<br>Contact ID's&nbsp;</a>
	<a class="button" style="color: white" href="server_status.php">&nbsp;FreeDMR Server's Status&nbsp;</a>
  </div>
</div>
<!--
<a class="button" style="color: white" href="bridges.php">Bridges</a>
-->

<!-- Example of buttons dropdown HTML code -->
<!--
<p></p>

<--
<div class="dropdown">
  <button class="dropbtn">Reflectors</button>
  <div class="dropdown-content">
    <a class="button" target='_blank' style="color: white" href="#">YSF Reflector</a>
    <a class="button" target='_blank' style="color: white" href="#">XLX950</a>
  </div>
</div>
-->
<br><br>
<iframe src="https://free.timeanddate.com/clock/i8h7812l/n1325/tluk/tcb0e0e6/pcb0e0e6/tt0/ta1" frameborder="0" width="299" height="19"></iframe>
<br>
