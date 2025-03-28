<!-- index.php -->
<!DOCTYPE html>
<html>
<head>
    <title>MUC Management App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>MUC Web Application</h1>

<!-- Navigation Buttons -->
<form method="get">
    <button name="page" value="show_table">Show Table</button>
    <button name="page" value="add_supplier">Add Supplier</button>
    <button name="page" value="annual_expenses">Annual Expenses</button>
    <button name="page" value="budget_projection">Budget Projection</button>
</form>

<hr>

<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'show_table':
        include 'show_table.php';
        break;
    case 'add_supplier':
        include 'add_supplier.php';
        break;
    case 'annual_expenses':
        include 'annual_expenses.php';
        break;
    case 'budget_projection':
        include 'budget_projection.php';
        break;
    default:
        echo "<p>Welcome to the MUC Database App. Use the buttons above to interact.</p>";
        break;
}
?>

</body>
</html>
