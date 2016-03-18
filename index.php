<html>
	<head>
		<title>PFClac v0.2</title>
		<link rel="stylesheet" type="text/css" href="./style.css">
	</head>
	
	<body>
		<form name="CharacterForm" method="get" action="" >
		<?php
			include('./config.php');
			include('./functions.php');

			//Connect to database
			$conn = mysql_connect($hostname,$username,$password) or die("Unable to connect to MySQL");
			mysql_select_db($db) or die("Unable to connect to database");

			$q = "SELECT CharID,CharacterName from d_CharacterStatsStatic";
			$characterList = mysql_query($q) or die("Unable to execute query: ".mysql_error());

			If (mysql_num_rows($characterList) > 0 ) {
				echo 'Select Character: ';
				echo '<select id="selectCharacter" name="selectCharacter">';
				echo '<option value="0"></option>';
				while ($row = mysql_fetch_assoc($characterList)) {
					extract($row);
					echo '<option value="'.$CharID.'">'.$CharacterName.'</option>';
				} // End While
				echo '</select>';
			} // End If
			echo '<input type="submit" value="Load Character" />';

			echo '<br /><hr /><br />';

			// Test Query
			$cid = 0;
			If ($_GET['selectCharacter']) {
				$cid = $_GET['selectCharacter'];
			} else { $cid = 0; }
			$query = "SELECT * FROM d_CharacterStatsStatic WHERE CharID = ".$cid;
			$result = mysql_query($query) or die("Error in query: $query.".mysql_error());
			if(mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_assoc($result)) {
					extract($row);
						echo '<div><h2>'.$CharacterName.'</h2></div><div style="clear:both;"></div>';
						echo '<table border=0 cellpadding=1>';
						echo '<tr><td><div class="header"><span class="header">Ability Name</span></div></td><td><div class="header"><span class="header">Ability Score</span></div></td><td><div class="header"><span class="header">Ability Modifier</span></div></td></tr>';
						echo '<tr><td><div class="heading">STR</div></td><td><div class="stats">'.$Strength.'</div></td><td><div class="stats">'.getStatMod($Strength).'</div></td></tr>';
						echo '<tr><td><div class="heading">DEX</div></td><td><div class="stats">'.$Dexterity.'</div></td></td><td><div class="stats">'.getStatMod($Dexterity).'</div></td></tr>';
						echo '<tr><td><div class="heading">CON</div></td><td><div class="stats">'.$Constitution.'</div></td></td><td><div class="stats">'.getStatMod($Constitution).'</div></td></tr>';
						echo '<tr><td><div class="heading">INT</div></td><td><div class="stats">'.$Intelligence.'</div></td></td><td><div class="stats">'.getStatMod($Intelligence).'</div></td></tr>';
						echo '<tr><td><div class="heading">WIS</div></td><td><div class="stats">'.$Wisdom.'</div></td></td><td><div class="stats">'.getStatMod($Wisdom).'</div></td></tr>';
						echo '<tr><td><div class="heading">CHA</div></td><td><div class="stats">'.$Charisma.'</div></td></td><td><div class="stats">'.getStatMod($Charisma).'</div></td></tr>';
						echo '</table>';
				} // End While
			} // End If
			
			// Cleanup and close database connection
			mysql_free_result($result);
			mysql_close($conn);
		?>
		</form>
	</body>
</html>