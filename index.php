<html>
	<head>
		<title>IdaPHP</title>
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script>
			$(document).ready(function(){
			//Reset IDA on page refresh
			$.ajax({
				async: false,
				type: "GET",
				url: "IdaProcess.php?reset",
				data: "",
				success: function(msg)
				{
					var log = $('#messagelog').html() + "<strong>Ida:</strong>  " + msg + "<br/>";
					$('#messagelog').html(log);
				}
			});	});
		</script>
		<script>
			function getResponse() {
				var input = $("#userInput").val();
				$("#userInput").val("");
				$("#userInput").focus();
				if (input != "")
				{
					var log = $('#messagelog').html() + "<strong>You:</strong>  " + input + "<br/>";
					$('#messagelog').html(log);
					$.ajax({
						async: false,
						type: "GET",
						url: "IdaProcess.php?message="+input,
						data: "",
						success: function(msg)
						{
							var log = $('#messagelog').html() + "<strong>Ida:</strong>  " + msg + "<br/>";
							$('#messagelog').html(log);
						}
					});	
				}
				$("#messagelog").scrollTop($("#messagelog")[0].scrollHeight);
			}
		</script>
	</head>
	
	<body>
		<div id="container" align="center">
			<strong>Message Log</strong>
			<div style="border-style: solid; border-thickness: 1px; height: 300px; width: 500px; overflow: auto" id="messagelog" align="left">
			</div>
			<input type="text" id="userInput" size="90" /><input type="button" value="Say-it" onclick="getResponse()"/>
			<br/>
			<a href="about.html">About</a> | <a href="trainingInterface.php">Keyword Tool</a>
			<script>
				$("#userInput").keyup(function(e)
				{
					if(e.keyCode == 13)
					{
						getResponse()
					}
				});
			</script>
		</div>
	</body>
</html>