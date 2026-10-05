<?php
// ==========================================
// LIVE DATABASE CREDENTIALS
// Replace these with the details provided by your web host!
// ==========================================
$servername = "localhost"; 
$username = "if0_43094308"; 
$password = "kingz10000";  
$dbname = "if0_43094308_scotty_tennis_db";    

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection safely (doesn't reveal server info to users)
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Sorry, we are experiencing technical difficulties. Please try again later.");
}

// Check if the form was actually submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // SANITIZE INPUTS (Crucial for live websites to prevent XSS and SQL Injection)
    $full_name = htmlspecialchars(strip_tags(trim($_POST['full_name'])));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
    $skill_level = htmlspecialchars(strip_tags(trim($_POST['skill_level'])));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format. Please try again.";
    } else {
        // PREPARED STATEMENT (The safest way to insert data)
        $stmt = $conn->prepare("INSERT INTO members (full_name, email, phone, skill_level) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $phone, $skill_level);

        // Execute the query
        if ($stmt->execute()) {
            $success_message = "Welcome to the club, $full_name! Your application has been successfully submitted.";
        } else {
            // Log the actual error for you, show generic error to the user
            error_log("Insert Error: " . $stmt->error);
            $error_message = "An error occurred while submitting your application. Please try again.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!-- HTML to display the success or error message to the user -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status | Scotty Tennis Club</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; }
        .status-container {
            text-align: center;
            background: white;
            padding: 60px 40px;
            max-width: 600px;
            margin: 100px auto;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .status-container h1 { font-family: 'Oswald', sans-serif; font-size: 2.5rem; margin-bottom: 15px;}
        .status-container p { margin-bottom: 30px; font-size: 1.1rem; color: #4b5563; line-height: 1.6; }
        .btn-back { background: #111827; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 6px; transition: 0.3s; }
        .btn-back:hover { background: #6fee15; color: #111827; }
        .text-success { color: #6fee15; }
        .text-error { color: #ef4444; }
    </style>
</head>
<body>
    <div class="status-container">
        <?php if (isset($success_message)): ?>
            <h1 class="text-success">Application Received!</h1>
            <p><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <h1 class="text-error">Oops!</h1>
            <p><?php echo $error_message; ?></p>
        <?php else: ?>
            <h1>No Data Received</h1>
            <p>Please submit the form directly from the Join Us page.</p>
        <?php endif; ?>
        
        <a href="../home.html" class="btn-back">Return to Home</a>
    </div>
</body>
</html>