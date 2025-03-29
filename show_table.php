<h2>Show Table</h2>
<form method="post">
    <label>Enter Table Name:</label>
    <input type="text" name="table" required>
    <input type="submit" value="Show">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'db.php'; // ✅ THIS IS REQUIRED!

    $table = $_POST['table'];
    $allowedTables = ['parts', 'suppliers', 'orders', 'supplier_phones'];

    if (!in_array($table, $allowedTables)) {
        echo "<p style='color:red;'>❌ Invalid table name.</p>";
        return;
    }

    try {
        $stmt = $pdo->query("SELECT * FROM `$table`");
        // Rest of your code...
    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
?>