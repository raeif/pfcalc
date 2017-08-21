<!DOCTYPE HTML>
<html>
	<head>
		<title>Pathfinder Calculator | Item Creation</title>
		<link rel="stylesheet" type="text/css" href="./style.css">
	</head>
	<body>
		<?php
			require './functions.php';
			require './header.php';
		?>
		
		<div class="body-content">
		<form name="itemcreation" method="post" action="">
			<?php
				# Check if POST, assign $char array and check if valid
				$valid = true;
				$posted = ($_SERVER['REQUEST_METHOD'] === 'POST') ? true : false;
				$calculated = false;

				$badMarketPrice = false;
				$badArcanium = false;
				$badItemType = false;
				$badWorkArea = false;
				$badCreateAssts = false;
				$badCasterAssts = false;
				
				$vMarketPrice = 0;
				$vArcanium = 0;
				$vItemType = 0;
				$vWorkArea = 0;
				$vCreateAssts = 0;
				$vCasterAssts = 0;
				
				$basecost = $vMarketPrice / 2;
				$vArcaniumMod = $vArcanium*100;
				$vItemTypeMod = 0;
				$vWorkAreaMod = 0;
				$vCreateAsstsMod = 0;
				$vCasterAsstsMod = 0;
				$timeMod = 1;
				
				If ($posted) {
					$vMarketPrice = intval($_POST['iMarketPrice']);
					$badMarketPrice = ($vMarketPrice <= 0) ? true : false;
					$vArcanium = intval($_POST['iArcanium']);
					$badArcanium = ($vArcanium < 0) ? true : false;
					$vItemType = intval($_POST['iItemType']);
					$badItemType = ($vItemType == 0) ? true : false;
					$vWorkArea = intval($_POST['iWorkArea']);
					$badWorkArea = ($vWorkArea == 0) ? true : false;
					$vCreateAssts = intval($_POST['iCreateAssts']);
					$badCreateAssts = (($vCreateAssts < 0) || ($vCreateAssts > 5)) ? true : false;
					$vCasterAssts = intval($_POST['iCasterAssts']);
					$badCasterAssts = (($vCasterAssts < 0) || ($vCasterAssts > 5)) ? true : false;
					
					$valid = ($badMarketPrice || $badArcanium || $badItemType || $badWorkArea || $badCreateAssts || $badCasterAssts) ? false : true;
					if ($valid) {
						$basecost = $vMarketPrice / 2;
						$vArcaniumMod = $vArcanium*100;
						switch ($vItemType) {
							case 0:
								$vItemTypeMod = 0;
							case 1: #Scroll
								$vItemTypeMod = 0;
								break;
							case 2: #Potion
								$vItemTypeMod = 0;
								break;
							case 3: #Wand
								$vItemTypeMod = -0.25;
								break;
							case 4: #Ring
								$vItemTypeMod = -0.25;
								break;
							case 5: #Weapon
								$vItemTypeMod = -0.25;
								break;
							case 6: #Armor
								$vItemTypeMod = -0.25;
								break;
							case 7: #Rod
								$vItemTypeMod = 0;
								break;
							case 8: #Staff
								$vItemTypeMod = 0;
								break;
							case 9: #Wondrous
								$vItemTypeMod = -0.25;
								break;
						} // End Switch
						switch ($vWorkArea) {
							case 0:
								$vWorkAreaMod = 0;
							case 1: #Traveling
								$vWorkAreaMod = 0.5;
								break;
							case 2: #Stationary
								$vWorkAreaMod = 0;
								break;
							case 3: #Lab
								$vWorkAreaMod = -0.25;
								break;
							case 4: #Masterwork Lab
								$vWorkAreaMod = -0.50;
								break;
						} // End Switch
						$vCreateAsstsMod = $vCreateAssts*-0.1;
						$vCasterAsstsMod = $vCasterAssts*-0.1;

						$eCost = $basecost - $vArcaniumMod;
						If ($eCost < 0) { $eCost = 0; }

						$timeMod = 1 + $vItemTypeMod + $vWorkAreaMod + $vCreateAsstsMod + $vCasterAsstsMod;
						#echo '1 + '.$vItemTypeMod.' + '.$vWorkAreaMod.' + '.$vCreateAsstsMod.' + '.$vCasterAsstsMod.' = '.$timeMod;
						$basetime = intval(ceil($vMarketPrice / 100));
						$eTime = $basetime*$timeMod;
						$eTime = ($eTime < 1) ? 1 : ceil($eTime);
						$calculated = true;
					 } // End If
				} // End If
			?>

			<?php
				# If not valid, display a warning message
				If (!($valid)) { echo '<h3 style="color:red;font-weight:bold">Please check all fields</h3>'; }
			?>
			
			<!-- Market Price -->
			<div class="leftbloc">Market Price:</div><div class="rightbloc"><input style="width:100px" type="text" value="<?php echo $vMarketPrice; ?>" name="iMarketPrice">
			<?php If ($badMarketPrice) { echo '<div class="error">Invalid Market Price!</div>'; } ?></div>
			
			<!-- Arcanium -->
			<div class="leftbloc">Arcanium:</div><div class="rightbloc"><input style="width:30px" type="text" value="<?php echo $vArcanium; ?>" name="iArcanium">
			<?php If ($badArcanium) { echo '<div class="error">Invalid value for Arcanium!</div>'; } ?>
			</select></div>
			
			<!-- Item Type -->
			<div class="leftbloc">Item Type:</div><div class="rightbloc">
			<select name="iItemType">
				<option value="0" <?php If($vItemType == 0) { echo 'selected';} ?>></option>
				<option value="1" <?php If($vItemType == 1) { echo 'selected';} ?>>Scroll</option>
				<option value="2" <?php If($vItemType == 2) { echo 'selected';} ?>>Potion</option>
				<option value="3" <?php If($vItemType == 3) { echo 'selected';} ?>>Wand</option>
				<option value="4" <?php If($vItemType == 4) { echo 'selected';} ?>>Ring</option>
				<option value="5" <?php If($vItemType == 5) { echo 'selected';} ?>>Weapon</option>
				<option value="6" <?php If($vItemType == 6) { echo 'selected';} ?>>Armor</option>
				<option value="7" <?php If($vItemType == 7) { echo 'selected';} ?>>Rod</option>
				<option value="8" <?php If($vItemType == 8) { echo 'selected';} ?>>Staff</option>
				<option value="9" <?php If($vItemType == 9) { echo 'selected';} ?>>Wondrous</option>
			</select>
			<?php If ($badItemType) { echo '<div class="error">An Item Type must be selected!</div>'; } ?>
			</div>
			
			<div class="leftbloc">Work Area:</div><div class="rightbloc">
			<select name="iWorkArea">
				<option value="0" <?php If($vWorkArea == 0) { echo 'selected';} ?>></option>
				<option value="1" <?php If($vWorkArea == 1) { echo 'selected';} ?>>Traveling</option>
				<option value="2" <?php If($vWorkArea == 2) { echo 'selected';} ?>>Stationary</option>
				<option value="3" <?php If($vWorkArea == 3) { echo 'selected';} ?>>Lab</option>
				<option value="4" <?php If($vWorkArea == 4) { echo 'selected';} ?>>Masterwork Lab</option>
			</select>
			<?php If ($badWorkArea) { echo '<div class="error">A Work Area must be selected!</div>'; } ?>
			</div>
			
			<div class="leftbloc">Item Creation Assistants:</div><div class="rightbloc"><input style="width:30px" type="text" value="<?php echo $vCreateAssts; ?>" name="iCreateAssts">
			<?php If ($badCreateAssts) { echo '<div class="error">Value must be between 0 and 5</div>'; } ?>
			</div>
			
			<div class="leftbloc">Spell Caster Assistants:</div><div class="rightbloc"><input style="width:30px" type="text" value="<?php echo $vCasterAssts; ?>" name="iCasterAssts">
			<?php If ($badCasterAssts) { echo '<div class="error">Value must be between 0 and 5</div>'; } ?>
			</div>
			
			<div class="leftbloc"><span style="color:#08088A;font-size:12px">Final Time Mod:</span></div><div class="rightbloc"><span style="color:#08088A;font-size:12px"><?php echo (round((1-$timeMod)*100)).'%'; ?></span></div>
			
			<div style="margin:auto;height:30px;line-height:30px"><input type="submit" value="Calculate Effort"></div>
			<?php
				If ($calculated && $posted && $valid ) {
					$hr = ($eTime == 1) ? 'hour' : 'hours';
					$numdays = ($eTime > 8) ? intval($eTime/8) : 0;
					$dy = ($numdays == 1) ? 'day' : 'days';
					$numhours = ($eTime > 8) ? intval($eTime%8) : $eTime;
					echo '<div class="leftbloc"></div><div class="rightbloc"></div>';
					echo '<div class="leftbloc"><span style="font-weight:bold;font-size:20px">Cost to Create:</span></div><div class="rightbloc">'.$eCost.'gp</div>';
					echo '<div class="leftbloc"><span style="font-weight:bold;font-size:20px">Time to Create:</span></div><div class="rightbloc">'.$eTime.' '.$hr.' ('.$numdays.' '.$dy.' '.$numhours.' '.$hr.')</div>';
				}
			?>
		</form>
	</body>
</html>