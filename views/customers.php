<?php
include '../db/db.php';
include '../includes/header.php';

// Handle form submissions for add, update, or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $name = $_POST['CustomerName'];
            $category = $_POST['Category'];
            $sql = "INSERT INTO Customer (CustomerName, Category) VALUES (?, ?)";
            $params = array($name, $category);
            sqlsrv_query($conn, $sql, $params);
        } elseif ($action === 'delete') {
            $id = $_POST['CustomerID'];
            $sql = "DELETE FROM Customer WHERE CustomerID = ?";
            $params = array($id);
            sqlsrv_query($conn, $sql, $params);
        }
    }
}

$sql = "SELECT * FROM Customer";
$stmt = sqlsrv_query($conn, $sql);

echo "<h2>Customers</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['CustomerID']}</td>
            <td>{$row['CustomerName']}</td>
            <td>{$row['Category']}</td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='CustomerID' value='{$row['CustomerID']}'>
                    <button type='submit' name='action' value='delete'>Delete</button>
                </form>
            </td>
        </tr>";
}
echo "</table>";
sqlsrv_free_stmt($stmt);
?>

<h3>Add Customer</h3>
<form method="POST">
    <input type="text" name="CustomerName" placeholder="Customer Name" required>
    <select name="Category" required>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
    </select>
    <button type="submit" name="action" value="add">Add Customer</button>
</form>

<?php include '../includes/footer.php'; ?>
