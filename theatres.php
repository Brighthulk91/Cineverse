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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Cinema Halls</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 flex min-h-screen">

    <nav class="w-72 bg-slate-900 border-r border-slate-800 sticky top-0 h-screen p-8 space-y-2 flex flex-col shadow-2xl">
        <div class="flex items-center space-x-3 mb-12">
            <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center shadow-lg shadow-cyan-500/20">
                <i class="fa fa-building text-slate-950 text-xs"></i>
            </div>
            <h1 class="text-xl font-black text-white tracking-tighter italic">CINEVERSE <span class="text-cyan-500 font-black">ADMIN</span></h1>
        </div>
        
        <div class="space-y-1">
            <a href="dash.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-chart-pie w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Overview</span>
            </a>
            
            <a href="movie.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-clapperboard w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Movie Library</span>
            </a>

            <a href="timings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-clock w-5 group-hover:text-cyan-400"></i>
                <span class="text-sm">Showtimes</span>
            </a>

            <a href="theatres.php" class="flex items-center space-x-3 p-4 rounded-2xl bg-cyan-600 text-slate-950 shadow-lg shadow-cyan-900/20 font-bold transition-all">
                <i class="fa fa-building w-5"></i>
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

        <div class="pt-10 mt-auto border-t border-slate-800">
            <a href="logout.php" class="flex items-center space-x-3 p-4 rounded-2xl text-red-500 hover:bg-red-500/10 transition-all font-bold text-xs uppercase tracking-widest">
                <i class="fa fa-power-off"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </nav>

    <main class="flex-grow p-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-cyan-600/5 rounded-full blur-[120px] -z-10"></div>

        <header class="flex justify-between items-end mb-12">
            <div>
                <p class="text-[10px] font-black text-cyan-500 uppercase tracking-[0.3em] mb-2">Infrastructure</p>
                <h2 class="text-4xl font-extrabold text-white tracking-tight uppercase italic">Cinema <span class="text-cyan-500">Halls</span></h2>
            </div>
            <a href="add_theatre.php" class="bg-slate-800 border border-slate-700 hover:bg-slate-700 text-white px-6 py-3 rounded-xl font-black text-xs transition-all flex items-center">
                <i class="fa fa-plus mr-2 text-cyan-400"></i> NEW FACILITY
            </a>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <?php 
            $res = mysqli_query($con, "SELECT * FROM theatre ORDER BY id DESC");
            if(mysqli_num_rows($res) > 0):
                while($row = mysqli_fetch_array($res)): 
            ?>
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-8 rounded-[2.5rem] group hover:border-cyan-500/50 transition-all duration-500 shadow-xl">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-slate-950 border border-slate-800 rounded-2xl flex items-center justify-center text-cyan-400 group-hover:scale-110 transition-transform">
                        <i class="fa fa-couch text-xl"></i>
                    </div>
                    <div class="flex gap-2">
                        <a href="theatre_edit.php?eid=<?php echo htmlspecialchars($row['id']); ?>" 
                           class="w-10 h-10 flex items-center justify-center bg-slate-950 border border-slate-800 text-slate-500 hover:text-cyan-400 hover:border-cyan-400/30 rounded-xl transition-all">
                            <i class="fa fa-pen-to-square"></i>
                        </a>
                        <a href="theatre_delete.php?did=<?php echo htmlspecialchars($row['id']); ?>" 
                           onclick="return confirm('Deleting a theatre will affect its associated showtimes. Continue?');"
                           class="w-10 h-10 flex items-center justify-center bg-slate-950 border border-slate-800 text-slate-500 hover:text-red-400 hover:border-red-400/30 rounded-xl transition-all">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>

                <h3 class="text-2xl font-black text-white group-hover:text-cyan-400 transition-colors">
                    <?php echo htmlspecialchars($row['Theatre_Name']); ?>
                </h3>
                
                <div class="mt-4 flex flex-col space-y-2">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest flex items-center">
                        <i class="fa fa-location-dot mr-2 text-slate-600"></i>
                        <?php echo htmlspecialchars($row['Location']); ?>
                    </p>
                    <div class="pt-4 mt-4 border-t border-slate-800/50 flex justify-between items-center">
                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Seating Capacity</span>
                        <span class="bg-cyan-500/10 text-cyan-400 px-3 py-1 rounded-lg text-xs font-black">
                            <?php echo htmlspecialchars($row['Seats']); ?> SEATS
                        </span>
                    </div>
                </div>
            </div>
            <?php 
                endwhile; 
            else:
            ?>
            <div class="col-span-full text-center py-24 bg-slate-900/30 rounded-[3rem] border border-dashed border-slate-800">
                <i class="fa fa-couch text-5xl text-slate-800 mb-6 block"></i>
                <h3 class="text-slate-500 font-bold uppercase tracking-widest">No Facilities Registered</h3>
                <p class="text-slate-600 text-xs mt-2">Click 'New Facility' to add a cinema hall.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>