<?php
//Turn on error reporting.
ini_set('display_errors', 'On');
//Connect to the database.
$mysqli = new mysqli("localhost", "user", "password", "database");
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
// Clears prepopulated form following Update Asset submit button click. 
function clearForm() {
	document.getElementById("id").value="";
	document.getElementById("Description").value="";
	document.getElementById("Carrot").value="";
	document.getElementById("Cut").value="";
	document.getElementById("Clarity").value="";
	document.getElementById("Color").value="";
	document.getElementById("cDate").value="";
	document.getElementById("Owned_By").value="";
}
</script>

</head>

<body>
<h1>Diamond Buyers Inc.</h1>
<h2>Edit An Asset</h2>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Current Confidential Asset Details</legend>
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
                    <td class="bold">Owned_By</td>
				</tr>

<?php // Get Customer records.
if(isset($_POST['EditAsset'])){
	if(!($stmt = $mysqli->prepare("SELECT id, Description, Carrot, Cut, Clarity, Color, Create_Date, Owned_By
								   FROM Asset WHERE id=?"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}

if(!($stmt->bind_param("i", $_POST['EditAsset']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

if(!$stmt->bind_result($id, $Description, $Carrot, $Cut, $Clarity, $Color, $Create_Date, $Owned_By)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

while($stmt->fetch()){
	echo "<tr>\n<td>\n" . $id . "\n</td>\n<td>\n" . $Description . "\n</td>\n<td>\n" . $Carrot . "\n</td>\n<td>\n" . $Cut . "\n</td>\n<td>\n" . $Clarity . "\n</td>\n<td>\n" . $Color . "\n</td>\n<td>\n" . $Create_Date . "\n</td>\n<td>\n" . $Owned_By . "\n</td>\n</tr>";
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
				<td>Asset ID:</td>
				<td><input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="text" name="id" id="id" size="8" value="<?php echo $id; ?>" disabled="disabled" /></td>
				</tr>
				<tr>
				<td width="148">Description:</td>
                <td width="209"><input name="Description" type="text" id="Description" maxlength="255" size="40" required value="<?php echo $Description; ?>"></td>
				</tr>
				<tr>
                <td>Carrot:</td>
                <td><input name="Carrot" type="text" id="Carrot" maxlength="30" size="30" required value="<?php echo $Carrot; ?>"></td>
				</tr>
                <tr>
                <td>Cut:</td>
                <td><input name="Cut" type="text" id="Cut" maxlength="30" size="30" required value="<?php echo $Cut; ?>"></td>
                </tr>
                <tr>
				<td>Clarity:</td>
                <td><input name="Clarity" type="text" id="Clarity" maxlength="30" size="30" required value="<?php echo $Clarity; ?>"></td>
				</tr>
                <tr>
				<td>Color:</td>
                <td><input name="Color" type="text" id="Color" maxlength="25" size="25" required value="<?php echo $Color; ?>"></td>
				</tr>
				<tr>
                <td>Create Date:</td>
                <td><input name="cDate" type="date" id="cDate" required value="<?php echo $Create_Date; ?>"></td>
				</tr>
                <tr>
				<td>Owned by:</td>
                <td><input type="hidden" name="Owned_By" value="<?php echo $Owned_By; ?>"/>
                <input type="text" name="Owned_By" id="Owned_By" size="8" value="<?php echo $Owned_By; ?>" disabled="disabled" /></td>
                </tr>
				<tr><td></td><td align="right"><input type="submit" value="Update Asset" name="submit" id="submit"></td></tr>
                </table>
			</form>
		</fieldset>
	</fieldset>
	<br><br>
	<button type="button" class="button" onclick="location.href = 'http://web.engr.oregonstate.edu/~kearnsc/Diamonds/';">Return to Main Page</button>
	<br><br>
</div>
<?php 
/* Form handler - Executes on 'Update Asset' submit button clicked. */
if(isset($_POST['submit'])){
	/* Prepare statement for UPDATE Asset's details. */
	if(!($stmt = $mysqli->prepare("UPDATE Asset SET Description=?, Carrot=?, Cut=?, Clarity=?, Color=?, Create_Date=?
								   WHERE id=?"))) {
		echo "<p class=\"error\">Prepare for Customers UPDATE query failed: "  . $stmt->errno . " " . $stmt->error . "</p>" ; 
	}

	/* Bind Parameters for UPDATE Asset's details. */
	if(!($stmt->bind_param("sissssi", $_POST['Description'], $_POST['Carrot'], $_POST['Cut'], $_POST['Clarity'], $_POST['Color'], $_POST['cDate'], $_POST['id']))){
	echo "<p class=\"error\">Bind failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	}

	/* Execute UPDATE Asset's details. */
	if(!$stmt->execute()){
		echo "<p class=\"error\">Execute failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	} else {
		echo "<p class=\"success\">Updated " . $stmt->affected_rows . " rows in table \"Asset\".</p>";
		echo "<p class=\"success\">Form has been cleared.</p>";
	}
	// Clear the form!
	echo "<script type=\"text/javascript\">clearForm();</script>";
}
?>

</body>
</html>
