<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Login | VCW Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --faculty-green: #22c55e; /* Vibrant Green */
            --dark-navy: rgba(15, 23, 42, 0.75);
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
            overflow: hidden;
        }

        /* Overlay for better text focus */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: var(--dark-navy);
            z-index: 1;
        }

        .login-box {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 40px;
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            width: 380px;
            text-align: center;
        }

        h2 {
            font-weight: 700;
            font-size: 30px;
            margin-bottom: 35px;
            letter-spacing: -1px;
        }

        h2 span {
            color: var(--faculty-green);
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 600;
            margin-left: 5px;
            margin-bottom: 8px;
            display: block;
        }

        input {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: white;
            box-sizing: border-box;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--faculty-green);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        }

        input::placeholder {
            color: #64748b;
        }

        .btn {
            background: var(--faculty-green);
            color: #ffffff;
            border: none;
            padding: 16px;
            width: 100%;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            margin-top: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px rgba(34, 197, 94, 0.2);
        }

        .btn:hover {
            background: #16a34a;
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(34, 197, 94, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
        }

        .back-link:hover {
            color: white;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Faculty <span>Portal</span></h2>
    <form action="faculty_login_action.php" method="POST">
        <div class="input-group">
            <label>Staff Identifier</label>
            <input type="text" name="fac_id" placeholder="Enter Faculty ID" required>
        </div>
        
        <div class="input-group">
            <label>Security Password</label>
            <input type="password" name="fac_pass" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn">Secure Login</button>
    </form>

    <a href="index.php" class="back-link">← Back to Institution Home</a>
</div>

</body>
</html>