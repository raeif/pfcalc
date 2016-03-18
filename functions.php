<?php

	// Functions
	
	function getStatMod($stat) {
		if ($stat < 10) {
			switch ($stat) {
				case 9: return(-1);
				case 8: return(-1);
				case 7: return(-2);
				case 6: return(-2);
				case 5: return(-3);
				case 4: return(-3);
				case 3: return(-4);
				case 2: return(-4);
				case 1: return(-5);
			} // End switch
		} else {
			return(floor(($stat-10)/2));
		} // End Else
	} // End Function