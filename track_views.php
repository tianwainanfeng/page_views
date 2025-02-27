<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Start debugging...<br>"; // Add this to see if PHP runs at all
*/

// ======== Database connection ========

$servername = getenv('PROJECT_DB_HOST');
$username = getenv('PROJECT_DB_USER');
$password = getenv('PROJECT_DB_PASSWORD');
$dbname = getenv('PROJECT_DB_NAME');

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ======== Communicate with database  ========

// Detect the correct page URL:
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    $page_url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH); // Get the referring page path
} else {
    $page_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Get the current PHP file path
}

// Ensure the page_url always starts with a single leading slash
$page_url = '/' . ltrim($page_url, '/');

$page_url = $conn->real_escape_string($page_url); // Prevent SQL injection

// Get user details
$ip_address = $_SERVER['REMOTE_ADDR'] === '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
$ip_address = $conn->real_escape_string($ip_address);

$user_agent = $conn->real_escape_string($_SERVER['HTTP_USER_AGENT']);
$referrer = isset($_SERVER['HTTP_REFERER']) ? $conn->real_escape_string($_SERVER['HTTP_REFERER']) : '';

// Start transaction for atomic updates
$conn->begin_transaction();

// -- Update Total View Count --

$conn->query("
    INSERT INTO page_views (page_url, view_count)
    VALUES ('$page_url', 1)
    ON DUPLICATE KEY UPDATE view_count = view_count + 1
");

// -- Update Unique IP Count --

$ip_check = $conn->query("SELECT * FROM page_view_ips WHERE page_url = '$page_url' AND ip_address = '$ip_address'");

if ($ip_check->num_rows == 0) {
    $conn->query("INSERT INTO page_view_ips (page_url, ip_address, view_count) VALUES ('$page_url', '$ip_address', 1)");
    $conn->query("UPDATE page_views SET unique_ip_count = unique_ip_count + 1 WHERE page_url = '$page_url'");
} else {
    $conn->query("UPDATE page_view_ips SET view_count = view_count + 1 WHERE page_url = '$page_url' AND ip_address = '$ip_address'");
}

// -- Update Unique User-Agent Count --

$user_agent_check = $conn->query("SELECT * FROM page_view_user_agents WHERE page_url = '$page_url' AND user_agent = '$user_agent'");

if ($user_agent_check->num_rows == 0) {
    $conn->query("INSERT INTO page_view_user_agents (page_url, user_agent, view_count) VALUES ('$page_url', '$user_agent', 1)");
    $conn->query("UPDATE page_views SET unique_user_agent_count = unique_user_agent_count + 1 WHERE page_url = '$page_url'");
} else {
    $conn->query("UPDATE page_view_user_agents SET view_count = view_count + 1 WHERE page_url = '$page_url' AND user_agent = '$user_agent'");
}

// -- Update Unique Referrer Count --

if (!empty($referrer)) {
    $referrer_check = $conn->query("SELECT * FROM page_view_referrers WHERE page_url = '$page_url' AND referrer = '$referrer'");

    if ($referrer_check->num_rows == 0) {
        $conn->query("INSERT INTO page_view_referrers (page_url, referrer, view_count) VALUES ('$page_url', '$referrer', 1)");
        $conn->query("UPDATE page_views SET unique_referrer_count = unique_referrer_count + 1 WHERE page_url = '$page_url'");
    } else {
        $conn->query("UPDATE page_view_referrers SET view_count = view_count + 1 WHERE page_url = '$page_url' AND referrer = '$referrer'");
    }
}

// Commit transaction
$conn->commit();

// ======== Results from database ========

// Fetch latest view count
$result = $conn->query("SELECT view_count FROM page_views WHERE page_url = '$page_url'");
$row = $result->fetch_assoc();
echo $row ? $row['view_count'] : 1;
$view_count = $row['view_count'];

$conn->close();

?>
