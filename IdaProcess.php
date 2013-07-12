<?php
	session_start();
	require_once("connect.php");
	
	if (isset($_GET['reset']))
	{
		session_destroy();
		echo "Hi, I'm Ida.";
	}
	else
	{
		$previousMessage = (isset( $_SESSION['previousMessage'] ))? $_SESSION['previousMessage']:"";
		$previousInput = (isset( $_SESSION['previousInput'] ))? $_SESSION['previousInput']:"";
		$learningStep = (isset( $_SESSION['learningStep'] ))? $_SESSION['learningStep']:"";
		$userInput = $_GET['message'];
		
		//Split the message into chunks.
		$userInputChunks = explode(" ", $userInput);
		
		//Determine if it is a question or a statement
		
		//Check to see if it is involved in a learning proceedure
		if ($learningStep == "" || $learningStep == 0)
		{
			//Query the keywords using these chunks
			$keywords = "";
			$isFirst = true;
			foreach ($userInputChunks as $chunk)
			{
				if ($isFirst == false)
				{
					$keywords .= " OR ";
				}
				$isFirst = false;
				$keywords .= "KeywordValue = '$chunk'";
			}
			
			$keywordResult = $mysqli->query("SELECT * FROM keywords WHERE $keywords ORDER BY ResponseID ASC");
			if (!$keywordResult) die("I'm having a brain fart at the moment.  Please try again.");
			
			//Calculate the best response
			if ($keywordResult->num_rows == 0)
			{
				$_SESSION['learningStep'] = 1;
				die("I'm not sure what you are talking about.  Will you tell me more?");
			}
			
			
			//If a response is not good enough try to learn something new.
			
			//Otherwise echo this response and save the current message and input in session.
		}
		else
		{
			switch($learningStep)
			{
				case 1:
					echo "This is where I confirm that you are going to teach me something.";
					$_SESSION['learningStep'] = 2;
					break;
				case 2:
					echo "This is where I confirm that what you said is true.";
					$_SESSION['learningStep'] = 3;
					break;
				case 3:
					echo "This is where I thank you for the information and then save it into the database.";
					$_SESSION['learningStep'] = 0;
					break;
			}
		}
	}
	
?>