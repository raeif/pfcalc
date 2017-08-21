<!DOCTYPE HTML>
<html>
	<head>
		<title>InTable</title>
		<!-- <link rel="stylesheet" type="text/css" href="./test.css"> -->
		<link rel="stylesheet" type="text/css" href="./style.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script>
			function showTable(str) {
				if (str == "") {
					document.getElementById("output").innerHTML = "";
					return;
				} else { 
					if (window.XMLHttpRequest) {
						// code for IE7+, Firefox, Chrome, Opera, Safari
						xmlhttp = new XMLHttpRequest();
					} else {
						// code for IE6, IE5
						xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
					}
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							document.getElementById("output").innerHTML = xmlhttp.responseText;
						} // End if
					}; // End function
					xmlhttp.open("GET","showtable.php?q="+str,true);
					xmlhttp.send();
				} // End if else
			} // End function
		</script>
	</head>

	<body>
		<?php
			require './functions.php';
			require './header.php';
		?>
		<div class="body-content">

		<form name="intableform" method="post" action="updatetable.php">
		<div class="leftbloc">Select table:</div><div class="rightbloc">
			<select name="intableselect" onchange="showTable(this.value)">
				<option value=""></option>
				<option value="0"<?php if(isset($_GET['t'])) { if($_GET['t']=="0") { echo " selected"; }} ?>>Class Abilities</option>
				<option value="1"<?php if(isset($_GET['t'])) { if($_GET['t']=="1") { echo " selected"; }} ?>>Mundane Equipment</option>
				<option value="2"<?php if(isset($_GET['t'])) { if($_GET['t']=="2") { echo " selected"; }} ?>>Feats</option>
				<option value="3"<?php if(isset($_GET['t'])) { if($_GET['t']=="3") { echo " selected"; }} ?>>Magic Items</option>
				<option value="4"<?php if(isset($_GET['t'])) { if($_GET['t']=="4") { echo " selected"; }} ?>>Racial Abilities</option>
				<option value="5"<?php if(isset($_GET['t'])) { if($_GET['t']=="5") { echo " selected"; }} ?>>Spells</option>
			</select>
			<?php if(isset($_GET['t'])) { echo '
			<script type="text/javascript">
				showTable('.$_GET['t'].');
			</script>'; } ?>
		</div>
		<div id="output"></div>
		</form>
		</div>
	</body>
</html>