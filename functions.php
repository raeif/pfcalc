<?php

###################################
### Global variable definitions ###
###################################

	include('./config.php');
	$mysqli = new mysqli($hostname,$username,$password,$db);
	if($mysqli->connect_errno) { echo "Failed to connect to MySql: "."(".mysqli_connect_error().")".mysqli_connect_errno(); }
	$dbconn = new PDO($dbdriver.":dbname=".$db.";host=".$hostname.";charset=utf8",$username,$password);
	$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$redis = new Redis();
	$redis->connect($hostname);

	$ttl = 3600;

#########################
### Utility Functions ###
#########################

	function br() { echo '<br><hr><br>'; }
	function nb() { echo '<br><br>'; }

#######################
### Redis Functions ###
#######################

	function getFromRedis($key) {
		# Returns value of a redis key with error checking
		Global $redis;
		try { return $redis->get($key);	}
		catch (Exception $e) { echo $e->getMessage(); }
	} // End Function

	function setInRedis($key,$value,$newttl) {
		# Creates/Updates redis key with value and time to live with error checking
		Global $redis;
		try { $redis->setex($key,$newttl,$value); }
		catch (Exception $e) { $e->getMessage(); }
	} // End Function

	function refreshRedisTTL($key,$newttl) {
		# Updates ttl on Redis key
		Global $redis;
		try { $redis->expire($key,$newttl); }
		catch (Exception $e) { $e->getMessage(); }
	} // End Function

##########################################
### SQL and Array Management Functions ###
##########################################

	function convertToArray( $result ){
		# Convert the result of a query to an array
		$resultArray = array();
		for( $count=0; $row = $result->fetch_assoc(); $count++ ) { $resultArray[$count] = $row; }
		return $resultArray;
	} // End Function

	function runQuery($query) {
		# Run a sql query against mysql server
		Global  $mysqli;
		$result = $mysqli->query($query);
		return $result;
	} // End Function

	function searchForCharacterName($keyword) {
		Global $dbconn;
		$q = $dbconn->prepare("SELECT name from v_character WHERE name LIKE ? ORDER BY name");
		$keyword = $keyword.'%';
		$q->bindParam(1,$keyword,PDO::PARAM_STR,100);
		$results = array();
		If ($q->execute()) { $results = $q->fetchAll(PDO::FETCH_COLUMN); }
		else { trigger_error('Error executing statement:', E_USER_ERROR); }
		return $results;
	} // End Function

#############################################
### Character and Stat specific Functions ###
#############################################

	function getCharacter($pcid) {
		# Returns an associative array with all primary character stats
		Global $ttl;
		$char = array();
		# Check Redis first
		#$redischar = getFromRedis($pcid.'.c');
		# Debugging: Disable redis check
		$redischar = false;
		# If does not return false (ie found), decode to return
		if ($redischar != false) {
			$char = json_decode($redischar,true);
			refreshRedisTTL($pcid.'.c',$ttl);
		} else {
			# Else query database and create character array
			$result = runQuery('SELECT * FROM v_character WHERE pcid ='.$pcid);
			If ($result->num_rows == 1) {
				$arr = convertToArray($result);
				$char = $arr[0];
				$char['classes'] = getClasses($char['pcid'],true);
				# Add character to Redis for future queries
				setInRedis($pcid.'.c',json_encode($char),$ttl);
			} // End If
		} // End Else
		return $char;
	} // End Function

	function getAllCharacters() {
		# Returns a multi-dimensional array of all characters [Dimension1: Index (Incr INT) / Dimension2: Key:Value (Associative)]
		#  This function does not cache characters in Redis!
		$list = runQuery('SELECT * FROM v_character');
		$a = array();
		If ($list->num_rows > 0) {
			foreach ($list as $char) {
				$char['classes'] = getClasses($char['pcid'],false);
				$a[] = $char;
			} // End Foreach
		} // End If
		return $a;
	} // End Function

	function getStatMod($stat) {
		# Returns the modifier value of given stat
		return(floor(($stat-10)/2));
	} // End Function

	function getClasses($pcid,$cache) {
		# Returns an indexed array of all classes assigned to the character: Class:Level (ex. "Fighter:1")
		Global $ttl;
		$redischar = getFromRedis($pcid.'.cl');
		If ($redischar == false) {
			$result = runQuery("SELECT * FROM v_classes WHERE pcid = ".$pcid);
			$classes = array();
			while ($row = $result->fetch_assoc()) {
				# => Later, add push all values returned from query to redis
				array_push($classes,$row['ClassName'].':'.$row['classlevel']);
			} // End While
			if ($cache) { setInRedis($pcid.'.cl',json_encode($classes),$ttl); }
		} else {
			$classes = json_decode($redischar,true);
			if ($cache) { refreshRedisTTL($pcid.'.cl',$ttl); }
		} // End If Else
		return $classes;
	} //End Function

	function longAlignment($alignment) {
		# Returns the long name of the Alignment (ex. "LG" is "Lawful Good")
		$a = '';
		switch($alignment) {
			case 'LG' :
				return('Lawful Good');
				break;
			case 'LN' :
				return('Lawful Neutral');
				break;
			case 'LE' :
				return('Lawful Evil');
				break;
			case 'NG' :
				return('Neutral Good');
				break;
			case 'TN' :
				return('True Neutral');
				break;
			case 'NE' :
				return('Neutral Evil');
				break;
			case 'CG' :
				return('Chaotic Good');
				break;
			case 'CN' :
				return('Chaotic Neutral');
				break;
			case 'CE' :
				return('Chaotic Evil');
				break;
		} // End Switch
	} // End Function

	function asBonus($bonus) {
		return ($bonus >= 0) ? '+'.$bonus : $bonus;
	} // End Function

	function getBonusbyType($pcid,$type,$stat) {
		# Returns total of all bonuses of specified type

		# 1. Initialize variables
		$stackingbonus = 0;
		$nonstackingbonus = 0;

		# 2. Calculate stacking bonuses first
		$result = runQuery("SELECT bonus,bonustype,statname,stacks FROM v_bonuses WHERE pcid = ".$pcid." AND bonustype = '".$type."' AND statname = '".$stat."' AND stacks = 1");
		# If query returns results then loop through
		If($result <> null) {
			$allstackingbonuses = convertToArray($result);
			foreach($allstackingbonuses as $bonus) { $stackingbonus += $bonus['bonus']; }
		} // End If

		# 3. Calculate nonstacking bonuses
		$result = runQuery("SELECT bonus,bonustype,statname,stacks FROM v_bonuses WHERE pcid = ".$pcid." AND bonustype = '".$type."' AND statname = '".$stat."' AND stacks = 0");
		If($result <> null) {
			$allnonstackingbonuses = convertToArray($result);
			$maxbonusvalue = 0;
			foreach($allnonstackingbonuses as $bonus) { If($bonus['bonus'] > $maxbonusvalue) { $maxbonusvalue = $bonus['bonus']; } }
			$nonstackingbonus = $maxbonusvalue;
		} // End If

		# 4. Return total of stacking and nonstacking bonuses
		return $stackingbonus + $nonstackingbonus;
	} // End Function

	function createBonusDesc($bonuses) {
		# Returns text string converting $bonuses array to format: "(+X [BonusType], +Y [BonusType])"
		# Parameter $bonuses must be an array of arrays of format: [0](bonus,bonustype), [1](bonus,bonustype) and so on
		$returned = "";
		If (count($bonuses) > 0) {
			$returned = "(";
			foreach($bonuses as $bonus) {
				$bonusvalue = $bonus['bonus'];
				$bonustype = $bonus['bonustype'];
				If($bonusvalue <> 0) { If ($returned == "(") { $returned .= asBonus($bonusvalue)." ".$bonustype; } Else { $returned .= ", ".asBonus($bonusvalue)." ".$bonustype; } }
			} // End Foreach
			$returned = $returned.")";
		} // End If
		return $returned;
	} // End Function

############################################
### Calculate Derived Stats with bonuses ###
############################################

	function getAC($pcid) {
		# Calculates AC and returns array['AC' => int, 'ACDesc' => str]
		# Initialize
		$stat = 'AC';
		$acbonuses = array();
		$char = getCharacter($pcid);
		# Armor Bonuses
		$armorbonus = getBonusbyType($pcid,'Armor',$stat);
		If($armorbonus <> 0) { array_push($acbonuses,array('bonus' => $armorbonus,'bonustype' => 'Armor')); }
		# Shield Bonuses
		$shieldbonus = getBonusbyType($pcid,'Shield',$stat);
		If($shieldbonus <> 0) { array_push($acbonuses,array('bonus' => $shieldbonus,'bonustype' => 'Shield')); }
		# Dex Bonus
		$dexbonus = getStatMod($char['dex']);
		If($dexbonus <> 0) { array_push($acbonuses,array('bonus' => $dexbonus,'bonustype' => 'Dexterity')); }
		# Dodge Bonuses
		$dodgebonus = getBonusbyType($pcid,'Dodge',$stat);
		If($dodgebonus <> 0) { array_push($acbonuses,array('bonus' => $dodgebonus,'bonustype' => 'Dodge')); }
		# Size Bonuses
		$sizebonus = getBonusbyType($pcid,'Size',$stat);
		If($sizebonus <> 0) { array_push($acbonuses,array('bonus' => $sizebonus,'bonustype' => 'Size')); }
		# Natural Armor Bonuses
		$naturalarmorbonus = getBonusbyType($pcid,'Natural Armor',$stat);
		If($naturalarmorbonus <> 0) { array_push($acbonuses,array('bonus' => $naturalarmorbonus,'bonustype' => 'Natural Armor')); }
		# Deflection Bonuses
		$deflectionbonus = getBonusbyType($pcid,'Deflection',$stat);
		If($deflectionbonus <> 0) { array_push($acbonuses,array('bonus' => $deflectionbonus,'bonustype' => 'Deflection')); }
		# Add it all up
		$ac = 10 + $armorbonus + $shieldbonus + $dexbonus + $dodgebonus + $sizebonus + $naturalarmorbonus + $deflectionbonus;
		$acdesc = createBonusDesc($acbonuses);
		return array('AC' => $ac,'ACDesc' => $acdesc);
	} // End Function

	function getDerivedStats($pcid,$recalculate) {
		# Return an associative array of derived stats; Recalculate (bool) bypasses Redis and recalculates all derived stats
		#  Derived Stats: Base Attack Bonus (BAB), Melee BAB, Ranged BAB, Fort Save, Ref Save, Will Save, AC, Combat Maneuver Bonus (CMB), Combat Maneuver Defense (CMD), Initiative
		Global $ttl;
		$char = getCharacter($pcid);
		$redischar = getFromRedis($pcid.'.d');
		$derivedStats = array("Fortitude" => 0,"Reflex" => 0,"Will" => 0);
		# If character not found in redis or recalculate bool is true
		if ($redischar == false || $recalculate){
			$BAB = 0;
			$result = runQuery("SELECT classlevel,BABProgression,FortSaveProgression,RefSaveProgression,WillSaveProgression FROM v_classes WHERE pcid = ".$pcid);
			$classes = convertToArray($result);
			$numclasses = $result->num_rows;
			foreach ($classes as $class) {
				# Calculate Attack Bonuses
				$BABmult = $class['BABProgression'];
				$classBAB = floor($BABmult*$class['classlevel']);
				$BAB += $classBAB;
				# Calculate Saves
				if ($class['FortSaveProgression'] == 1) { $derivedStats['Fortitude'] += (2 + floor($class['classlevel'] / 2)); } else { $derivedStats['Fortitude'] += (floor($class['classlevel'] / 3)); }
				if ($class['RefSaveProgression'] == 1) { $derivedStats['Reflex'] += (2 + floor($class['classlevel'] / 2)); } else { $derivedStats['Reflex'] += (floor($class['classlevel'] / 3)); }
				if ($class['WillSaveProgression'] == 1) { $derivedStats['Will'] += (2 + floor($class['classlevel'] / 2)); } else { $derivedStats['Will'] += (floor($class['classlevel'] / 3)); }
			} // End Foreach

			# Calculate final Attack Bonuses
			$derivedStats["BAB"] = $BAB;
			$derivedStats['BaseAttacks'] = "";
			$derivedStats['NumAttacks'] = ($BAB % 5 > 0) ? ceil($BAB/5) : floor($BAB/5);
			for($i=1;$i<=$derivedStats['NumAttacks'];$i++) { $derivedStats['BaseAttacks'] .= ($i==1) ? asBonus($BAB) : ' / '.asBonus($BAB-(($i-1)*5)); }
			$derivedStats["meleeBAB"] = $BAB + getStatMod($char['str']);
			$derivedStats["rangedBAB"] = $BAB + getStatMod($char['dex']);

			# Calculate final Saves
			$derivedStats['Fortitude'] += getStatMod($char['con']);
			$derivedStats['Reflex'] += getStatMod($char['dex']);
			$derivedStats['Will'] += getStatMod($char['wis']);

			# Get AC
			$acarr = getAC($pcid);
			$derivedStats['AC'] = $acarr['AC'];
			$derivedStats['ACDesc'] = $acarr['ACDesc'];

			# Calculate CMB and CMD
			$derivedStats['CMB'] = $derivedStats['BAB'] + getStatMod($char['str']);
			$derivedStats['CMD'] = $derivedStats['BAB'] + 10 + getStatMod($char['str']) + getStatMod($char['dex']);

			# Calculate Initiative
			$derivedStats['init'] = getStatMod($char['dex']);
			setInRedis($pcid.'.d',json_encode($derivedStats),$ttl);
		} else {
			$derivedStats = json_decode($redischar,true);
			refreshRedisTTL ($pcid.'.d',$ttl);
		} // End Else
		return $derivedStats;
	} // End Function

#########################
### Display Functions ###
#########################

	function displayStats($pcid) {
		# Displays the stats block
		$pc = getCharacter($pcid);
		$dstats = getDerivedStats($pcid,true);
		echo '<div style="clear:both;"></div><table class="center" border=0 cellpadding=1>';
		echo '<tr><td colspan="7" style="text-align:left"><h2>'.$pc['name'].' ['.$pc['size'].']</h2></td></tr>';
		echo '<tr><td colspan="7" style="text-align:left;margin-left:10px"><h4>'.$pc['race'].' ('.$pc['alignment'].')&nbsp;&nbsp;&nbsp;';
		$classnum = 0;
		foreach ($pc['classes'] as $class) {
			$thisclass = explode(":",$class);
			if ($classnum == 0) { echo $thisclass[0].' ('.$thisclass[1].')'; } else { echo ' / '.$thisclass[0].' ('.$thisclass[1].')'; }
			$classnum++;
		} // End Foreach
		echo '</h4></td></tr>';
		echo '<tr><td><div class="header"><span class="header">Ability Name</span></div></td><td><div class="header"><span class="header">Ability Score</span></div></td><td><div class="header"><span class="header">Ability Modifier</span></div></td></tr>';
		echo '<tr><td><div class="heading">STR</div></td><td><div class="stats">'.$pc['str'].'</div></td><td><div class="stats">'.getStatMod($pc['str']).'</div></td><td><div style="text-align:right">BAB:</div></td><td style="width:65px"><div class="otherstats">'.asBonus($dstats['BAB']).'</div></td><td><div style="text-align:right">Fort:</div></td><td><div class="otherstats">'.asBonus($dstats['Fortitude']).'</div></td></tr>';
		echo '<tr><td><div class="heading">DEX</div></td><td><div class="stats">'.$pc['dex'].'</div></td></td><td><div class="stats">'.getStatMod($pc['dex']).'</div></td><td><div style="text-align:right">Melee:</div></td><td style="width:65px"><div class="otherstats">'.asBonus($dstats['meleeBAB']).'</div></td><td><div style="text-align:right">Ref:</div></td><td><div class="otherstats">'.asBonus($dstats['Reflex']).'</div></td></tr>';
		echo '<tr><td><div class="heading">CON</div></td><td><div class="stats">'.$pc['con'].'</div></td></td><td><div class="stats">'.getStatMod($pc['con']).'</div></td><td><div style="text-align:right">Ranged:</div></td><td style="width:65px"><div class="otherstats">'.asBonus($dstats['rangedBAB']).'</div></td><td><div style="text-align:right">Will:</div></td><td><div class="otherstats">'.asBonus($dstats['Will']).'</div></td></tr>';
		echo '<tr><td><div class="heading">INT</div></td><td><div class="stats">'.$pc['int'].'</div></td></td><td><div class="stats">'.getStatMod($pc['int']).'</div></td></tr>';
		echo '<tr><td><div class="heading">WIS</div></td><td><div class="stats">'.$pc['wis'].'</div></td></td><td><div class="stats">'.getStatMod($pc['wis']).'</div></td><td><div style="text-align:right">CMB:</div></td><td><div class="otherstats">'.asBonus($dstats['CMB']).'</div></td><td><div style="text-align:right">AC:</div></td><td><div class="otherstats">'.$dstats['AC'].'</div></td><td><div style="text-align:left">'.$dstats['ACDesc'].'</div></td></tr>';
		echo '<tr><td><div class="heading">CHA</div></td><td><div class="stats">'.$pc['cha'].'</div></td></td><td><div class="stats">'.getStatMod($pc['cha']).'</div></td><td><div style="text-align:right">CMD:</div></td><td><div class="otherstats">'.$dstats['CMD'].'</div></td><td><div style="text-align:right">Initiative:</div></td><td><div class="otherstats">'.asBonus($dstats['init']).'</div></td></tr>';
		echo '</table>';
	} // End Function
?>
