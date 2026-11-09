<?php 
session_start(); 
include('dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE | Explore Cinema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .movie-card:hover .overlay { background: linear-gradient(to top, rgba(124, 58, 237, 0.9), transparent); }
    </style>
</head>
<body class="bg-[#020617] text-slate-100 selection:bg-violet-500">

    <nav class="sticky top-0 z-50 bg-[#020617]/80 backdrop-blur-2xl border-b border-slate-800/50 px-6 md:px-12 py-5 flex items-center justify-between">
        <div class="flex items-center gap-10">
            <h1 class="text-2xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400 italic">CINE <span class="text-white">VERSE</span></h1>
            <div class="hidden lg:flex gap-8 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                <a href="userdashboard.php" class="text-violet-400 border-b-2 border-violet-400 pb-1">Explore</a>
                <a href="coming_soon.php" class="hover:text-white transition">Upcoming</a>
                <a href="my_bookings.php" class="hover:text-white transition">My Tickets</a>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <?php if(isset($_SESSION['name'])): ?>
                <div class="flex items-center gap-3 bg-slate-900/50 border border-slate-800 px-4 py-2 rounded-2xl">
                    <div class="w-7 h-7 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-full flex items-center justify-center text-[10px] font-bold shadow-lg">
                        <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                    </div>
                    <span class="text-xs font-black tracking-tight"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                </div>
                <a href="userlogout.php" class="w-10 h-10 flex items-center justify-center bg-slate-900 border border-slate-800 rounded-xl text-slate-500 hover:text-red-400 hover:border-red-400/30 transition-all">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </a>
            <?php else: ?>
                <a href="index.php" class="bg-white text-slate-950 px-6 py-2.5 rounded-xl text-xs font-black transition-all hover:bg-cyan-400 active:scale-95 shadow-xl shadow-cyan-400/10">SIGN IN</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="relative px-6 md:px-12 pt-24 pb-20 overflow-hidden">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-violet-600/10 blur-[140px] rounded-full -z-10 animate-pulse"></div>
        <div class="max-w-5xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-[10px] font-black uppercase tracking-widest mb-6">
                <span class="w-1.5 h-1.5 bg-violet-500 rounded-full animate-ping"></span> Live Now
            </div>
            <h2 class="text-6xl md:text-8xl font-black mb-10 tracking-tighter leading-[0.95] italic uppercase">
                Cinema is <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-fuchsia-400 to-cyan-400">Immortal.</span>
            </h2>
            <div class="flex flex-wrap gap-4">
                <a href="#movies" class="bg-violet-600 text-white px-10 py-5 rounded-2xl font-black hover:bg-violet-500 transition-all shadow-2xl shadow-violet-600/20 uppercase text-xs tracking-widest">Browse Movies</a>
                <a href="coming_soon.php" class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 px-10 py-5 rounded-2xl font-black hover:bg-slate-800 transition-all uppercase text-xs tracking-widest">View Schedule</a>
            </div>
        </div>
    </header>

    <main id="movies" class="px-6 md:px-12 py-20 bg-slate-950/50 backdrop-blur-3xl rounded-t-[4rem] border-t border-slate-800/50">
        <div class="flex justify-between items-end mb-16">
            <div>
                <h3 class="text-3xl font-black italic tracking-tighter">NOW SHOWING</h3>
                <div class="h-1.5 w-16 bg-gradient-to-r from-violet-500 to-cyan-500 mt-3 rounded-full"></div>
            </div>
            <div class="hidden md:block text-right">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]"><?php echo date('l, F d'); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-10">
            <?php
            $run = mysqli_query($con, "SELECT * FROM movies ORDER BY Movie_id DESC");
            if(mysqli_num_rows($run) > 0):
                while($row = mysqli_fetch_array($run)): ?>
                
                <a href="mdetails.php?id=<?php echo htmlspecialchars($row['Movie_id']);?>" class="movie-card group relative block bg-slate-900 rounded-[2.5rem] overflow-hidden border border-slate-800 hover:border-violet-500/50 transition-all duration-500 shadow-2xl">
                    <div class="aspect-[2/3] overflow-hidden">
                        <img src="uploads/<?php echo htmlspecialchars($row['poster']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-90 group-hover:opacity-100">
                    </div>
                    
                    <div class="overlay absolute inset-0 bg-gradient-to-t from-[#020617] via-transparent to-transparent flex flex-col justify-end p-8 transition-all duration-500">
                        <p class="text-cyan-400 text-[9px] font-black uppercase tracking-[0.2em] mb-2"><?php echo htmlspecialchars($row['type']); ?></p>
                        <h4 class="text-2xl font-extrabold leading-none mb-4 group-hover:translate-x-1 transition-transform"><?php echo htmlspecialchars($row['Movie_Name']); ?></h4>
                        
                        <div class="flex items-center justify-between opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                            <span class="text-[9px] font-black bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10 uppercase tracking-widest">
                                <?php echo htmlspecialchars($row['RunTime']); ?>
                            </span>
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-950 shadow-xl">
                                <i class="fa-solid fa-play ml-1"></i>
                            </div>
                        </div>
                    </div>
                </a>

                <?php endwhile;
            else: ?>
                <div class="col-span-full py-32 text-center bg-slate-900/30 border-2 border-dashed border-slate-800 rounded-[3rem]">
                    <i class="fa-solid fa-film text-5xl text-slate-800 mb-6 block"></i>
                    <p class="text-slate-500 font-black uppercase tracking-widest text-sm">Our projectors are currently resting</p>
                    <p class="text-slate-700 text-[10px] font-bold uppercase mt-2">Check back soon for new releases</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-[#020617] border-t border-slate-800/50 p-20 text-center">
        <h2 class="text-xl font-black italic mb-8 tracking-tighter">CINE <span class="text-violet-500">VERSE</span></h2>
        <div class="flex justify-center gap-8 mb-10">
            <a href="#" class="w-12 h-12 flex items-center justify-center bg-slate-900 border border-slate-800 rounded-2xl text-slate-500 hover:text-violet-400 hover:border-violet-400/30 transition-all"><i class="fa-brands fa-instagram text-lg"></i></a>
            <a href="#" class="w-12 h-12 flex items-center justify-center bg-slate-900 border border-slate-800 rounded-2xl text-slate-500 hover:text-cyan-400 hover:border-cyan-400/30 transition-all"><i class="fa-brands fa-twitter text-lg"></i></a>
            <a href="#" class="w-12 h-12 flex items-center justify-center bg-slate-900 border border-slate-800 rounded-2xl text-slate-500 hover:text-fuchsia-400 hover:border-fuchsia-400/30 transition-all"><i class="fa-brands fa-discord text-lg"></i></a>
        </div>
        <p class="text-slate-600 text-[10px] font-black uppercase tracking-[0.5em]">© 2026 CINEVERSE Systems • All Rights Reserved</p>
    </footer>

</body>
</html>