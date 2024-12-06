<?php
include '../db/db.php';
include '../includes/header.php';

// Function to check if a record exists in a table
function recordExists($conn, $table, $column, $value) {
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
    $params = array($value);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    return $row['count'] > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $jobID = $_POST['JobID'];
            $productType = $_POST['ProductType'];
            $loadType = $_POST['LoadType'];
            $transportUnitID = $_POST['TransportUnitID'];

            // Validate JobID and TransportUnitID
            if (!recordExists($conn, 'Job', 'JobID', $jobID)) {
                echo "Error: JobID does not exist.";
            } elseif (!recordExists($conn, 'TransportUnit', 'TransportUnitID', $transportUnitID)) {
                echo "Error: TransportUnitID does not exist.";
            } else {
                $sql = "INSERT INTO Loads (JobID, ProductType, LoadType, TransportUnitID) VALUES (?, ?, ?, ?)";
                $params = array($jobID, $productType, $loadType, $transportUnitID);
                $stmt = sqlsrv_query($conn, $sql, $params);
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
            }
        } elseif ($action === 'delete') {
            $id = $_POST['LoadID'];
            $sql = "DELETE FROM Loads WHERE LoadID = ?";
            $params = array($id);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
}

$sql = "SELECT * FROM Loads";
$stmt = sqlsrv_query($conn, $sql);

?>

<h3>Add Load</h3>
<form method="POST">
    <input type="number" name="JobID" placeholder="Job ID" required>
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
    <input type="number" name="TransportUnitID" placeholder="Transport Unit ID" required>
    <button type="submit" name="action" value="add">Add Load</button>
</form>

<?php

echo "<h2>Loads</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Job ID</th>
            <th>Product Type</th>
            <th>Load Type</th>
            <th>Transport Unit ID</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['LoadID']}</td>
            <td>{$row['JobID']}</td>
            <td>{$row['ProductType']}</td>
            <td>{$row['LoadType']}</td>
            <td>{$row['TransportUnitID']}</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='LoadID' value='{$row['LoadID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<?php include '../includes/footer.php'; ?>