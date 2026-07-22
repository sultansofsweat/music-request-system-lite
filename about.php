<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$version=array("Build"=>0,"Major"=>0,"Minor"=>0,"Revision"=>0);
	if(file_exists($database_folder . "version.txt"))
	{
		$version=unserialize(file_get_contents($database_folder . "version.txt"));
	}
?>
<html>
<head>
<title>Music Request System LITE - About</title>
<link rel="icon" href="favicon.ico">
<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<?php
	if(!file_exists($database_folder . "version.txt"))
	{
		if(file_exists("firstuse.txt"))
		{
			echo("<script type=\"text/javascript\">window.location = 'setup.php'</script>");
			die("This MRS instance is either not set up yet or is broken. You should be automatically redirected. If you are not, please <a href=\"setup.php\">click here</a>.");
		}
		else
		{
			die(trigger_error("Could not find required database files",E_USER_ERROR));
		}
	}
?>
<h1>Music Request System LITE</h1>
<h2>Software Version &amp; Copyright Information</h2>
<p>This MRS LITE software is version <?php echo $version["Major"]; ?>.<?php echo $version["Minor"]; ?>, revision <?php echo $version["Revision"]; ?>.<br>
The build code is <?php echo $version["Build"]; ?>.</p>
<p>The Music Request System (MRS) is copyright &copy; 2015-2026 Brad Hunter/<a href="http://www.youtube.com/user/carnelprod666" target="_blank">CarnelProd666</a>. The MRS is licensed under the <a href="mrs_license.php">DBAD Public License</a>, version 1.1, except for the components listed below. Learn more about the MRS <a href="http://firealarms.mooo.com/mrs-lite" target="_blank">here</a>. Comments should be directed to the system administrator and/or <a href="http://github.com/sultansofsweat" target="_blank">the software writer</a>.</p>
<p>For systems running PHP versions less than 5.5.0, the MRS makes use of <a href="https://github.com/ircmaxell/password_compat/" target="_blank">password_compat</a>, produced by ircmaxell and licensed under the <a href="https://github.com/ircmaxell/password_compat/blob/master/LICENSE.md" target="_blank">MIT license</a>.</p>
<p><a href="index.php">Abscond</a></p>
</body>
</html>