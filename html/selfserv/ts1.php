<h3>TS 1 Static TGs: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?php echo _STATIC_TGINFO?></span></span></h3>
<div class="actual"><?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["TS1="]){echo implode(", ", $_SESSION["opt_base"]["TS1="])."<br>";} ?></span></div>
<input type="text" name="ts1" pattern="[0-9,\s]+" title="<?php echo _TSERR_ONLY?>">
<div class="error"><?php echo $ts1Err?></div>
<input type="radio" name="aod_ts1" value="add" checked="checked"><?php echo _ADD?>
<input type="radio" name="aod_ts1" value="delete"><?php echo _DELETE?>
