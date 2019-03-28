<!DOCTYPE html>
<html>
<head><title>UpdateTable</title></head>
<body>
	<?php require './functions.php'; ?>
	<?php
		$posted = ($_SERVER['REQUEST_METHOD'] === 'POST') ? true : false;
		If($posted) {
			$table = $_POST['intable'];
			$id = $_POST['id'];
			$name = $_POST['name'];
			$bonusvalue = $_POST['bonusvalue'];
			$tostat = $_POST['statname'];
			$bonustype = $_POST['bonustype'];
			$shortdesc = $_POST['shortdesc'];
			$longdesc = $_POST['longdesc'];
			$stacks = $_POST['stacks'];
			$url = $_POST['url'];
			$q = "INSERT INTO ".$table."(id,`name`,bonusvalue,tostat,bonustype,shortdesc,longdesc,stacks,url) VALUES (".$id.",'".$name."',".$bonusvalue.",".$tostat.",".$bonustype.",'".$shortdesc."','".$longdesc."',".$stacks.",'".$url."')";
			$result = runQuery($q);
			Header('Location: .\intable.php?t='.$_POST['intablenum']);
		} // End If
	?>
</body>
</html>