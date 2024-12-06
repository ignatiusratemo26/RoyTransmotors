<?php
include '../db/db.php';
include '../includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $location = $_POST['DepotLocation'];
            $sql = "INSERT INTO Depot (DepotLocation) VALUES (?)";
            $params = array($location);
            sqlsrv_query($conn, $sql, $params);
        } elseif ($action === 'delete') {
            $id = $_POST['DepotID'];
            $sql = "DELETE FROM Depot WHERE DepotID = ?";
            $params = array($id);
            sqlsrv_query($conn, $sql, $params);
        }
    }
}

// Fetch depots
$sql = "SELECT * FROM Depot";
$stmt = sqlsrv_query($conn, $sql);

echo "<h2>Depots</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['DepotID']}</td>
            <td>{$row['DepotLocation']}</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='DepotID' value='{$row['DepotID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<h3>Add Depot</h3>
<form method="POST">
    <input type="text" name="DepotLocation" placeholder="Depot Location" required>
    <button type="submit" name="action" value="add">Add Depot</button>
</form>

<?php include '../includes/footer.php'; ?>
