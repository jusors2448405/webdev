<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = false;

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = $_GET['id'];
$work = getWorkById($id);

// Check if work exists
if (!$work) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $link = trim($_POST['link'] ?? '');
    
    // Validate form data
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    
    // Handle file upload if new image is provided
    $image = $work['image']; // Default to existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Invalid file type. Only JPEG, PNG and GIF are allowed.';
        } else {
            // Generate a unique filename
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadPath = '../uploads/' . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Delete old image if it exists and is not the default
                if (!empty($work['image']) && file_exists('..' . $work['image']) && strpos($work['image'], 'default') === false) {
                    unlink('..' . $work['image']);
                }
                
                // Store path without leading slash to ensure consistent path handling
                $image = 'uploads/' . $fileName;
            } else {
                $errors[] = 'Failed to upload image. Please try again.';
            }
        }
    }
    
    // If no errors, update work
    if (empty($errors)) {
        $workData = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'link' => $link,
            'image' => $image
        ];
        
        if (updateWork($workData)) {
            $success = true;
            // Refresh work data
            $work = getWorkById($id);
        } else {
            $errors[] = 'Failed to update work. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="/css/admin.css">
    <title>Edit Work - Mark Fernandez Photography</title>
    <style>
        /* Additional simple styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f9f9f9;
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
            padding: 0;
            margin: 0;
        }
        .admin-top-nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .admin-top-nav a:hover, .admin-top-nav a.active {
            color: #3498db;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3498db;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            box-sizing: border-box;
        }
        .form-group textarea {
            min-height: 100px;
        }
        .help-text {
            font-size: 0.85em;
            color: #777;
            margin-top: 5px;
        }
        .form-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
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
        .alert ul {
            margin: 5px 0 0 20px;
        }
        .current-image {
            margin-bottom: 15px;
        }
        .current-image p {
            margin-bottom: 8px;
            font-size: 0.9em;
            color: #555;
        }
        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .file-preview {
            margin-top: 10px;
        }
        .file-preview img {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Edit Work</h1>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    
    <nav class="admin-top-nav">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add-work.php">Add New Work</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    
    <?php if ($success): ?>
    <div class="alert alert-success">
        Work updated successfully!
    </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($work['title']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($work['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($work['category'] ?? '') ?>">
                <p class="help-text">Used for filtering works on the portfolio page</p>
            </div>
            
            <div class="form-group">
                <label for="link">Project Link</label>
                <input type="url" id="link" name="link" value="<?= htmlspecialchars($work['link'] ?? '') ?>">
                <p class="help-text">External link to the project (optional)</p>
            </div>
            
            <div class="form-group">
                <label for="image">Image</label>
                <div class="current-image">
                    <p>Current image:</p>
                    <img src="../<?= htmlspecialchars($work['image']) ?>" alt="<?= htmlspecialchars($work['title']) ?>">
                </div>
                <input type="file" id="image" name="image" accept="image/*">
                <div class="file-preview"></div>
                <p class="help-text">Upload a new image only if you want to change the current one</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Update Work</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
    <script>
    // Simple image preview
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const filePreview = document.querySelector('.file-preview');
        
        if (imageInput && filePreview) {
            imageInput.addEventListener('change', function() {
                // Clear previous preview
                filePreview.innerHTML = '';
                
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Image Preview';
                        filePreview.appendChild(img);
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        
        // Auto-hide alerts after 5 seconds
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
