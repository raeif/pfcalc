<!DOCTYPE HTML>
<html>
	<head>
		<title>Bonuser</title>
		<!-- <link rel="stylesheet" type="text/css" href="./test.css"> -->
		<link rel="stylesheet" type="text/css" href="./style.css">
	</head>
	<body>
		<?php
			require './functions.php';
			require './header.php';
		?>
        <div class="body-content">
            <?php
                $pcid = 3;
                $pc = getCharacter($pcid);
                $dstats = getDerivedStats($pcid,true);
                //Derived Stats: Base Attack Bonus (BAB), Melee BAB, Ranged BAB, Fort Save, Ref Save, Will Save, AC, Combat Maneuver Bonus (CMB), Combat Maneuver Defense (CMD), Initiative
            ?>
            <div class="leftbloc" style="font-weight:bold">Character Name:</div><div class="rightbloc"><?php echo $pc['name']; ?></div>
            <?php
                foreach($dstats as $key => $value) {
					if ($key == 'NumAttacks' || $key == 'AC' || $key == 'ACDesc' || $key == 'CMD') {
						echo '<div class="leftbloc" style="font-weight:bold">'.$key.':</div><div class="rightbloc">'.$value.'</div><br/>';
					} else {
                    	echo '<div class="leftbloc" style="font-weight:bold">'.$key.':</div><div class="rightbloc">'.asBonus($value).'</div><br/>';
				} // if else
                } // foreach
            ?>
        </div>
    </body>
</html>
