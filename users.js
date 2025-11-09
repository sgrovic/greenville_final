console.log("✅ users.js loaded");

function addUser() {
    document.getElementById("userModal").style.display = "flex";
  }
  
  function closeUserModal() {
    document.getElementById("userModal").style.display = "none";
  }

  function editUser(username) {
    alert("Edit user: " + username);
    // Later: load user details in a modal form
  }
  
  async function deleteUser(username) {
    if (!confirm("Are you sure you want to delete user " + username + "?")) return;
  
    try {
      const response = await fetch("users_api.php?action=delete", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ Username: username })
      });
  
      const result = await response.json();
      if (result.success) {
        alert("✅ User deleted! xxxxxxxxxxxxxxxxxxxxx");
        loadUsers(); // refresh list
      } else {
        alert("❌ Delete failed: " + result.message);
      }
    } catch (err) {
      console.error("Delete failed:", err);
      alert("❌ Error deleting user.");
    }
  }
  
  async function loadUsers() {
    const tableBody = document.getElementById("usersTableBody");
    if (!tableBody) return;
  
    tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Loading...</td></tr>`;
  
    try {
      const response = await fetch("users_api.php?action=list");
      const data = await response.json();
  
      if (data.success) {
        tableBody.innerHTML = data.data.map(user => `
          <tr>
            <td>${user.username}</td>
            <td>${user.lastname}</td>
            <td>${user.firstname}</td>
            <td>${user.middlename}</td>
            <td class="hide-password">${user.password}</td>
            <td>${user.status}</td>
            <td>
                <button class="btn edit" onclick="editFromRowUsers(this)('${user.username}')">✏️ Edit</button>
                <button class="btn delete" onclick="deleteUser('${user.username}')">🗑️ Delete</button>
            </td>
          </tr>
        `).join("");
      } else {
        tableBody.innerHTML = `<tr><td colspan="6">${data.message}</td></tr>`;
      }
    } catch (err) {
      tableBody.innerHTML = `<tr><td colspan="6">Error loading users</td></tr>`;
      console.error("❌ Error loading users:", err);
    }
  }
  
  /*
  // ✅ Delegated listener so it works even if Students.php is loaded dynamically
  document.addEventListener("submit", async (e) => {
    if (e.target && e.target.id === "userForm") {
      e.preventDefault();
  
      const form = e.target;
      const data = Object.fromEntries(new FormData(form).entries());
  
      if (data.password === data.confirmPassword) {
        //alert("Passwords match ✅");
      } else {
        alert("Passwords do not match ❌");
        return;
      }

      // quick validation
      if (!data.username || !data.firstname || !data.lastname) {
        alert("User Number, First Name and Last Name are required.");
        return;
      }
  
      try {
        const res = await fetch("users_api.php?action=add", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
        });
  
        const out = await res.json();
        console.log("API add response:", out);
  
        if (out.success) {
          alert("✅ User saved successfully!");
          closeUserModal();
          loadUsers(); // refresh table
        } else {
          alert("❌ Failed: " + out.message);
        }
      } catch (err) {
        console.error("Save failed:", err);
        alert("❌ Error saving User.");
      }
    }
  });
*/

  function checkPassword() {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
  
    if (password === "") {
      alert("Password cannot be empty!");
      return false;
    }
  
    if (password === confirmPassword) {
      alert("Passwords match ✅");
      return true;
    } else {
      alert("Passwords do not match ❌");
      return false;
    }
  }

  let userSearchTimeout;

// 🔍 Live search users with debounce
document.addEventListener("input", function (e) {
  if (e.target.id === "searchUser") {
    clearTimeout(userSearchTimeout);

    const query = e.target.value.trim();

    userSearchTimeout = setTimeout(async () => {
      if (query.length === 0) {
        loadUsers(); // reload all users when cleared
        return;
      }

      try {
        const response = await fetch("users_api.php?action=search", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ Lastname: query })
        });

        const result = await response.json();
        const tbody = document.getElementById("usersTableBody");
        tbody.innerHTML = "";

        if (result.success && result.data.length > 0) {
          result.data.forEach(user => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${user.username}</td>
              <td>${user.lastname}</td>
              <td>${user.firstname}</td>
              <td>${user.middlename}</td>
              <td>${user.status}</td>
              <td>
                  <button class="btn edit" onclick="editFromRowUsers('${user.username}')">✏️ Edit</button>
                  <button class="btn delete" onclick="deleteUser('${user.username}')">🗑️ Delete</button>
              </td>              
            `;
            tbody.appendChild(tr);
          });
        } else {
          tbody.innerHTML = "<tr><td colspan='5' style='text-align:center;'>No results found</td></tr>";
        }
      } catch (err) {
        console.error("Search failed:", err);
      }
    }, 300); // wait 300ms after typing stops
  }
});

let isEditingUser = false; // define flag

// Open modal for adding new student
function openAddUserForm() {
  //alert("openAddUserForm"); 
  isEditingUser = false;
  document.getElementById("formTitle").textContent = "➕ Add User";
  const form = document.getElementById("userForm");
  form.reset();
  form.action.value = "add";
  form.username.value = "";
  document.getElementById("userModal").style.display = "flex";
}

// Open modal for editing an existing student
function openEditUserForm(user) {
  //alert("openEditUserForm"); 
  isEditingUser = true;
  document.getElementById("formTitle").textContent = "✏️ Edit User";
  const form = document.getElementById("userForm");

  // Fill form with existing values
  form.action.value = "edit";
  //form.studentid.value = student.studentid; // assuming API provides ID
  form.username.value = user.username;
  form.firstname.value = user.firstname;
  form.middlename.value = user.middlename;
  form.lastname.value = user.lastname;
  form.password.value = user.password;
  form.confirmPassword = user.password;
  form.status.value = user.status;

  //alert("username: "+user.username+":"+" firstname: "+user.firstname+":"+" middlename: "+user.middlename);
  //alert("lastname: "+user.lastname+":"+" password: "+user.password+":"+" status: "+user.status);

  document.getElementById("userModal").style.display = "flex";
}

document.addEventListener("submit", async (e) => {
  if (e.target && e.target.id === "userForm") {
    e.preventDefault();

    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());

    if (data.password === data.confirmPassword) {
      //alert("Passwords match ✅");
    } else {
      alert("Passwords do not match ❌❌❌");
      return;
    }

    //alert("x username: "+data.username+":"+" firstname: "+data.firstname+":"+" middlename: "+data.middlename);
    //alert("x lastname: "+data.lastname+":"+" password: "+data.password+":"+" status: "+data.status);
    
    //alert(JSON.stringify(data, null, 2));

    delete data.confirmPassword;

    //alert(JSON.stringify(data, null, 2));

    // quick validation
    if (!data.username || !data.firstname || !data.lastname) {
      alert("User Name, First Name and Last Name are required.");
      return;
    }

    try {
      const action = isEditingUser ? "update" : "add"; // choose API action
      const res = await fetch(`users_api.php?action=${action}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });

      const out = await res.json();
      console.log(`API ${action} response:`, out);

      if (out.success) {
        alert(`✅ User ${isEditingUser ? "updated" : "added"} successfully!`);
        closeUserForm();
        loadUsers(); // refresh table
      } else {
        alert("❌ Failed: " + out.message);
      }
    } catch (err) {
      console.error("Save failed:", err);
      alert("❌ Error saving student.");
    }
  }
});

function editFromRowUsers(button) {
  const row = button.closest("tr");
  const cells = row.querySelectorAll("td");

  // Build student object from row values
  const user = {
    username: cells[0].textContent,
    firstname: cells[2].textContent,
    middlename: cells[3].textContent,
    lastname: cells[1].textContent,
    password: cells[4].textContent,
    status: cells[5].textContent,
  };

  //alert("* username: "+user.username+":"+" firstname: "+user.firstname+":"+" middlename: "+user.middlename);
  //alert("* lastname: "+user.lastname+":"+" password: "+user.password+":"+" status: "+user.status);

  openEditUserForm(user);
}

function closeUserForm() {
  document.getElementById("userModal").style.display = "none";
}
