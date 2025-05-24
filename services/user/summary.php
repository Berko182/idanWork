<?php
include '../../header.php';

if (!isset($_SESSION['user_code']) || !isset($_SESSION['reservation_id'])) {
    echo "שגיאה: משתמש או הזמנה לא קיימים.";
    exit();
}

$user_code = $_SESSION['user_code'];
$reservation_id = $_SESSION['reservation_id'];

$host="127.0.0.1";
$port=3306;
$socket="";
$user="root";
$password="";
$dbname="itayrm_dogs_boarding_house";

$conn = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());
/*
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
*/

// שליפת פרטי ההזמנה
$sql = "SELECT toys, bathing, photos, special_food, training, total_payments_services,lodge, total_payments, start_date, end_date
        FROM reservation
        WHERE id = ? AND user_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $reservation_id, $user_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "ההזמנה לא נמצאה.";
    exit();
}

$reservation = $result->fetch_assoc();

// המרת תאריכים לפורמט קריא
$startDate = date("d/m/Y", strtotime($reservation['start_date']));
$endDate = date("d/m/Y", strtotime($reservation['end_date']));
// חישוב ההפרש בימים
$start = new DateTime($reservation['start_date']);
$end = new DateTime($reservation['end_date']);
$nights = $start->diff($end)->days;
if ($nights === 0) {
    $nights = 1;
}
//$price_per_night = $reservation['lodge'];
$price_per_night=50;
$total_services = $reservation['total_payments_services'];
$total_price = ($nights * $price_per_night) + $total_services;

//עדכון מחיר סופי
$update_sql = "UPDATE reservation 
               SET total_payments = ?
               WHERE id = ? AND user_code = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("iii", $total_price, $reservation_id, $user_code);
$update_stmt->execute();
$update_stmt->close();


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>סיכום הזמנה</title>
<link rel="stylesheet" href="summary-css.css">
</head>
<body>

<h1>סיכום ההזמנה שלך</h1>
<h3>מתאריך <?= $startDate ?> עד <?= $endDate ?> | <?= $nights ?> לילות</h3>

<table>
    <thead>
        <tr>
            <th>שירות</th>
            <th>מחיר</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>צעצועים</td><td><?= number_format($reservation['toys'], 2) ?> ₪</td></tr>
        <tr><td>אמבטיה</td><td><?= number_format($reservation['bathing'], 2) ?> ₪</td></tr>
        <tr><td>תמונות</td><td><?= number_format($reservation['photos'], 2) ?> ₪</td></tr>
        <tr><td>אוכל מיוחד</td><td><?= number_format($reservation['special_food'], 2) ?> ₪</td></tr>
        <tr><td>אילוף</td><td><?= number_format($reservation['training'], 2) ?> ₪</td></tr>
        <tr class="total"><td>סך כל שירותים</td><td><?= number_format($total_services, 2) ?> ₪</td></tr>

        <!-- שורה מודגשת ומיוחדת לחיפוש תאריך והזמנה -->
        <tr class="trama-data">
            <td colspan="2">מתאריך <?= $startDate ?> עד <?= $endDate ?> | <?= $nights ?> לילות</td>
        </tr>

        <tr><td>סה"כ לילות</td><td><?= $nights ?></td></tr>
        <tr><td>מחיר ללילה</td><td><?= number_format($price_per_night, 2) ?> ₪</td></tr>
        <tr class="total"><td>סה"כ לתשלום</td><td><?= number_format($total_price, 2) ?> ₪</td></tr>
    </tbody>
</table>

</body>
</html>



