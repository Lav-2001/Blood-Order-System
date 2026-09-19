<?php

require_once "db.php";


// ========================================
// 1. Check request method
// ========================================

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("ERROR: Form was not submitted using POST.");
}


// ========================================
// 2. Get form data
// ========================================

$name = $_POST["name"];
$age = $_POST["age"];
$email = $_POST["email"];
$contact = $_POST["contact"];
$bloodgroup = $_POST["bloodgroup"];
$address = $_POST["address"];
$password = $_POST["password"];
$confirmpassword = $_POST["confirmpassword"];


// ========================================
// 3. Check empty fields
// ========================================

if (
    empty($name) ||
    empty($age) ||
    empty($email) ||
    empty($contact) ||
    empty($bloodgroup) ||
    empty($address) ||
    empty($password) ||
    empty($confirmpassword)
) {
    die("ERROR: Please fill all fields.");
}


// ========================================
// 4. Check password
// ========================================

if ($password != $confirmpassword) {
    die("ERROR: Passwords do not match.");
}


// ========================================
// 5. Check if email already exists
// ========================================

$sql = "SELECT id FROM users WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("ERROR: Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();
    $conn->close();

    die("ERROR: This email is already registered. Please use another email.");

}

$stmt->close();


// ========================================
// 6. Hash password
// ========================================

$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// ========================================
// 7. Insert data
// ========================================

$sql = "INSERT INTO users
        (name, age, email, contact, bloodgroup, address, password)
        VALUES (?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("ERROR: Insert prepare failed: " . $conn->error);
}


// ========================================
// 8. Bind values
// ========================================

$stmt->bind_param(
    "sisssss",
    $name,
    $age,
    $email,
    $contact,
    $bloodgroup,
    $address,
    $hashed_password
);


// ========================================
// 9. Execute
// ========================================

if ($stmt->execute()) {

    echo "<h2>Registration Successful!</h2>";
    echo "<p>User has been saved into the database.</p>";
    echo "<a href='../frontend/pages/register.html'>Register another user</a>";

} else {

    echo "<h2>Registration Failed!</h2>";
    echo "<p>Database Error: " . $stmt->error . "</p>";

}


// ========================================
// 10. Close connection
// ========================================

$stmt->close();
$conn->close();

?>