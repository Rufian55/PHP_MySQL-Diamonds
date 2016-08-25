<?php
//Turn on error reporting.
ini_set('display_errors', 'On');
//Connect to the database.
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

<script type="text/javascript">
// Clears prepopulated form following Update Customer submit button click. 
function clearForm() {
	document.getElementById("C_ID").value="";
	document.getElementById("Balance").value="";
	document.getElementById("id").value="";
}
</script>
</head>

<body>
<h1>Diamond Buyers Inc.</h1>
<h2>Update Customer Balance</h2>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Current Confidential Customer Balance</legend>
		<fieldset class="fieldset-left">
			<table class="table_display">
				<tr>
					<td class="bold">Account ID</td>
					<td class="bold">Customer ID</td>
					<td class="bold">Balance</td>
				</tr>

<?php // Get Customer records.

if(isset($_POST['updateBalance'])){
	if(!($stmt = $mysqli->prepare("SELECT id, C_ID, Balance FROM Account WHERE id=?"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}

if(!($stmt->bind_param("i", $_POST['updateBalance']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

if(!$stmt->bind_result($id, $C_ID, $Balance)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

while($stmt->fetch()){
	echo "<tr>\n<td>\n" . $id . "\n</td>\n<td>\n" . $C_ID . "\n</td>\n<td>\n" . $Balance . "\n</td>\n</tr>";
	}

$stmt->close();
}
?>
			</table>
		</fieldset>
	</fieldset>
</div>
<br><br>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Make Corrections</legend>
		<fieldset class="fieldset-left">
			<form method="post">
            	<table class="ANC">
				<tr>
				<td>Account ID:</td>
				<td><input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="text" name="id" id="id" size="8" value="<?php echo $id; ?>" disabled="disabled" /></td>
				</tr>
				<tr>
				<td>Customer ID:</td>
				<td><input type="hidden" name="C_ID" value="<?php echo $C_ID; ?>"/>
				<input type="text" name="C_ID" id="C_ID" size="8" value="<?php echo $C_ID; ?>" disabled="disabled" /></td>
				</tr>
				<tr>
                <tr>
				<td>Balance:</td>
                <td><input name="Balance" type="text" id="Balance" maxlength="12" size="10" required value="<?php echo $Balance; ?>"></td>
                </tr>
				<tr><td></td><td align="right"><input type="submit" value="Update Balance" name="submit" id="submit"></td></tr>
                </table>
			</form>
		</fieldset>
	</fieldset>
	<br><br>
	<button type="button" class="button" onclick="location.href = 'http://web.engr.oregonstate.edu/~kearnsc/Diamonds/';">Return to Main Page</button>
	<br><br>
</div>

<?php 
/* Form handler - Executes on 'Update Balance' submit button clicked. */
if(isset($_POST['submit'])){
	// Prepare statement for UPDATE customer's details.
	if(!($stmt = $mysqli->prepare("UPDATE Account SET Balance=? WHERE id=?"))) {
		echo "<p class=\"error\">Prepare for Account Balance UPDATE query failed: " . $stmt->errno . " " . $stmt->error . "</p>"; 
	}

	// Bind Parameters for UPDATE customer's Balance.
	if(!($stmt->bind_param("di", $_POST['Balance'], $_POST['id']))){
	echo "<p class=\"error\">Bind failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	}

	// Execute UPDATE customer's Balance.
	if(!$stmt->execute()){
		echo "<p class=\"error\">Execute failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	} else {
		echo "<p class=\"success\">Updated " . $stmt->affected_rows . " row (Balance) in customer's \"Account\".</p>";
		echo "<p class=\"success\">Form has been cleared.</p>";
	}
	// Clear the form!
	echo "<script type=\"text/javascript\">clearForm();</script>";
}
?>

</body>

</html>