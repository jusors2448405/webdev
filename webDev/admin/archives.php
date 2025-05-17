<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = $_GET['delete'];
    if (deleteArchived($id)) {
        $successMessage = "Archived work deleted successfully!";
    } else {
        $errorMessage = "Error deleting archived work.";
    }
}

// Handle restore request
if (isset($_GET['restore']) && !empty($_GET['restore'])) {
    $id = $_GET['restore'];
    if (restoreArchived($id)) {
        header('Location: archives.php');
        exit;
    } else {
        $errorMessage = "Error restoring archived work.";
    }
}

// Get archived works
$archived_works = getArchivedWorks();

// Set page title
$page_title = 'Archived Works';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="/css/admin.css">
    <title>Archived Works - Portfolio</title>
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
            <h1>Archived Works</h1>
            <div>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>

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
            <h2>Archived Portfolio Works (<?= count($archived_works) ?>)</h2>

            <?php if (empty($archived_works)): ?>
            <div class="empty-state">
                <p>No archived works found.</p>
            </div>
            <?php else: ?>
            <table class="works-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date Archived</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($archived_works as $work): ?>
                    <tr>
                   
                        <td>
                            <img src="../<?= htmlspecialchars($work['image']) ?>" alt="<?= htmlspecialchars($work['title']) ?>" class="thumbnail">
                        </td>
                        <td><?= htmlspecialchars($work['title']) ?></td>
                        <td><?= htmlspecialchars($work['category'] ?? 'Uncategorized') ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($work['date_archived'])))?></td>
                        <td>
                             <a href="javascript:void(0);" class="btn btn-info" 
                             onclick="confirmRestore(<?= $work['id'] ?>, '<?= htmlspecialchars(addslashes($work['title'])) ?>')">Restore</a>
                              <a href="javascript:void(0);" class="btn btn-delete"
                              onclick="confirmDelete(<?= $work['id'] ?>, '<?= htmlspecialchars(addslashes($work['title'])) ?>')">Delete</a>
                            </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function confirmDelete(id, title) {
        if (confirm(`Are you sure you want to permanently delete "${title}"? This cannot be undone.`)) {
            window.location.href = `archives.php?delete=${id}`;
        }
    }
    function confirmRestore(id, title) {
        if (confirm(`Are you sure you want to restore "${title}"?`)) {
            window.location.href = `archives.php?restore=${id}`;
        }
    }
    </script>
</body>
</html>