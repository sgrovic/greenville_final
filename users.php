<div class="users-container">
  <h2>👥 User List</h2>

  <div class="users-controls">
    <input type="text" id="searchUser" placeholder="🔍 Search user...">
    <button class="btn add" onclick="openAddUserForm()">➕ Add User</button>
  </div>

  <table class="users-table">
    <thead>
      <tr>
        <!--<th>ID</th>-->
        <th>User Name</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th style="display:none;">Password</th>
        <th>Access Level</th>        
        <!--<th>Privilege</th>-->
      </tr>
    </thead>
    <tbody id="usersTableBody">
      <tr><td colspan="6" style="text-align:center;">Loading...</td></tr>
    </tbody>
  </table>
</div>

<!-- Add User Modal -->
<div id="userModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeUserModal()">&times;</span>
    
    <h2 id="formTitle">➕ Add User</h2>
    <form id="userForm">
      
      <label>Username:</label>
      <input type="text" name="username" required>

      <label>Last Name:</label>
      <input type="text" name="lastname" required>
      
      <label>First Name:</label>
      <input type="text" name="firstname" required>

      <label>Middle Name:</label>
      <input type="text" name="middlename">

      <label>Password:</label>
      <input type="password" name="password" required>

      <label>Confirm Password:</label>
      <input type="password" name="confirmPassword" required>

      <label>Status:</label>
      <select name="status" required>
        <option value="">Select Status</option>
        <option value="Admin">Admin</option>
        <option value="User">User</option>
      </select>

      <div class="form-buttons">
        <button type="submit" class="btn save">💾 Save</button>
        <button type="button" class="btn cancel" onclick="closeUserModal()">❌ Cancel</button>
      </div>
    </form>
  </div>
</div>


<style>
  .users-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }
  .users-container h2 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 22px;
    color: #2e8b57;
  }
  .users-controls {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
  }
  .users-controls input {
    width: 60%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
  }
  /*
  .btn.add {
    background: #2e8b57;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
  }
  .btn.add:hover {
    background: #1c6d1c;
  }
  */
  .users-table {
    width: 100%;
    border-collapse: collapse;
  }
  .users-table th, .users-table td {
    border: 1px solid #ccc;
    padding: 10px;
    font-size: 14px;
    text-align: left;
  }
  .users-table th {
    background: #2e8b57;
    color: white;
  }
  .users-table tr:nth-child(even) {
    background: #f9f9f9;
  }

  /* Modal reuse (same as Students.php) */
  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
  }
  .modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  }
  .modal-content h2 {
    margin-top: 0;
    color: #2e8b57;
  }
  .modal-content label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
  }
  .modal-content input,
  .modal-content select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  .close {
    float: right;
    font-size: 20px;
    cursor: pointer;
  }

/*
  .btn.edit {
  background: #007bff;
  color: white;
  border: none;
  padding: 5px 10px;
  margin-right: 5px;
  border-radius: 5px;
  cursor: pointer;
}
.btn.edit:hover { background: #0056b3; }

.btn.delete {
  background: #dc3545;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
}
.btn.delete:hover { background: #a71d2a; }
*/

/* Buttons */
.form-buttons {
  margin-top: 15px; 
  display: flex; 
  justify-content: space-between;
}
.form-buttons .btn {
  padding: 10px 16px; 
  border: none; 
  border-radius: 6px; 
  cursor: pointer; 
  font-size: 14px; 
  font-weight: bold;
}
.form-buttons .btn.save {
  background: #2e8b57; 
  color: #fff;
}
.form-buttons .btn.save:hover {
  background: #1c6d1c;
}
.form-buttons .btn.cancel {
  background: #ccc;
}
.form-buttons .btn.cancel:hover {
  background: #999;
}

.hide-password {
  display: none;
}
</style>
