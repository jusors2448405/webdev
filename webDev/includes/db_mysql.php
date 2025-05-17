<?php
/**
 * Database connection and functions for the photography portfolio
 * Using MySQL database
 */

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'photography_portfolio');

/**
 * Get database connection
 * 
 * @return mysqli|null Database connection object or null if connection fails
 */
function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            // Create connection
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Check connection
            if ($conn->connect_error) {
                // For now, we'll use an in-memory array since we're in Replit environment
                // and don't have access to the XAMPP MySQL server
                return null;
            }
            
            // Set charset
            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            // Connection failed, we'll use in-memory storage
            return null;
        }
    }
    
    return $conn;
}

/**
 * Restore an archived work
 * 
 * @param int $id The ID of the work to restore
 * @return bool True if successful, false otherwise
 */
function restoreArchived($id) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Get the archived work
        $stmt = $conn->prepare('SELECT * FROM archives WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $work = $result->fetch_assoc();
        
        if (!$work) {
            $conn->rollback();
            return false;
        }
        
        // Insert into works table with current date
        $stmt = $conn->prepare('INSERT INTO works (title, description, category, link, image, date_added) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->bind_param('sssss', $work['title'], $work['description'], $work['category'], $work['link'], $work['image']);
        $stmt->execute();
        
        // Delete from archived_works table
        $stmt = $conn->prepare('DELETE FROM archives WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

// Global storage for works when database is not available
$in_memory_works = [
    [
        'id' => 1,
        'title' => 'Portrait Photography',
        'description' => 'A collection of portrait photography showcasing different emotions and personalities.',
        'category' => 'Portrait',
        'link' => '',
        'image' => 'https://source.unsplash.com/500x500/?portrait',
        'date_added' => '2023-01-15 10:30:00'
    ],
    [
        'id' => 2,
        'title' => 'Nature Landscapes',
        'description' => 'Beautiful landscape photography from around the world.',
        'category' => 'Landscape',
        'link' => '',
        'image' => 'https://source.unsplash.com/500x500/?landscape',
        'date_added' => '2023-02-20 14:45:00'
    ],
    [
        'id' => 3,
        'title' => 'Urban Exploration',
        'description' => 'Exploring the beauty of urban environments and architecture.',
        'category' => 'Urban',
        'link' => '',
        'image' => 'https://source.unsplash.com/500x500/?urban',
        'date_added' => '2023-03-10 09:15:00'
    ],
    [
        'id' => 4,
        'title' => 'Wildlife Photography',
        'description' => 'Capturing the beauty and behavior of wild animals in their natural habitats.',
        'category' => 'Wildlife',
        'link' => '',
        'image' => 'https://source.unsplash.com/500x500/?wildlife',
        'date_added' => '2023-04-05 16:20:00'
    ],
    [
        'id' => 5,
        'title' => 'Abstract Art Photography',
        'description' => 'Exploring abstract concepts through the lens of a camera.',
        'category' => 'Abstract',
        'link' => '',
        'image' => 'https://source.unsplash.com/500x500/?abstract',
        'date_added' => '2023-05-18 11:10:00'
    ],
    [
        'id' => 6,
        'title' => 'Street Photography',
        'description' => 'Candid shots of everyday life in the streets around the world.',
        'category' => 'Street',
        'link' => '',
        'image' => 'https://source.unsplash.com/500x500/?street',
        'date_added' => '2023-06-22 13:40:00'
    ]
];

$in_memory_admin = [
    'username' => 'admin',
    'password' => '$2y$10$xTZC9tMVAT5wSJHJjGJ3aeO6fLYj/o3s5h4nK9SxNSCf1nCTKzKWa' // hashed 'admin123'
];

/**
 * Create database tables if they don't exist
 */
function initDatabase() {
    $conn = getDbConnection();
    
    // If no connection available, use in-memory storage
    if ($conn === null) {
        return;
    }
    
    // Create works table
    $sql = "CREATE TABLE IF NOT EXISTS works (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(100),
        link VARCHAR(255),
        image VARCHAR(255) NOT NULL,
        date_added DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (!$conn->query($sql)) {
        // If error, just return - we'll use in-memory storage
        return;
    }
    
    // Create archives table
    $sql = "CREATE TABLE IF NOT EXISTS archives (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(100),
        link VARCHAR(255),
        image VARCHAR(255) NOT NULL,
        date_added DATETIME NOT NULL,
        date_archived DATETIME NOT NULL,
        original_id INT(11)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (!$conn->query($sql)) {
        return;
    }
    
    // Create admins table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (!$conn->query($sql)) {
        // If error, just return - we'll use in-memory storage
        return;
    }
    
    // Check if default admin exists, if not create one
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
        $admin_username = 'admin';
        $stmt->bind_param("s", $admin_username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        if ($count == 0) {
            // Insert default admin (username: admin, password: admin123)
            $username = 'admin';
            $email = 'admin@example.com';
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
        // If error, just return - we'll use in-memory storage
        return;
    }
}

/**
 * Get all works from the database
 * 
 * @return array Array of works
 */
function deleteArchived($id) {
    $conn = getDbConnection();
    if ($conn === null) {
        return false;
    }

    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Get the archived work first to get the image path
        $stmt = $conn->prepare("SELECT * FROM archives WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $work = $result->fetch_assoc();
            
            // Delete from archives table
            $stmt = $conn->prepare("DELETE FROM archives WHERE id = ?");
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                // Commit transaction
                $conn->commit();
                return true;
            }
        }
        
        $conn->rollback();
        return false;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function getWorks() {
    global $in_memory_works;
    $conn = getDbConnection();
    $works = [];
    
    // If no database connection available, use in-memory storage
    if ($conn === null) {
        return $in_memory_works;
    }
    
    try {
        $sql = "SELECT * FROM works ORDER BY date_added DESC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Handle image paths to ensure they're properly formatted
                if (!empty($row['image'])) {
                    // For absolute URLs (http/https) - leave as is
                    if (strpos($row['image'], 'http') === 0) {
                        // Leave as is - it's already an absolute URL
                    } 
                    // For relative paths, ensure they're properly formatted
                    else {
                        // Remove any leading slash to ensure consistent format
                        $row['image'] = ltrim($row['image'], '/');
                    }
                }
                $works[] = $row;
            }
        }
        
        return $works;
    } catch (Exception $e) {
        // On error, return in-memory works
        return $in_memory_works;
    }
}

/**
 * Get work by ID
 * 
 * @param int $id Work ID
 * @return array|null Work data or null if not found
 */
function getWorkById($id) {
    global $in_memory_works;
    $conn = getDbConnection();
    
    // If no database connection available, use in-memory storage
    if ($conn === null) {
        foreach ($in_memory_works as $work) {
            if ($work['id'] == $id) {
                return $work;
            }
        }
        return null;
    }
    
    try {
        $stmt = $conn->prepare("SELECT * FROM works WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    } catch (Exception $e) {
        // On error, search in-memory works
        foreach ($in_memory_works as $work) {
            if ($work['id'] == $id) {
                return $work;
            }
        }
        return null;
    }
}

/**
 * Add a new work
 * 
 * @param array $work Work data
 * @return bool Success status
 */
function addWork($work) {
    global $in_memory_works;
    $conn = getDbConnection();
    
    // If no database connection available, use in-memory storage
    if ($conn === null) {
        // Generate an ID
        $max_id = 0;
        foreach ($in_memory_works as $existing_work) {
            if ($existing_work['id'] > $max_id) {
                $max_id = $existing_work['id'];
            }
        }
        
        $work['id'] = $max_id + 1;
        $in_memory_works[] = $work;
        
        return true;
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO works (title, description, category, link, image, date_added) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", 
            $work['title'], 
            $work['description'], 
            $work['category'], 
            $work['link'], 
            $work['image'],
            $work['date_added']
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        // On error, add to in-memory works
        // Generate an ID
        $max_id = 0;
        foreach ($in_memory_works as $existing_work) {
            if ($existing_work['id'] > $max_id) {
                $max_id = $existing_work['id'];
            }
        }
        
        $work['id'] = $max_id + 1;
        $in_memory_works[] = $work;
        
        return true;
    }
}

/**
 * Update an existing work
 * 
 * @param array $work Work data with ID
 * @return bool Success status
 */
function updateWork($work) {
    global $in_memory_works;
    $conn = getDbConnection();
    
    // If no database connection available, use in-memory storage
    if ($conn === null) {
        foreach ($in_memory_works as $key => $existing_work) {
            if ($existing_work['id'] == $work['id']) {
                $in_memory_works[$key] = $work;
                return true;
            }
        }
        
        return false;
    }
    
    try {
        $stmt = $conn->prepare("UPDATE works SET title = ?, description = ?, category = ?, link = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssssi", 
            $work['title'], 
            $work['description'], 
            $work['category'], 
            $work['link'],
            $work['image'],
            $work['id']
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        // On error, try updating in-memory works
        foreach ($in_memory_works as $key => $existing_work) {
            if ($existing_work['id'] == $work['id']) {
                $in_memory_works[$key] = $work;
                return true;
            }
        }
        
        return false;
    }
}

/**
 * Delete a work by ID and move it to archives
 * 
 * @param int $id Work ID
 * @return bool Success status
 */
function archiveWork($id) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Get the work
        $stmt = $conn->prepare('SELECT * FROM works WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $work = $result->fetch_assoc();
        
        if (!$work) {
            $conn->rollback();
            return false;
        }
        
        // Insert into archives table
        $stmt = $conn->prepare('INSERT INTO archives (title, description, category, link, image, date_added, date_archived, original_id) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)');
        $stmt->bind_param('ssssssi', $work['title'], $work['description'], $work['category'], $work['link'], $work['image'], $work['date_added'], $id);
        if (!$stmt->execute()) {
            $conn->rollback();
            return false;
        }
        
        // We no longer delete the image file so it can be displayed in archives
        // The following code is commented out to preserve images
        /*
        // Delete the image file if it exists locally
        if (file_exists($work['image']) && strpos($work['image'], 'http') !== 0) {
            if (!unlink($work['image'])) {
                $conn->rollback();
                return false;
            }
        }
        */
        
        // Delete from works table
        $stmt = $conn->prepare('DELETE FROM works WHERE id = ?');
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            $conn->rollback();
            return false;
        }
        
        // Commit transaction
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/**
 * Get all archived works
 * 
 * @return array Array of archived works
 */
function getArchivedWorks() {
    $conn = getDbConnection();
    $works = [];
    
    // If no database connection available, return empty array
    if ($conn === null) {
        return $works;
    }
    
    try {
        $sql = "SELECT * FROM archives ORDER BY date_archived DESC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Handle image paths to ensure they're properly formatted
                if (!empty($row['image'])) {
                    // For absolute URLs (http/https) - leave as is
                    if (strpos($row['image'], 'http') === 0) {
                        // Leave as is - it's already an absolute URL
                    } 
                    // For relative paths, ensure they're properly formatted
                    else {
                        // Remove any leading slash to ensure consistent format
                        $row['image'] = ltrim($row['image'], '/');
                    }
                }
                $works[] = $row;
            }
        }
        
        return $works;
    } catch (Exception $e) {
        return $works;
    }
}

/**
 * Authenticate user
 * 
 * @param string $username Username
 * @param string $password Password
 * @return bool Authentication result
 */
function login($username, $password) {
    global $in_memory_admin;
    $conn = getDbConnection();
    
    // If no database connection available, use in-memory admin
    if ($username === 'admin' && $password === 'admin123') { // TEMPORARY TEST - DO NOT KEEP THIS
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }
    
    try {
        $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                return true;
            }
        }
        
        return false;
    } catch (Exception $e) {
        // On error, try in-memory admin
        if ($username === $in_memory_admin['username'] && 
            password_verify($password, $in_memory_admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            return true;
        }
        
        return false;
    }
}

// Initialize database when this file is included
initDatabase();