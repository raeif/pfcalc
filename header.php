<div style="text-align:center"><a href="http://www.d20pfsrd.com/" target="_blank"><img src="./img/pathfinder-logo1.png" alt="Pathfinder RolePlaying Game"></a></div>
<div class="pageheader">
	<a class="button" href="./index.php"><span class="center">Home</span></a>
	<div class="dropdown"><button class="dropbtn"><span class="center">View Character</span></button>
		<div class="dropdown-content">
			<?php
				$allcharacters = getAllCharacters();
				If (count($allcharacters) > 0) {
					foreach($allcharacters as $char) {
						echo '<a href="./character.php?selectCharacter='.$char['pcid'].'"><div class="pctitle">'.$char['name'].'</div><div class="pcclass">'.$char['race'].' ';
						$classnum = 0;
						foreach ($char['classes'] as $class) {
							$thisclass = explode(":",$class);
							if ($classnum == 0) { echo $thisclass[0].' ('.$thisclass[1].')'; } else { echo ' / '.$thisclass[0].' ('.$thisclass[1].')'; }
							$classnum++;
						} // End Foreach
						echo '</div></a>';
					} // End Foreach
				} // End If
			?>
		</div>
	</div>
	<a class="button" href="./addcharacter.php"><span class="center">Add New Character</span></a>
	<a class="button" href="./removecharacter.php"><span class="center">Remove Character</span></a>
	<a class="button" href="./intable.php"><span class="center">Update a Table</span></a>
	<a class="button" href="./itemcreation.php"><span class="center">Item Creation Calculator</span></a>
</div>
<?php	br(); ?>
