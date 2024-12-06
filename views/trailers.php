<?php
include '../db/db.php';
include '../includes/header.php';

// Handle form submissions for add or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $registrationNumber = $_POST['RegistrationNumber'];
            $weightCapacity = $_POST['WeightCapacity'];
            $sql = "INSERT INTO Trailer (RegistrationNumber, WeightCapacity) VALUES (?, ?)";
            $params = array($registrationNumber, $weightCapacity);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        } elseif ($action === 'delete') {
            $id = $_POST['TrailerID'];
            $sql = "DELETE FROM Trailer WHERE TrailerID = ?";
            $params = array($id);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
}

$sql = "SELECT * FROM Trailer";
$stmt = sqlsrv_query($conn, $sql);

?>

<h3>Add Trailer</h3>
<form method="POST">
    <input type="text" name="RegistrationNumber" placeholder="Registration Number" required>
    <input type="number" name="WeightCapacity" placeholder="Weight Capacity" required>
    <button type="submit" name="action" value="add">Add Trailer</button>
</form>

<?php

echo "<h2>Trailers</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Registration Number</th>
            <th>Weight Capacity</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['TrailerID']}</td>
            <td>{$row['RegistrationNumber']}</td>
            <td>{$row['WeightCapacity']}</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='TrailerID' value='{$row['TrailerID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>



<?php include '../includes/footer.php'; ?>