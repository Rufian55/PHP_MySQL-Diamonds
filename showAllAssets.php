<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connect to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "kearnsc-db", "w1nUsqnlrqoqpUq4", "kearnsc-db");
?>

<!DOCTYPE HTML>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="cache-control" content="no-cache" />
<link rel="stylesheet" type="text/css" href="../Diamonds/style.css" />
<link rel="shortcut icon" href="../Diamonds/images/favicon.ico" />
<title>Diamond Buyers Inc.</title>
</head>

<body>
<h1>Diamond Buyers Inc.</h1>
<h2>Show All Assets</h2>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Confidential</legend>
		<fieldset class="fieldset-left">
			<table class="table_display">
				<tr>
					<td class="bold">Asset ID</td>
					<td class="bold">Description</td>
					<td class="bold">Carrot</td>
					<td class="bold">Cut</td>
					<td class="bold">Clarity</td>
					<td class="bold">Color</td>
					<td class="bold">Create Date</td>
					<td class="bold">Owned By</td>
				</tr>
<?php
if(!($stmt = $mysqli->prepare("SELECT id, Description, Carrot, Cut, Clarity, Color, Create_Date, Owned_By
							   FROM Asset
							   ORDER BY Asset.id ASC"))){
	echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

if(!$stmt->bind_result($id, $Description, $Carrot, $Cut, $Clarity, $Color, $Create_Date, $Owned_By)){
	echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

while($stmt->fetch()){
	echo "<tr>\n<td>\n" . $id . "\n</td>\n<td>\n" . $Description . "\n</td>\n<td>\n" . $Carrot . "\n</td>\n<td>\n" . $Cut . "\n</td>\n<td>\n" . $Clarity . "\n</td>\n<td>\n" . $Color . "\n</td>\n<td>\n" . $Create_Date . "\n</td>\n<td>\n" . $Owned_By . "\n</td>\n</tr>";
}

$stmt->close();
?>
			</table>
		</fieldset>
	</fieldset>
    <br><br>
    <button type="button" class="button" onclick="location.href = 'http://web.engr.oregonstate.edu/~kearnsc/Diamonds/';">Return to Main Page</button>
</div>
</body>

</html>