<h2>Add New Supplier</h2>

<form method="post">
    <div>
        <label for="supplier_id">Supplier ID:</label>
        <input type="number" id="supplier_id" name="supplier_id" required>
    </div>
    
    <div>
        <label for="name">Supplier Name:</label>
        <input type="text" id="name" name="name" required maxlength="100">
    </div>
    
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required maxlength="100">
    </div>
    
    <input type="submit" value="Add Supplier">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'db.php'; // Include database connection
    
    // Retrieve and sanitize input
    $supplier_id = filter_input(INPUT_POST, 'supplier_id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    try {
        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO suppliers (supplier_id, name, email) VALUES (:id, :name, :email)");
        
        // Bind parameters
        $stmt->bindParam(':id', $supplier_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Supplier added successfully!</p>";
            
            // Clear form fields after successful submission
            echo "<script>
                document.getElementById('supplier_id').value = '';
                document.getElementById('name').value = '';
                document.getElementById('email').value = '';
            </script>";
        }
        
    } catch (PDOException $e) {
        // Handle specific error cases
        if ($e->errorInfo[1] == 1062) { // Duplicate entry error
            echo "<p style='color:red;'>❌ Error: A supplier with this ID or email already exists.</p>";
        } else {
            echo "<p style='color:red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>