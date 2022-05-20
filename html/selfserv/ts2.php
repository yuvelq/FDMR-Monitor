<h3>TS 2 Static TGs: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?php echo _STATIC_TGINFO?></span></span></h3>
<div class="actual"> <?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["TS2="]){echo implode(", ", $_SESSION["opt_base"]["TS2="])."<br>";} ?></span></div>
<input type="text" name="ts2" pattern="[0-9,\s]+" title="<?php echo _TSERR_ONLY?>">
<div class="error"><?php echo $ts2Err?></div>
<input type="radio" name="aod_ts2" value="add" checked="checked"><?php echo _ADD?>
<input type="radio" name="aod_ts2" value="delete"><?php echo _DELETE?>
