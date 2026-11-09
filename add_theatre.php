<?php 
session_start(); 
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php');

$message = "";

if(isset($_POST['add_theatre'])){
    $name = $_POST['name']; 
    $loc = $_POST['loc']; 
    $seats = intval($_POST['seats']);

    // Securely insert using prepared statement
    $stmt = $con->prepare("INSERT INTO theatre (Theatre_Name, Location, Seats) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $loc, $seats);
    
    if($stmt->execute()){
        header('location:theatres.php?msg=added');
        exit();
    } else {
        $message = "Error: Could not add facility.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Add Theatre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center p-6 relative">
    
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[120px]"></div>

    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 p-10 rounded-[3rem] w-full max-w-lg shadow-2xl relative">
        <header class="mb-10 text-center">
            <div class="inline-block px-4 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                Infrastructure Expansion
            </div>
            <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">New <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Cinema Hall</span></h2>
        </header>

        <?php if($message): ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl text-xs font-bold uppercase tracking-widest text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="add_theatre.php" method="post" class="space-y-6">
            
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Theatre Name</label>
                <div class="relative">
                    <i class="fa fa-building absolute left-5 top-1/2 -translate-y-1/2 text-slate-600"></i>
                    <input type="text" name="name" placeholder="e.g. Grand Apex Hall" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all font-medium placeholder:text-slate-700">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Physical Location</label>
                <div class="relative">
                    <i class="fa fa-location-dot absolute left-5 top-1/2 -translate-y-1/2 text-slate-600"></i>
                    <input type="text" name="loc" placeholder="e.g. Level 4, North Plaza" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all font-medium placeholder:text-slate-700">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Seating Capacity</label>
                <div class="relative">
                    <i class="fa fa-chair absolute left-5 top-1/2 -translate-y-1/2 text-slate-600"></i>
                    <input type="number" name="seats" placeholder="e.g. 150" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all font-medium placeholder:text-slate-700">
                </div>
            </div>

            <div class="flex space-x-4 pt-4">
                <a href="theatres.php" class="flex-1 text-center bg-slate-800 hover:bg-slate-700 text-slate-300 py-4 rounded-2xl font-bold transition-all border border-slate-700">
                    CANCEL
                </a>
                <button type="submit" name="add_theatre" 
                    class="flex-[2] bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 py-4 rounded-2xl font-black shadow-lg shadow-cyan-900/20 transition-all active:scale-[0.98] uppercase">
                    Register Hall
                </button>
            </div>
        </form>

        <p class="text-center text-slate-600 text-[10px] mt-8 uppercase font-bold tracking-widest">
            CINEVERSE SYSTEM • Facility Manager
        </p>
    </div>
</body>
</html>