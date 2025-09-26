<?php
require_once 'auth_middleware.php';
require_once '../config/config.php';

$message = '';
$error = '';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_education'])) {
            $stmt = $pdo->prepare('INSERT INTO education (degree, institution, start_year, end_year) VALUES (?, ?, ?, ?)');
            $stmt->execute([$_POST['degree'], $_POST['institution'], $_POST['start_year'], $_POST['end_year']]);
            $message = "Education added successfully!";
        } elseif (isset($_POST['update_education'])) {
            $id = $_POST['update_education'];
            $degree = $_POST['degree_' . $id];
            $institution = $_POST['institution_' . $id];
            $start_year = $_POST['start_year_' . $id];
            $end_year = $_POST['end_year_' . $id];

            $stmt = $pdo->prepare('UPDATE education SET degree = ?, institution = ?, start_year = ?, end_year = ? WHERE id = ?');
            $stmt->execute([$degree, $institution, $start_year, $end_year, $id]);
            $message = "Education updated successfully!";
        } elseif (isset($_POST['delete_education'])) {
            $id = $_POST['delete_education'];
            $stmt = $pdo->prepare('DELETE FROM education WHERE id = ?');
            $stmt->execute([$id]);
            $message = "Education deleted successfully!";
        }
    }

    // Fetch all education
    $edu_stmt = $pdo->query('SELECT * FROM education ORDER BY start_year DESC');
    $educations = $edu_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Education</title>
    <style>
        body { font-family: sans-serif; margin: 0; }
        .sidebar { width: 250px; background: #333; color: white; position: fixed; height: 100%; padding-top: 20px; }
        .sidebar h2 { text-align: center; }
        .sidebar ul { list-style-type: none; padding: 0; }
        .sidebar ul li a { display: block; color: white; padding: 15px 20px; text-decoration: none; }
        .sidebar ul li a:hover { background: #555; }
        .main-content { margin-left: 250px; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 5px; text-decoration: none; }
        .form-container, .table-container { max-width: 800px; margin-top: 20px; background: #f9f9f9; padding: 20px; border-radius: 8px; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input[type="text"] { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 0.75rem 1.5rem; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .delete-btn { background-color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="edit-about.php">About Me</a></li>
        <li><a href="edit-education.php">Education</a></li>
        <li><a href="edit-experience.php">Experience</a></li>
        <li><a href="edit-skills.php">Skills</a></li>
        <li><a href="edit-projects.php">Projects</a></li>
        <li><a href="manage-documents.php">Documents</a></li>
        <li><a href="manage-passwords.php">Download Passwords</a></li>
        <li><a href="settings.php">Settings</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Edit Education</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    <hr>

    <?php if ($message): ?><p class="success"><?php echo $message; ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>

    <div class="form-container">
        <h3>Add New Education</h3>
        <form action="edit-education.php" method="POST">
            <div class="form-group">
                <label>Degree</label>
                <input type="text" name="degree" required>
            </div>
            <div class="form-group">
                <label>Institution</label>
                <input type="text" name="institution" required>
            </div>
            <div class="form-group">
                <label>Start Year</label>
                <input type="text" name="start_year" required>
            </div>
            <div class="form-group">
                <label>End Year</label>
                <input type="text" name="end_year" placeholder="e.g., Present" required>
            </div>
            <button type="submit" name="add_education">Add Education</button>
        </form>
    </div>

    <div class="table-container">
        <h3>Existing Education</h3>
        <form action="edit-education.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Degree</th>
                        <th>Institution</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($educations as $edu): ?>
                    <tr>
                        <td><input type="text" name="degree_<?php echo $edu['id']; ?>" value="<?php echo htmlspecialchars($edu['degree']); ?>"></td>
                        <td><input type="text" name="institution_<?php echo $edu['id']; ?>" value="<?php echo htmlspecialchars($edu['institution']); ?>"></td>
                        <td>
                            <input type="text" name="start_year_<?php echo $edu['id']; ?>" value="<?php echo htmlspecialchars($edu['start_year']); ?>" style="width: 60px;"> -
                            <input type="text" name="end_year_<?php echo $edu['id']; ?>" value="<?php echo htmlspecialchars($edu['end_year']); ?>" style="width: 60px;">
                        </td>
                        <td>
                            <button type="submit" name="update_education" value="<?php echo $edu['id']; ?>">Update</button>
                            <button type="submit" name="delete_education" value="<?php echo $edu['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

</body>
</html>