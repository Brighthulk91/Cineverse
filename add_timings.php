<?php 
session_start(); 
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php');

$message = "";

if(isset($_POST['add_schedule'])){
    $time = $_POST['showtime'];
    $theatre = $_POST['theatre_name'];
    $gold = intval($_POST['gold_rate']);
    $silver = intval($_POST['silver_rate']);
    $seats = intval($_POST['seats']);
    $date = isset($_POST['show_date']) && $_POST['show_date'] !== '' ? $_POST['show_date'] : date('Y-m-d');

    // Store a clean display time (e.g. "06:30 PM") and the actual show_date
    // in its own column -- the schema already has a show_date column, so the
    // old "stuff the theatre name into the time string" workaround is gone.
    $formatted_time = date('h:i A', strtotime($time));

    $stmt = $con->prepare("INSERT INTO timings (showtime, Theatre_Name, show_date, ticket_rate_Gold, ticket_rate_Silver, seats_remaining) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiii", $formatted_time, $theatre, $date, $gold, $silver, $seats);
    
    if($stmt->execute()){
        header('location:timings.php?msg=added');
        exit();
    } else {
        $message = "Error: Could not save schedule.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Add Showtime</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center p-6 relative">
    
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 w-[600px] h-[600px] bg-emerald-600/10 rounded-full blur-[120px]"></div>

    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 p-10 rounded-[3rem] w-full max-w-2xl shadow-2xl relative">
        <header class="mb-10 text-center">
            <div class="inline-block px-4 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                Schedule Creator
            </div>
            <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">Add New <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">Showtime</span></h2>
        </header>

        <?php if($message): ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl text-xs font-bold uppercase tracking-widest text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Show Time</label>
                <input type="time" name="showtime" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 outline-none focus:ring-2 focus:ring-emerald-500/50 text-white font-medium">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Show Date</label>
                <input type="date" name="show_date" value="<?php echo date('Y-m-d'); ?>" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 outline-none focus:ring-2 focus:ring-emerald-500/50 text-white font-medium">
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Assign Theatre</label>
                <select name="theatre_name" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 outline-none focus:ring-2 focus:ring-emerald-500/50 text-white font-medium appearance-none">
                    <option value="">Select a Location</option>
                    <?php 
                    $t_res = mysqli_query($con, "SELECT Theatre_Name FROM theatre");
                    while($t_row = mysqli_fetch_array($t_res)) {
                        echo "<option value='".htmlspecialchars($t_row['Theatre_Name'])."'>".htmlspecialchars($t_row['Theatre_Name'])."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Gold Rate ($)</label>
                <input type="number" name="gold_rate" placeholder="e.g. 15" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 outline-none focus:ring-2 focus:ring-emerald-500/50 text-white font-medium">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Silver Rate ($)</label>
                <input type="number" name="silver_rate" placeholder="e.g. 10" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 outline-none focus:ring-2 focus:ring-emerald-500/50 text-white font-medium">
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Available Seats</label>
                <input type="number" name="seats" placeholder="Total capacity" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 outline-none focus:ring-2 focus:ring-emerald-500/50 text-white font-medium">
            </div>

            <div class="flex space-x-4 pt-4 md:col-span-2">
                <a href="timings.php" class="flex-1 text-center bg-slate-800 hover:bg-slate-700 text-slate-300 py-4 rounded-2xl font-bold transition-all border border-slate-700">
                    CANCEL
                </a>
                <button type="submit" name="add_schedule" 
                    class="flex-[2] bg-gradient-to-r from-emerald-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-slate-950 py-4 rounded-2xl font-black shadow-lg shadow-emerald-900/20 transition-all active:scale-[0.98]">
                    CREATE SCHEDULE
                </button>
            </div>
        </form>
    </div>
</body>
</html>