-- BlockChain Queries file Blockchain-queries.sql
-- ******DO NOT IMPORT THIS FILE. FOR REFERENCE ONLY.********
-- Cut and paste queries to phpMyAdmin as needed for testing.

-- Select accoount details and balance for all accounts.
SELECT A.id AS Account, A.Balance, Cu.id AS 'Cust_ID', Cu.Lname, Cu.Fname, Cu.Addr_1, Cu.Addr_2, Cu.City, Cu.State, Cu.Zip, Cu.Phone 
FROM Customers Cu
INNER JOIN Account A
ON Cu.id = A.C_ID;

-- Select account details and balance for account by account id.
SELECT A.id AS 'Account', A.Balance, Cu.id AS 'Cust_ID', Cu.Lname, Cu.Fname, Cu.Addr_1, Cu.Addr_2, Cu.City, Cu.State, Cu.Zip, Cu.Phone 
FROM Customers Cu
INNER JOIN Account A
ON Cu.id = A.C_ID
WHERE A.id = 396402;

-- Select account details and balance for account by customer id.
SELECT Cu.id, A.id AS Account, A.Balance, Cu.Lname, Cu.Fname, Cu.Addr_1, Cu.Addr_2, Cu.City, Cu.State, Cu.Zip, Cu.Phone 
FROM Customers Cu
INNER JOIN Account A
ON Cu.id = A.C_ID
WHERE Cu.id = 200004;

-- Select account details and balance for account by customer phone.
SELECT Cu.id AS Cust_ID, A.id AS 'Account #', A.Balance, Cu.Lname, Cu.Fname, Cu.Addr_1, Cu.Addr_2, Cu.City, Cu.State, Cu.Zip, Cu.Phone 
FROM Customers Cu
INNER JOIN Account A
ON Cu.id = A.C_ID
WHERE Cu.phone = 7275551234;

-- Record a BlockChain Transaction.
LOCK TABLES Ledger WRITE, Contract WRITE, Contract_Asset WRITE, Contract_Customers WRITE, Asset WRITE;
INSERT INTO Ledger(date_time) VALUES (NOW());
SET @last_id_in_Ledger = LAST_INSERT_ID();
INSERT INTO Contract (Asset_ID, B_Acct_ID, S_Acct_ID, Eff_Date, Trans_at, Com_pd, L_ID)
	VALUES(
	500004,
	396402,
	396404,
	'2016-07-23 14:09:20',
	17000.20,
	4500.00,
	@last_id_in_Ledger
);
SET @last_id_in_Contract = LAST_INSERT_ID();
INSERT INTO Contract_Asset VALUES(
	@last_id_in_Contract,
	(SELECT Asset_ID FROM Contract WHERE id = @last_id_in_Contract)); 
INSERT INTO Contract_Customers VALUES(
	@last_id_in_Contract,
	200002);
INSERT INTO Contract_Customers VALUES(
	@last_id_in_Contract,
	200004);
UPDATE Asset SET Owned_By = 396402 WHERE Asset.id = 500004;
UNLOCK TABLES;

SELECT * FROM Contract_Asset;

SELECT * FROM Contract_Customers
ORDER BY Contract_ID, Customer_ID;

SELECT * FROM Asset
ORDER BY Asset.id DESC;

-- Who owns an Asset?
SELECT AST.id, CA.Contract_ID, CO.Trans_at AS 'Value', CO.B_Acct_ID, CO.Eff_Date, CU.Lname, CU.Fname FROM Asset AST
INNER JOIN Contract_Asset CA ON CA.Asset_ID = AST.id
INNER JOIN Contract CO ON CO.id = CA.Contract_ID
INNER JOIN Contract_Customers CC ON CC.Contract_ID = CO.id
INNER JOIN Customers CU ON CU.id = CC.Customer_ID
WHERE AST.id = 500004
ORDER BY CO.Eff_Date DESC LIMIT 1

-- Asset details by Account?
SELECT * FROM Asset
WHERE Owned_By = 396402
ORDER BY Asset.id DESC;

-- Total Transactions and Commisions Paid by DateTime
SELECT SUM(Trans_at) AS 'Contracted', SUM(Com_pd) AS 'Total Commissions' FROM Contract
WHERE Eff_Date > '2011-11-11 00:00:00' AND Eff_Date < '2016-07-24 00:00:00';

-- Add New Customer and Account.
LOCK TABLES Customers WRITE, Account WRITE;
INSERT INTO Customers (Lname, Fname, Addr_1, Addr_2, City, State, Zip, Phone)
VALUES
('Szell', 'Christian', '23 Hideaway Ln.', 'Apt. 13', 'New York', 'NY', 10019, 2125557541);
SET @last_id_in_Customers = LAST_INSERT_ID();
INSERT INTO Account (C_ID, Balance)
VALUES
(@last_id_in_Customers, 905245);
UNLOCK TABLES;

-- Add New Asset.
INSERT INTO Asset (Description, Carrot, Cut, Clarity, Color, Create_Date, Owned_By)
VALUES
('Diamond', 2.55, 'Princess', 'V1', 'E', '2016-07-22 13:39:39', 369400);

-- Update Customers
UPDATE Customers
SET Lname = 'Jones',
Fname = 'Samuel',
Addr_1 = '23 Nardo Ave.',
Addr_2 = 'Apt 16',
City = 'San Jose',
State = 'CA',
Zip = 998690000,
Phone = 8546524512
WHERE Customers.id = 200004;

-- Record a BlockChain Transaction  with full balance adjustments.
LOCK TABLES Ledger WRITE, Contract WRITE, Contract_Asset WRITE, Contract_Customers WRITE, Asset WRITE, Account WRITE;
INSERT INTO Ledger(date_time) VALUES (NOW());
SET @last_id_in_Ledger = LAST_INSERT_ID();
INSERT INTO Contract (Asset_ID, B_Acct_ID, S_Acct_ID, Eff_Date, Trans_at, Com_pd, L_ID)
	VALUES(
	500008,
	396405,
	396407,
	'2016-07-23 14:09:20',
	1000.69,
	100.00,
	@last_id_in_Ledger
);
SET @last_id_in_Contract = LAST_INSERT_ID();
INSERT INTO Contract_Asset VALUES(
	@last_id_in_Contract,
	(SELECT Asset_ID FROM Contract WHERE id = @last_id_in_Contract)); 
INSERT INTO Contract_Customers VALUES(
	@last_id_in_Contract,
	200005);
INSERT INTO Contract_Customers VALUES(
	@last_id_in_Contract,
	200007);
UPDATE Asset SET Owned_By = 396405 WHERE Asset.id = 500008;
UPDATE Account SET Balance = Balance - (1000.69 + 100.00/2) WHERE id = 396405;
UPDATE Account SET Balance = Balance + (1000.69 - 100.00/2) WHERE id = 396407;
UNLOCK TABLES;

-- General Ledger query.
SELECT L.id, L.date_time, CO.id, CO.Asset_ID, CO.Trans_at, CO.Com_pd, AST.Description, AST.Owned_By FROM Ledger L
INNER JOIN Contract CO ON CO.L_ID = L.id
INNER JOIN Contract_Asset CA ON CA.Contract_ID = CO.id
INNER JOIN Asset AST ON AST.id = CA.Asset_ID
ORDER BY L.id ASC;

-- Show Asset ID Ownership history, aka Ownership chain.
SELECT L.id, L.date_time, CO.id as ID, CO.Asset_ID, CO.Trans_at, CO.Com_pd, AST.Description, AST.Owned_By
FROM Ledger L
INNER JOIN Contract CO ON CO.L_ID = L.id
INNER JOIN Contract_Asset CA ON CA.Contract_ID = CO.id
INNER JOIN Asset AST ON AST.id = CA.Asset_ID
WHERE CO.Asset_ID = 500004
ORDER BY L.id ASC