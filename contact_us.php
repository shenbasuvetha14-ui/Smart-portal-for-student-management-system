<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | VCW Portal</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            /* Inga unga background image name-ah kudunga */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('telephoneimage.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.15); /* Semi-transparent white */
            backdrop-filter: blur(15px); /* Glass effect */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            width: 500px;
            text-align: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 { 
            color: #ffc107; /* Yellow for Heading */
            font-size: 28px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .info {
            text-align: left;
            margin-top: 25px;
            line-height: 2;
            font-size: 16px;
        }

        .info p { margin: 10px 0; }

        .info strong { 
            color: #ffc107; 
            margin-right: 10px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            background: #ffc107;
            color: #000;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: bold;
            transition: 0.4s;
        }

        .back-btn:hover { 
            background: #fff;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

    <div class="contact-card">
        <h2>Contact Support</h2>
        
        <div class="info">
            <p><strong>🎓 Principal:</strong> Dr. S.K. Jayanthi, M.Sc., Ph.D.</p>
            <p><strong>📍 Address:</strong> Vellalar College for Women (Autonomous), Thindal, Erode - 638012.</p>
            <p><strong>📞 Phone:</strong> 0424-2244101 / 102</p>
            <p><strong>✉️ Email:</strong> principalvcw@gmail.com</p>
            <p><strong>🕒 Office Hours:</strong> 9:00 AM - 4:30 PM (Mon-Sat)</p>
        </div>

        <a href="index.php" class="back-btn">Back to Home Portal</a>
    </div>

</body>
</html>