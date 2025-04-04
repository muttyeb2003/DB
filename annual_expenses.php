<h2>Show Table</h2>
<form method="post" class="tables">
    <div>
        <label>Enter Year 1: </label>
        <input type="number" name="start_year" min="0" required>
    </div>
    
    <div>
        <label>Enter Year 2: </label>
        <input type="number" name="end_year" min="0" required>
    </div>
    
    <input type="submit" value="Show expenses">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require('db.php'); 
    $link = mysqli_connect($host, $user, $pass);
    
    mysqli_select_db($link, $db);

    $start_year = intval($_POST['start_year']);
    $end_year = intval($_POST['end_year']);

    // Validate years: non-negative, logical range, and within bounds
    if ($start_year >= 0 && $end_year >= 0 && $start_year > 2014 && $end_year < 2025 && $start_year <= $end_year) {

        $sql = "SELECT YEAR(o.order_date) AS year, SUM(p.price * op.quantity) AS total_spent 
                FROM orders o
                JOIN order_parts op ON o.order_id = op.order_id
                JOIN parts p ON op.part_id = p._id
                WHERE YEAR(o.order_date) BETWEEN ? AND ?
                GROUP BY YEAR(o.order_date)
                ORDER BY YEAR(o.order_date)";
        
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ii", $start_year, $end_year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<h3>Total Money Spent on Parts (Yearly)</h3>";
        echo "<table border='1'><tr><th>Year</th><th>Total Spent (CAD)</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['year']) . "</td><td>" . number_format($row['total_spent'], 2, '.', ',') . "</td></tr>";
        }
        
        echo "</table>";
        
    } else {
        echo "<p style='color:red;'>Please enter a valid, non-negative year range between 2015 and 2024.</p>";
    }
}
?>
