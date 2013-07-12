<?php
	if (isset($_POST['keyword1']) && isset($_POST['message1']))
	{
		if ($_POST['keyword1'] != "" && $_POST['message1'] != "")
		{
			require_once("connect.php");
			$responseType = $_POST['type'];
			$mysqli->query("INSERT INTO responses(ResponseType) VALUES('$responseType')");

			$responseID = $mysqli->insert_id;
			
			for($i = 1; $i < 6; $i++)
			{
				$responseMessage = $mysqli->real_escape_string($_POST['message'.$i]);
				if ($responseMessage != "")
					$mysqli->query("INSERT INTO messages(ResponseID, MessageValue) VALUES($responseID, '$responseMessage')");
				
				$keyword = $mysqli->real_escape_string($_POST['keyword'.$i]);
				if ($keyword != "")
				{
					$keywordWeight = strlen($keyword)/15.0;
					$mysqli->query("INSERT INTO keywords(ResponseID, KeywordValue, KeywordWeight) VALUES($responseID, '$keyword', $keywordWeight)");
				}
			}
		
			echo "Saved.";	
		}
		else
		{
			echo "Not enough input.";
		}
		
	}


?>
<html>
	<head><title>IdaPHP Training Interface</title></head>
	<body>
		<div id = "container" align="center">
			<h1>IdaPHP Training Interface</h1>
			<form method="post" action="trainingInterface.php">
				Type:
				<br/>
				<label for="Question">Question </label><input type="radio" name="type" id="Question" value="Question" /><br/>
				<label for="Statement">Statement </label><input type="radio" name="type" id="Statement"  value="Statement" checked=checked/><br/><br/>
				Keywords: Only the first one is required. <br/>
				<input type="text" name="keyword1"/><br/>
				<input type="text" name="keyword2"/><br/>
				<input type="text" name="keyword3"/><br/>
				<input type="text" name="keyword4"/><br/>
				<input type="text" name="keyword5"/><br/>
				<br/>
				Messages: Only the first one is required.<br/>
				<input type="text" name="message1" size="125"/><br/>
				<input type="text" name="message2" size="125"/><br/>
				<input type="text" name="message3" size="125"/><br/>
				<input type="text" name="message4" size="125"/><br/>
				<input type="text" name="message5" size="125"/><br/>
				<br/>				
				<input type="submit"/>
			</form>
		</div>
	</body>
</html>