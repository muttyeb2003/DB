<?php
require_once 'db.php';
?>

<h2>Add New Supplier</h2>
<form method="post" class="supplier" style="max-width: 500px; margin: 0 auto;">
    <div style="margin-bottom: 1rem;">
        <label for="supplier_id">Supplier ID:</label>
        <input type="number" id="supplier_id" name="supplier_id" required min="1" style="width: 100%; padding: 0.5rem;">
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="name">Supplier Name:</label>
        <input type="text" id="name" name="name" required maxlength="100" style="width: 100%; padding: 0.5rem;">
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required maxlength="100" style="width: 100%; padding: 0.5rem;">
    </div>

    <input type="submit" value="Add Supplier" style="width: 100%; padding: 0.75rem; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if ($supplier_id < 1) {
        echo "<p style='color:red; text-align:center;'>❌ Error: Supplier ID must be a positive number.</p>";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO suppliers (supplier_id, name, email) VALUES (:id, :name, :email)");
            $stmt->bindParam(':id', $supplier_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            if ($stmt->execute()) {
                echo "<p style='color:green; text-align:center;'>✅ Supplier added successfully!</p>";
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<p style='color:red; text-align:center;'>❌ Error: A supplier with this ID or email already exists.</p>";
            } else {
                echo "<p style='color:red; text-align:center;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
}
?>