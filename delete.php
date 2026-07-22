<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$disabled=false;
	
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
	
	$editable=false;
	if(in_array($request_id,array_keys($requests["New"])))
	{
		$editable=true;
	}
	
	$request_ip="";
	if(in_array($request_id,array_keys($requests["New"])))
	{
		$request_ip=$requests["New"][$request_id]["IP"];
	}
	if(in_array($request_id,array_keys($requests["Queued"])))
	{
		$request_ip=$requests["Queued"][$request_id]["IP"];
	}
	if(in_array($request_id,array_keys($requests["Done"])))
	{
		$request_ip=$requests["Done"][$request_id]["IP"];
	}
	if($request_ip != $_SERVER['REMOTE_ADDR'])
	{
		$editable=false;
	}
?>
<html>
<head>
<title>Music Request System LITE - Delete Request</title>
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
	
	if($admin !== true && $editable !== true)
	{
		echo("<script type=\"text/javascript\">window.location = 'index.php'</script>");
		die("You are not authorized to delete this post. You should be automatically redirected. If you are not, please <a href=\"index.php\">click here</a>.");
	}
	
	if(!empty($_GET['s']) && $disabled === false)
	{
		$req_id=preg_replace("/[^0-9]/","",$_GET['req']);
		
		if(in_array($req_id,array_keys($requests["New"])))
		{
			unset($requests["New"][$req_id]);
		}
		if(in_array($req_id,array_keys($requests["Queued"])))
		{
			unset($requests["Queued"][$req_id]);
		}
		if(in_array($req_id,array_keys($requests["Done"])))
		{
			unset($requests["Done"][$req_id]);
		}
		
		$fh=@fopen($database_folder . "requests.txt",'w');
		if($fh)
		{
			@fwrite($fh,serialize($requests));
			@fclose($fh);
		}
		echo("<p>Request has been deleted. Please <a href=\"index.php\">click here</a>.</p>");
		$disabled=true;
	}
?>
<h1>Music Request System LITE</h1>
<h2>Delete Request Entirely</h2>
<form method="get" action="delete.php">
<input type="hidden" name="s" value="y">
<input type="hidden" name="req" value="<?php echo $request_id; ?>">
Please note that this is PERMANENT and IIREVERSIBLE!!!<br><br>
<input type="submit" value="Confirm delete" <?php if($disabled === true) { echo "disabled=\"disabled\""; } ?>>
</form>
<p><a href="index.php">Never mind</a></p>
</body>
</html>