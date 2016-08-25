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
// Clears prepopulated form following Update Customer submit button click. 
function clearForm() {
	document.getElementById("Lname").value="";
	document.getElementById("Fname").value="";
	document.getElementById("Addr_1").value="";
	document.getElementById("Addr_2").value="";
	document.getElementById("City").value="";
	document.getElementById("State").value="";
	document.getElementById("Zip").value="";
	document.getElementById("Phone").value="";
	document.getElementById("id").value="";
}
</script>

</head>

<body>
<h1>Diamond Buyers Inc.</h1>
<h2>Update Customer Details</h2>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Current Confidential Customer Details</legend>
		<fieldset class="fieldset-left">
			<table class="table_display">
				<tr>
					<td class="bold">Customer ID</td>
            		<td class="bold">Last Name</td>
            		<td class="bold">First Name</td>
            		<td class="bold">Address_1</td>
            		<td class="bold">Address_2</td>
            		<td class="bold">City</td>
                    <td class="bold">State</td>
            		<td class="bold">Zip</td>
            		<td class="bold">Phone</td>
				</tr>

<?php // Get Customer records.
if(isset($_POST['updateCustomer'])){
	if(!($stmt = $mysqli->prepare("SELECT id, Lname, Fname, Addr_1, Addr_2, City, State, Zip, Phone
								   FROM Customers WHERE id=?"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}

if(!($stmt->bind_param("i", $_POST['updateCustomer']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

if(!$stmt->bind_result($id, $Lname, $Fname, $Addr_1, $Addr_2, $City, $State, $Zip, $Phone)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

while($stmt->fetch()){
	echo "<tr>\n<td>\n" . $id . "\n</td>\n<td>\n" . $Lname . "\n</td>\n<td>\n" . $Fname . "\n</td>\n<td>\n" . $Addr_1 . "\n</td>\n<td>\n" . $Addr_2 . "\n</td>\n<td>\n" . $City . "\n</td>\n<td>\n" . $State . "\n</td>\n<td>\n" . $Zip . "\n</td>\n<td>\n" . $Phone . "\n</td>\n</tr>";
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
				<td>Cusotmer ID:</td>
				<td><input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="text" name="id" id="id" size="8" value="<?php echo $id; ?>" disabled="disabled" /></td>
				</tr>
				<tr>
				<td width="148">Last Name:</td>
                <td width="209"><input name="Lname" type="text" id="Lname" maxlength="30" size="30" required value="<?php echo $Lname; ?>"></td>
				</tr>
				<tr>
                <td>First Name:</td>
                <td><input name="Fname" type="text" id="Fname" maxlength="30" size="30" required value="<?php echo $Fname; ?>"></td>
				</tr>
                <tr>
                <td>Address 1:</td>
                <td><input name="Addr_1" type="text" id="Addr_1" maxlength="30" size="30" required value="<?php echo $Addr_1; ?>"></td>
                </tr>
                <tr>
				<td>Address 2:</td>
                <td><input name="Addr_2" type="text" id="Addr_2" maxlength="30" size="30" value="<?php echo $Addr_2; ?>"></td>
				</tr>
                <tr>
                <td>City:</td>
                <td><input name="City" type="text" id="City" maxlength="25" size="25" required value="<?php echo $City; ?>"></td>
				</tr>
				<tr>
                <td>State:</td>
                <td><input name="State" type="text" id="State" maxlength="2" size="2" required value="<?php echo $State; ?>"></td>
				</tr>
                <tr>
				<td>Zip:</td>
                <td><input name="Zip" type="text" id="Zip" maxlength="10" size="10" required value="<?php echo $Zip; ?>"></td>
                </tr>
                <tr>
				<td>Phone:</td>
                <td><input name="Phone" type="text" id="Phone" maxlength="20" size="20" required value="<?php echo $Phone; ?>"></td>
                </tr>
				<tr><td></td><td align="right"><input type="submit" value="Update Customer" name="submit" id="submit"></td></tr>
                </table>
			</form>
		</fieldset>
	</fieldset>
	<br><br>
	<button type="button" class="button" onclick="location.href = 'http://web.engr.oregonstate.edu/~kearnsc/Diamonds/';">Return to Main Page</button>
	<br><br>
</div>

<?php 
/* Form handler - Executes on 'Update Customer' submit button clicked. */
if(isset($_POST['submit'])){
	/* Prepare statement for UPDATE customer's details. */
	if(!($stmt = $mysqli->prepare("UPDATE Customers SET Lname=?, Fname=?, Addr_1=?, Addr_2=?, City=?, State=?, Zip=?, Phone=?
								   WHERE id=?"))) {
		echo "<p class=\"error\">Prepare for Customers UPDATE query failed: "  . $stmt->errno . " " . $stmt->error . "</p>" ; 
	}

	/* Bind Parameters for INSERT new customer's details. */
	if(!($stmt->bind_param("ssssssiii", $_POST['Fname'], $_POST['Lname'], $_POST['Addr_1'], $_POST['Addr_2'], $_POST['City'], $_POST['State'], $_POST['Zip'], $_POST['Phone'], $_POST['id']))){
	echo "<p class=\"error\">Bind failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	}

	/* Execute INSERT new customer's details. */
	if(!$stmt->execute()){
		echo "<p class=\"error\">Execute failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
	} else {
		echo "<p class=\"success\">Updated " . $stmt->affected_rows . " rows in Customer table \"Customers\".</p>";
		echo "<p class=\"success\">Form has been cleared.</p>";
	}
	// Clear the form!
	echo "<script type=\"text/javascript\">clearForm();</script>";
}
?>

</body>

</html>
