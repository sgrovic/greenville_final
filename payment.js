console.log("✅ payment.js loaded");

async function loadpayments() {
  console.log("✅ loadpayments() started");

  const tableBody = document.getElementById("paymentTableBody");
  if (!tableBody) {
    console.error("❌ Table body not found!");
    return;
  }

  tableBody.innerHTML = `<tr><td colspan="10" style="text-align:center;">Loading...</td></tr>`;

  try {
    const response = await fetch("payments_api.php?action=list");
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
      data.data.forEach(payment => {
        const row = `<tr>
          <td>${payment.paymentnumber}</td>
          <td>${payment.firstname}</td>
          <td>${payment.middlename}</td>
          <td>${payment.lastname}</td>
          <td>${payment.gender}</td>
          <td>${payment.dob}</td>
          <td>${payment.address}</td>
          <td>${payment.guardian}</td>
          <td>${payment.contactnumber}</td>
          <td>${payment.relationship}</td>
          <td>
            <button class="btn edit" onclick="editFromRow(this)"('${payment.paymentnumber}')">✏️ Edit</button>
            <button class="btn delete" onclick="deletepayment('${payment.paymentnumber}')">🗑️ Delete</button>
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
function addpayment() {
  document.getElementById("paymentFormModal").style.display = "flex";
}
function closepaymentForm() {
  document.getElementById("paymentFormModal").style.display = "none";
}

/*
// ✅ Delegated listener so it works even if payments.php is loaded dynamically
document.addEventListener("submit", async (e) => {
  if (e.target && e.target.id === "paymentForm") {
    e.preventDefault();

    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());

    // quick validation
    if (!data.paymentnumber || !data.firstname || !data.lastname) {
      alert("payment Number, First Name and Last Name are required.");
      return;
    }

    try {
      const res = await fetch("payments_api.php?action=add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });

      const out = await res.json();
      console.log("API add response:", out);

      if (out.success) {
        alert("✅ payment saved successfully! xxx");
        closepaymentForm();
        loadpayments(); // refresh table
      } else {
        alert("❌ Failed: " + out.message);
      }
    } catch (err) {
      console.error("Save failed:", err);
      alert("❌ Error saving payment.");
    }
  }
});
*/

async function deletepayment(paymentNumber) {
  if (!confirm("Are you sure you want to delete payment " + paymentNumber + "?")) return;

  try {
    const response = await fetch("payments_api.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ paymentNumber: paymentNumber })
    });

    const result = await response.json();
    if (result.success) {
      alert("✅ payment deleted!");
      loadpayments(); // reload table
    } else {
      alert("❌ Delete failed: " + result.message);
    }
  } catch (err) {
    console.error("Delete failed:", err);
    alert("❌ Error deleting payment.");
  }
}

let searchTimeout;

// 🔍 Live search payments with debounce
document.addEventListener("input", function (e) {
  alert("addEventListener"); 
  if (e.target.id === "searchpayment") {
    clearTimeout(searchTimeout);

    const query = e.target.value.trim();

    searchTimeout = setTimeout(async () => {
      if (query.length === 0) {
        loadpayments(); // reload all when cleared
        return;
      }

      try {
        const response = await fetch("payments_api.php?action=search", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ Lastname: query })
        });

        const result = await response.json();
        const tbody = document.getElementById("paymentTableBody");
        tbody.innerHTML = "";

        if (result.success && result.data.length > 0) {
          result.data.forEach(payment => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${payment.paymentnumber}</td>
              <td>${payment.firstname}</td>
              <td>${payment.middlename}</td>
              <td>${payment.lastname}</td>
              <td>${payment.gender}</td>
              <td>${payment.dob}</td>
              <td>${payment.address}</td>
              <td>${payment.guardian}</td>
              <td>${payment.contactnumber}</td>
              <td>${payment.relationship}</td>
              <td>
                <button class="btn edit" onclick="editFromRow(this)"('${payment.paymentnumber}')">✏️ Edit</button>
                <button class="btn delete" onclick="deletepayment('${payment.paymentnumber}')">🗑️ Delete</button>
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

// Open modal for adding new payment
function openAddpaymentForm() {
  alert("openAddpaymentForm"); 
  isEditing = false;
  document.getElementById("formTitle").textContent = "➕ Add payment";
  const form = document.getElementById("paymentForm");
  form.reset();
  form.action.value = "add";
  form.paymentid.value = "";
  document.getElementById("paymentFormModal").style.display = "flex";
}

// Open modal for editing an existing payment
function openEditpaymentForm(payment) {
  alert("openEditpaymentForm"); 
  isEditing = true;
  document.getElementById("formTitle").textContent = "✏️ Edit payment";
  const form = document.getElementById("paymentForm");

  // Fill form with existing values
  form.action.value = "edit";
  //form.paymentid.value = payment.paymentid; // assuming API provides ID
  form.paymentnumber.value = payment.paymentnumber;
  form.firstname.value = payment.firstname;
  form.middlename.value = payment.middlename;
  form.lastname.value = payment.lastname;
  form.gender.value = payment.gender;
  form.dob.value = payment.dob;
  form.address.value = payment.address;
  form.guardian.value = payment.guardian;
  form.contactnumber.value = payment.contactnumber;
  form.relationship.value = payment.relationship;

  document.getElementById("paymentFormModal").style.display = "flex";
}

document.addEventListener("submit", async (e) => {
  if (e.target && e.target.id === "paymentForm") {
    e.preventDefault();

    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());

    // quick validation
    if (!data.paymentnumber || !data.firstname || !data.lastname) {
      alert("payment Number, First Name and Last Name are required.");
      return;
    }

    try {
      const action = isEditing ? "update" : "add"; // choose API action
      const res = await fetch(`payments_api.php?action=${action}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });

      const out = await res.json();
      console.log(`API ${action} response:`, out);

      if (out.success) {
        alert(`✅ payment ${isEditing ? "updated" : "added"} successfully!`);
        closepaymentForm();
        loadpayments(); // refresh table
      } else {
        alert("❌ Failed: " + out.message);
      }
    } catch (err) {
      console.error("Save failed:", err);
      alert("❌ Error saving payment.");
    }
  }
});

function editFromRow(button) {
  const row = button.closest("tr");
  const cells = row.querySelectorAll("td");

  // Build payment object from row values
  const payment = {
    //paymentid: cells[0].textContent,
    paymentnumber: cells[0].textContent,
    firstname: cells[1].textContent,
    middlename: cells[2].textContent,
    lastname: cells[3].textContent,
    gender: cells[4].textContent,
    dob: cells[5].textContent,
    address: cells[6].textContent,
    guardian: cells[7].textContent,
    contactnumber: cells[8].textContent,
    relationship:cells[9].textContent
  };

  openEditpaymentForm(payment);
}



