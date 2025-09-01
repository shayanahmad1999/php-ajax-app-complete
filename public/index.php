<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP AJAX PostgreSQL App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-users"></i> User Management System</h1>
            <p>A modern PHP application with AJAX and PostgreSQL</p>
        </header>

        <div class="main-content">
            <div class="form-section">
                <h2><i class="fas fa-user-plus"></i> Add New User</h2>
                <form id="userForm" class="user-form">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Phone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </form>
            </div>

            <div class="users-section">
                <h2><i class="fas fa-list"></i> Users List</h2>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search users...">
                    <i class="fas fa-search"></i>
                </div>
                <div id="usersList" class="users-list">
                    <!-- Users will be loaded here via AJAX -->
                </div>
            </div>
        </div>

        <div id="notification" class="notification"></div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>

