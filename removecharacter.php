<!DOCTYPE HTML>
<html>
	<head>
		<title>Pathfinder Calculator | Remove Character</title>
		<link rel="stylesheet" type="text/css" href="./style.css">
	</head>
	<body>
		<?php
			require './functions.php';
			require './header.php';
		?>
		<div class="body-content">
			<form name="removecharacter" method="post" action="">
				<?php
					$posted = ($_SERVER['REQUEST_METHOD'] === 'POST') ? true : false;
					If ($posted) {
						$pcid = $_POST['pcid'];
						$pcname = convertToArray($result = runQuery("SELECT name FROM v_character WHERE pcid = ".$pcid))['name'];
						$q2 = "DELETE FROM d_pc_charstats WHERE pcid = ".$pcid;
						$q3 = "DELETE FROM d_pc_classes WHERE pcid = ".$pcid;
						$result = runQuery($q2);
						$result = runQuery($q3);
						echo '<div class="center"><h3 style="color:red;font-weight:bold">'.$pcname.' has been removed.</h3></div>';
					} // End If
				?>
				<div class="leftbloc">Select Character to Remove:</div><div class="rightbloc"><select name="pcid"><option value="0" selected></option>
				<?php
					$allCharacters = getAllCharacters();
					foreach ($allCharacters as $char) { echo '<option value="'.$char['pcid'].'">'.$char['name'].'</option>'; }
				?></select></div>
				<div style="margin:auto;height:30px;line-height:30px"><input type="submit" value="Remove Character from Database"></div>
			</form>
		</div>
	</body>
</html>