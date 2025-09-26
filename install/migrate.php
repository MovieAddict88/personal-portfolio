<?php
// --- Simple Migration Script for Education Feature ---

require_once '../config/config.php';

echo "<pre>";
echo "Starting migration...\n";

try {
    // 1. Connect to the database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful.\n";

    // 2. Create the 'education' table if it doesn't exist
    $education_table_sql = "
    CREATE TABLE IF NOT EXISTS `education` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `degree` VARCHAR(255) NOT NULL,
      `institution` VARCHAR(255) NOT NULL,
      `start_year` VARCHAR(4),
      `end_year` VARCHAR(10)
    );";

    $pdo->exec($education_table_sql);
    echo "Checked/created the `education` table successfully.\n";

    // 3. Check if the 'education' column exists in 'about_me' before trying to drop it
    $check_column_stmt = $pdo->query("SHOW COLUMNS FROM `about_me` LIKE 'education'");
    $column_exists = $check_column_stmt->fetch(PDO::FETCH_ASSOC);

    if ($column_exists) {
        // If the column exists, drop it
        $pdo->exec("ALTER TABLE `about_me` DROP COLUMN `education`;");
        echo "Dropped the old `education` column from `about_me` table.\n";
    } else {
        echo "Old `education` column not found in `about_me` table (already migrated or fresh install).\n";
    }

    echo "\nMigration complete! The database is now up to date.\n";
    echo "You can now safely delete this `migrate.php` file from the 'install' directory.";

} catch (PDOException $e) {
    die("Error during migration: " . $e->getMessage());
}

echo "</pre>";
?>