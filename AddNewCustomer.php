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
</head>

<body>
<h1>Diamond Buyers Inc.</h1>
<h2>Add New Customer</h2>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Data Entry</legend>
		<fieldset class="fieldset-left">
			<form method="post">
            	<table class="ANC">
				<tr>
				<td width="148">Last Name:</td>
                <td width="209"><input name="Lname" type="text" id="Lname" maxlength="30" size="30" autocomplete="off" required></td>
				</tr>
				<tr>
                <td>First Name:</td>
                <td><input name="Fname" type="text" id="Fname" maxlength="30" size="30" autocomplete="off" required></td>
				</tr>
                <tr>
                <td>Address 1:</td>
                <td><input name="Addr_1" type="text" id="Addr_1" maxlength="30" size="30" autocomplete="off" required></td>
                </tr>
                <tr>
				<td>Address 2:</td>
                <td><input name="Addr_2" type="text" id="Addr_2" maxlength="30" size="30" autocomplete="off" required></td>
				</tr>
                <tr>
                <td>City:</td>
                <td><input name="City" type="text" id="City" maxlength="25" size="25" autocomplete="off" required></td>
				</tr>
				<tr>
                <td>State:</td>
                <td><input name="State" type="text" id="State" maxlength="2" size="2" autocomplete="off" required></td>
				</tr>
                <tr>
				<td>Zip:</td>
                <td><input name="Zip" type="text" id="Zip" maxlength="10" size="10" autocomplete="off" required></td>
                </tr>
                <tr>
				<td>Phone:</td>
                <td><input name="Phone" type="text" id="Phone" maxlength="20" size="20" autocomplete="off" required></td>
                </tr>
				<tr>
				<td>Opening Balance:</td>
                <td><input name="Open" type="text" id="Open" maxlength="10" size="10" autocomplete="off" required value="0.00"></td>
                </tr>
				<tr><td></td><td align="right"><input type="submit" value="Add Customer" name="submit" id="submit"></td></tr>
                </table>
			</form>
		</fieldset>
	</fieldset>
	<br><br>
	<button type="button" class="button" onclick="location.href='http://web.engr.oregonstate.edu/~kearnsc/Diamonds/';">Return to Main Page</button>
	<br><br>
</div>

<?php
/* Form handler - Executes on 'Add Customer' submit button clicked. */
if(isset($_POST['submit'])){
	/* Lock tables for writing - protect integrity of new custoemr's ID retrieval. */
	if(!$mysqli->query("LOCK TABLES Customers WRITE, Account WRITE")) {
		echo "<p class=\"error\">ERROR! Tables did not lock for writing: (" . $mysqli->errno . ")" . $mysqli->error . "</p>";
	}

	/* Prepare statement for INSERT new customer's details. */
	if(!($stmt = $mysqli->prepare("INSERT INTO Customers (Lname, Fname, Addr_1, Addr_2, City, State, Zip, Phone)
								   VALUES(?,?,?,?,?,?,?,?)"))) {
			echo "<p class=\"error\">Prepare for Customers INSERT query failed: "  . $stmt->errno . " " . $stmt->error . "</p>" ; 
	}

	/* Bind Parameters for INSERT new customer's details. */
	if(!($stmt->bind_param("ssssssii", $_POST['Fname'], $_POST['Lname'], $_POST['Addr_1'], $_POST['Addr_2'], $_POST['City'], $_POST['State'], $_POST['Zip'], $_POST['Phone']))){
		echo "<p class=\"error\">Bind failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	}

	/* Execute INSERT new customer's details. */
	if(!$stmt->execute()){
		echo "<p class=\"error\">Execute failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	} else {
		echo "<p class=\"success\">Added " . $stmt->affected_rows . " new Customer to table \"Customers\".</p>";
	}

	/* Retrieve last insert ID which = new Customer ID.
	   Adapted from http://www.w3schools.com/php/php_mysql_insert_lastid.asp */
	if($temp = $mysqli->insert_id) {
		echo "<p class=\"success\">New Cusotmer ID = " . $temp . "</p>";
	} else {
    	echo "<p class=\"error\">There was an error retrieving the new Customer ID. Error: " . $mysqli->error . "</p>";
	}

	/* Prepare statement for new customer's new account. */
	if(!($stmt = $mysqli->prepare("INSERT INTO Account (C_ID, Balance) VALUES(?,?)"))) {
		echo "<p classs=\"error\">Prepare for Account INSERT query failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	}

	/* Bind Parameters for INSERT new customer's Account details. */
	if(!($stmt->bind_param("ii", $temp, $_POST['Open']))){
		echo "<p class=\"error\">Bind failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	}

	/* Execute INSERT new customer's Account details. */
	if(!$stmt->execute()){
		echo "<p class=\"error\">Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error . "</p>";
	}

	/* UNLOCK tables. */
	if(!$mysqli->query("UNLOCK TABLES")) {
		echo "<p class=\"error\">ERROR! Tables did not unlock following write: (" . $mysqli->errno . ")" . $mysqli->error . "</p>";
	}

	$stmt->close();
}
?>

</body>

</html>