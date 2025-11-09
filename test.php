<?php
// Example: Fetch student/payment details from database
// (replace this with your actual DB query)
$student = [
    "studentnumber" => "2025-001",
    "name"          => "Juan Dela Cruz",
    "program"       => "BS Computer Science",
    "schoolyear"    => "2025-2026",
    "paymentmode"   => "Cash",
    "tuitionfee"    => 20000,
    "miscellaneous" => 5000,
    "downpayment"   => 5000
];
$student["total"]   = $student["tuitionfee"] + $student["miscellaneous"];
$student["balance"] = $student["total"] - $student["downpayment"];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Payment</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .payment-form {
      width: 380px;
      padding: 20px;
      margin: 20px auto;
      border: 1px solid #aaa;
      border-radius: 8px;
      background: #fdfdfd;
    }
    .payment-form h2 {
      margin-bottom: 15px;
      text-align: center;
    }
    .payment-form label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    .payment-form input,
    .payment-form select {
      width: 100%;
      padding: 6px;
      margin-top: 5px;
      border: 1px solid #aaa;
      border-radius: 4px;
    }
    .payment-form button {
      margin-top: 15px;
      width: 100%;
      padding: 10px;
      background: #2e8b57;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .payment-form button:hover {
      background: #1c6d1c;
    }
  </style>
</head>
<body>

<div class="payment-form">
  <h2>Payment</h2>
  <form method="post" action="save_payment.php">
    <label>Student Number:</label>
    <input type="text" name="studentnumber" value="<?= $student['studentnumber'] ?>" readonly>

    <label>Name:</label>
    <input type="text" name="name" value="<?= $student['name'] ?>" readonly>

    <label>Program:</label>
    <select name="program">
      <option <?= ($student['program'] == "BS Computer Science") ? "selected" : "" ?>>BS Computer Science</option>
      <option <?= ($student['program'] == "BS Information Technology") ? "selected" : "" ?>>BS Information Technology</option>
      <option <?= ($student['program'] == "BS Business Administration") ? "selected" : "" ?>>BS Business Administration</option>
    </select>

    <label>School Year:</label>
       <select name="schoolyear">
      <?php 
      $currentYear = date("Y"); 
      for ($y = $currentYear; $y <= $currentYear + 5; $y++) {
        $next = $y + 1;
        $sy = "$y-$next";
        $selected = ($student['schoolyear'] == $sy) ? "selected" : "";
        echo "<option value='$sy' $selected>$sy</option>";
       }
      ?>
    </select>

    <label>Payment Mode:</label>
    <select name="paymentmode" id="paymentmode">
      <option value="">-- Select --</option>
      <option value="Cash" <?= ($student['paymentmode'] == "Cash") ? "selected" : "" ?>>Cash</option>
      <option value="Installment" <?= ($student['paymentmode'] == "Installment") ? "selected" : "" ?>>Installment</option>
    </select>

    <label>Tuition Fee:</label>
    <input type="number" name="tuitionfee" id="tuitionfee" value="<?= $student['tuitionfee'] ?>">

    <label>Miscellaneous:</label>
    <input type="number" name="miscellaneous" id="miscellaneous" value="<?= $student['miscellaneous'] ?>">

    <label>Total:</label>
    <input type="number" name="total" id="total" value="<?= $student['total'] ?>" readonly>

    <label>Downpayment:</label>
    <input type="number" name="downpayment" id="downpayment" value="<?= $student['downpayment'] ?>">


    <div id="termsSection" style="margin-top:15px; display:none;">
        <label>Number of Terms:</label>
        <select id="numTerms">
            <option value="3" selected>3</option>
            <option value="6">3</option>
        </select>

        <!--
        <h4 style="margin-top:10px;">Installment Breakdown</h4>
        <div id="termsList"></div>
        -->
    </div>

   <!--
  <h4 style="margin-top:10px;">Installment Breakdown</h4>
  -->
  <div id="termsList"></div>
    

    <label>Balance:</label>
    <input type="number" name="balance" id="balance" value="<?= $student['balance'] ?>" readonly>

    <button type="submit">Submit Payment</button>
</div>




    
  </form>
</div>

<script>

    
// Auto-calculate total & balance
// Auto-calculate total & balance
function updateTotals() {
  let tuition = parseFloat(document.getElementById("tuitionfee").value) || 0;
  let misc = parseFloat(document.getElementById("miscellaneous").value) || 0;
  let down = parseFloat(document.getElementById("downpayment").value) || 0;
  let paymentMode = document.getElementById("paymentmode").value;

  let total = tuition + misc;
  let balance = total - down;

  // Apply +4% interest for installment mode
  if (paymentMode === "Installment" && balance > 0) {
    balance = balance + (balance * 0.04);
  }

  document.getElementById("total").value = total.toFixed(2);
  document.getElementById("balance").value = balance.toFixed(2);

  updateTerms(); // refresh installment terms
}

// Show installment terms
function updateTerms() {
  let paymentMode = document.getElementById("paymentmode").value;
  let balance = parseFloat(document.getElementById("balance").value) || 0;
  let termsSection = document.getElementById("termsSection");
  let termsList = document.getElementById("termsList");
  let numTerms = document.getElementById("numTerms").value;

  termsList.innerHTML = ""; // clear old terms

  if (paymentMode === "Installment" && balance > 0) {
    termsSection.style.display = "block";

    let perTerm = (balance / numTerms).toFixed(2);
    for (let i = 1; i <= numTerms; i++) {
      let termInput = `<label>Term ${i}:</label>
                       <input type="text" name="term${i}" value="${perTerm}" readonly>`;
      termsList.innerHTML += termInput;
    }
  } else {
    termsSection.style.display = "none";
  }
}

// Attach event listeners
document.getElementById("tuitionfee").addEventListener("input", updateTotals);
document.getElementById("miscellaneous").addEventListener("input", updateTotals);
document.getElementById("downpayment").addEventListener("input", updateTotals);
document.getElementById("paymentmode").addEventListener("change", updateTerms);
document.getElementById("numTerms").addEventListener("change", updateTerms);


// Run once on load
updateTotals();
</script>

</body>
</html>
