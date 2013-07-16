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
		$previousMessage = (isset( $_SESSION['previousResponse'] ))? $_SESSION['previousResponse']:"";
		$previousInput = (isset( $_SESSION['previousInput'] ))? $_SESSION['previousInput']:"";
		$learningStep = (isset( $_SESSION['learningStep'] ))? $_SESSION['learningStep']:"";
		
		$userInput = $_GET['message'];
		//Format the input
		$userInputFormatted = preg_replace("/\pP+/", "",  $userInput);
		$userInputFormatted = strtolower($userInputFormatted);
		
		//Split the input into chunks.
		$userInputChunks = explode(" ", $userInputFormatted);
		
		//Determine if it is a question or a statement
		$inputType = classifyInput($userInput);
		
		//Check to see if it is involved in a learning proceedure
		if ($learningStep == "" || $learningStep == 0)
		{
			//Query the keywords using these chunks
			$keywords = "(";
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
			$keywords .= ")";
			$keywordResult = $mysqli->query("SELECT * from keywords INNER JOIN responses on keywords.ResponseID = responses.ResponseID
											WHERE $keywords
											and responses.ResponseType = '$inputType'");
			
			if (!$keywordResult) die("I'm having a brain fart at the moment.  Please try again.");
			
			//Calculate the best response
			if ($keywordResult->num_rows == 0)
			{
				$_SESSION['previousInput'] = $userInput;
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
						$currentResponseID = $currentKeyword['ResponseID'];
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

				$_SESSION['previousInput'] = $userInput;
				$_SESSION['learningStep'] = 1;
			}
			else
			{
				$messageResult = $mysqli->query("SELECT * FROM messages WHERE ResponseID = $bestResponseID ORDER BY RAND() LIMIT 1");
				
				if (!$messageResult)  die("I'm having a brain fart at the moment.  Please try again.");
				
				$message = $messageResult->fetch_assoc();
				
				$_SESSION['previousResponse'] = $bestResponseID;
				$_SESSION['previousInput'] = $userInput;
				echo $message['MessageValue'];
			}
		}
		else
		{
			switch($learningStep)
			{
				case 1:
					echo "Are you telling me the truth?";
					$_SESSION['phraseToLearn'] = $userInput;
					$_SESSION['learningStep'] = 2;
					break;
				case 2:
					if (in_array("yes", $userInputChunks) || in_array("yep", $userInputChunks) || in_array("yah", $userInputChunks))
					{
						echo "Thanks for telling me that!";
						$responseType = classifyInput($_SESSION['previousInput']);
						$responseMessage = $mysqli->real_escape_string($_SESSION['phraseToLearn']);
						
						//Format the input
						$userInputFormatted = preg_replace("/\pP+/", "",  $_SESSION['previousInput']);
						$userInputFormatted = strtolower($userInputFormatted);
						$keywords = explode(" ",$userInputFormatted);
						
						$mysqli->query("INSERT INTO responses(ResponseType) VALUES('$responseType')");
						
						$responseID = $mysqli->insert_id;
						
						$mysqli->query("INSERT INTO messages(ResponseID, MessageValue) VALUES($responseID, '$responseMessage')");
						
						foreach($keywords as $keyword)
						{
							$keyword = $mysqli->real_escape_string($keyword);
							$keywordWeight = strlen($keyword)/15.0;
							$mysqli->query("INSERT INTO keywords(ResponseID, KeywordValue, KeywordWeight) VALUES($responseID, '$keyword', $keywordWeight)");
						}
					}
					else
					{
						echo "No one likes a liar!";
					}
					$_SESSION['learningStep'] = 0;
					break;
			}
		}
	}
	
	
	function classifyInput($input)
	{
		$questionStarters = array("who","what","where","when","why","how","which","wherefore","whom","whose","wherewith","whither","whence","do","does");

		if ($input[strlen($input)-1] == "?")
		{
			return "Question";
		}
		
		if ($input[strlen($input)-1] == "." || $input[strlen($input)-1] == "!" )
			return "Statement";
		
		//Format the input
		$userInputFormatted = preg_replace("/\pP+/", "",  $input);
		$userInputFormatted = strtolower($userInputFormatted);
		
		//Split the input into chunks.
		$userInputChunks = explode(" ", $userInputFormatted);
		
		foreach ($questionStarters as $keyword)
		{
			if ($userInputChunks[0] == $keyword)
				return "Question";
		}
		
		return "Statement";
	}
?>