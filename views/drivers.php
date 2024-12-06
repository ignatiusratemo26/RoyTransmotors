<?php
include '../db/db.php';
include '../includes/header.php';

// Handle form submissions for add or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $name = $_POST['DriverName'];
            $license = $_POST['LicenseNumber'];
            $phone = $_POST['Phone'];
            $email = $_POST['Email'];
            $sql = "INSERT INTO Driver (DriverName, LicenseNumber, Phone, Email) VALUES (?, ?, ?, ?)";
            $params = array($name, $license, $phone, $email);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        } elseif ($action === 'delete') {
            $id = $_POST['DriverID'];
            $sql = "DELETE FROM Driver WHERE DriverID = ?";
            $params = array($id);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
}

$sql = "SELECT * FROM Driver";
$stmt = sqlsrv_query($conn, $sql);

?>

<h3>Add Driver</h3>
<form method="POST">
    <input type="text" name="DriverName" placeholder="Driver Name" required>
    <input type="text" name="LicenseNumber" placeholder="License Number" required>
    <input type="text" name="Phone" placeholder="Phone" required>
    <input type="email" name="Email" placeholder="Email" required>
    <button type="submit" name="action" value="add">Add Driver</button>
</form>


<?php

echo "<h2>Drivers</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>License Number</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['DriverID']}</td>
            <td>{$row['DriverName']}</td>
            <td>{$row['LicenseNumber']}</td>
            <td>{$row['Phone']}</td>
            <td>{$row['Email']}</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='DriverID' value='{$row['DriverID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<?php include '../includes/footer.php'; ?>