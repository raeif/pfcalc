<!DOCTYPE HTML>
<html>
	<head>
		<title>Pathfinder Calculator | Display Character</title>
		<link rel="stylesheet" type="text/css" href="./style.css">
	</head>
	
	<body>
		<?php
			require './functions.php';
			require './header.php';
		?>

		<form name="character" method="get" action="" >
			<div class="body-content">
				<?php If (isset($_GET['selectCharacter'])) { If ($_GET['selectCharacter'] != 0) { displayStats($_GET['selectCharacter']); } } ?>
			</div>
		</form>
	</body>
</html>