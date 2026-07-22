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
<title>Music Request System LITE - Request Rules</title>
<link rel="icon" href="favicon.ico">
<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<h1>Music Request System LITE</h1>
<h2>Terms of Use</h2>
<?php
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		echo("<p><b>NOTE to the BOFH:</b> you may edit these rules by editing the \"rules.txt\" file located in the system root.</p>");
	}
?>
<p><?php echo nl2br(file_get_contents("rules.txt")); ?></p>
<p><a href="index.php">Abscond</a></p>
</body>
</html>