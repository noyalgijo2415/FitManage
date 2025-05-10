<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code - SmartFit Gym</title>
    <link rel="icon" type="image/x-icon" href="logo.png">
    <!-- Instascan Library -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    
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
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
            text-align: center;
        }

        /* QR Scanner Container */
        .scanner-container {
            background: white;
            color: #333;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* Video Preview */
        video {
            width: 100%;
            border: 3px solid #6366F1;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        /* Message */
        .message {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        /* Loading/Error Messages */
        .error {
            color: red;
            font-weight: bold;
        }

        /* Button */
        .btn {
            background: #6366F1;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #4F46E5;
        }
    </style>
</head>
<body>

    <div class="scanner-container">
        <h1>Scan Your QR Code</h1>
        <p class="message">Point your camera at the QR code to mark attendance.</p>
        
        <video id="preview"></video>

        <form id="attendanceForm" method="POST" action="mark_attendance.php">
            <input type="hidden" name="qr_data" id="qr_data">
        </form>

        <button class="btn" onclick="startScanner()">Start Scanner</button>
    </div>

    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

        scanner.addListener('scan', function (content) {
            document.getElementById('qr_data').value = content;
            document.getElementById('attendanceForm').submit();
        });

        function startScanner() {
            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    alert("❌ No cameras found.");
                }
            }).catch(function (e) {
                console.error(e);
                alert("⚠️ Error accessing the camera.");
            });
        }
    </script>

</body>
</html>
