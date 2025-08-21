<?php
/**
 * Feedback Submission Handler
 * 
 * This script receives POST data from the feedback form,
 * validates and sanitizes the inputs, and inserts them into the database.
 */

// Include database configuration
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if JSON is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
$required_fields = ['name', 'email', 'rating', 'comments'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit;
    }
}

// Extract and sanitize data
$name = trim($input['name']);
$email = trim($input['email']);
$rating = (int)$input['rating'];
$comments = trim($input['comments']);

// Validate name (2-50 characters, letters and spaces only)
if (!preg_match('/^[a-zA-Z\s]{2,50}$/', $name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name must be 2-50 characters and contain only letters and spaces']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

// Validate rating (1-5)
if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit;
}

// Validate comments (10-1000 characters)
if (strlen($comments) < 10 || strlen($comments) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Comments must be between 10 and 1000 characters']);
    exit;
}

// Get database connection
$pdo = getDatabaseConnection();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Prepare SQL statement
    $sql = "INSERT INTO feedback (name, email, rating, comments) VALUES (:name, :email, :rating, :comments)";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
    
    // Execute the statement
    $result = $stmt->execute();
    
    if ($result) {
        // Success response
        echo json_encode([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'id' => $pdo->lastInsertId()
        ]);
    } else {
        // Database error
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save feedback']);
    }
    
} catch (PDOException $e) {
    // Log error if in debug mode
    if (DEBUG_MODE) {
        error_log("Database error: " . $e->getMessage());
    }
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    
} catch (Exception $e) {
    // Log error if in debug mode
    if (DEBUG_MODE) {
        error_log("General error: " . $e->getMessage());
    }
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}
?>
