<?php
include '../db/db.php';
include '../includes/header.php';

// Handle form submissions for add or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $lorryID = $_POST['LorryID'];
            $driverID = $_POST['DriverID'];
            $assistantID = $_POST['AssistantID'];
            $depotID = $_POST['DepotID'];
            $containerID = $_POST['ContainerID'];
            $sql = "INSERT INTO TransportUnit (LorryID, DriverID, AssistantID, DepotID, ContainerID) VALUES (?, ?, ?, ?, ?)";
            $params = array($lorryID, $driverID, $assistantID, $depotID, $containerID);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        } elseif ($action === 'delete') {
            $id = $_POST['TransportUnitID'];
            $sql = "DELETE FROM TransportUnit WHERE TransportUnitID = ?";
            $params = array($id);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
}

$sql = "SELECT * FROM TransportUnit";
$stmt = sqlsrv_query($conn, $sql);
?>

<h3>Add Transport Unit</h3>
<form method="POST">
    <input type="text" name="LorryID" placeholder="Lorry ID" required>
    <input type="text" name="DriverID" placeholder="Driver ID" required>
    <input type="text" name="AssistantID" placeholder="Assistant ID" required>
    <input type="text" name="DepotID" placeholder="Depot ID" required>
    <input type="text" name="ContainerID" placeholder="Container ID" required>
    <button type="submit" name="action" value="add">Add Transport Unit</button>
</form>

<?php
echo "<h2>Transport Units</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Lorry ID</th>
            <th>Driver ID</th>
            <th>Assistant ID</th>
            <th>Depot ID</th>
            <th>Container ID</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['TransportUnitID']}</td>
            <td>{$row['LorryID']}</td>
            <td>{$row['DriverID']}</td>
            <td>{$row['AssistantID']}</td>
            <td>{$row['DepotID']}</td>
            <td>{$row['ContainerID']}</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='TransportUnitID' value='{$row['TransportUnitID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>


<?php include '../includes/footer.php'; ?>
