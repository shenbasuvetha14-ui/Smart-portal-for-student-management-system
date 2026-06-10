<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | VCW Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-yellow: #ffc107;
            --dark-overlay: rgba(15, 23, 42, 0.7); /* Deep Navy overlay */
        }

        body {
            background: url('welcomeimage.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Full page overlay for better readability */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: var(--dark-overlay);
            z-index: 1;
        }

        .login-box {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px); /* Stronger blur for premium look */
            -webkit-backdrop-filter: blur(15px);
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.1); /* Subtle glass border */
            color: white;
            width: 360px;
            text-align: center;
        }

        h2 {
            font-weight: 700;
            margin-bottom: 30px;
            letter-spacing: -0.5px;
            font-size: 28px;
        }

        h2 span {
            color: var(--primary-yellow);
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 13px;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            box-sizing: border-box;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-yellow);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.2);
        }

        input::placeholder {
            color: #94a3b8;
        }

        .btn {
            background: var(--primary-yellow);
            color: #000;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .btn:hover {
            background: #e5ad06;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .footer-links {
            margin-top: 25px;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-yellow);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Student <span>Login</span></h2>
    <form action="student_login_action.php" method="POST">
        <div class="input-group">
            <label>Registration Number</label>
            <input type="text" name="s_reg" placeholder="Enter Reg No" required>
        </div>
        
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="s_pass" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn">Login to Portal</button>
    </form>

    <div class="footer-links">
        <a href="index.php" class="back-link">← Back to Home</a>
    </div>
</div>

</body>
</html>