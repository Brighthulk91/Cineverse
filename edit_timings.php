<?php 
session_start(); 
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php');

// --- 1. DELETE LOGIC ---
if(isset($_GET['did'])) {
    $did = intval($_GET['did']);
    $delete_stmt = $con->prepare("DELETE FROM timings WHERE id = ?");
    $delete_stmt->bind_param("i", $did);
    if($delete_stmt->execute()){
        header('location:timings.php?msg=deleted');
        exit();
    }
}

// --- 2. FETCH LOGIC ---
if(!isset($_GET['eid'])) {
    header('location:timings.php');
    exit();
}

$eid = intval($_GET['eid']);

// Securely fetch existing data using prepared statement
$stmt = $con->prepare("SELECT * FROM timings WHERE id = ?");
$stmt->bind_param("i", $eid);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if(!$data) {
    header('location:timings.php');
    exit();
}

// --- 3. UPDATE LOGIC ---
if(isset($_POST['update'])){
    $t = $_POST['time'];
    $show_date = $_POST['show_date'];

    // The time input submits 24-hour HH:MM. Reformat it to the same
    // display style ("06:30 PM") used everywhere else in the app (see
    // add_timings.php), so showtime stays consistent across records.
    $formatted_time = date('h:i A', strtotime($t));

    // Fixed: Changed show_time to showtime to match your DB schema
    $update_stmt = $con->prepare("UPDATE timings SET showtime = ?, show_date = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $formatted_time, $show_date, $eid);
    
    if($update_stmt->execute()){
        header('location:timings.php?msg=updated');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Edit Schedule</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center p-6 relative">
    
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 w-[600px] h-[600px] bg-violet-600/10 rounded-full blur-[120px]"></div>

    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 p-10 rounded-[3rem] w-full max-w-lg shadow-2xl relative">
        <header class="mb-10 text-center">
            <div class="inline-block px-4 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                Schedule Management
            </div>
            <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">Update <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">Schedule</span></h2>
        </header>

        <form action="edit_timings.php?eid=<?php echo htmlspecialchars($eid); ?>" method="post" class="space-y-6">
            
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">New Show Time</label>
                <div class="relative">
                    <i class="fa fa-clock absolute left-5 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="time" name="time" value="<?php echo htmlspecialchars(date('H:i', strtotime($data['showtime']))); ?>" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 pl-12 outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition-all text-white font-medium">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Scheduled Date</label>
                <div class="relative">
                    <i class="fa fa-calendar absolute left-5 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="date" name="show_date" value="<?php echo htmlspecialchars($data['show_date'] ?? ''); ?>" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 pl-12 outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition-all text-white font-medium">
                </div>
            </div>

            <div class="flex space-x-4 pt-4">
                <a href="timings.php" class="flex-1 text-center bg-slate-800 hover:bg-slate-700 text-slate-300 py-4 rounded-2xl font-bold transition-all border border-slate-700">
                    CANCEL
                </a>
                <button type="submit" name="update" 
                    class="flex-[2] bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-4 rounded-2xl font-black shadow-lg shadow-violet-900/20 transition-all active:scale-[0.98]">
                    SAVE CHANGES
                </button>
            </div>
        </form>

        <p class="text-center text-slate-600 text-[10px] mt-8 uppercase font-bold tracking-widest">
            CINEVERSE SYSTEM • v2.0
        </p>
    </div>
</body>
</html>