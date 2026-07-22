<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
?>
<html>
<head>
<title>Music Request System LITE - License</title>
<link rel="icon" href="favicon.ico">
<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<h1>Music Request System LITE</h1>
<h2>Software License</h2>
<p style="font-family:'Courier New',monospace;"><?php echo nl2br(file_get_contents("mrs_license.txt")); ?></p>
<p><a href="about.php">Abscond</a></p>
</body>
</html>