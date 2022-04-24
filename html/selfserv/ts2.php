<h3>TS 2 Static TGs: <span class="tooltip"><img src="img/info.png" alt=""><span class="tooltiptext"><?=_STATIC_TGINFO?></span></span></h3>
<div class="actual"> <?=_ACTUAL_SELECTION?><span class="actl-item"><?php if($_SESSION["opt_base"]["TS2="]){echo implode(", ", $_SESSION["opt_base"]["TS2="])."<br>";} ?></span></div>
<input type="text" name="ts2" pattern="[0-9,\s]+" title="<?=_TSERR_ONLY?>">
<div class="error"><?=$ts2Err?></div>
<input type="radio" name="aod_ts2" value="add" checked="checked"><?=_ADD?>
<input type="radio" name="aod_ts2" value="delete"><?=_DELETE?>
