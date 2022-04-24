<h3>TS 1 Static TGs: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?=_STATIC_TGINFO?></span></span></h3>
<div class="actual"><?=_ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["TS1="]){echo implode(", ", $_SESSION["opt_base"]["TS1="])."<br>";} ?></span></div>
<input type="text" name="ts1" pattern="[0-9,\s]+" title="<?=_TSERR_ONLY?>">
<div class="error"><?=$ts1Err?></div>
<input type="radio" name="aod_ts1" value="add" checked="checked"><?=_ADD?>
<input type="radio" name="aod_ts1" value="delete"><?=_DELETE?>