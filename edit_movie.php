<?php
session_start();
include('dbcon.php');

// Security check
if(!isset($_SESSION['user'])) { header('location:admin.php'); exit(); }

// --- 1. DELETE LOGIC ---
if(isset($_GET['did'])) {
    $did = intval($_GET['did']);
    $del_stmt = $con->prepare("DELETE FROM movies WHERE Movie_id = ?");
    $del_stmt->bind_param("i", $did);
    $del_stmt->execute();
    header('location:movie.php');
    exit();
}

// --- 2. SAVE/UPDATE LOGIC ---
$message = "";
$skip_save = false;

if(isset($_POST['save_movie'])) {
    $name = $_POST['mname'];
    $type = $_POST['mtype'];
    $runtime = $_POST['mruntime'];
    $actor = $_POST['mactor'];
    $actress = $_POST['mactress'];
    $description = $_POST['mdescription'];
    $eid = isset($_POST['eid']) ? intval($_POST['eid']) : 0;

    // Release Year is stored in the existing Release_date column as Jan 1st of that year
    $year = intval($_POST['myear']);
    $release_date = $year > 0 ? $year . '-01-01' : null;

    // Handle Image
    $poster_name = $_POST['existing_poster'] ?? '';
    $upload_error = '';
    if(!empty($_FILES['mimg']['name']) && $_FILES['mimg']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $orig_name = $_FILES['mimg']['name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        $tmp_path = $_FILES['mimg']['tmp_name'];

        // Verify the extension is allowed and the file is actually an image
        // (not just something renamed to look like one), to prevent
        // uploading executable files such as .php scripts.
        $image_info = @getimagesize($tmp_path);

        if(!in_array($ext, $allowed_ext, true) || $image_info === false || !in_array($image_info['mime'], $allowed_mime, true)) {
            $upload_error = "Invalid file type. Please upload a JPG, PNG, GIF, or WEBP image.";
        } else {
            $safe_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            $upload_dir = __DIR__ . "/uploads/";
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if(move_uploaded_file($tmp_path, $upload_dir . $safe_name)) {
                $poster_name = $safe_name;
            } else {
                $upload_error = "Could not save the uploaded image. Please try again.";
            }
        }
    }

    if($upload_error !== '') {
        // Don't save the movie record with a bad/partial upload; fall
        // through and re-render the form with the error instead.
        $message = $upload_error;
        $skip_save = true;
        $isEdit = $eid > 0;
        $data = [
            'Movie_Name' => $name, 'type' => $type, 'RunTime' => $runtime,
            'poster' => $_POST['existing_poster'] ?? '', 'Release_date' => $release_date,
            'Actor' => $actor, 'Actress' => $actress, 'Description' => $description
        ];
        $release_year = $year;
    } else {
        if($eid > 0) {
            $stmt = $con->prepare("UPDATE movies SET Movie_Name=?, type=?, RunTime=?, poster=?, Release_date=?, Actor=?, Actress=?, Description=? WHERE Movie_id=?");
            $stmt->bind_param("ssssssssi", $name, $type, $runtime, $poster_name, $release_date, $actor, $actress, $description, $eid);
        } else {
            $stmt = $con->prepare("INSERT INTO movies (Movie_Name, type, RunTime, poster, Release_date, Actor, Actress, Description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $name, $type, $runtime, $poster_name, $release_date, $actor, $actress, $description);
        }

        $stmt->execute();
        header('location:movie.php');
        exit();
    }
}

// --- 3. UI SETUP ---
// Skipped when we're re-rendering the form after an upload error above,
// since $data/$isEdit/$release_year are already populated from the post.
if(!$skip_save) {
    $isEdit = isset($_GET['eid']);
    $data = [
        'Movie_Name' => '', 'type' => '', 'RunTime' => '', 'poster' => '',
        'Release_date' => '', 'Actor' => '', 'Actress' => '', 'Description' => ''
    ];

    if($isEdit) {
        $eid = intval($_GET['eid']);
        $stmt = $con->prepare("SELECT * FROM movies WHERE Movie_id = ?");
        $stmt->bind_param("i", $eid);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
    }

    $release_year = !empty($data['Release_date']) ? date('Y', strtotime($data['Release_date'])) : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CINEVERSE Admin | <?php echo $isEdit ? 'Edit' : 'Add'; ?> Movie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center p-6">
    <div class="bg-slate-900 w-full max-w-lg p-10 rounded-[3rem] border border-slate-800 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-violet-600/20 rounded-full blur-3xl"></div>
        
        <h2 class="text-2xl font-black mb-8 uppercase tracking-tighter italic">
            <?php echo $isEdit ? 'Edit' : 'Add New'; ?> <span class="text-violet-500">Movie</span>
        </h2>

        <?php if($message): ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl text-xs font-bold uppercase tracking-widest text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="edit_movie.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php if($isEdit): ?>
                <input type="hidden" name="eid" value="<?= htmlspecialchars($eid ?? '') ?>">
                <input type="hidden" name="existing_poster" value="<?php echo htmlspecialchars($data['poster']); ?>">
            <?php endif; ?>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Movie Title</label>
                <input type="text" name="mname" value="<?php echo htmlspecialchars($data['Movie_Name']); ?>" required 
                       class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl outline-none focus:border-violet-500 transition-all text-white">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Genre</label>
                    <input type="text" name="mtype" value="<?php echo htmlspecialchars($data['type']); ?>" required 
                           class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl outline-none focus:border-violet-500 transition-all text-white">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Runtime (Mins)</label>
                    <input type="text" name="mruntime" value="<?php echo htmlspecialchars($data['RunTime']); ?>" required 
                           class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl outline-none focus:border-violet-500 transition-all text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Release Year</label>
                    <input type="number" name="myear" min="1900" max="2100" placeholder="e.g. 2026"
                           value="<?php echo htmlspecialchars($release_year); ?>" required
                           class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl outline-none focus:border-violet-500 transition-all text-white">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Lead Actor</label>
                    <input type="text" name="mactor" value="<?php echo htmlspecialchars($data['Actor'] ?? ''); ?>" placeholder="e.g. Robert Pattinson"
                           class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl outline-none focus:border-violet-500 transition-all text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Lead Actress</label>
                    <input type="text" name="mactress" value="<?php echo htmlspecialchars($data['Actress'] ?? ''); ?>" placeholder="e.g. Zoë Kravitz"
                           class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl outline-none focus:border-violet-500 transition-all text-white">
                </div>
                <div></div>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Short Description</label>
                <textarea name="mdescription" rows="3" placeholder="A brief synopsis of the movie..."
                          class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl outline-none focus:border-violet-500 transition-all text-white resize-none"><?php echo htmlspecialchars($data['Description'] ?? ''); ?></textarea>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-2">Poster Image</label>
                <input type="file" name="mimg" class="w-full bg-slate-950 border border-slate-800 p-4 rounded-2xl text-sm text-slate-400">
                <?php if($isEdit && !empty($data['poster'])): ?>
                    <p class="text-[10px] mt-2 text-slate-500 italic">Current: <?php echo htmlspecialchars($data['poster']); ?></p>
                <?php endif; ?>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" name="save_movie" 
                        class="flex-grow bg-violet-600 hover:bg-violet-500 py-4 rounded-2xl font-black uppercase text-sm tracking-widest shadow-lg shadow-violet-900/40 transition-all active:scale-95 text-white">
                    Save Changes
                </button>
                <a href="movie.php" class="px-8 bg-slate-800 hover:bg-slate-700 flex items-center justify-center rounded-2xl font-black text-sm uppercase tracking-widest transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>