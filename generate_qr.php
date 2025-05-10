<?php
$qr_code_folder = 'qr_codes/';
if (!is_dir($qr_code_folder)) {
    mkdir($qr_code_folder, 0777, true); // Create folder if it doesn't exist
}

session_start();
require 'phpqrcode/qrlib.php';  // Include QR Code Library

if (!isset($_SESSION['client_id'])) {
    die("<div class='error'>❌ Please log in to generate your QR Code.</div>");
}

$client_id = $_SESSION['client_id'];
$qr_text = "client_id:$client_id";

// Set QR Code file path
$qr_file = "qr_codes/user_" . $client_id . ".png";
QRcode::png($qr_text, $qr_file, QR_ECLEVEL_L, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Code</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* Reset & Font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Body Styling */
        body {
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            color: white;
        }

        /* Container */
        .qr-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #333;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            margin: 15px auto;
            display: block;
        }

        p {
            color: #555;
            font-size: 14px;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            background: #6366F1;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #4F46E5;
        }
    </style>
</head>
<body>

    <div class="qr-container">
        <h2>Your QR Code</h2>
        <img src="<?php echo $qr_file; ?>" alt="QR Code" class="qr-code">
        <p>Scan this QR code at the gym entrance to mark attendance.</p>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>

</body>
</html>
