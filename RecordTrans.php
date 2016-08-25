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
</head>

<body>
<h1>Diamond Buyers Inc.</h1>
<h2>Record A Transaction</h2>
<div>
	<fieldset class="fieldset-auto-width">
	<legend>Data Entry</legend>
		<fieldset class="fieldset-left">
			<form method="post">
            	<table class="ANC">
				<tr>
				<td width="148">Asset ID:</td>
				<td width="209"><?php
					echo '<select name="Asset_ID">';
					if(!($stmt = $mysqli->prepare("SELECT id, Owned_By FROM Asset ORDER BY id ASC"))){
						echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
					}
					if(!$stmt->execute()){
						echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					if(!$stmt->bind_result($id, $Owned_By)){
						echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					while($stmt->fetch()){
						echo '<option value=" '. $id . ' "> ' . $id . '</option>\n';
					}
					$stmt->close();
					echo '</select>';
					$Seller
					?></td>
				</tr>
				<tr>
                <td>Buyer's Account ID:</td>
                <td><?php
					echo '<select name="B_Acct_ID">';
					if(!($stmt = $mysqli->prepare("SELECT id FROM Account ORDER BY id ASC"))){
						echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
					}
					if(!$stmt->execute()){
						echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					if(!$stmt->bind_result($id)){
						echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
					}
					while($stmt->fetch()){
						echo '<option value=" '. $id . ' "> ' . $id . '</option>\n';
					}
					$stmt->close();
					echo '</select>';
					?></td>
				</tr>
                <tr>
				<td>Effective Date</td>
                <td><input name="Eff_Date" type="date" id="Eff_Date" value="<?php echo date("Y-m-d"); ?>" required></td>
				</tr>
                <tr>
                <td>Transacted At:</td>
                <td><input name="Trans_at" type="number" step="any" id="Trans_at" maxlength="9" min="0" required></td>
				</tr>
				<tr>
                <td>Commission Paid:</td>
                <td><input name="Com_pd" type="number" step="any" id="Com_pd" maxlength="9" min="0" required></td>
				</tr>
				<tr><td></td><td align="right"><input type="submit" value="Record Transaction" name="submit" id="submit"></td></tr>
                </table>
			</form>
		</fieldset>
	</fieldset>
	<br><br>
	<button type="button" class="button" onclick="location.href='http://web.engr.oregonstate.edu/~kearnsc/Diamonds/';">Return to Main Page</button>
	<br><br>
</div>

<?php
// Form handler - Executes on 'Record Transaction' submit button clicked.
if(isset($_POST['submit'])){

/**	1. Determine Seller's Account ID from Asset ID being sold. */
	if(!($stmt = $mysqli->prepare("SELECT Owned_By FROM Asset WHERE id = ?"))){
	echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
	}
	if(!($stmt->bind_param("i", $_POST['Asset_ID']))){
		echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
	}
	if(!$stmt->execute()){
		echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
	if(!$stmt->bind_result($Owned_By)){
		echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
	// Our Seller's Account ID.
 	while($stmt->fetch()){
		$S_Acct_ID = $Owned_By;
	}
	echo "<p class=\"success\">Seller's Account: " . $S_Acct_ID . " | Buyer's Account: " . $_POST['B_Acct_ID'] . "</p>";
	$stmt->close();


/** 2. Check for Buyer's Account NOT EQUAL to Seller's Account and process tranasaction. */
	if($_POST['B_Acct_ID'] != $S_Acct_ID) {

		// Lock tables for writing - protect integrity of new Ledger ID retrieval.
		if(!$mysqli->query("LOCK TABLES Ledger WRITE, Contract WRITE, Contract_Asset WRITE,
								Contract_Customers WRITE, Asset WRITE, Account WRITE")) {
			echo "<p class=\"error\">ERROR! Tables did not lock for writing: (" . $mysqli->errno . ")" . $mysqli->error . "</p>";
		}


/******	3. Make Ledger entry and retrieve ID. */
		// Prepare statement for INSERT new Ledger record.
		if(!($stmt = $mysqli->query("INSERT INTO Ledger (date_time) VALUES(NOW())"))) {
			echo "<p class=\"error\">Ledger INSERT timestamp query failed: "  . $stmt->errno . " " . $stmt->error . "</p>" ; 
		}
		// Retrieve last insert ID which = new Ledger ID.
		// Adapted from http://www.w3schools.com/php/php_mysql_insert_lastid.asp
		$temp = $mysqli->insert_id;
		echo "<p class=\"success\">New Ledger ID = " . $temp . "</p>";


/******	4. Record Contract details and retrieve new Contract record ID. */

		// Prepare statement for new Contract record.
		if(!($stmt = $mysqli->prepare("INSERT INTO Contract (Asset_ID, B_Acct_ID, S_Acct_ID, Eff_Date, Trans_at, Com_pd, L_ID)
									   VALUES(?,?,?,?,?,?,?)"))) {
			echo "<p classs=\"error\">Prepare for Contract INSERT query failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Bind Parameters for INSERT new Contract details.
		if(!($stmt->bind_param("iiisddi", $_POST['Asset_ID'], $_POST['B_Acct_ID'], $S_Acct_ID, $_POST['Eff_Date'], $_POST['Trans_at'], $_POST['Com_pd'], $temp))){
			echo "<p class=\"error\">Bind 4 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Execute INSERT new Contract details.
		if(!$stmt->execute()){
			echo "<p class=\"error\">Execute 4 failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error . "</p>";
		}
		// Retrieve last insert ID which = new Contract ID.
		$temp = $mysqli->insert_id;
		echo "<p class=\"success\">Last Contract insert_id, a.k.a. New Contract ID = " . $temp . "</p>";


/******	5. Add Contract_Asset junction table record. */

		// Prepare statement for new Contract_Asset record.
		if(!($stmt = $mysqli->prepare("INSERT INTO Contract_Asset (Contract_ID, Asset_ID) VALUES(?,?)"))) {
			echo "<p classs=\"error\">Prepare for Contract_Asset INSERT query failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Bind Parameters for INSERT new Contract_Asset details.
		if(!($stmt->bind_param("ii", $temp, $_POST['Asset_ID']))){
			echo "<p class=\"error\">Bind 5 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Execute INSERT new Contract_Asset details.
		if(!$stmt->execute()){
			echo "<p class=\"error\">Execute 5 failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error . "</p>";
		} else {
			echo "<p class=\"success\">Contract_Asset junction table updated successfully.</p>"; 
		}


/******	6. Add Buyer's Contract_Customer junction table record. */

		// Prepare statement for Contract_Customers Buyer's record.
		if(!($stmt = $mysqli->prepare("INSERT INTO Contract_Customers (Contract_ID, Customer_ID)
									   VALUES(?,(SELECT C_ID FROM Account WHERE id = ?))"))) {
			echo "<p classs=\"error\">Prepare for Contract_Asset INSERT query failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Bind Parameters for INSERT new Contract_Customer Buyer's record.
		if(!($stmt->bind_param("ii", $temp, $_POST['B_Acct_ID']))){
			echo "<p class=\"error\">Bind 6 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Execute INSERT new Contract_Customer Record A.
		if(!$stmt->execute()){
			echo "<p class=\"error\">Execute 6 failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error . "</p>";
		}


/*****	7. Add Seller's Contract_Customer junction table record. */

		// Prepare statement for Contract_Customers Seller's record.
		if(!($stmt = $mysqli->prepare("INSERT INTO Contract_Customers (Contract_ID, Customer_ID)
									   VALUES(?,(SELECT C_ID FROM Account WHERE id = ?))"))) {
			echo "<p classs=\"error\">Prepare for Contract_Asset INSERT query failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Bind Parameters for INSERT new Contract_Customer Seller's record.
		if(!($stmt->bind_param("ii", $temp, $S_Acct_ID))){
			echo "<p class=\"error\">Bind 7 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Execute INSERT new Contract_Customer Seller's Record.
		if(!$stmt->execute()){
			echo "<p class=\"error\">Execute 7 failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error . "</p>";
		} else {
			echo "<p class=\"success\">Contract_Customer junction table updated successfully.</p>";
		}


/******	8. Update Asset Ownership account. */

		// Prepare statement for UPDATE Asset's details.
		if(!($stmt = $mysqli->prepare("UPDATE Asset SET Owned_By=? WHERE id=?"))) {
			echo "<p class=\"error\">Prepare for Asset UPDATE query failed: "  . $stmt->errno . " " . $stmt->error . "</p>" ; 
		}
		// Bind Parameters for UPDATE Asset's details.
		if(!($stmt->bind_param("ii", $_POST['B_Acct_ID'], $_POST['Asset_ID']))){
			echo "<p class=\"error\">Bind 8 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Execute UPDATE Asset's ownership record.
		if(!$stmt->execute()){
			echo "<p class=\"error\">Execute 8 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		} else {
			echo "<p class=\"success\">Updated " . $stmt->affected_rows . " row in Asset table.</p>";
		}


/****** 9. Retrieve current Buyer's account Balance and update it with tranasction details. */

		// Prepare statement for Buyer's account balance query.
		if(!($stmt = $mysqli->prepare("SELECT Balance FROM Account WHERE id=?"))){
			echo "Prepare 9 failed: "  . $stmt->errno . " " . $stmt->error;
		}
		// Bind Parameters for INSERT new Contract_Customer record B.
		if(!($stmt->bind_param("i", $_POST['B_Acct_ID']))){
			echo "<p class=\"error\">Bind_param 9 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		if(!$stmt->execute()){
			echo "Execute 9 failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($Balance)){
			echo "Bind_result 9 failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		// Our Buyer's current account balance.
		while($stmt->fetch()){
			$B_Balance = $Balance;
		}
		$stmt->close();

		// Calculate new Buyer's Account balance less transaction cost and 1/2 of the commission.
		$B_Balance = $B_Balance - ($_POST['Trans_at'] + ($_POST['Com_pd'] / 2));

		// Prepare statement for UPDATE Buyer's Account Balance.
		if(!($stmt = $mysqli->prepare("UPDATE Account SET Balance=? WHERE id=?"))) {
			echo "<p class=\"error\">Prepare for Account Balance UPDATE query failed: " . $stmt->errno . " " . $stmt->error . "</p>"; 
		}
		// Bind Parameters for UPDATE buyer's account balance.
		if(!($stmt->bind_param("di", $B_Balance, $_POST['B_Acct_ID']))){
			echo "<p class=\"error\">Bind for Update Buyer's account (9) failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Execute UPDATE Buyer's account balance.
		if(!$stmt->execute()){
			echo "<p class=\"error\">Execute for Update Buyer's account (9) failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		} else {
			echo "<p class=\"success\">Updated " . $stmt->affected_rows . " row (Balance) in Buyer's Account.</p>";
		}


/****** 10. Retrieve current Seller's account Balance and update it with tranasction details. */

		// Prepare statement for Seller's account balance query.
		if(!($stmt = $mysqli->prepare("SELECT Balance FROM Account WHERE id=?"))){
			echo "Prepare 10 failed: "  . $stmt->errno . " " . $stmt->error;
		}
		// Bind Parameters for Seller's Contract_Customer record query.
		if(!($stmt->bind_param("i", $S_Acct_ID))){
			echo "<p class=\"error\">Bind_param 10 failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		if(!$stmt->execute()){
			echo "Execute 10 failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($Balance)){
			echo "Bind-result 10 failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		// Our Seller's curretn account balance.
		while($stmt->fetch()){
			$S_Balance = $Balance;
		}
		$stmt->close();

		// Calculate new Seller's Account balance less transaction cost and 1/2 of the commission.
		$S_Balance = $S_Balance + ($_POST['Trans_at'] - ($_POST['Com_pd'] / 2));

		// Prepare statement for UPDATE Seller's Account Balance.
		if(!($stmt = $mysqli->prepare("UPDATE Account SET Balance=? WHERE id=?"))) {
			echo "<p class=\"error\">Prepare for Account Balance UPDATE query failed: " . $stmt->errno . " " . $stmt->error . "</p>"; 
		}
		// Bind Parameters for UPDATE Seller's account balance.
		if(!($stmt->bind_param("di", $S_Balance, $S_Acct_ID))){
			echo "<p class=\"error\">Bind for update Seller's account failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		}
		// Execute UPDATE Seller's account balance.
		if(!$stmt->execute()){
			echo "<p class=\"error\">Execute Update Seller's account failed: "  . $stmt->errno . " " . $stmt->error . "</p>";
		} else {
			echo "<p class=\"success\">Updated " . $stmt->affected_rows . " row (Balance) in Seller's Account.</p>";
		}
		// UNLOCK tables.
		if(!$mysqli->query("UNLOCK TABLES")) {
			echo "<p class=\"error\">ERROR! Tables did not unlock following write: (" . $mysqli->errno . ")" . $mysqli->error . "</p>";
		}
		$stmt->close();


	} else {echo "<p class=\"error\">Buyer's Account ID and Seller's Account ID cannot be the same!</p>";}
}
?>
</body>
</html>
