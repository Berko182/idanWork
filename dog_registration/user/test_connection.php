<?php
// הגדרת כותרת לתשובה
header('Content-Type: application/json');

// היה וקיימת תקלה כלשהי, מניעת הצגת הודעות שגיאה מפורטות למשתמש
ini_set('display_errors', 0);
error_reporting(0);

// ניסיון חיבור למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);

// בדיקה אם החיבור הצליח
if ($conn->connect_error) {
    // שגיאת התחברות
    echo json_encode([
        'status' => 'error',
        'message' => 'שגיאת התחברות למסד הנתונים',
        'error_details' => 'שגיאת חיבור פנימית'
    ]);
} else {
    // חיבור תקין - סגירת החיבור למסד הנתונים
    $conn->close();
    
    // החזרת הודעת הצלחה
    echo json_encode([
        'status' => 'success',
        'message' => 'החיבור למסד הנתונים תקין',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>