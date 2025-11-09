<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Please Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background: white;
      padding: 20px 30px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 320px;
    }

    .login-container h2 {
      margin-top: 0;
      margin-bottom: 20px;
      font-size: 18px;
      text-align: center;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .form-group input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .button-group {
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    .btn {
      background-color: #228B22;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }

    .btn:hover {
      background-color: #1c6d1c;
    }

    .btn.cancel {
      background-color: #666;
    }

    .btn.cancel:hover {
      background-color: #444;
    }

    .error-message {
      color: red;
      font-size: 14px;
      text-align: center;
      margin-top: 10px;
      display: none;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2><i class="fa-solid fa-lock"></i>  Please Login</h2>
    <form id="loginForm">
      <div class="form-group">
        <label for="username">User Name</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="form-group">
        <label for="password">Enter Password</label>
        <input type="password" id="password" name="password" required>
      </div>

      <div class="button-group">
        <button type="submit" class="btn">Login</button>
        <button type="button" class="btn cancel" onclick="window.close()">Cancel</button>
      </div>

      <div id="error" class="error-message"></div>
    </form>
  </div>

<script>
document.getElementById("loginForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const errorDiv = document.getElementById("error");

  try {
    const response = await fetch("authenticate.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ username, password })
    });

    const data = await response.json();

    if (data.success) {
      // Save user info to sessionStorage
      sessionStorage.setItem("firstname", data.firstname);
      sessionStorage.setItem("lastname", data.lastname);
      sessionStorage.setItem("status", data.status);

      // Redirect to index.php
      window.location.href = "index.php";
    } else {
      errorDiv.style.display = "block";
      errorDiv.textContent = data.message;
    }
  } catch (err) {
    errorDiv.style.display = "block";
    errorDiv.textContent = "Server error. Please try again.";
  }
});
</script>

</body>
</html>
