<?php 
session_start(); 
include('dbcon.php'); 

// Securely fetch movie ID from URL
if(!isset($_GET['id'])) {
    header('location:index.php');
    exit();
}

$id = intval($_GET['id']);

// Prepared statement to prevent SQL Injection via URL parameters
$stmt = $con->prepare("SELECT * FROM movies WHERE Movie_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if(!$row) {
    die("<div class='bg-slate-950 text-white min-h-screen flex items-center justify-center font-bold'>Movie not found.</div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['Movie_Name']); ?> | SBA Screening</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen">
    
    <div class="relative h-[65vh] w-full overflow-hidden">
        <img src="uploads/<?php echo htmlspecialchars($row['poster']); ?>" 
             class="absolute inset-0 w-full h-full object-cover scale-105 blur-sm opacity-30">
        
        <div class="absolute inset-0 bg-gradient-to-t from-[#020617] via-[#020617]/60 to-transparent"></div>

        <div class="absolute inset-0 flex items-end p-8 md:p-20">
            <div class="max-w-7xl mx-auto w-full grid grid-cols-1 md:grid-cols-4 gap-10 items-end">
                <div class="hidden md:block">
                    <img src="uploads/<?php echo htmlspecialchars($row['poster']); ?>" 
                         class="w-full rounded-2xl shadow-2xl shadow-black border border-white/10 transform -rotate-2">
                </div>
                
                <div class="md:col-span-3 space-y-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="bg-violet-600/20 text-violet-400 border border-violet-500/30 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Featured</span>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest"><?php echo htmlspecialchars($row['type']); ?></span>
                    </div>
                    <h1 class="text-5xl md:text-8xl font-black mb-4 tracking-tighter text-white uppercase italic drop-shadow-2xl">
                        <?php echo htmlspecialchars($row['Movie_Name']); ?>
                    </h1>
                    <div class="flex items-center space-x-6 text-cyan-400 font-bold uppercase tracking-widest text-sm">
                        <span class="flex items-center"><i class="fa fa-clock mr-2"></i> <?php echo htmlspecialchars($row['RunTime']); ?></span>
                        <span class="flex items-center"><i class="fa fa-calendar-day mr-2"></i> <?php echo htmlspecialchars(date('Y', strtotime($row['Release_date']))); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-8 py-16 grid grid-cols-1 md:grid-cols-3 gap-16">
        <div class="md:col-span-2 space-y-12">
            <section>
                <h2 class="text-xs font-black text-slate-500 uppercase tracking-[0.3em] mb-6 flex items-center">
                    <span class="w-8 h-px bg-slate-800 mr-4"></span> Synopsis
                </h2>
                <p class="text-2xl text-slate-300 leading-relaxed font-light">
                    <?php echo htmlspecialchars($row['Description']); ?>
                </p>
            </section>

            <div class="flex flex-wrap gap-6 pt-6">
                <a href="booking.php?movie_id=<?php echo htmlspecialchars($row['Movie_id']); ?>" 
                   class="flex-1 text-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 px-10 py-5 rounded-2xl font-black text-white shadow-xl shadow-violet-900/40 transition-all active:scale-95 uppercase tracking-widest">
                    Book Tickets Now
                </a>
                <a href="userdashboard.php" 
                   class="text-center bg-slate-900 border border-slate-800 hover:bg-slate-800 px-10 py-5 rounded-2xl font-black text-slate-400 transition-all uppercase tracking-widest">
                    Back to Explore
                </a>
            </div>
        </div>

        <aside class="space-y-8">
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-500/5 rounded-full blur-2xl"></div>
                
                <h3 class="text-[10px] font-black text-slate-500 uppercase mb-8 tracking-[0.2em] flex items-center">
                    <i class="fa fa-users-viewfinder mr-2 text-cyan-500"></i> Cast & Crew
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase mb-1">Lead Actor</p>
                        <p class="text-xl font-bold text-white"><?php echo htmlspecialchars($row['Actor'] ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase mb-1">Lead Actress</p>
                        <p class="text-xl font-bold text-white"><?php echo htmlspecialchars($row['Actress'] ?? 'N/A'); ?></p>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-slate-800/50">
                    <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest leading-loose">
                        Official CINEVERSE Selection • Rated PG-13
                    </p>
                </div>
            </div>
        </aside>
    </main>

    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full h-px bg-gradient-to-r from-transparent via-violet-500/50 to-transparent"></div>
</body>
</html>