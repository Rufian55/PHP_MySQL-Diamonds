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
<script src="datePrep.js"></script>
</head>

<body>
<h1>Diamond Buyers Inc.</h1>
<h2>Add New Asset</h2>
<?php // Get to be Owned_By Account ID.
if(isset($_POST['AddNewAsset'])){
	$id = $_POST['AddNewAsset'];
}
?>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Data Entry</legend>
		<fieldset class="fieldset-left">
			<form method="post">
            	<table class="ANC">
				<tr>
				<td width="100">Description:</td>
                <td width="200"><input name="Descr" type="text" id="Descr" maxlength="255" size="30" autocomplete="off" required></td>
				</tr>
				<tr>
                <td>Carrot:</td>
                <td><input name="Carrot" type="text" id="Carrot" maxlength="12" size="12" autocomplete="off" required></td>
				</tr>
                <tr>
                <td>Cut:
                <td><input name="Cut" type="text" id="Cut" maxlength="10" size="10" autocomplete="off" required></td>
                </tr>
                <tr>
				<td>Clarity:</td>
                <td><input name="Clarity" type="text" id="Clarity" maxlength="5" size="5" autocomplete="off" required></td>
				</tr>
                <tr>
                <td>Color:</td>
                <td><input name="Color" type="text" id="Color" maxlength="1" size="2" autocomplete="off" required></td>
				</tr>
				<tr>
                <td>Create Date:</td>
                <td><input name="cDate" type="date" id="cDate" maxlength="12" size="12" autocomplete="off" required></td>
				</tr>
                <tr>
				<td>Owned By:</td>
                <td><input type="hidden" name="Owned_By" value="<?php echo $id; ?>"/>
                <input type="text" name="Owned_By" id="Owned_By" size="8" value="<?php echo $id; ?>" disabled="disabled" /></td></td>
                </tr>
				<tr><td></td><td align="right"><input type="submit" value="Add Asset" name="submit" id="submit"></td></tr>
                </table>
			</form>
            <script type="text/javascript">document.getElementById('cDate').value = getTDate();</script>
		</fieldset>
	</fieldset>
	<br><br>
	<button type="button" class="button" onclick="location.href = 'http://web.engr.oregonstate.edu/~kearnsc/Diamonds/';">Return to Main Page</button>
	<br><br>
</div>

<?php
/* Form handler - Executes on 'Add Asset' submit button clicked. */
if(isset($_POST['submit'])){
	/* Prepare statement for INSERT new customer's details. */
	if(!($stmt = $mysqli->prepare("INSERT INTO Asset (Description, Carrot, Cut, Clarity, Color, Create_Date, Owned_By)
								   VALUES(?,?,?,?,?,?,?)"))) {
			echo "<p class=\"error\">Prepare for Asset INSERT query failed: "  . $stmt->errno . " " . $stmt->error . "</p>" ; 
	}

	/* Bind Parameters for INSERT new Asset's details. */
	if(!($stmt->bind_param("sdssssi", $_POST['Descr'], $_POST['Carrot'], $_POST['Cut'], $_POST['Clarity'], $_POST['Color'], $_POST['cDate'], $_POST['Owned_By']))){
	echo "<p class=\"error\">Bind failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	}

	/* Execute INSERT new Asset's details. */
	if(!$stmt->execute()){
		echo "<p class=\"error\">Execute failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	} else {
		echo "<p class=\"success\">Added " . $stmt->affected_rows . " new Asset to Asset table.</p>";
	}

	$stmt->close();
}
?>

</body>
</html>
