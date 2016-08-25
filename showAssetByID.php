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
<h2>Show Asset By Asset ID</h2>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Confidential</legend>
		<fieldset class="fieldset-left">
			<table class="table_display">
				<tr>
					<td class="bold">Asset ID</td>
					<td class="bold">Contract ID</td>
					<td class="bold">Value</td>
					<td class="bold">Account ID</td>
					<td class="bold">Effective Date</td>
					<td class="bold">Last Name</td>
					<td class="bold">First Name</td>
				</tr>
<?php
if(!($stmt = $mysqli->prepare("SELECT AST.id, CA.Contract_ID, CO.Trans_at AS 'Value',
									  CO.B_Acct_ID, CO.Eff_Date, CU.Lname, CU.Fname
							   FROM Asset AST
							   INNER JOIN Contract_Asset CA ON CA.Asset_ID = AST.id
							   INNER JOIN Contract CO ON CO.id = CA.Contract_ID
							   INNER JOIN Contract_Customers CC ON CC.Contract_ID = CO.id
							   INNER JOIN Customers CU ON CU.id = CC.Customer_ID
							   WHERE AST.id = ?
							   ORDER BY CO.Eff_Date DESC LIMIT 1"))){
	echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}

if(!($stmt->bind_param("i", $_POST['Asset_ID']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

if(!$stmt->bind_result($id, $Contract_ID, $Value, $B_Acct_ID, $Eff_Date, $Lname, $Fname)){
	echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

while($stmt->fetch()){
	echo "<tr>\n<td>\n" . $id . "\n</td>\n<td>\n" . $Contract_ID . "\n</td>\n<td>\n" . "$" . $Value . "\n</td>\n<td>\n" . $B_Acct_ID . "\n</td>\n<td>\n" . $Eff_Date . "\n</td>\n<td>\n" . $Lname . "\n</td>\n<td>\n" . $Fname . "\n</td>\n</tr>";
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