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
	if(in_array($request_id,array_keys($requests["Queued"])))
	{
		$editable=true;
	}
?>
<html>
<head>
<title>Music Request System LITE - Mark Request As Played</title>
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
	
	if($admin !== true || $editable !== true)
	{
		echo("<script type=\"text/javascript\">window.location = 'index.php'</script>");
		die("You are not authorized to finish this post. You should be automatically redirected. If you are not, please <a href=\"index.php\">click here</a>.");
	}
	
	if(!empty($_GET['s']) && $disabled === false)
	{
		$req_id=preg_replace("/[^0-9]/","",$_GET['req']);
		
		$request=$requests["Queued"][$req_id];
		$request["Status"]=3;
		$request["LastAction"]=array("PLAYED",time());
		unset($requests["Queued"][$req_id]);
		$requests["Done"][$req_id]=$request;
		
		$fh=@fopen($database_folder . "requests.txt",'w');
		if($fh)
		{
			@fwrite($fh,serialize($requests));
			@fclose($fh);
		}
		echo("<p>Request has been played. Please <a href=\"index.php\">click here</a>.</p>");
		$disabled=true;
	}
?>
<h1>Music Request System LITE</h1>
<h2>Play Request</h2>
<form method="get" action="played.php">
<input type="hidden" name="s" value="y">
<input type="hidden" name="req" value="<?php echo $request_id; ?>">
<input type="submit" value="Confirm play" <?php if($disabled === true) { echo "disabled=\"disabled\""; } ?>>
</form>
<p><a href="index.php">Never mind</a></p>
</body>
</html>