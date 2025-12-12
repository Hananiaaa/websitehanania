<?php
require_once 'config.php';

try {
    // Test connection
    echo "Database connection successful!<br>";
    
    // Test if articles table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM articles");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Number of articles in database: " . $result['count'] . "<br>";
    
    // Test if users table exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Number of users in database: " . $result['count'] . "<br>";
    
    echo "All tests passed! The database is properly configured.";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>