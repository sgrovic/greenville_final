<div class="student-container">
  <h2>👩‍🎓 Student List</h2>

  <div class="student-controls">
    <input type="text" id="searchStudent" placeholder="🔍 Search student...">
    <button class="btn add" onclick="openAddStudentForm()">➕ Add Student</button>
  </div>

  <table class="student-table">
    <thead>
      <tr>
        <th>Student Number</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>        
        <th>Gender</th>
        <th>Birthdate</th>
        <th>Address</th>
        <th>Guardian</th>
        <th>Contact Number</th>
        <th>Relationship</th>
      </tr>
    </thead>
    <tbody id="studentTableBody">
      <tr><td colspan="10" style="text-align:center;">Loading...</td></tr>
    </tbody>
  </table>
</div>

<!-- Add Student Modal -->
<!-- Student Modal -->
<div id="studentFormModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeStudentForm()">&times;</span>
    <h2 id="formTitle">➕ Add Student</h2>

    <form id="studentForm">
      <input type="hidden" name="action" value="add"> <!-- add/edit -->
      <input type="hidden" name="studentid"> <!-- for edit -->

      <label>Student Number:</label>
      <input type="text" name="studentnumber" required>

      <label>Last Name:</label>
      <input type="text" name="lastname" required>

      <label>First Name:</label>
      <input type="text" name="firstname" required>

      <label>Middle Name:</label>
      <input type="text" name="middlename">

      <label>Gender:</label>
      <select name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>

      <label>Date of Birth:</label>
      <input type="date" name="dob" required>

      <label>Address:</label>
      <input type="text" name="address" required>

      <label>Guardian:</label>
      <input type="text" name="guardian" required>

      <label>Contact Number:</label>
      <input type="text" name="contactnumber" required>

      <label>Relationship:</label>
      <input type="text" name="relationship" required>

      <div class="form-buttons">
        <button type="submit" class="btn save">💾 Save</button>
        <button type="button" class="btn cancel" onclick="closeStudentForm()">❌ Cancel</button>
      </div>
    </form>
  </div>
</div>


<style>
/* Modal Background */
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

/* Modal Box */
.modal-content {
  background: #fff; 
  padding: 20px 30px; 
  border-radius: 10px; 
  width: 400px; 
  max-height: 90vh; 
  overflow-y: auto; 
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  position: relative;
}

/* Close Button */
.modal-content .close {
  position: absolute; 
  top: 10px; right: 15px; 
  font-size: 22px; 
  cursor: pointer; 
  color: #888;
}
.modal-content .close:hover {
  color: #000;
}

/* Labels + Inputs */
.modal-content label {
  display: block; 
  margin: 10px 0 5px; 
  font-weight: bold; 
  color: #333;
}
.modal-content input, 
.modal-content select {
  width: 100%; 
  padding: 8px; 
  border: 1px solid #ccc; 
  border-radius: 6px; 
  font-size: 14px;
}

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

  .student-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }
  .student-container h2 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 22px;
    color: #2e8b57;
  }
  .student-controls {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
  }
  .student-controls input {
    width: 60%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
  }

  .student-table {
    width: 100%;
    border-collapse: collapse;
  }
  .student-table th, .student-table td {
    border: 1px solid #ccc;
    padding: 10px;
    font-size: 14px;
    text-align: left;
  }
  .student-table th {
    background: #2e8b57;
    color: white;
  }
  .student-table tr:nth-child(even) {
    background: #f9f9f9;
  }
  
  /* Modal Styles */
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

</style>
