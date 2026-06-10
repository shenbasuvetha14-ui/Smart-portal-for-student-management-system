<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Details - Computer Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }
        .header-title {
            text-align: center;
            color: #28a745;
            font-size: 2.5rem;
            margin-bottom: 40px;
        }
        .faculty-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .faculty-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #28a745;
            transition: transform 0.3s ease;
        }
        .faculty-card:hover {
            transform: translateY(-5px);
        }
        .faculty-name {
            font-size: 1.4rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .designation {
            color: #666;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        .email {
            color: #28a745;
            text-decoration: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .email:hover {
            text-decoration: underline;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 40px;
            text-decoration: none;
            color: #555;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1 class="header-title">Faculty - Computer Applications</h1>

    <div class="faculty-grid">
        <div class="faculty-card">
            <div class="faculty-name">Mr.N.Senthilkumaran, <small>M.C.A., M.Phil., (Ph.D)</small></div>
            <div class="designation">Director</div>
            <a href="mailto:senthilkumaran@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> senthilkumaran@vcw.ac.in</a>
        </div>

        <div class="faculty-card">
            <div class="faculty-name">Mrs. P.Anitha, <small>M.C.A., M.Phil., B.Ed.,</small></div>
            <div class="designation">Assistant Professor</div>
            <a href="mailto:anitha.p@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> anitha.p@vcw.ac.in</a>
        </div>

        <div class="faculty-card">
            <div class="faculty-name">Mrs. D.Savitha, <small>M.C.A., M.Phil.,(NET).,</small></div>
            <div class="designation">Assistant Professor</div>
            <a href="mailto:savitha.v@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> savitha.v@vcw.ac.in</a>
        </div>

        <div class="faculty-card">
            <div class="faculty-name">Dr. N. Geetha, <small>M.C.A., M.Phil., Ph.D.,(SET).,</small></div>
            <div class="designation">Assistant Professor</div>
            <a href="mailto:geetha.n@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> geetha.n@vcw.ac.in</a>
        </div>

        <div class="faculty-card">
            <div class="faculty-name">Dr.S.S.Kokila, <small>M.Sc., M.Phil.,Ph.D.,</small></div>
            <div class="designation">Assistant Professor</div>
            <a href="mailto:kokila.ss@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> kokila.ss@vcw.ac.in</a>
        </div>

        <div class="faculty-card">
            <div class="faculty-name">Dr.C.Premavathi, <small>M.Sc., M.Phil., Ph.D.,</small></div>
            <div class="designation">Associate Professor</div>
            <a href="mailto:c.premavathi@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> c.premavathi@vcw.ac.in</a>
        </div>
   
 <div class="faculty-card">
            <div class="faculty-name">Dr. L. Baby Victoria, <small> M.C.A., M.Phil., M.Ed., Ph.D.,</small></div>
            <div class="designation">Assistant Professor</div>
            <a href="mailto:l.babyvictoria@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> kokila.ss@vcw.ac.in</a>
        </div>

 <div class="faculty-card">
            <div class="faculty-name">Dr. M. Subathra, <small>M.C.A., M.Phil., Ph.D.,</small></div>
            <div class="designation">Assistant Professor</div>
            <a href="mailto:m.subathra@vcw.ac.in" class="email"><i class="fas fa-envelope"></i> kokila.ss@vcw.ac.in</a>
        </div>


    <a href="index.php" class="back-link">← Back to Home</a>

</body>
</html>