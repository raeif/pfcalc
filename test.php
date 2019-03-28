<!DOCTYPE HTML>
<html>
	<head>
		<title>Test Page</title>
		<!-- <link rel="stylesheet" type="text/css" href="./test.css"> -->
		<link rel="stylesheet" type="text/css" href="./style.css">
	</head>
	<body>
		<?php
			require './functions.php';
			require './header.php';
		?>
		<div class="body-content">
			<div class="center"><h1>Test</h1></div>
			<?php br(); ?>

			<form name="dostuff" method="post" action="">
			<div class="leftbloc">BAB:</div><div class="rightbloc"><input style="width:25" type="text" name="bab" autofocus></div>
			<div class="float_center"><input type="submit" class="child" value="Submit"></div>
			<?php
				If($_SERVER['REQUEST_METHOD'] === 'POST') {
					$BAB = $_POST['bab'];
					$attacks = "";
					$numattacks = ($BAB % 5 > 0) ? ceil($BAB/5) : floor($BAB/5);
					for($i=1;$i<=$numattacks;$i++) {
						$attacks .= ($i==1) ? asBonus($BAB) : ' / '.(asBonus($BAB-(($i-1)*5)));
					} // End For
					echo '<div class="float_center"><span class="child">'.$attacks.'</span></div>';
				} // End If
			?>
			</form>

<!-- Standard Test page Code
			<form name="getcharacter" method="post" action="">
			<?php /* $all = getAllCharacters(); ?>
			<div class="center">Character: <select name="pcselect">
			<?php
				foreach ($all as $character) {
					echo ($_POST['pcselect'] == $character['pcid']) ? '<option value="'.$character['pcid'].'" selected>'.$character['name'].'</option>' : '<option value="'.$character['pcid'].'">'.$character['name'].'</option>'; }
				echo '</select>';
				if ($_SERVER['REQUEST_METHOD'] === 'POST') { $char = getCharacter($_POST['pcselect']); }
			?>
			<div class="center" style="height:40px;line-height:40px"><input type="submit" value="Load Character"></div>
			<?php br(); ?>

			<div><div class="abilitynamebloc" style="display:inline-block;margin:1px">STR</div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo $char['str']; ?></div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo asBonus(getStatMod($char['str'])); ?></div></div>
			<div><div class="abilitynamebloc" style="display:inline-block;margin:1px">DEX</div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo $char['dex']; ?></div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo asBonus(getStatMod($char['dex'])); ?></div></div>
			<div><div class="abilitynamebloc" style="display:inline-block;margin:1px">CON</div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo $char['con']; ?></div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo asBonus(getStatMod($char['con'])); ?></div></div>
			<div><div class="abilitynamebloc" style="display:inline-block;margin:1px">INT</div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo $char['int']; ?></div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo asBonus(getStatMod($char['int'])); ?></div></div>
			<div><div class="abilitynamebloc" style="display:inline-block;margin:1px">WIS</div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo $char['wis']; ?></div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo asBonus(getStatMod($char['wis'])); ?></div></div>
			<div><div class="abilitynamebloc" style="display:inline-block;margin:1px">CHA</div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo $char['cha']; ?></div><div class="abilityscorebloc" style="display:inline-block;margin:1px"><?php echo asBonus(getStatMod($char['cha'])); ?></div></div>
			</form>
			<?php */ ?>
<!-- Standard Test page Code -->

<!--			<form name="updatedb" method="post" action="">
				<div style="font-style:verdana,sans-serif;font-size:14px">
				<?php /*
					$tables = array('l_classabilities','l_equipment','l_feats','l_magicitems','l_racialabilities','l_spells');
					$thistable = -1;
					$nextid = 0;
					If ($_SERVER['REQUEST_METHOD'] === 'POST') {
						$thistable = $_POST['intable'];
						$nextid = convertToArray($r = runQuery('SELECT MAX(id) as id FROM '.$tables[$thistable]))['id'] + 1;
						$e = false;
						$e = $nextid;
						echo ($e ? '<div class="center" style="color:red;font-size:24px;font-weight:bold;heigtht:60px;line-height:60px">'.$e.'</div>' : "");
					} // End If
				?>
				<div class="leftbloc"><span style="text-align:right">Select Input Table</span></div>
				<div class="rightbloc" style="text-align:left"><select name="intable">
					<option value="0"<?php if($thistable==0) echo ' selected'; ?>>Class Abilities</option>
					<option value="1"<?php if($thistable==1) echo ' selected'; ?>>Mundane Equipment</option>
					<option value="2"<?php if($thistable==2) echo ' selected'; ?>>Feats</option>
					<option value="3"<?php if($thistable==3) echo ' selected'; ?>>Magic Items</option>
					<option value="4"<?php if($thistable==4) echo ' selected'; ?>>Racial Abilities</option>
					<option value="5"<?php if($thistable==5) echo ' selected'; ?>>Spells</option></select></div>
				<div class="center" style="heigtht:60px;line-height:60px"><input type="submit" value="Submit"></div>
				<hr>
				<div class="leftbloc"><span style="text-align:right">Bonus Value:</span></div>
				<div class="rightbloc"><span style="text-align:left"><input type="number" name="id" value="<?php echo ($nextid>0 ? $nextid : ""); ?>" min="1"></span></div>


				</div>
<!--
				<?php
/*					$tablename = "r_BonusToStatsRef";
					$fieldname = "statname";
					If ($_SERVER['REQUEST_METHOD'] === 'POST') {
						$val = $_POST['inputbox'];
						$r = runQuery("INSERT INTO ".$tablename."(".$fieldname.") VALUES ('".$val."')");
					} // End If */
				?>
				<div class="leftbloc"><span style="text-align:right;padding-right:2px">Field Value:</span></div><div class="rightbloc"><span style="text-align:left;padding-left:2px"><input type="text" name="inputbox" autofocus></span></div>
				<div class="center"><input type="submit" value="Submit"></div>
				<?php #br(); ?>
				<table class="center" border=1 cellpadding=1>
				<?php /*
					$result = runQuery("SELECT * FROM ".$tablename);
					$numrows = $result->num_rows;
					$a = convertToArray($result);
					if ($numrows > 1) {
						foreach ($a as $a1) {
							echo '<tr>';
							foreach ($a1 as $a2) { echo '<td>'.$a2.'</td>'; }
							echo '</tr>';
						} // Foreach
					} else {
						echo '<tr>';
						foreach ($a as $a1) { echo '<td>'.$a1.'</td>'; }
						echo '</tr>';
					} // End If Else */
				?>
				</table> ->
			</form> -->
		</div>
	</body>
</html>
