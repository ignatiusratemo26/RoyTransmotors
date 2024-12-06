<?php
include '../db/db.php';
include '../includes/header.php';
?>

<h2>Reports</h2>

<!-- Job Details Report -->
<h3>Job Details with Payment and Customer Information</h3>
<?php
$sql = "
    SELECT 
        Job.JobID, 
        Job.StartLocation, 
        Job.Destination, 
        Job.DateScheduled, 
        Job.DateCompleted,
        Customer.CustomerName, 
        Payment.Amount AS PaymentAmount, 
        Payment.PaymentDate 
    FROM 
        Job
    INNER JOIN 
        Customer ON Job.CustomerID = Customer.CustomerID
    LEFT JOIN 
        Payment ON Job.JobID = Payment.JobID
    ORDER BY 
        Job.DateScheduled DESC
";
$stmt = sqlsrv_query($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>Job ID</th>
            <th>Start Location</th>
            <th>Destination</th>
            <th>Scheduled Date</th>
            <th>Completion Date</th>
            <th>Customer</th>
            <th>Payment Amount</th>
            <th>Payment Date</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['JobID']}</td>
            <td>{$row['StartLocation']}</td>
            <td>{$row['Destination']}</td>
            <td>{$row['DateScheduled']->format('Y-m-d')}</td>
            <td>" . ($row['DateCompleted'] ? $row['DateCompleted']->format('Y-m-d') : 'Pending') . "</td>
            <td>{$row['CustomerName']}</td>
            <td>" . ($row['PaymentAmount'] ? '$' . number_format($row['PaymentAmount'], 2) : 'N/A') . "</td>
            <td>" . ($row['PaymentDate'] ? $row['PaymentDate']->format('Y-m-d') : 'N/A') . "</td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<!-- Transport Unit Utilization Report -->
<h3>Transport Unit Utilization</h3>
<?php
$sql = "
    SELECT 
        TransportUnit.TransportUnitID, 
        Lorry.RegistrationNumber AS LorryReg,
        Driver.DriverName, 
        Assistant.AssistantName, 
        COUNT(Loads.LoadID) AS JobsHandled
    FROM 
        TransportUnit
    LEFT JOIN 
        Lorry ON TransportUnit.LorryID = Lorry.LorryID
    LEFT JOIN 
        Driver ON TransportUnit.DriverID = Driver.DriverID
    LEFT JOIN 
        Assistant ON TransportUnit.AssistantID = Assistant.AssistantID
    LEFT JOIN 
        Loads ON TransportUnit.TransportUnitID = Loads.TransportUnitID
    GROUP BY 
        TransportUnit.TransportUnitID, 
        Lorry.RegistrationNumber, 
        Driver.DriverName, 
        Assistant.AssistantName
    ORDER BY 
        JobsHandled DESC
";
$stmt = sqlsrv_query($conn, $sql);

echo "<table border='1'>
        <tr>
            <th>Transport Unit ID</th>
            <th>Lorry Registration</th>
            <th>Driver Name</th>
            <th>Assistant Name</th>
            <th>Jobs Handled</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['TransportUnitID']}</td>
            <td>{$row['LorryReg']}</td>
            <td>{$row['DriverName']}</td>
            <td>{$row['AssistantName']}</td>
            <td>{$row['JobsHandled']}</td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<!-- Summary of Payments -->
<h3>Payment Summary</h3>
<?php
$sql = "
    SELECT 
        SUM(Amount) AS TotalPayments, 
        COUNT(PaymentID) AS PaymentCount, 
        MIN(PaymentDate) AS FirstPaymentDate, 
        MAX(PaymentDate) AS LastPaymentDate
    FROM 
        Payment
";
$stmt = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

echo "<table border='1'>
        <tr>
            <th>Total Payments</th>
            <th>Number of Payments</th>
            <th>First Payment Date</th>
            <th>Last Payment Date</th>
        </tr>
        <tr>
            <td>Ksh" . number_format($row['TotalPayments'], 2) . "</td>
            <td>{$row['PaymentCount']}</td>
            <td>{$row['FirstPaymentDate']->format('Y-m-d')}</td>
            <td>{$row['LastPaymentDate']->format('Y-m-d')}</td>
        </tr>
    </table>";
sqlsrv_free_stmt($stmt);
?>

<?php include '../includes/footer.php'; ?>
