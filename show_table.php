<h2>Show Table</h2>

<form method="post">
    <label>Enter Table Name:</label>
    <input type="text" name="table" required>
    <input type="submit" value="Show">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $table = $_POST['table'];

    // Whitelist of allowed table names
    $allowedTables = ['parts', 'suppliers', 'orders', 'supplier_phones'];

    if (!in_array($table, $allowedTables)) {
        echo "<p style='color:red;'>❌ Invalid table name.</p>";
        return;
    }

    try {
        $stmt = $pdo->query("SELECT * FROM `$table`");

        if ($stmt->rowCount() > 0) {
            echo "<table border='1'><tr>";

            // Get column names
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $column = $stmt->getColumnMeta($i);
                echo "<th>{$column['name']}</th>";
            }
            echo "</tr>";

            // Print rows
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No data found in <b>$table</b>.</p>";
        }

    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
?>
