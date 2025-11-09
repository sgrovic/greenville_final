console.log("✅ students.js loaded");

async function loadStudents() {
  console.log("✅ loadStudents() started");

  const tableBody = document.getElementById("studentTableBody");
  if (!tableBody) {
    console.error("❌ Table body not found!");
    return;
  }

  tableBody.innerHTML = `<tr><td colspan="10" style="text-align:center;">Loading...</td></tr>`;

  try {
    const response = await fetch("students_api.php?action=list");
    const text = await response.text();
    console.log("RAW API Response:", text);

    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("❌ JSON parse error:", e);
      return;
    }

    console.log("Parsed JSON:", data);

    if (data.success) {
      tableBody.innerHTML = "";
      data.data.forEach(student => {
        const row = `<tr>
          <td>${student.studentnumber}</td>
          <td>${student.lastname}</td>
          <td>${student.firstname}</td>
          <td>${student.middlename}</td>
          <td>${student.gender}</td>
          <td>${student.dob}</td>
          <td>${student.address}</td>
          <td>${student.guardian}</td>
          <td>${student.contactnumber}</td>
          <td>${student.relationship}</td>
          <td>
            <button class="btn edit" onclick="editFromRowStudents(this)"('${student.studentnumber}')">✏️ Edit</button>
            <button class="btn delete" onclick="deleteStudent('${student.studentnumber}')">🗑️ Delete</button>
          </td>
        </tr>`;
        tableBody.insertAdjacentHTML("beforeend", row);
      });
    } else {
      tableBody.innerHTML = `<tr><td colspan="10" style="text-align:center; color:red;">${data.message}</td></tr>`;
    }

  } catch (err) {
    console.error("❌ Fetch error:", err);
    tableBody.innerHTML = `<tr><td colspan="10" style="text-align:center; color:red;">Failed to load data</td></tr>`;
  }
}

// Placeholder for Add button
function addStudent() {
  document.getElementById("studentFormModal").style.display = "flex";
}

function closeStudentForm() {
  document.getElementById("studentFormModal").style.display = "none";
}

function closePaymentForm() {
  document.getElementById("paymentFormModal").style.display = "none";
}

async function deleteStudent(studentNumber) {
  if (!confirm("Are you sure you want to delete student " + studentNumber + "?")) return;

  try {
    const response = await fetch("students_api.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ StudentNumber: studentNumber })
    });

    const result = await response.json();
    if (result.success) {
      alert("✅ Student deleted!");
      loadStudents(); // reload table
    } else {
      alert("❌ Delete failed: " + result.message);
    }
  } catch (err) {
    console.error("Delete failed:", err);
    alert("❌ Error deleting student.");
  }
}

let searchTimeout;

// 🔍 Live search students with debounce
document.addEventListener("input", function (e) {
  //alert(e.target.id);
  if (e.target.id == "searchStudent") {
    clearTimeout(searchTimeout);

    const query = e.target.value.trim();

    searchTimeout = setTimeout(async () => {
      if (query.length === 0) {
        loadStudents(); // reload all when cleared
        return;
      }

      try {
        const response = await fetch("students_api.php?action=search", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ Lastname: query })
        });

        const result = await response.json();
        const tbody = document.getElementById("studentTableBody");
        tbody.innerHTML = "";

        if (result.success && result.data.length > 0) {
          result.data.forEach(student => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${student.studentnumber}</td>
              <td>${student.lastname}</td>
              <td>${student.firstname}</td>
              <td>${student.middlename}</td>
              <td>${student.gender}</td>
              <td>${student.dob}</td>
              <td>${student.address}</td>
              <td>${student.guardian}</td>
              <td>${student.contactnumber}</td>
              <td>${student.relationship}</td>
              <td>
                <button class="btn edit" onclick="editFromRowStudents(this)"('${student.studentnumber}')">✏️ Edit</button>
                <button class="btn delete" onclick="deleteStudent('${student.studentnumber}')">🗑️ Delete</button>
              </td>
            `;
            tbody.appendChild(tr);
          });
        } else {
          tbody.innerHTML = "<tr><td colspan='10' style='text-align:center;'>No results found</td></tr>";
        }
      } catch (err) {
        console.error("Search failed:", err);
      }
    }, 300); // wait 300ms after typing stops
  }
  else if (e.target.id == "searchStudentpayment") {
    clearTimeout(searchTimeout);

    const query = e.target.value.trim();

    searchTimeout = setTimeout(async () => {
      if (query.length === 0) {
        loadStudents(); // reload all when cleared
        return;
      }

      try {
        const response = await fetch("students_api.php?action=search", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ Lastname: query })
        });

        const result = await response.json();
        const tbody = document.getElementById("studentTableBody");
        tbody.innerHTML = "";

        if (result.success && result.data.length > 0) {
          result.data.forEach(student => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${student.studentnumber}</td>
              <td>${student.firstname}</td>
              <td>${student.middlename}</td>
              <td>${student.lastname}</td>
              <td>${student.gender}</td>
              <td>
                <button class="btn edit" onclick="paymentform(this)"('${student.studentnumber}')">✏️ Pay</button>
                <button class="btn delete" onclick="printpayment('${student.studentnumber}')">🗑️ Print</button>
              </td>
            `;
            tbody.appendChild(tr);
          });
        } else {
          tbody.innerHTML = "<tr><td colspan='10' style='text-align:center;'>No results found</td></tr>";
        }
      } catch (err) {
        console.error("Search failed:", err);
      }
    }, 300); // wait 300ms after typing stops
  }
});

let isEditing = false; // define flag

// Open modal for adding new student
function openAddStudentForm() {
  alert("openAddStudentForm"); 
  isEditing = false;
  document.getElementById("formTitle").textContent = "➕ Add Student";
  const form = document.getElementById("studentForm");
  form.reset();
  form.action.value = "add";
  form.studentid.value = "";
  document.getElementById("studentFormModal").style.display = "flex";
}

// Open modal for editing an existing student
function openEditStudentForm(student) {
  //alert("openEditStudentForm"); 
  isEditing = true;
  document.getElementById("formTitle").textContent = "✏️ Edit Student";
  const form = document.getElementById("studentForm");

  // Fill form with existing values
  form.action.value = "edit";
  //form.studentid.value = student.studentid; // assuming API provides ID
  form.studentnumber.value = student.studentnumber;
  form.firstname.value = student.firstname;
  form.middlename.value = student.middlename;
  form.lastname.value = student.lastname;
  form.gender.value = student.gender;
  form.dob.value = student.dob;
  form.address.value = student.address;
  form.guardian.value = student.guardian;
  form.contactnumber.value = student.contactnumber;
  form.relationship.value = student.relationship;

  document.getElementById("studentFormModal").style.display = "flex";
}

function openPaymentForm(student) {
  //alert("openPaymentForm"); 
  document.getElementById("formTitle").textContent = "✏️ Payment";
  const form = document.getElementById("paymentForm");

  // Fill form with existing values
  form.action.value = "edit";
  //form.studentid.value = student.studentid; // assuming API provides ID
  form.studentnumber.value = student.studentnumber;
  form.firstname.value = student.firstname;
  form.middlename.value = student.middlename;
  form.lastname.value = student.lastname;
  form.name.value = student.name;
  //form.gender.value = student.gender;
  //form.dob.value = student.dob;
  //form.address.value = student.address;
  //form.guardian.value = student.guardian;
  //form.contactnumber.value = student.contactnumber;
  //form.relationship.value = student.relationship;

  document.getElementById("paymentFormModal").style.display = "flex";
}

document.addEventListener("submit", async (e) => {
  if (e.target && e.target.id === "studentForm") {
    e.preventDefault();

    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());

    // quick validation
    if (!data.studentnumber || !data.firstname || !data.lastname) {
      alert("Student Number, First Name and Last Name are required.");
      return;
    }

    try {
      const action = isEditing ? "update" : "add"; // choose API action
      const res = await fetch(`students_api.php?action=${action}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });

      const out = await res.json();
      console.log(`API ${action} response:`, out);

      if (out.success) {
        alert(`✅ Student ${isEditing ? "updated" : "added"} successfully!`);
        closeStudentForm();
        loadStudents(); // refresh table
      } else {
        alert("❌ Failed: " + out.message);
      }
    } catch (err) {
      console.error("Save failed:", err);
      alert("❌ Error saving student.");
    }
  }
});

function editFromRowStudents(button) {
  const row = button.closest("tr");
  const cells = row.querySelectorAll("td");

  alert("1 ✅" + cells[1].textContent);
  alert("2 ✅" + cells[2].textContent);
  alert("3 ✅" + cells[3].textContent);
  // Build student object from row values
  const student = {
    //studentid: cells[0].textContent,
    studentnumber: cells[0].textContent,
    firstname: cells[2].textContent,
    middlename: cells[3].textContent,
    lastname: cells[1].textContent,
    gender: cells[4].textContent,
    dob: cells[5].textContent,
    address: cells[6].textContent,
    guardian: cells[7].textContent,
    contactnumber: cells[8].textContent,
    relationship:cells[9].textContent
  };

  openEditStudentForm(student);
}

function paymentform(button) {
  const row = button.closest("tr");
  const cells = row.querySelectorAll("td");

  // Build student object from row values
  const student = {
    //studentid: cells[0].textContent,
    studentnumber: cells[0].textContent,
    firstname: cells[1].textContent,
    middlename: cells[2].textContent,
    lastname: cells[3].textContent,
    name: [cells[1].textContent, cells[2].textContent, cells[3].textContent]
            .map(x => x.trim())
            .filter(Boolean)
            .join(" ")
    //gender: cells[4].textContent,
    //dob: cells[5].textContent,
    //address: cells[6].textContent,
    //guardian: cells[7].textContent,
    //contactnumber: cells[8].textContent,
    //relationship:cells[9].textContent
  };

  //alert("Passwords match ✅" + student.name);

  //openEditStudentForm(student);
  openPaymentForm(student);
}

function updateFullName() {
  let first = document.getElementById("firstname").value.trim();
  let middle = document.getElementById("middlename").value.trim();
  let last = document.getElementById("lastname").value.trim();

  // Combine with spaces (skip empty middle if not provided)
  let fullName = [first, middle, last].filter(Boolean).join(" ");

  document.getElementById("fullname").value = fullName;
}


