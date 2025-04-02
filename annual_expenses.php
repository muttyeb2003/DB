<h2>Show Table</h2>
<form method="post" class="tables">
    <div>
        <label>Enter Year 1: </label>
        <input type="number" name="start_year" required>
    </div>
    
    <div>
        <label>Enter Year 2: </label>
        <input type="number" name="end_year" required>
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

    if ($start_year > 2014 && $end_year < 2025 && $start_year <= $end_year) {

        
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
        echo "<table border='1'><tr><th>Year</th><th>Total Spent</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['year']) . "</td><td>" . htmlspecialchars($row['total_spent']) . "</td></tr>";
        }
        
        echo "</table>";
        
        
    } else {
        echo "<p>Please enter a valid year range.</p>";
    }
}
?>

