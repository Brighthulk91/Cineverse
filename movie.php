<?php 
session_start(); 
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CINEVERSE Admin | Movie Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 flex min-h-screen">

    <nav class="w-72 bg-slate-900 border-r border-slate-800 sticky top-0 h-screen p-8 flex flex-col shadow-2xl">
        <div class="flex items-center space-x-3 mb-12">
            <div class="w-8 h-8 bg-violet-600 rounded-lg flex items-center justify-center shadow-lg shadow-violet-600/20">
                <i class="fa fa-film text-white text-xs"></i>
            </div>
            <h1 class="text-xl font-black text-white tracking-tighter italic">CINEVERSE <span class="text-violet-500">ADMIN</span></h1>
        </div>
        
        <div class="space-y-1 flex-grow">
            <a href="dash.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-chart-pie w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Overview</span>
            </a>
            <a href="movie.php" class="flex items-center space-x-3 p-4 rounded-2xl bg-violet-600 text-white shadow-lg font-bold">
                <i class="fa fa-clapperboard w-5"></i>
                <span class="text-sm">Movie Library</span>
            </a>
            <a href="timings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-clock w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Showtimes</span>
            </a>
            <a href="theatres.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-building w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Theatres</span>
            </a>

            <a href="bookings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-receipt w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Bookings</span>
            </a>

            <a href="users.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-users w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Customers</span>
            </a>
        </div>

        <div class="pt-10 border-t border-slate-800">
            <a href="logout.php" class="flex items-center space-x-3 p-4 rounded-2xl text-red-500 hover:bg-red-500/10 transition-all font-bold text-xs uppercase tracking-widest">
                <i class="fa fa-power-off"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </nav>

    <main class="flex-grow p-12">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl font-extrabold text-white tracking-tight">Movie Library</h2>
                <p class="text-slate-500 text-sm mt-1">Manage and curate your active screening catalog.</p>
            </div>
            <a href="edit_movie.php" class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-slate-950 px-8 py-4 rounded-2xl font-black text-sm transition-all shadow-xl shadow-cyan-900/20 flex items-center">
                <i class="fa fa-plus mr-2"></i> ADD NEW MOVIE
            </a>
        </header>

        <div class="grid grid-cols-1 gap-6">
            <?php 
            $run = mysqli_query($con, "SELECT * FROM movies ORDER BY Movie_id DESC");
            if(mysqli_num_rows($run) > 0):
                while($row = mysqli_fetch_array($run)): 
            ?>
            <div class="bg-slate-900/50 border border-slate-800 p-6 rounded-[2rem] flex items-center gap-8 hover:border-slate-700 transition-all group">
                <div class="relative">
                    <img src="uploads/<?php echo htmlspecialchars($row['poster']); ?>" 
                         class="w-20 h-24 object-cover rounded-2xl shadow-xl border border-white/5"
                         onerror="this.src='https://via.placeholder.com/150x200?text=No+Poster'">
                </div>

                <div class="flex-grow">
                    <h3 class="font-extrabold text-xl text-white group-hover:text-cyan-400 transition-colors">
                        <?php echo htmlspecialchars($row['Movie_Name']); ?>
                    </h3>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-[10px] text-slate-500 font-black uppercase tracking-widest bg-slate-950 px-3 py-1 rounded-full border border-slate-800">
                            <?php echo htmlspecialchars($row['type']); ?>
                        </span>
                        <span class="text-xs text-slate-400">
                            <i class="fa fa-hourglass-half mr-1"></i> <?php echo htmlspecialchars($row['RunTime']); ?>
                        </span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="edit_movie.php?eid=<?php echo htmlspecialchars($row['Movie_id']); ?>" 
                       class="w-12 h-12 flex items-center justify-center bg-slate-800 rounded-2xl text-slate-400 hover:bg-violet-600 hover:text-white transition-all shadow-lg"
                       title="Edit Movie">
                        <i class="fa fa-pen-nib"></i>
                    </a>
                    <a href="edit_movie.php?did=<?php echo htmlspecialchars($row['Movie_id']); ?>" 
                       onclick="return confirm('Are you sure?');"
                       class="w-12 h-12 flex items-center justify-center bg-slate-800 rounded-2xl text-slate-400 hover:bg-red-600 hover:text-white transition-all shadow-lg"
                       title="Delete Movie">
                        <i class="fa fa-trash-can"></i>
                    </a>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div class="text-center py-20 bg-slate-900/30 rounded-[3rem] border border-dashed border-slate-800">
                <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">No movies found</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>