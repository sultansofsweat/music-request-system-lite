<?php
	require("errorhandler.php");
	
	$database_folder=".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "mrs" . DIRECTORY_SEPARATOR;
	
	function sort_reqs_asc($a,$b)
	{
		if(empty($a["Time"]) || empty($b["Time"]))
		{
			trigger_error("Invalid array passed to sort_requests",E_USER_WARNING);
			return 0;
		}
        if($a["Time"] < $b["Time"])
        {
            return -1;
        }
        elseif($a["Time"] > $b["Time"])
        {
            return 1;
        }
        else
        {
            return 0;
        }
	}
	function sort_reqs_desc($a,$b)
	{
        if(empty($a["Time"]) || empty($b["Time"]))
		{
			trigger_error("Invalid array passed to sort_requests",E_USER_WARNING);
			return 0;
		}
        if($a["Time"] < $b["Time"])
        {
            return 1;
        }
        elseif($a["Time"] > $b["Time"])
        {
            return -1;
        }
        else
        {
            return 0;
        }
	}
	
	session_start();
	
	$admin=false;
	if(!empty($_SESSION['mrsadmin']) && $_SESSION['mrsadmin'] == dirname(__FILE__))
	{
		$admin=true;
	}
	
	$requests=array("New"=>array(),"Queued"=>array(),"Done"=>array());
	if(file_exists($database_folder . "requests.txt"))
	{
		$requests=unserialize(file_get_contents($database_folder . "requests.txt"));
	}
	
	$date_format="l F jS, Y, g:i A T";
	if(file_exists($database_folder . "date_format.txt"))
	{
		$date_format=file_get_contents($database_folder . "date_format.txt");
	}
	
	$stale_marker=72;
	if(file_exists($database_folder . "stale_requests.txt"))
	{
		$stale_marker=file_get_contents($database_folder . "stale_requests.txt");
	}
	
	if(file_exists($database_folder . "timezone.txt"))
	{
		date_default_timezone_set(file_get_contents($database_folder . "timezone.txt"));
	}
	
	usort($requests["New"],"sort_reqs_asc");
	usort($requests["Queued"],"sort_reqs_asc");
	usort($requests["Done"],"sort_reqs_desc");
?>
<html>
<head>
<title>Music Request System LITE</title>
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
<?php
	if($admin === true)
	{
		echo("<p>You are currently in administrative mode! <a href=\"logout.php\">Log off</a><br>");
		echo("<a href=\"admin.php\">Admin console</a> | <a href=\"post.php\">Make request</a> | ");
		if(file_exists($database_folder . "open.txt") && file_get_contents($database_folder . "open.txt") == "y")
		{
			echo("<a href=\"close_system.php\">Hold the line</a> | ");
		}
		else
		{
			echo("<a href=\"open_system.php\">Open the line</a> | ");
		}
	}
	else
	{
		echo("<a href=\"login.php\">Log in as admin</a> | <a href=\"rules.php\">Request rules</a> | ");
		if(file_exists($database_folder . "open.txt") && file_get_contents($database_folder . "open.txt") == "y")
		{
			echo("<a href=\"post.php\">Make request</a> | ");
		}
		else
		{
			echo("<span style=\"text-decoration:line-through;\">Make request</span> | ");
		}
	}
	echo("<a href=\"about.php\">About the MRS</a></p>");
	
	echo("<p>There are currently " . count($requests["New"]) . " <b><u>new</u></b> requests on the system, " . count($requests["Queued"]) . " requests in-queue, and an all-time total of " . array_sum($requests) . " requests made.</p>");
?>
<hr>
<?php
	if(count($requests["New"]) > 0)
	{
		foreach($requests["New"] as $request)
		{
			echo("<p><b><u>" . $request["Text"] . "<br>
			Requested by: " . $request["User"] . ", on " . date($date_format,$request["Time"]) . "<br>
			Request has not been acknowledged yet!<br>");
			if($admin === true)
			{
				echo("<a href=\"info.php?req=" . $request["ID"] . "\">More info</a> | <a href=\"queue.php?req=" . $request["ID"] . "\">Queue request</a> | <a href=\"decline.php?req=" . $request["ID"] . "\">Decline request</a> | <a href=\"edit.php?req=" . $request["ID"] . "\">Edit request</a> | <a href=\"delete.php?req=" . $request["ID"] . "\">Delete request</a> | <a href=\"ban.php?req=" . $request["ID"] . "\">Ban user</a>");
			}
			elseif($request["IP"] == $_SERVER["REMOTE_ADDR"])
			{
				echo("<a href=\"edit.php?req=" . $request["ID"] . "\">Edit request</a> | <a href=\"delete.php?req=" . $request["ID"] . "\">Delete request</a>");
			}
			echo("</u></b></p><hr>");
		}
	}
	if(count($requests["Queued"]) > 0)
	{
		foreach($requests["Queued"] as $request)
		{
			echo("<p>" . $request["Text"] . "<br>
			Requested by: " . $request["User"] . ", on " . date($date_format,$request["Time"]) . "<br>
			Request has been put in queue!<br>");
			if($admin === true)
			{
				echo("<a href=\"info.php?req=" . $request["ID"] . "\">More info</a> | <a href=\"played.php?req=" . $request["ID"] . "\">Mark request as played</a> | <a href=\"edit.php?req=" . $request["ID"] . "\">Edit request</a> | <a href=\"delete.php?req=" . $request["ID"] . "\">Delete request</a> | <a href=\"ban.php?req=" . $request["ID"] . "\">Ban user</a>");
			}
			elseif($request["IP"] == $_SERVER["REMOTE_ADDR"])
			{
				echo("<a href=\"delete.php?req=" . $request["ID"] . "\">Delete request</a>");
			}
			echo("</p><hr>");
		}
	}
	if(count($requests["Done"]) > 0)
	{
		foreach($requests["Done"] as $request)
		{
			if($request["Time"] < (time()-$stale_marker*60*60))
			{
				break;
			}
			echo("<p><i>" . $request["Text"] . "<br>
			Requested by: " . $request["User"] . ", on " . date($date_format,$request["Time"]) . "<br>");
			if($request["Status"] === true)
			{
				echo("Request has already been played!");
			}
			else
			{
				echo("Request has been declined!");
			}
			echo("<br>");
			if($admin === true)
			{
				echo("<a href=\"info.php?req=" . $request["ID"] . "\">More info</a>");
			}
			echo("</i></p><hr>");
		}
	}
?>
</body>
</html>