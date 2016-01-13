<?php
session_start();
require_once '../composants/db.php';

function cleandata($data){
	return trim(htmlentities($data));
}

if (!isset($_SESSION['isconnected'])) {
	header('location : index.php');
}


?>