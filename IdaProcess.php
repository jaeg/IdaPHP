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
		//Behavior Constants
		$WEIGHT_THRESHOLD = .2;
		
		//Session Variables
		$previousMessage = (isset( $_SESSION['previousMessage'] ))? $_SESSION['previousMessage']:"";
		$previousInput = (isset( $_SESSION['previousInput'] ))? $_SESSION['previousInput']:"";
		$learningStep = (isset( $_SESSION['learningStep'] ))? $_SESSION['learningStep']:"";
		
		$userInput = $_GET['message'];
		//Format the input
		$userInputFormatted = preg_replace("/\pP+/", "",  $userInput);
		$userInputFormatted = strtolower($userInputFormatted);
		
		//Split the input into chunks.
		$userInputChunks = explode(" ", $userInputFormatted);
		
		//Determine if it is a question or a statement
		$inputType = "Statement";
		
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
			
			$keywordResult = $mysqli->query("SELECT * from keywords INNER JOIN responses on keywords.ResponseID = responses.ResponseID
											WHERE $keywords
											and responses.ResponseType = '$inputType'");
			
			if (!$keywordResult) die("I'm having a brain fart at the moment.  Please try again.");
			
			//Calculate the best response
			if ($keywordResult->num_rows == 0)
			{
				$_SESSION['learningStep'] = 1;
				die("I'm not sure what you are talking about.  Will you tell me more?");
			}
			
			$maxWeight = 0;
			$currentWeight = 0;
			$currentResponseID = 0;
			$bestResponseID = 0;
			
			while ($currentKeyword = $keywordResult->fetch_assoc())
			{
				if ($currentResponseID == 0)
				{
					$currentResponseID =  $currentKeyword['ResponseID'];
				}
				
				if ($currentResponseID != $currentKeyword['ResponseID'])
				{
					if ($maxWeight < $currentWeight)
					{
						$maxWeight = $currentWeight;
						$bestResponseID = $currentResponseID;
						$currentWeight = 0;
					}
				}

				$currentWeight = $currentWeight + $currentKeyword['KeywordWeight'];
			}
			
			if ($maxWeight < $currentWeight)
			{
				$maxWeight = $currentWeight;
				$bestResponseID = $currentResponseID;
			}
			
			
			//If a response is not good enough try to learn something new.
			if ($maxWeight < $WEIGHT_THRESHOLD)
			{
				echo "I don't feel comfortable in my knowledge about this.  Can you tell me more?";
				$_SESSION['learningStep'] = 1;
			}
			else
			{
				$messageResult = $mysqli->query("SELECT * FROM messages WHERE ResponseID = $bestResponseID ORDER BY RAND() LIMIT 1");
				
				if (!$messageResult)  die("I'm having a brain fart at the moment.  Please try again.");
				
				$message = $messageResult->fetch_assoc();
				
				$_SESSION['previousMessage'] = $message;
				$_SESSION['previousInput'] = $userInputFormatted;
				echo $message['MessageValue'];
			}
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