<?php
/**
 * Admin Panel for Feedback System
 * 
 * This script provides an admin interface to view, sort, and manage feedback submissions.
 * Protected with a simple password authentication system.
 */

// Include database configuration
require_once 'config.php';

// Start session for admin authentication
session_start();

// Admin credentials (in production, use proper authentication)
$admin_username = 'admin';
$admin_password = 'admin123'; // Change this to a secure password

// Check if user is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($username === $admin_username && $password === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
            $is_logged_in = true;
        } else {
            $login_error = 'Invalid username or password';
        }
    } elseif ($_POST['action'] === 'logout') {
        session_destroy();
        $is_logged_in = false;
    } elseif ($_POST['action'] === 'delete' && $is_logged_in) {
        $feedback_id = (int)($_POST['feedback_id'] ?? 0);
        if ($feedback_id > 0) {
            deleteFeedback($feedback_id);
        }
    }
}

// Handle AJAX requests
if (isset($_GET['ajax']) && $is_logged_in) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get_feedback') {
        $sort_by = $_GET['sort'] ?? 'created_at';
        $sort_order = $_GET['order'] ?? 'DESC';
        $feedback_data = getFeedbackData($sort_by, $sort_order);
        echo json_encode($feedback_data);
        exit;
    }
}

// Get feedback data
function getFeedbackData($sort_by = 'created_at', $sort_order = 'DESC') {
    $pdo = getDatabaseConnection();
    if (!$pdo) {
        return ['error' => 'Database connection failed'];
    }
    
    // Validate sort parameters
    $allowed_sort_fields = ['id', 'name', 'email', 'rating', 'created_at'];
    $sort_by = in_array($sort_by, $allowed_sort_fields) ? $sort_by : 'created_at';
    $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';
    
    try {
        $sql = "SELECT id, name, email, rating, comments, created_at 
                FROM feedback 
                ORDER BY $sort_by $sort_order";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return ['error' => 'Database error'];
    }
}

// Delete feedback
function deleteFeedback($id) {
    $pdo = getDatabaseConnection();
    if (!$pdo) {
        return false;
    }
    
    try {
        $sql = "DELETE FROM feedback WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

// Get statistics
function getFeedbackStats() {
    $pdo = getDatabaseConnection();
    if (!$pdo) {
        return null;
    }
    
    try {
        $stats = [];
        
        // Total feedback count
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM feedback");
        $stats['total'] = $stmt->fetch()['total'];
        
        // Average rating
        $stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM feedback");
        $stats['avg_rating'] = round($stmt->fetch()['avg_rating'], 1);
        
        // Rating distribution
        $stmt = $pdo->query("SELECT rating, COUNT(*) as count FROM feedback GROUP BY rating ORDER BY rating DESC");
        $stats['rating_distribution'] = $stmt->fetchAll();
        
        return $stats;
    } catch (PDOException $e) {
        return null;
    }
}

$stats = getFeedbackStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Feedback System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .login-form {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 100px auto;
        }
        
        .login-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .login-btn {
            width: 100%;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        
        .error {
            color: #dc3545;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .controls {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .sort-controls {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .sort-controls select, .sort-controls button {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            background: white;
        }
        
        .refresh-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .feedback-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .rating {
            color: #ffd700;
            font-weight: bold;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .back-link {
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .sort-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$is_logged_in): ?>
            <!-- Login Form -->
            <div class="login-form">
                <h2>Admin Login</h2>
                <?php if (isset($login_error)): ?>
                    <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="login-btn">Login</button>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="index.html" class="back-link">← Back to Feedback Form</a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Admin Dashboard -->
            <a href="index.html" class="back-link">← Back to Feedback Form</a>
            
            <div class="header">
                <h1>Admin Panel - Feedback System</h1>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
            
            <!-- Statistics -->
            <?php if ($stats): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $stats['total']; ?></h3>
                    <p>Total Feedback</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['avg_rating']; ?></h3>
                    <p>Average Rating</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count($stats['rating_distribution']); ?></h3>
                    <p>Rating Levels</p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Controls -->
            <div class="controls">
                <div class="sort-controls">
                    <label for="sort">Sort by:</label>
                    <select id="sort">
                        <option value="created_at">Date</option>
                        <option value="rating">Rating</option>
                        <option value="name">Name</option>
                        <option value="email">Email</option>
                    </select>
                    
                    <label for="order">Order:</label>
                    <select id="order">
                        <option value="DESC">Newest First</option>
                        <option value="ASC">Oldest First</option>
                    </select>
                    
                    <button class="refresh-btn" onclick="loadFeedback()">Refresh</button>
                </div>
            </div>
            
            <!-- Feedback Table -->
            <div class="feedback-table">
                <div id="loading" class="loading">Loading feedback...</div>
                <table id="feedbackTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Rating</th>
                            <th>Comments</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="feedbackBody">
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Load feedback data
        function loadFeedback() {
            const sortBy = document.getElementById('sort').value;
            const sortOrder = document.getElementById('order').value;
            
            document.getElementById('loading').style.display = 'block';
            document.getElementById('feedbackTable').style.display = 'none';
            
            fetch(`admin.php?ajax=1&action=get_feedback&sort=${sortBy}&order=${sortOrder}`)
                .then(response => response.json())
                .then(data => {
                    displayFeedback(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loading').textContent = 'Error loading feedback';
                });
        }
        
        // Display feedback in table
        function displayFeedback(feedback) {
            const tbody = document.getElementById('feedbackBody');
            tbody.innerHTML = '';
            
            if (feedback.error) {
                document.getElementById('loading').textContent = feedback.error;
                return;
            }
            
            feedback.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${escapeHtml(item.name)}</td>
                    <td>${escapeHtml(item.email)}</td>
                    <td><span class="rating">${'★'.repeat(item.rating)}</span></td>
                    <td>${escapeHtml(item.comments.substring(0, 100))}${item.comments.length > 100 ? '...' : ''}</td>
                    <td>${formatDate(item.created_at)}</td>
                    <td>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="feedback_id" value="${item.id}">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            document.getElementById('loading').style.display = 'none';
            document.getElementById('feedbackTable').style.display = 'table';
        }
        
        // Helper functions
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('sort')) {
                loadFeedback();
                
                document.getElementById('sort').addEventListener('change', loadFeedback);
                document.getElementById('order').addEventListener('change', loadFeedback);
            }
        });
    </script>
</body>
</html>
