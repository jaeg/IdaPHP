<?php
if (!isset($mysqli))
{
	$mysqli = new mysqli("localhost","chatbot_user","TBELrJ9RYQYvuKCq","chatbot");
	
	if (mysqli_connect_errno())
	{
		die("Connect failed:".mysqli_connect_error()."<br/>");
	}
}


?>