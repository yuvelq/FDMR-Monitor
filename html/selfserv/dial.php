<h3>Dial: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?php echo _DIALINFO?></span></span></h3>
<div class="actual"> <?php echo _ACTUAL_SELECTION?><span class="actl-item"><?php echo $_SESSION["opt_base"]["DIAL="]."<br>"; ?></span></div>
<input type="text" name="dial" pattern="[0-9,\s]+" title="<?php echo _TSERR_ONLY?>">

