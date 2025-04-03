<h2>Show Table</h2>
<form method="post" class="tables">
    <label>Enter Table Name:</label>
    <input type="text" name="table" required>
    <input type="submit" value="Show">
</form>
<?php
require_once 'db.php'; // Now $pdo will be available here
?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'db.php'; // ✅ Ensure database connection is included

    $table = $_POST['table'];
    $allowedTables = ['parts', 'suppliers', 'orders', 'supplier_phones'];

    if (!in_array($table, $allowedTables)) {
        echo "<p style='color:red;'>❌ Invalid table name.</p>";
        return;
    }

    try {
        // Query to fetch table data
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            echo "<p style='color:blue;'>ℹ️ No records found in <strong>$table</strong>.</p>";
            return;
        }

        // Generate table headers dynamically
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>";
        foreach (array_keys($rows[0]) as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";

        // Generate table rows
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
?>