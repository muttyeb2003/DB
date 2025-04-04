<?php
require_once 'db.php'; // uses $pdo from db.php
?>

<h2>Budget Projection</h2>
<form method="post" class="tables" style="max-width: 500px; margin: 0 auto;">
    <div style="margin-bottom: 1rem;">
        <label for="years">Number of Years:</label>
        <input type="number" name="years" id="years" min="1" required style="width: 100%; padding: 0.5rem;">
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="rate">Inflation Rate (%):</label>
        <input type="number" name="rate" id="rate" min="0" step="0.01" required style="width: 100%; padding: 0.5rem;">
    </div>

    <input type="submit" value="Project Budget" style="width: 100%; padding: 0.75rem; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $years = intval($_POST['years']);
    $rate = floatval($_POST['rate']);

    if ($years > 0 && $rate >= 0) {
        try {
            $stmt = $pdo->prepare("
                SELECT SUM(p.price * op.quantity) AS total_spent
                FROM orders o
                JOIN order_parts op ON o.order_id = op.order_id
                JOIN parts p ON op.part_id = p._id
                WHERE YEAR(o.order_date) = 2022
            ");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['total_spent'] !== null) {
                $base_amount = floatval($row['total_spent']);

                echo "<h3>Projected Budget for Next $years Years (Starting 2023)</h3>";
                echo "<table border='1'><tr><th>Year</th><th>Projected Expense</th></tr>";

                for ($i = 1; $i <= $years; $i++) {
                    $year = 2022 + $i;
                    $projected = $base_amount * pow(1 + $rate / 100, $i);
                    echo "<tr><td>$year</td><td>" . number_format($projected, 2, '.', ',') . "</td></tr>";
                }

                echo "</table>";
            } else {
                echo "<p style='color:red; text-align:center;'>❌ No data found for 2022 to base the projection on.</p>";
            }

        } catch (PDOException $e) {
            echo "<p style='color:red; text-align:center;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Please enter a valid number of years and inflation rate.</p>";
    }
}
?>
