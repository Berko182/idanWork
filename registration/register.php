<?php
//berko
//$servername = "localhost";
//$username = "itayrm_ItayRam";
//$password = "itay0547862155";
//$dbname = "itayrm_dogs_boarding_housie";//chnged was: tayrm_dogs_boarding_housie

//$conn = new mysqli($servername, $username, $password, $dbname);
//if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$host="127.0.0.1";
$port=3306;
$socket="";
$user="root";
$password="";
$dbname="itayrm_dogs_boarding_house";

$conn = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());

$required_fields = ['username', 'password', 'first_name', 'last_name', 'city', 'street', 'house_number', 'zip_code', 'email', 'phone'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("שגיאה: שדה חסר - " . $field);
    }
}

$username = $_POST['username'];
$password = $_POST['password'];

// בדיקה אם סיסמה כבר קיימת
//berko
//$conn->query("SELECT * FROM users WHERE password = " . $password);
//$conn->query("select first_name, last_name" 
 //            . "  from employees"
 //            . " where subsidiary_id = " . $subsidiary_id);

$check = $conn->prepare("SELECT * FROM users WHERE password = ?");
$check->bind_param("s", $password);
$check->execute();
$result = $check->get_result();
if ($result->num_rows > 0) {
    die("שגיאה: הסיסמה כבר קיימת. אנא השתמש בסיסמה אחרת.");
}
 // יצירת user_code ייחודי
function generateUserCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return 'USR-' . $code;
}

$user_code = generateUserCode();
$check_code = $conn->prepare("SELECT * FROM users WHERE user_code = ?");
$check_code->bind_param("s", $user_code);
$check_code->execute();
while ($check_code->get_result()->num_rows > 0) {
    $user_code = generateUserCode();
    $check_code->bind_param("s", $user_code);
    $check_code->execute();
}

$stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, city, street, house_number, zip_code, email, phone, user_type, user_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
$stmt->bind_param("sssssssisss",
    $_POST['username'],
    $_POST['password'],
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['city'],
    $_POST['street'],
    $_POST['house_number'],
    $_POST['zip_code'],
    $_POST['email'],
    $_POST['phone'],
    $user_code
);

if ($stmt->execute()) {
    header("Location: login.html?username=" . urlencode($_POST['username']) . "&password=" . urlencode($_POST['password']));
    
} else {
    echo "שגיאה בהרשמה: " . $stmt->error;
}

$conn->close();
?>
