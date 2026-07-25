<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$date_format="l F jS, Y, g:i A T";
	if(file_exists($database_folder . "date_format.txt"))
	{
		$date_format=file_get_contents($database_folder . "date_format.txt");
	}
	
	if(file_exists($database_folder . "timezone.txt"))
	{
		date_default_timezone_set(file_get_contents($database_folder . "timezone.txt"));
	}
	
	$disabled=false;
	
	$request=array("ID"=>false,"User"=>false,"IP"=>false,"Time"=>false,"Text"=>false,"Status"=>false);
	
	$requests=array("New"=>array(),"Queued"=>array(),"Done"=>array());
	if(file_exists($database_folder . "requests.txt"))
	{
		$requests=unserialize(file_get_contents($database_folder . "requests.txt"));
	}
	
	$request_id=0;
	if(!empty($_GET['req']))
	{
		$request_id=preg_replace("/[^0-9]/","",$_GET['req']);
	}
	
	if(in_array($request_id,array_keys($requests["New"])))
	{
		$request=$requests["New"][$request_id];
	}
	if(in_array($request_id,array_keys($requests["Queued"])))
	{
		$request=$requests["Queued"][$request_id];
	}
	if(in_array($request_id,array_keys($requests["Done"])))
	{
		$request=$requests["Done"][$request_id];
	}
?>
<html>
<head>
<title>Music Request System LITE - Request Info</title>
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
	
	if($admin !== true)
	{
		echo("<script type=\"text/javascript\">window.location = 'index.php'</script>");
		die("You are not authorized to observe this post. You should be automatically redirected. If you are not, please <a href=\"index.php\">click here</a>.");
	}
?>
<h1>Music Request System LITE</h1>
<h2>Inspect Request</h2>
<p>Request ID: <?php echo $request["ID"]; ?><br>
Requested by: <?php echo $request["User"]; ?> (<?php echo $request["IP"]; ?>)<br>
Request: <?php echo $request["Text"]; ?><br>
Requested on: <?php echo date($date_format,$request["Time"]); ?><br>
Current status:&nbsp;
<?php
	switch($request["Status"])
	{
		case 0:
		echo "New";
		break;
		
		case 1:
		echo "Enqueued";
		break;
		
		case 2:
		echo "Denied";
		break;
		
		case 3:
		echo "Completed";
		break;
	}
?>
<br>
Last edited:&nbsp;
<?php
	if(!empty($request["Edited"]))
	{
		echo date($date_format,$request["Edited"]);
	}
	else
	{
		echo "<i>never</i>";
	}
?>
<br>
Edited by BOFH?&nbsp;
<?php
	if(!empty($request["Edited"]))
	{
		if(!empty($request["EditByAdmin"]))
		{
			echo "YES";
		}
		else
		{
			echo "NO";
		}
	}
	else
	{
		echo "N/A";
	}
?>
<br>
Last action:&nbsp;
<?php
	if(!empty($request["LastAction"]))
	{
		echo($request["LastAction"][0] . " at " . date($date_format,$request["LastAction"][1]));
	}
	else
	{
		echo "N/A";
	}
?>
<br>
<p><a href="index.php">Abscond</a></p>
</body>
</html>