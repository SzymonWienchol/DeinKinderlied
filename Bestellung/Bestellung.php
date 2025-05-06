<?php
header('Content-Type: text/html; charset=utf-8');

// Configuration
$recipientEmail = 'info@deinkinderlied.de'; 
$websiteName = 'DeinKinderlied.de';
$orderStoragePath = __DIR__ . '/bestellungen/';
header("Location: danke.php?order_id=" . urlencode($orderId));

// Validate and sanitize input
$requiredFields = ['child_name', 'mood', 'email'];
$errors = [];

session_start();
if (!isset($_SESSION['last_submit'])) {
    $_SESSION['last_submit'] = time();
} elseif (time() - $_SESSION['last_submit'] < 30) {
    die("Bitte warte 30 Sekunden zwischen Bestellungen.");
}
$_SESSION['last_submit'] = time();

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Pflichtfeld fehlt: " . htmlspecialchars($field);
    }
}

if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Ungültige E-Mail-Adresse";
}

// If errors, return to form
if (!empty($errors)) {
    header('Location: index.html?error=' . urlencode(implode('|', $errors)));
    exit;
}

// Prepare order data
$orderData = [
    'date' => date('Y-m-d H:i:s'),
    'child_name' => htmlspecialchars($_POST['child_name'] ?? ''),
    'child_age' => htmlspecialchars($_POST['child_age'] ?? ''),
    'special_details' => htmlspecialchars($_POST['special_details'] ?? ''),
    'mood' => htmlspecialchars($_POST['mood'] ?? ''),
    'email' => htmlspecialchars($_POST['email'] ?? ''),
    'special_requests' => htmlspecialchars($_POST['special_requests'] ?? ''),
    'payment_method' => htmlspecialchars($_POST['payment_method'] ?? ''),
    'ip_address' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT']
];

// Generate unique order ID
$orderId = 'L' . date('Ymd') . '-' . substr(md5(uniqid()), 0, 6);
$orderData['order_id'] = $orderId;

// 1. Save order locally 
if (!file_exists($orderStoragePath)) {
    mkdir($orderStoragePath, 0755, true);
}
file_put_contents(
    $orderStoragePath . $orderId . '.json',
    json_encode($orderData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

// 2. Send email notification
$emailSubject = "Neue Bestellung #{$orderId} - {$websiteName}";
$emailBody = "
<html>
<head>
    <title>{$emailSubject}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .order-details { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .label { font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <h2>Neue Bestellung #{$orderId}</h2>
    <div class='order-details'>
        <p><span class='label'>Kindername:</span> {$orderData['child_name']}</p>
        <p><span class='label'>Alter:</span> {$orderData['child_age']}</p>
        <p><span class='label'>Besonderheiten:</span> {$orderData['special_details']}</p>
        <p><span class='label'>Stimmung:</span> {$orderData['mood']}</p>
        <p><span class='label'>E-Mail:</span> {$orderData['email']}</p>
        <p><span class='label'>Besondere Wünsche:</span> {$orderData['special_requests']}</p>
        <p><span class='label'>Zahlungsmethode:</span> {$orderData['payment_method']}</p>
        <p><span class='label'>Bestelldatum:</span> {$orderData['date']}</p>
    </div>
</body>
</html>
";

$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=utf-8',
    'From: ' . $websiteName . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
    'Reply-To: ' . $orderData['email']
];

// Send email
$mailSent = mail($recipientEmail, $emailSubject, $emailBody, implode("\r\n", $headers));

// 3. Send confirmation to customer
if ($mailSent) {
    $customerSubject = "Deine Bestellung #{$orderId} - {$websiteName}";
    $customerBody = "
    <html>
    <body>
        <h2>Vielen Dank für deine Bestellung!</h2>
        <p>Wir haben deine Bestellung #{$orderId} erhalten und arbeiten nun an deinem persönlichen Kinderlied.</p>
        <p><strong>Bestelldetails:</strong></p>
        <p>Kindername: {$orderData['child_name']}</p>
        <p>Stimmung: {$orderData['mood']}</p>
        <p>Wir senden dir das fertige Lied innerhalb von 48 Stunden zu.</p>
        <p>Bei Fragen antworte einfach auf diese E-Mail.</p>
        <p>Dein Team von {$websiteName}</p>
    </body>
    </html>
    ";
    
    mail($orderData['email'], $customerSubject, $customerBody, implode("\r\n", $headers));
}

// Redirect to thank you page
header("Location: Danke.php?order_id={$orderId}");
exit;
?>
