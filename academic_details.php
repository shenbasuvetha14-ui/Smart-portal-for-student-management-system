<?php
session_start();
include 'config.php';
$f_id = $_SESSION['f_id'];

// Handling Form Submission
if (isset($_POST['save_all'])) {
    // Escaping strings to prevent SQL injection
    $ug_s = mysqli_real_escape_string($conn, $_POST['ug_s'] ?? '');
    $ug_u = mysqli_real_escape_string($conn, $_POST['ug_u'] ?? '');
    $ug_y = mysqli_real_escape_string($conn, $_POST['ug_y'] ?? '');
    
    $pg_s = mysqli_real_escape_string($conn, $_POST['pg_s'] ?? '');
    $pg_u = mysqli_real_escape_string($conn, $_POST['pg_u'] ?? '');
    $pg_y = mysqli_real_escape_string($conn, $_POST['pg_y'] ?? '');
    
    $mp_s = mysqli_real_escape_string($conn, $_POST['mp_s'] ?? '');
    $mp_u = mysqli_real_escape_string($conn, $_POST['mp_u'] ?? '');
    $mp_y = mysqli_real_escape_string($conn, $_POST['mp_y'] ?? '');
    
    $ph_s = mysqli_real_escape_string($conn, $_POST['ph_s'] ?? '');
    $ph_u = mysqli_real_escape_string($conn, $_POST['ph_u'] ?? '');
    $ph_y = mysqli_real_escape_string($conn, $_POST['ph_y'] ?? '');

    $pg_ex = $_POST['pg_ex'] ?? ''; 
    $ug_ex = $_POST['ug_ex'] ?? ''; 
    $res_ex = $_POST['res_ex'] ?? '';
    $spec = mysqli_real_escape_string($conn, $_POST['spec'] ?? ''); 
    
    $j_intl = (int)($_POST['j_intl'] ?? 0); 
    $j_nat = (int)($_POST['j_nat'] ?? 0); 
    $j_state = (int)($_POST['j_state'] ?? 0);
    
    $mem_acad = mysqli_real_escape_string($conn, $_POST['mem_acad'] ?? ''); 
    $mem_cam = mysqli_real_escape_string($conn, $_POST['mem_cam'] ?? '');

    $sql = "REPLACE INTO faculty_academic_details 
            (faculty_id, ug_subject, ug_university, ug_year, pg_subject, pg_university, pg_year, 
             mphil_subject, mphil_university, mphil_year, phd_subject, phd_university, phd_year,
             teaching_exp_pg, teaching_exp_ug, research_exp, specialization, 
             publications_intl, publications_nat, journals_state, membership_academic, membership_campus)
            VALUES 
            ('$f_id', '$ug_s', '$ug_u', '$ug_y', '$pg_s', '$pg_u', '$pg_y', 
             '$mp_s', '$mp_u', '$mp_y', '$ph_s', '$ph_u', '$ph_y',
             '$pg_ex', '$ug_ex', '$res_ex', '$spec', 
             '$j_intl', '$j_nat', '$j_state', '$mem_acad', '$mem_cam')";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Portfolio Updated Successfully!'); window.location.href='academic_details.php';</script>";
    }
}

// Fetch existing data
$res = mysqli_query($conn, "SELECT * FROM faculty_academic_details WHERE faculty_id='$f_id'");
$d = mysqli_fetch_assoc($res) ?: [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Academic Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0f172a; --card: #1e293b; --primary: #10b981; --text: #f1f5f9; --border: #334155; }
        body { background: var(--bg); color: var(--text); font-family: 'Poppins', sans-serif; margin: 0; padding: 40px 20px; }
        .container { max-width: 1100px; margin: auto; }
        header { margin-bottom: 30px; border-left: 5px solid var(--primary); padding-left: 20px; }
        h2 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        p { color: #94a3b8; font-size: 14px; }

        .card { background: var(--card); border-radius: 16px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1px solid var(--border); margin-bottom: 25px; }
        .section-title { color: var(--primary); font-size: 18px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        /* Grid Layouts */
        .qual-row { display: grid; grid-template-columns: 150px 1fr 1fr 150px; gap: 15px; align-items: center; margin-bottom: 15px; }
        .exp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .pub-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

        label { display: block; font-size: 12px; color: #94a3b8; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; }
        input, textarea { width: 100%; padding: 12px; background: #0f172a; border: 1px solid var(--border); color: white; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: var(--primary); outline: none; background: #1e293b; }

        .btn-container { text-align: right; }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 16px 40px; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 16px; transition: 0.3s; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); }
        
        .degree-label { font-weight: 600; color: #cbd5e1; }
        hr { border: 0; border-top: 1px solid var(--border); margin: 25px 0; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h2>Academic Portfolio Management</h2>
        <p>Maintain your professional qualifications and research milestones</p>
    </header>

    <form method="POST">
        <div class="card">
            <div class="section-title">🎓 I. Educational Qualifications</div>
            <div class="qual-row" style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px;">
                <span style="font-size: 12px; color: var(--primary);">DEGREE</span>
                <span style="font-size: 12px; color: var(--primary);">SUBJECT / SPECIALIZATION</span>
                <span style="font-size: 12px; color: var(--primary);">UNIVERSITY / COLLEGE</span>
                <span style="font-size: 12px; color: var(--primary);">YEAR</span>
            </div>
            
            <div class="qual-row">
                <span class="degree-label">Undergraduate</span>
                <input type="text" name="ug_s" value="<?= $d['ug_subject'] ?? '' ?>" placeholder="e.g. B.Sc Computer Science">
                <input type="text" name="ug_u" value="<?= $d['ug_university'] ?? '' ?>" placeholder="University Name">
                <input type="text" name="ug_y" value="<?= $d['ug_year'] ?? '' ?>" placeholder="Month & Year">
            </div>

            <div class="qual-row">
                <span class="degree-label">Postgraduate</span>
                <input type="text" name="pg_s" value="<?= $d['pg_subject'] ?? '' ?>" placeholder="e.g. M.C.A">
                <input type="text" name="pg_u" value="<?= $d['pg_university'] ?? '' ?>" placeholder="University Name">
                <input type="text" name="pg_y" value="<?= $d['pg_year'] ?? '' ?>" placeholder="Month & Year">
            </div>

            <div class="qual-row">
                <span class="degree-label">M.Phil</span>
                <input type="text" name="mp_s" value="<?= $d['mphil_subject'] ?? '' ?>" placeholder="Research Area">
                <input type="text" name="mp_u" value="<?= $d['mphil_university'] ?? '' ?>" placeholder="University Name">
                <input type="text" name="mp_y" value="<?= $d['mphil_year'] ?? '' ?>" placeholder="Year">
            </div>

            <div class="qual-row">
                <span class="degree-label">Ph.D</span>
                <input type="text" name="ph_s" value="<?= $d['phd_subject'] ?? '' ?>" placeholder="Doctoral Theme">
                <input type="text" name="ph_u" value="<?= $d['phd_university'] ?? '' ?>" placeholder="University Name">
                <input type="text" name="ph_y" value="<?= $d['phd_year'] ?? '' ?>" placeholder="Year">
            </div>
        </div>

        <div class="card">
            <div class="section-title">⏳ II. Teaching & Research Experience</div>
            <div class="exp-grid">
                <div>
                    <label>Teaching Experience (P.G.)</label>
                    <input type="text" name="pg_ex" value="<?= $d['teaching_exp_pg'] ?? '' ?>" placeholder="e.g. 5 Years">
                </div>
                <div>
                    <label>Teaching Experience (U.G.)</label>
                    <input type="text" name="ug_ex" value="<?= $d['teaching_exp_ug'] ?? '' ?>" placeholder="e.g. 10 Years">
                </div>
                <div>
                    <label>Research Experience</label>
                    <input type="text" name="res_ex" value="<?= $d['research_exp'] ?? '' ?>" placeholder="e.g. 3 Years">
                </div>
            </div>
            <div style="margin-top: 20px;">
                <label>Area of Specialization</label>
                <textarea name="spec" rows="2" placeholder="e.g. Data Mining, Machine Learning, Cloud Computing..."><?= $d['specialization'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="card">
            <div class="section-title">📰 III. Journal Publications</div>
            <div class="pub-grid">
                <div>
                    <label>International Journals</label>
                    <input type="number" name="j_intl" value="<?= $d['publications_intl'] ?? 0 ?>">
                </div>
                <div>
                    <label>National Journals</label>
                    <input type="number" name="j_nat" value="<?= $d['publications_nat'] ?? 0 ?>">
                </div>
                <div>
                    <label>State Level</label>
                    <input type="number" name="j_state" value="<?= $d['journals_state'] ?? 0 ?>">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="section-title">🏛️ IV. Academic Memberships</div>
            <div class="exp-grid">
                <div style="grid-column: span 1.5;">
                    <label>Academic Bodies (e.g. Board of Studies)</label>
                    <textarea name="mem_acad" rows="3"><?= $d['membership_academic'] ?? '' ?></textarea>
                </div>
                <div style="grid-column: span 1.5;">
                    <label>Campus Committees</label>
                    <textarea name="mem_cam" rows="3"><?= $d['membership_campus'] ?? '' ?></textarea>
                </div>
            </div>
        </div>

        <div class="btn-container">
            <button type="submit" name="save_all" class="btn-submit">UPDATE ENTIRE PORTFOLIO</button>
        </div>
    </form>
</div>

</body>
</html>