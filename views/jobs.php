<?php
include '../db/db.php';
include '../includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            // Gather form inputs
            $start = $_POST['StartLocation'];
            $destination = $_POST['Destination'];
            $customerID = $_POST['CustomerID'];
            $depotID = $_POST['DepotID'];
            $scheduled = $_POST['DateScheduled'];
            
            // Insert new job
            $sql = "INSERT INTO Job (StartLocation, Destination, CustomerID, DepotID, DateScheduled) 
                    OUTPUT INSERTED.JobID 
                    VALUES (?, ?, ?, ?, ?)";
            $params = array($start, $destination, $customerID, $depotID, $scheduled);
            $stmt = sqlsrv_query($conn, $sql, $params);

            // Fetch the newly created JobID
            $job = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $jobID = $job['JobID'];

            // Fetch customer category
            $customerQuery = "SELECT Category FROM Customer WHERE CustomerID = ?";
            $customerStmt = sqlsrv_query($conn, $customerQuery, array($customerID));
            $customer = sqlsrv_fetch_array($customerStmt, SQLSRV_FETCH_ASSOC);
            $category = $customer['Category'];

            // Fetch product type and load type for the job (assume one load per job)
            $loadQuery = "SELECT ProductType, LoadType FROM Loads WHERE JobID = ?";
            $loadStmt = sqlsrv_query($conn, $loadQuery, array($jobID));
            $load = sqlsrv_fetch_array($loadStmt, SQLSRV_FETCH_ASSOC);
            $productType = $load['ProductType'];
            $loadType = $load['LoadType'];

            // Calculate payment amount
            $amount = 0;

            // Add category base price
            if ($category === '1') {
                $amount += 1000;
            } elseif ($category === '2') {
                $amount += 800;
            } elseif ($category === '3') {
                $amount += 600;
            }

            // Add product type cost
            if ($productType === 'No Risk') {
                $amount += 500;
            } elseif ($productType === 'Medium Risk') {
                $amount += 1000;
            } elseif ($productType === 'High Risk') {
                $amount += 2000;
            }

            // Add load type cost
            if ($loadType === 'Small') {
                $amount += 300;
            } elseif ($loadType === 'Medium') {
                $amount += 600;
            } elseif ($loadType === 'Large') {
                $amount += 900;
            }

            // Insert payment record
            $paymentQuery = "INSERT INTO Payment (JobID, Amount, PaymentDate, PaymentStatus) 
                             VALUES (?, ?, GETDATE(), 'Pending')";
            $paymentParams = array($jobID, $amount);
            sqlsrv_query($conn, $paymentQuery, $paymentParams);
        } elseif ($action === 'delete') {
            $id = $_POST['JobID'];
            $sql = "DELETE FROM Job WHERE JobID = ?";
            $params = array($id);
            sqlsrv_query($conn, $sql, $params);
        }
    }
}

// Fetch jobs
$sql = "SELECT Job.JobID, Job.StartLocation, Job.Destination, Job.DateScheduled, Job.DateCompleted, 
               Customer.CustomerName, Depot.DepotLocation 
        FROM Job 
        INNER JOIN Customer ON Job.CustomerID = Customer.CustomerID 
        INNER JOIN Depot ON Job.DepotID = Depot.DepotID";
$stmt = sqlsrv_query($conn, $sql);

?>

<h3>Add Job</h3>
<form method="POST">
    <input type="text" name="StartLocation" placeholder="Start Location" required>
    <input type="text" name="Destination" placeholder="Destination" required>
    <select name="CustomerID" required>
        <?php
        $customerQuery = sqlsrv_query($conn, "SELECT CustomerID, CustomerName FROM Customer");
        while ($customer = sqlsrv_fetch_array($customerQuery, SQLSRV_FETCH_ASSOC)) {
            echo "<option value='{$customer['CustomerID']}'>{$customer['CustomerName']}</option>";
        }
        ?>
    </select>
    <select name="DepotID" required>
        <?php
        $depotQuery = sqlsrv_query($conn, "SELECT DepotID, DepotLocation FROM Depot");
        while ($depot = sqlsrv_fetch_array($depotQuery, SQLSRV_FETCH_ASSOC)) {
            echo "<option value='{$depot['DepotID']}'>{$depot['DepotLocation']}</option>";
        }
        ?>
    </select>
    <input type="date" name="DateScheduled" required>
    <button type="submit" name="action" value="add">Add Job</button>
</form>

<?php
echo "<h2>Jobs</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Start</th>
            <th>Destination</th>
            <th>Customer</th>
            <th>Depot</th>
            <th>Scheduled</th>
            <th>Completed</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['JobID']}</td>
            <td>{$row['StartLocation']}</td>
            <td>{$row['Destination']}</td>
            <td>{$row['CustomerName']}</td>
            <td>{$row['DepotLocation']}</td>
            <td>" . $row['DateScheduled']->format('Y-m-d') . "</td>
            <td>" . ($row['DateCompleted'] ? $row['DateCompleted']->format('Y-m-d') : 'Pending') . "</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='JobID' value='{$row['JobID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<?php include '../includes/footer.php'; ?>
