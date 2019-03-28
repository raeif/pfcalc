<?php
	require './functions.php';

	If (!isset($_GET['keyword'])) { die(); }
	$keyword = $_GET['keyword'];
	$data = searchForCharacterName($keyword);
	echo json_encode($data);
?>