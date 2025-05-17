<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Set page title
$page_title = 'Dashboard';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = $_GET['delete'];
    if (archiveWork($id)) {
        $successMessage = "Work item deleted successfully!";
    } else {
        $errorMessage = "Error deleting work item.";
    }
}

// Handle archive request
if (isset($_GET['archive']) && !empty($_GET['archive'])) {
    $id = $_GET['archive'];
    if (archiveWork($id)) {
        $successMessage = "Work item archived successfully!";
    } else {
        $errorMessage = "Error archiving work item.";
    }
}

// Get all works
$works = getWorks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="/css/admin.css">
    <title>Simple Admin Dashboard - Portfolio</title>
    <style>
        /* Additional simple styles */
        .admin-page {
            display: block;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .admin-top-nav {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .admin-top-nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }
        .admin-top-nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .admin-top-nav a:hover, .admin-top-nav a.active {
            color: #3498db;
        }
        .works-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .works-table th, .works-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .works-table th {
            background-color: #f5f5f5;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3498db;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .btn-delete {
            background: #e74c3c;
        }
        .btn-info {
            background: #17a2b8;
        }
        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-page">
        <div class="admin-header">
            <h1>Mark Fernandez Photography Admin</h1>
            <div>
                <a href="archives.php" class="btn btn-info" style="margin-right: 10px;">View Archives</a>
                <a href="../index.php" class="btn btn-secondary">View Website</a>
            </div>
        </div>
        
        <nav class="admin-top-nav">
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="add-work.php">Add New Work</a></li>
                <li><a href="create-user.php">Create User</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        
        <?php if (isset($successMessage)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMessage) ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <?php endif; ?>
        
        <div class="admin-works">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Portfolio Works (<?= count($works) ?>)</h2>
              
            </div>
            
            <?php if (empty($works)): ?>
            <div class="empty-state">
                <p>No works found in your portfolio.</p>
                <a href="add-work.php" class="btn">Add Your First Work</a>
            </div>
            <?php else: ?>
            <table class="works-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($works as $work): ?>
                    <tr>
                        <td>
                            <img src="../<?= htmlspecialchars($work['image']) ?>" alt="<?= htmlspecialchars($work['title']) ?>" class="thumbnail">
                        </td>
                        <td><?= htmlspecialchars($work['title']) ?></td>
                        <td><?= htmlspecialchars($work['category'] ?? 'Uncategorized') ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($work['date_added']))) ?></td>
                        <td>
                            <a href="edit-work.php?id=<?= $work['id'] ?>" class="btn">Edit</a>
                            <a href="dashboard.php" class="btn btn-delete" 
                               onclick="confirmArchive(<?= $work['id'] ?>, '<?= htmlspecialchars(addslashes($work['title'])) ?>')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    // Simple confirm archive function
    function confirmArchive(id, title) {
        if (confirm(`Are you sure you want to delete "${title}"? You can view it later in the archives section.`)) {
            window.location.href = `dashboard.php?delete=${id}`;
        }
    }
    
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        
        if (alerts.length > 0) {
            setTimeout(() => {
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        }
    });
    </script>
</body>
</html>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="archives.php" class="btn btn-info me-2">View Archives</a>
            <a href="add-work.php" class="btn btn-primary">Add New Work</a>
        </div>
    </div>
