<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enrollment System</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f9; }
    .header { 
      background: #256837; 
      color: white; 
      padding: 15px; 
      font-size: 22px; 
      font-weight: bold; 
      border-bottom: 3px solid #1c6d1c;
      text-align: center;
    }
    .sidebar { 
      width: 220px; 
      background: #256837; 
      color: white; 
      float: left; 
      height: 100vh; 
      padding-top: 30px; 
      box-shadow: 2px 0 6px rgba(0,0,0,0.2);
    }
    .sidebar h3 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 18px;
      border-bottom: 1px solid rgba(255,255,255,0.3);
      padding-bottom: 10px;
    }
    .sidebar button { 
      display: block; 
      width: 85%; 
      margin: 12px auto; 
      padding: 12px; 
      border: none; 
      background: #256837; 
      color: white; 
      font-weight: bold; 
      cursor: pointer; 
      border-radius: 6px; 
      text-align: left;
      transition: all 0.2s ease-in-out;
      font-size: 15px;
    }
    .sidebar button:hover { 
      background: #39a055; 
      transform: scale(1.03);
    }
    .sidebar button:active { 
      background: #145214; 
      transform: scale(0.98);
    }
    .content { 
      margin-left: 230px; 
      padding: 25px; 
      min-height: 90vh;
      background: #fff;
      border-radius: 8px;
      margin-top: 20px;
      margin-right: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    
  </style>
</head>
<body>
  <div class="header">
    <h1>Enrollment System</h1>
    <button class="logout-btn"><i class="fa fa-sign-out-alt"></i> Logout</button>
  </div>

  <div class="sidebar">
    <h3>Menu</h3>
    
    <button onclick="loadPage('home')"><i class="fa fa-home"></i> Home</button>
    <button onclick="loadPage('payment')"><i class="fa fa-coins"></i> Payments</button>
    <button onclick="loadPage('students')"><i class="fa fa-users"></i> Students</button>
    <button onclick="loadPage('users')"><i class="fa fa-user-shield"></i> Users</button>
    <button onclick="logout()"><i class="fa fa-sign-out-alt"></i> Logout</button>
    
    <!--
    <button onclick="loadPage('home')">🏠 Home</button>
    <button onclick="loadPage('payment')">💰 Payment</button>
    <button onclick="loadPage('students')">🎓 Students</button>
    <button onclick="loadPage('users')">👥 Users</button>
    <button onclick="logout()">🚪 Logout</button>

    <a href="#"><i class="fa fa-home"></i> Home</a>
    <a href="#"><i class="fa fa-coins"></i> Payment</a>
    <a href="#"><i class="fa fa-users"></i> Students</a>
    <a href="#"><i class="fa fa-user-shield"></i> Users</a>
    <a href="#"><i class="fa fa-sign-out-alt"></i> Logout</a>
    -->
  </div>
  <div class="main-content" id="mainContent">
    <h2>Welcome to Enrollment System</h2>
    <p>Select a menu from the sidebar to continue.</p>
  </div>

  <script>
    async function loadPage(page) {
      const main = document.getElementById("mainContent");
      if (page === "home") {
        main.innerHTML = "<h2>Welcome to Enrollment System</h2><p>Select a menu from the sidebar to continue.</p>";
      } else if (page === "payment") {
        //main.innerHTML = "<h2>Student List</h2><p>Student table will load here.</p>";
        const response = await fetch("Payment.php");
        main.innerHTML = await response.text();
        //loadStudents(); // defined in students.js
      } else if (page === "students") {
        //main.innerHTML = "<h2>Student List</h2><p>Student table will load here.</p>";
        const response = await fetch("Students.php");
        main.innerHTML = await response.text();
        loadStudents(); // defined in students.js
      } else if (page === "users") {
        //main.innerHTML = "<h2>User List</h2><p>User management loads here.</p>";
        const response = await fetch("Users.php");
        main.innerHTML = await response.text();
        loadUsers(); // call after HTML is injected
      }
    }

    function logout() {
      sessionStorage.clear();
      window.location.href = "login.php";
    }

    window.onload = function() {
      const firstname = sessionStorage.getItem("firstname");
      const lastname = sessionStorage.getItem("lastname");
      const status = sessionStorage.getItem("status");

      if (!firstname || !lastname || !status) {
        window.location.href = "login.php"; 
      } else {
        if (status !== "Admin") {
          document.getElementById("usersMenu").style.display = "none";
        }
      }
      loadPage("home"); // show Home by default
    };

      <!-- external JS -->
  
  </script>

  <script src="students.js"></script>
  <script src="users.js"></script>  
</body>
</html>
