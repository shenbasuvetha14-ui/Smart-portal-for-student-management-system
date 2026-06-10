<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Faculty - Academic Details</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Base Styles */
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('formbackground.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* Container Styling */
        .form-container {
            width: 100%;
            max-width: 550px;
            background: rgba(255, 255, 255, 0.96); /* Clean White Glass Look */
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
        }

        /* Form Headings */
        h2 {
            color: #1a202c;
            text-align: center;
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .form-subtitle {
            text-align: center;
            color: #718096;
            margin-bottom: 30px;
            font-size: 14px;
            margin-top: 0;
        }

        .form-section {
            margin-bottom: 25px;
        }

        h3 {
            color: #2d3748;
            font-size: 16px;
            font-weight: 600;
            border-left: 4px solid #007bff; /* Professional Blue Accent */
            padding-left: 10px;
            margin-bottom: 15px;
            margin-top: 0;
        }

        /* Input Fields Styling */
        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 15px;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            font-size: 14px;
            background-color: #f8fafc;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        /* Input Focus Effect */
        input:focus {
            outline: none;
            border-color: #007bff;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        /* Submit Button Styling */
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
        }

        /* Button Hover & Click */
        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        button[type="submit"]:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Adding New Faculty Form</h2>
        <p class="form-subtitle">Please enter the academic and personal details below</p>
        
        <form action="generate_credentials.php" method="POST">
            
            <div class="form-section">
                <h3>Personal Details</h3>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="short_name" placeholder="Short Name (e.g. NSK, DS)" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
            </div>

            <div class="form-section">
                <h3>Academic Details</h3>
                <input type="text" name="qualification" placeholder="Qualification (e.g. M.Sc., Ph.D.)" required>
                <input type="text" name="designation" placeholder="Designation (e.g. Asst. Prof)" required>
                <input type="number" name="joining" placeholder="Year of Joining" required>
            </div>
            
            <button type="submit" name="create_faculty">Add Faculty</button>
        </form>
    </div>

</body>
</html>