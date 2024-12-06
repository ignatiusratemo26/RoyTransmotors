<?php
include '../db/db.php';
include '../includes/header.php';

// Handle form submissions for add or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $registrationNumber = $_POST['RegistrationNumber'];
            $capacity = $_POST['Capacity'];
            $depotID = $_POST['DepotID'];
            $sql = "INSERT INTO Lorry (RegistrationNumber, Capacity, DepotID) VALUES (?, ?, ?)";
            $params = array($registrationNumber, $capacity, $depotID);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        } elseif ($action === 'delete') {
            $id = $_POST['LorryID'];
            $sql = "DELETE FROM Lorry WHERE LorryID = ?";
            $params = array($id);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
}

$sql = "SELECT * FROM Lorry";
$stmt = sqlsrv_query($conn, $sql);
?>

<h3>Add Lorry</h3>
<form method="POST">
    <input type="text" name="RegistrationNumber" placeholder="Registration Number" required>
    <input type="text" name="Capacity" placeholder="Capacity" required>
    <input type="number" name="DepotID" placeholder="Depot ID" required>
    <button type="submit" name="action" value="add">Add Lorry</button>
</form>

<?php

echo "<h2>Lorries</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Registration Number</th>
            <th>Capacity</th>
            <th>Depot ID</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['LorryID']}</td>
            <td>{$row['RegistrationNumber']}</td>
            <td>{$row['Capacity']}</td>
            <td>{$row['DepotID']}</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='LorryID' value='{$row['LorryID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>



<?php include '../includes/footer.php'; ?>