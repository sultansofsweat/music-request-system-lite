<?php
	//This file contains the MRS error handler, which is used to replace the built-in that PHP uses
	
	function eh($errno, $errstr, $errfile, $errline)
	{
		switch ($errno)
		{
			case E_ERROR:
			case E_COMPILE_ERROR:
			case E_CORE_ERROR:
			echo "<p><b><u>ERROR:</u></b> " . $errstr . "<br>\n
			Located on line $errline of " . basename($errfile) . "<br>
			This is a fatal error, stopping execution. Threaten a thousand camels upon the server.</p>\n";
			exit(1);
			break;
			
			case E_USER_ERROR:
			echo "<p><b><u>ERROR:</u></b> " . $errstr . "<br>\n
			Located on line $errline of " . basename($errfile) . "</p>\n";
			break;
			
			case E_WARNING:
			case E_USER_WARNING:
			echo "<p><b><u>WARNING:</u></b> " . $errstr . "<br>\n
			Located on line $errline of " . basename($errfile) . "</p>\n";
			break;
			
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			echo "<p><b><u>SYSTEM WARNING:</u></b> " . $errstr . "<br>\n
			Located on line $errline of " . basename($errfile) . "<br>\n
			This is probably a problem. Continuing anyways, expect severe breakage.</p>\n";
			break;
			
			case E_DEPRECATED:
			echo "<p><b><u>SYSTEM DEPRECATION NOTICE:</u></b> " . $errstr . "<br>\n
			Located on line $errline of " . basename($errfile) . "<br>\n
			Please hit the MRS developers over the head with a frying pan until they fix this.</p>\n";
			break;
			
			case E_USER_DEPRECATED:
			echo "<p><b><u>DEPRECATION NOTICE:</u></b> " . $errstr . "<br>\n
			Located on line $errline of " . basename($errfile) . "</p>\n";
			break;

			case E_NOTICE:
			case E_USER_NOTICE:
			echo "<p><b><u>NOTICE:</u></b> " . $errstr . "<br>\n
			Located on line $errline of " . basename($errfile) . "</p>\n";
			break;

			default:
			echo "<p>Unidentified error <b><u>[$errno]</u></b>: $errstr<br>\n
			Located on line $errline of " . basename($errfile) . "</p>\n";
			break;
    	}

    	/* Don't execute PHP internal error handler */
    	return true;
	}
	
	//Shutdown function
	function sh()
	{
		$last_error = error_get_last();
		if(!empty($last_error) && isset($last_error['type']) && $last_error['type'] != "")
		{
			eh($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
	}
	
	//Set error handler to the custom one
	register_shutdown_function("sh");
	$oeh=set_error_handler("eh");
?>