<!DOCTYPE HTML>
<html>
	<head>
		<title>Pathfinder Calculator | Add New Character</title>
		<link rel="stylesheet" type="text/css" href="./style.css">
	</head>
	<body>
		<?php
			require './functions.php';
			require './header.php';
		?>

		<div class="body-content">
		<form name="addcharacter" method="post" action="">
			<?php
				# Check if POST, assign $char array and check if valid
				$valid = true;
				$posted = ($_SERVER['REQUEST_METHOD'] === 'POST') ? true : false;
				$numchars = 0;
				$pcname = "";
				$badname = false;
				$badstr = false;
				$baddex = false;
				$badcon = false;
				$badint = false;
				$badwis = false;
				$badcha = false;
				$pcstr = 10;
				$pcdex = 10;
				$pccon = 10;
				$pcint = 10;
				$pcwis = 10;
				$pccha = 10;
				If ($posted) {
					$char = array(
						'name' => $_POST['pcname'],
						'race' => $_POST['pcrace'],
						'alignment' => $_POST['pcalignment'],
						'class' => $_POST['pcclass'],
						'classlevel' => $_POST['pclevel'],
						'str' => $_POST['pcstr'],
						'dex' => $_POST['pcdex'],
						'con' => $_POST['pccon'],
						'int' => $_POST['pcint'],
						'wis' => $_POST['pcwis'],
						'cha' => $_POST['pccha']);
					switch ($char['race']) {
						case 1:
							$char['size'] = 5;
							break;
						case 2:
							$char['size'] = 5;
							break;
						case 3:
							$char['size'] = 5;
							break;
						case 4:
							$char['size'] = 4;
							break;
						case 5:
							$char['size'] = 5;
							break;
						case 6:
							$char['size'] = 5;
							break;
						case 7:
							$char['size'] = 4;
							break;
						case 8:
							$char['size'] = 5;
							break;
					} // End Switch
					#var_dump($char);
					$result = runQuery("SELECT name FROM v_character WHERE name = '".$char['name']."'");
					$numchars = count(convertToArray($result));
					$badname = ((strlen($char['name']) == 0) || (strlen($char['name']) > 100) || ($numchars == 1)) ? true : false;
					$badstr = (($_POST['pcstr'] < 3) || ($_POST['pcstr'] > 30)) ? true : false;
					$baddex = (($_POST['pcdex'] < 3) || ($_POST['pcdex'] > 30)) ? true : false;
					$badcon = (($_POST['pccon'] < 3) || ($_POST['pcstr'] > 30)) ? true : false;
					$badint = (($_POST['pcint'] < 3) || ($_POST['pcint'] > 30)) ? true : false;
					$badwis = (($_POST['pcwis'] < 3) || ($_POST['pcwis'] > 30)) ? true : false;
					$badcha = (($_POST['pccha'] < 3) || ($_POST['pccha'] > 30)) ? true : false;
					$pcname = $_POST['pcname'];
					$pcstr = $_POST['pcstr'];
					$pcdex = $_POST['pcdex'];
					$pccon = $_POST['pccon'];
					$pcint = $_POST['pcint'];
					$pcwis = $_POST['pcwis'];
					$pccha = $_POST['pccha'];
					$valid = ($badname || $badstr || $baddex || $badcon || $badint || $badwis || $badcha) ? false : true;
					if ($valid) {
						$q1 = "INSERT INTO d_pc_charstats (`name`,race,`size`,alignment,str,dex,con,`int`,wis,cha) VALUES ('".$char['name']."',".$char['race'].",".$char['size'].",".$char['alignment'].",".$char['str'].",".$char['dex'].",".$char['con'].",".$char['int'].",".$char['wis'].",".$char['cha'].")";
						$result = runQuery($q1);
						$q2 = "SELECT pcid FROM d_pc_charstats WHERE name = '".$char['name']."'";
						If ($result) { $newpcid = convertToArray($result2 = runQuery($q2))['pcid']; }
						$q3 = "INSERT INTO d_pc_classes (pcid,classid,classlevel) VALUES (".$newpcid.",".$char['class'].",".$char['classlevel'].")";
						$result = runQuery($q3);
						Header('Location: .\character.php?selectCharacter='.$newpcid);
					} // End If
				} // End If
			?>

			<?php
				# If not valid, display a warning message
				If (!($valid)) { echo '<h3 style="color:red;font-weight:bold">Please check all fields</h3>'; }
			?>
			
			<div class="leftbloc">Character Name:</div><div class="rightbloc"><input style="width:150" type="text" value="<?php echo $pcname; ?>" name="pcname">
				<?php
					If ($numchars == 1) { echo '<div class="error">Character Name already exists!</div>'; }
					If (($badname) && ($numchars == 0)) { echo '<div class="error">Character Name must be between 1 and 100 characters</div>'; }
				?></div>
			
			<div class="leftbloc">Race:</div><div class="rightbloc"><select name="pcrace">
			<?php 
				$allRaces = convertToArray($result = runQuery("SELECT RaceID,RaceName FROM r_RaceRef"));
				foreach ($allRaces as $race) {
					echo '<option value="'.$race['RaceID'].'"';
					echo ($race['RaceID'] == 1) ? ' selected>'.$race['RaceName'].'</option>' : '>'.$race['RaceName'].'</option>';
				} // End Foreach
			?></select></div>
			
			<div class="leftbloc">Alignment:</div><div class="rightbloc"><select name="pcalignment">
			<?php
				$allAlignments = convertToArray($result = runQuery("SELECT AlignmentID,AlignmentDesc FROM r_AlignmentRef"));
				foreach ($allAlignments as $alignments) {
					echo '<option value="'.$alignments['AlignmentID'].'"';
					echo ($alignments['AlignmentID'] == 1) ? ' selected>'.$alignments['AlignmentDesc'].'</option>' : '>'.$alignments['AlignmentDesc'].'</option>';;
				} // End Foreach
			?></select></div>
			
			<div class="leftbloc">Primary Class:</div><div class="rightbloc"><select name="pcclass">
			<?php
					$allClasses = convertToArray($result = runQuery("SELECT ClassID,ClassName FROM r_ClassRef"));
					foreach ($allClasses as $class) {
						echo '<option value="'.$class['ClassID'].'"';
						echo ($class['ClassID'] == 5) ? ' selected>'.$class['ClassName'].'</option>' : '>'.$class['ClassName'].'</option>';
					} // End Foreach
			?></select>
			<div style="display:inline;padding-left:5px;height:30px;line-height:30px;text-align:left"><select name="pclevel"><option value="1" selected>1</option>
			<?php for($i=2;$i<=20;$i++) { echo '<option value='.$i.'>'.$i.'</option>'; } ?></select></div></div>
			
			<div class="leftbloc"><div class="abilitynamebloc" style="float:right">STR</div></div><div class="rightbloc"><input class="abilityscorebloc" style="float:left" type="text" value="<?php echo $pcstr ?>" name="pcstr">
			<?php If ($badstr) { echo '<div class="error">Value must be between 3 and 18</div>'; } ?></div>
			<div class="leftbloc"><div class="abilitynamebloc" style="float:right">DEX</div></div><div class="rightbloc"><input class="abilityscorebloc" style="float:left" type="text" value="<?php echo $pcdex ?>" name="pcdex">
			<?php If ($baddex) { echo '<div class="error">Value must be between 3 and 18</div>'; } ?></div>
			<div class="leftbloc"><div class="abilitynamebloc" style="float:right">CON</div></div><div class="rightbloc"><input class="abilityscorebloc" style="float:left" type="text" value="<?php echo $pccon ?>" name="pccon">
			<?php If ($badcon) { echo '<div class="error">Value must be between 3 and 18</div>'; } ?></div>
			<div class="leftbloc"><div class="abilitynamebloc" style="float:right">INT</div></div><div class="rightbloc"><input class="abilityscorebloc" style="float:left" type="text" value="<?php echo $pcint ?>" name="pcint">
			<?php If ($badint) { echo '<div class="error">Value must be between 3 and 18</div>'; } ?></div>
			<div class="leftbloc"><div class="abilitynamebloc" style="float:right">WIS</div></div><div class="rightbloc"><input class="abilityscorebloc" style="float:left" type="text" value="<?php echo $pcwis ?>" name="pcwis">
			<?php If ($badwis) { echo '<div class="error">Value must be between 3 and 18</div>'; } ?></div>
			<div class="leftbloc"><div class="abilitynamebloc" style="float:right">CHA</div></div><div class="rightbloc"><input class="abilityscorebloc" style="float:left" type="text" value="<?php echo $pccha ?>" name="pccha">
			<?php If ($badcha) { echo '<div class="error">Value must be between 3 and 18</div>'; } ?></div>
			<div style="margin:auto;height:30px;line-height:30px"><input type="submit" value="Add Character to Database"></div>
		</form>
	</body>
</html>