<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | VCW Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-blue: #2563eb; /* Strong Professional Blue */
            --bg-overlay: rgba(2, 6, 23, 0.8); /* Darker overlay for Admin */
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

        /* Dark overlay for extra focus on admin card */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: var(--bg-overlay);
            z-index: 1;
        }

        .login-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.08); /* Transparent Glass */
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 380px;
            color: white;
            text-align: center;
        }

        h2 {
            font-weight: 800;
            font-size: 26px;
            margin-bottom: 35px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        h2 span {
            color: var(--admin-blue);
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 8px;
            margin-left: 4px;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 14px 18px;
            background: rgba(15, 23, 42, 0.5); /* Dark input background */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            box-sizing: border-box;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--admin-blue);
            background: rgba(15, 23, 42, 0.8);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
        }

        button {
            width: 100%;
            padding: 15px;
            background: var(--admin-blue);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            margin-top: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        button:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.5);
        }

        button:active {
            transform: translateY(0);
        }

        .home-link {
            display: inline-block;
            margin-top: 30px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .home-link:hover {
            color: white;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Admin <span>Login</span></h2>
        
        <form action="login_action.php" method="POST">
            <div class="input-group">
                <label>Administrator ID</label>
                <input type="text" name="username" placeholder="Enter Admin ID" required>
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <button type="submit">Access Dashboard</button>
        </form>

        <a href="index.php" class="home-link">← Return to Main Portal</a>
    </div>

</body>
</html>