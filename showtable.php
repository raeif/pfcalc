<!DOCTYPE html>
<html>
<head>
	<style>
	table {
		width: 100%;
		border-collapse: collapse;
	}

	table, td, th {
		border: 1px solid black;
		padding: 5px;
	}

	th {text-align: center;}
	</style>
</head>

<body>
	<?php require './functions.php'; ?>
	<?php
		$q = intval($_GET['q']);

		switch ($q) {
			case 0:
				$table = 'l_classabilities';
				break;
			case 1:
				$table = 'l_equipment';
				break;
			case 2:
				$table = 'l_feats';
				break;
			case 3:
				$table = 'l_magicitems';
				break;
			case 4:
				$table = 'l_racialabilities';
				break;
			case 5:
				$table = 'l_spells';
				break;
		} // End Switch
		If($_GET['q'] <> "") {
			# Set next id value
			$sql = "SELECT MAX(id) AS id FROM ".$table;
			$nextid = intval(convertToArray($result = runQuery($sql))[0]['id']) + 1;
			$sql = 'SELECT * FROM r_BonusToStatsRef';
			$arrToStat = convertToArray(runQuery($sql));
			$sql = 'SELECT * FROM r_BonusTypesRef';
			$arrBonusTypes = convertToArray(runQuery($sql));

			# Display Input Tables
			echo '<div class="leftbloc">ID:</div><div class="rightbloc"><input style="width:25" type="text" value="'.$nextid.'" name="id"></div>';
			echo '<div class="leftbloc">Ability Name:</div><div class="rightbloc"><input style="width:80" type="text" value="" name="name"></div>';
			echo '<div class="leftbloc">Bonus Value:</div><div class="rightbloc"><input style="width:25" type="text" value="" name="bonusvalue"></div>';
			echo '<div class="leftbloc">Affected Stat:</div><div class="rightbloc"><select name="statname"><option value=""></option>';
			foreach($arrToStat as $stat) { echo '<option value="'.$stat['statid'].'">'.$stat['statname'].'</option>'; }
			echo '</select></div>';
			echo '<div class="leftbloc">Bonus Type:</div><div class="rightbloc"><select name="bonustype"><option value=""></option>';
			foreach($arrBonusTypes as $bonustype) { echo '<option value="'.$bonustype['typeid'].'">'.$bonustype['bonustype'].'</option>'; }
			echo '</select></div>';
			echo '<div class="leftbloc">Short Description:</div><div class="rightbloc"><input style="height:30;width:80" type="text" value="" name="shortdesc"></div>';
			echo '<div class="leftbloc">Long Description:</div><div class="rightbloc"><input style="height:60;width:80" type="text" value="" name="longdesc"></div>';
			echo '<div class="leftbloc">Stacks:</div><div class="rightbloc">';
			echo '<select name="stacks"><option value="1">Yes</option><option value="0">No</option></select></div>';
			echo '<div class="leftbloc">URL:</div><div class="rightbloc"><input style="width:80" type="text" value="" name="url"></div>';
			echo '<div class="float_center"><input class="child" type="hidden" name="intable" value="'.$table.'"></div>';
			echo '<div class="float_center"><input class="child" type="hidden" name="intablenum" value="'.$q.'"></div>';
			echo '<div class="float_center"><input class="child" type="submit" value="Add to Table"></div>';

			# Print selected table
			$sql = "SELECT lt.id,lt.`name`,lt.bonusvalue,bts.statname,bt.bonustype,lt.shortdesc,lt.stacks FROM ".$table." lt INNER JOIN r_BonusToStatsRef bts ON lt.tostat = bts.statid INNER JOIN r_BonusTypesRef bt ON lt.bonustype = bt.typeid;";
			$result = runQuery($sql);
			echo '<div><table>
			<tr>
			<th>ID</th>
			<th>Ability Name</th>
			<th>Bonus</th>
			<th>Affected Stat</th>
			<th>Bonus Type</th>
			<th>Short Description</th>
			<th>Stacks</th>
			</tr>';
			while($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>".$row['id']."</td>";
				echo "<td>".$row['name']."</td>";
				echo "<td>".asBonus($row['bonusvalue'])."</td>";
				echo "<td>".$row['statname']."</td>";
				echo "<td>".$row['bonustype']."</td>";
				echo "<td>".$row['shortdesc']."</td>";
				echo ($row['stacks'] == 1) ? "<td>Yes</td>" : "<td>No</td>";
				echo "</tr>";
			} // End While
			echo "</table></div>";
		} // End If
	?>
</body>
</html>