<?php
include '../db/db.php';
include '../includes/header.php';

// Define payment factors
$customerTypeFactors = [
    'Regular' => 1.0,
    'Premium' => 1.5
];

$productTypeFactors = [
    'No Risk' => 1.0,
    'Medium Risk' => 1.2,
    'High Risk' => 1.5
];

$loadTypeFactors = [
    'Small' => 1.0,
    'Medium' => 1.3,
    'Large' => 1.5
];

// Function to calculate payment amount
function calculatePaymentAmount($customerType, $productType, $loadType) {
    global $customerTypeFactors, $productTypeFactors, $loadTypeFactors;
    return $customerTypeFactors[$customerType] * $productTypeFactors[$productType] * $loadTypeFactors[$loadType] * 100; // Example base amount
}

// Handle form submissions for add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $jobID = $_POST['JobID'];
        $customerType = $_POST['CustomerType'];
        $productType = $_POST['ProductType'];
        $loadType = $_POST['LoadType'];
        $amount = calculatePaymentAmount($customerType, $productType, $loadType);
        $paymentDate = date('Y-m-d');

        $sql = "INSERT INTO Payment (JobID, Amount, PaymentDate) VALUES (?, ?, ?)";
        $params = array($jobID, $amount, $paymentDate);
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}

$sql = "SELECT * FROM Payment";
$stmt = sqlsrv_query($conn, $sql);

?>

<h3>Add Payment</h3>
<form method="POST">
    <input type="number" name="JobID" placeholder="Job ID" required>
    <select name="CustomerType" required>
        <option value="Regular">Regular</option>
        <option value="Premium">Premium</option>
    </select>
    <select name="ProductType" required>
        <option value="No Risk">No Risk</option>
        <option value="Medium Risk">Medium Risk</option>
        <option value="High Risk">High Risk</option>
    </select>
    <select name="LoadType" required>
        <option value="Small">Small</option>
        <option value="Medium">Medium</option>
        <option value="Large">Large</option>
    </select>
    <button type="submit" name="action" value="add">Add Payment</button>
</form>

<?php

echo "<h2>Payments</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Job ID</th>
            <th>Amount</th>
            <th>Payment Date</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $paymentDate = $row['PaymentDate']->format('Y-m-d'); // Format the DateTime object
    echo "<tr>
            <td>{$row['PaymentID']}</td>
            <td>{$row['JobID']}</td>
            <td>{$row['Amount']}</td>
            <td>{$paymentDate}</td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<?php include '../includes/footer.php'; ?>