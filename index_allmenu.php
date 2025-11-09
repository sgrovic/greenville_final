<?php
// Dummy data (replace with DB later)
$students = [
    ["D","D","D","D","Male","04/09/2025","D"],
    ["E","E","E","E","Female","04/09/2025","E"],
];

$users = [
    ["Diana.Yap-Diangco", "Yap-Diangco", "Diana", "Arinal", "User"],
    ["Bianca.Yap-Diangco", "Yap-Diangco", "Bianca", "Arinal", "User"],
    ["ayapdiangco", "Yap-Diangco", "Albert", "Castillote", "Admin"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment System</title>

    <h2 id="welcome"></h2>
    <!-- Example Users menu -->
    <!-- button id="usersMenu">Users</button -->

    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        .header { background: #eee; padding: 15px; font-size: 22px; font-weight: bold; border-bottom: 1px solid #ccc; }
        .container { display: flex; }
        .sidebar { width: 200px; background: #2d7a44; padding: 20px 10px; min-height: 100vh; }
        .sidebar button { display: block; width: 100%; padding: 10px; margin: 10px 0; background: #2d7a44; color: #fff; border: none; cursor: pointer; }
        .sidebar button:hover { background: #256837; }
        .content { flex: 1; padding: 20px; }
        .card { background: #fff; border: 1px solid #ccc; padding: 20px; display: flex; gap: 20px; }
        .list-section { flex: 2; }
        .list-section table { width: 100%; border-collapse: collapse; }
        .list-section th, .list-section td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        .list-section th { background: #eee; }
        .form-section { flex: 1; }
        .form-section input, .form-section select { width: 100%; padding: 6px; margin: 5px 0; }
        .form-section button { background: #2d7a44; color: white; padding: 8px 12px; margin: 5px; border: none; cursor: pointer; }
        .form-section button:hover { background: #256837; }
        .search-box { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">Enrollment System</div>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <button onclick="loadPage()">Home</button>
            <button onclick="showStudents()">Students</button>
            <button>Payment</button>
            <button onclick="showUsers()">Users</button>
            <button onclick="logout()">Logout</button>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Students Section -->
            <div id="studentsSection" class="card">
                <div class="list-section">
                    <h3>Student List</h3>
                    <div class="search-box">
                        <input type="text" id="searchStudent" placeholder="Search...">
                        <button onclick="searchStudent()">Search</button>
                    </div>
                    <table id="studentTable">
                        <thead>
                            <tr>
                                <th>Student Number</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Gender</th>
                                <th>Birthdate</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $s): ?>
                            <tr>
                                <td><?= $s[0] ?></td>
                                <td><?= $s[1] ?></td>
                                <td><?= $s[2] ?></td>
                                <td><?= $s[3] ?></td>
                                <td><?= $s[4] ?></td>
                                <td><?= $s[5] ?></td>
                                <td><?= $s[6] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-section">
                    <h3>Add Record</h3>
                    <form method="POST" action="save_student.php">
                        <input type="text" name="student_number" placeholder="Student Number" required>
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="middle_name" placeholder="Middle Name">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <input type="date" name="birthdate">
                        <input type="text" name="address" placeholder="Address">
                        <input type="text" name="guardian" placeholder="Guardian">
                        <input type="text" name="relationship" placeholder="Relationship">
                        <input type="text" name="contact" placeholder="Contact Number">
                        <div>
                            <button type="reset">Clear</button>
                            <button type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Section -->
            <div id="usersSection" class="card" style="display:none;">
                <div class="list-section">
                    <h3>User List</h3>
                    <div class="search-box">
                        <input type="text" id="searchUser" placeholder="Search...">
                        <button onclick="searchUser()">Search</button>
                    </div>
                    <table id="userTable">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $u): ?>
                            <tr>
                                <td><?= $u[0] ?></td>
                                <td><?= $u[1] ?></td>
                                <td><?= $u[2] ?></td>
                                <td><?= $u[3] ?></td>
                                <td><?= $u[4] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-section">
                    <h3>Add Record</h3>
                    <form method="POST" action="save_user.php">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="middle_name" placeholder="Middle Name">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        <select name="access">
                            <option value="">Select Access</option>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                        <div>
                            <button type="reset">Clear</button>
                            <button type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    function loadPage(page) {
      const main = document.getElementById("mainContent");
      if (page === "home") {
        main.innerHTML = "<h2>Welcome to Enrollment System</h2><p>Select a menu from the sidebar to continue.</p>";
      } else if (page === "payment") {
        main.innerHTML = "<h2>Payment Page</h2><p>Payment details here.</p>";
      } else if (page === "students") {
        main.innerHTML = "<h2>Student List</h2><p>Student table will load here.</p>";
      } else if (page === "users") {
        main.innerHTML = "<h2>User List</h2><p>User management loads here.</p>";
      }
    }
function searchStudent() {
    let input = document.getElementById("searchStudent").value.toLowerCase();
    let rows = document.querySelectorAll("#studentTable tbody tr");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
function searchUser() {
    let input = document.getElementById("searchUser").value.toLowerCase();
    let rows = document.querySelectorAll("#userTable tbody tr");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
function showHome() {
    document.getElementById("studentsSection").style.display = "none";
    document.getElementById("usersSection").style.display = "none";
}
function showStudents() {
    document.getElementById("studentsSection").style.display = "flex";
    document.getElementById("usersSection").style.display = "none";
}
function showUsers() {
    document.getElementById("studentsSection").style.display = "none";
    document.getElementById("usersSection").style.display = "flex";
}

function logout() {
    // Redirect to login page (replace with your own file)
    window.location.href = "login.php";  

    // OR if you want to just close the tab (not recommended for normal apps)
    // window.close();
}


window.onload = function() {
  const firstname = sessionStorage.getItem("firstname");
  const lastname = sessionStorage.getItem("lastname");
  const status = sessionStorage.getItem("status");

  if (!firstname || !lastname || !status) {
    // If not logged in, redirect back
    window.location.href = "login.php";
  } else {
    document.getElementById("welcome").textContent = 
      "Welcome " + firstname + " " + lastname + " (" + status + ")";
    
    // Example: Hide Users menu if not admin
    if (status !== "Admin") {
      document.getElementById("usersMenu").style.display = "none";
    }
  }
};


</script>
</body>
</html>
