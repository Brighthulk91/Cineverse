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
    <title>CINEVERSE Admin | Show Schedules</title>
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
            <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center shadow-lg shadow-emerald-600/20">
                <i class="fa fa-clock text-white text-xs"></i>
            </div>
            <h1 class="text-xl font-black text-white tracking-tighter italic">CINEVERSE <span class="text-emerald-500">ADMIN</span></h1>
        </div>
        
        <div class="space-y-1">
            <a href="dash.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-chart-pie w-5 group-hover:text-emerald-400"></i>
                <span class="text-sm">Overview</span>
            </a>
            
            <a href="movie.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-clapperboard w-5 group-hover:text-emerald-400"></i>
                <span class="text-sm">Movie Library</span>
            </a>

            <a href="timings.php" class="flex items-center space-x-3 p-4 rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-900/20 font-bold transition-all">
                <i class="fa fa-clock w-5"></i>
                <span class="text-sm">Showtimes</span>
            </a>

            <a href="theatres.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-building w-5 group-hover:text-emerald-400"></i>
                <span class="text-sm">Theatres</span>
            </a>

            <a href="bookings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-receipt w-5 group-hover:text-emerald-400"></i>
                <span class="text-sm">Bookings</span>
            </a>

            <a href="users.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-users w-5 group-hover:text-emerald-400"></i>
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

    <main class="flex-grow p-12">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl font-extrabold text-white tracking-tight">Show Schedules</h2>
                <p class="text-slate-500 text-sm mt-1">Configure showtime slots and hall assignments.</p>
            </div>
            <a href="add_timings.php" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-8 py-4 rounded-2xl font-black text-sm transition-all shadow-xl shadow-emerald-900/20 flex items-center uppercase">
                <i class="fa fa-plus mr-2"></i> Add Showtime
            </a>
        </header>

        <?php if(isset($_GET['msg'])): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-bold flex items-center">
                <i class="fa fa-circle-check mr-3"></i>
                Action completed: <?php echo htmlspecialchars($_GET['msg']); ?> successfully.
            </div>
        <?php endif; ?>

        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-[2.5rem] overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/50 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
                    <tr>
                        <th class="p-8 border-b border-slate-800">Show Date</th>
                        <th class="p-8 border-b border-slate-800">Display Time</th>
                        <th class="p-8 border-b border-slate-800">Assigned Theatre</th>
                        <th class="p-8 border-b border-slate-800">Rates / Seats</th>
                        <th class="p-8 border-b border-slate-800 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    <?php 
                    $res = mysqli_query($con, "SELECT * FROM timings ORDER BY show_date ASC, showtime ASC");
                    if(mysqli_num_rows($res) > 0):
                        while($row = mysqli_fetch_array($res)): 
                    ?>
                    <tr class="hover:bg-slate-800/20 transition-colors group">
                        <td class="p-8">
                            <span class="font-bold text-slate-200 text-sm">
                                <?php echo !empty($row['show_date']) ? htmlspecialchars(date('M d, Y', strtotime($row['show_date']))) : '—'; ?>
                            </span>
                        </td>
                        <td class="p-8">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center text-emerald-400 border border-slate-800 group-hover:border-emerald-500/30 transition-all">
                                    <i class="fa fa-stopwatch"></i>
                                </div>
                                <span class="font-mono text-xl font-bold text-white"><?php echo htmlspecialchars($row['showtime']); ?></span>
                            </div>
                        </td>
                        <td class="p-8">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-200"><?php echo htmlspecialchars($row['Theatre_Name']); ?></span>
                                <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest mt-1">Active Hall</span>
                            </div>
                        </td>
                        <td class="p-8">
                            <div class="flex flex-col gap-1 text-xs font-bold">
                                <span class="text-amber-400">Gold $<?php echo htmlspecialchars($row['ticket_rate_Gold']); ?> / Silver $<?php echo htmlspecialchars($row['ticket_rate_Silver']); ?></span>
                                <span class="text-slate-500"><?php echo htmlspecialchars($row['seats_remaining']); ?> seats left</span>
                            </div>
                        </td>
                        <td class="p-8 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="edit_timings.php?eid=<?php echo htmlspecialchars($row['id']); ?>" 
                                   class="w-12 h-12 flex items-center justify-center bg-slate-800 rounded-2xl text-slate-400 hover:bg-emerald-600 hover:text-white transition-all shadow-lg"
                                   title="Edit Showtime">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                                <a href="edit_timings.php?did=<?php echo htmlspecialchars($row['id']); ?>" 
                                   onclick="return confirm('Remove this showtime? This may affect existing bookings.');"
                                   class="w-12 h-12 flex items-center justify-center bg-slate-800 rounded-2xl text-slate-400 hover:bg-red-600 hover:text-white transition-all shadow-lg"
                                   title="Delete">
                                    <i class="fa fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="p-20 text-center">
                            <i class="fa fa-calendar-xmark text-5xl text-slate-800 mb-4 block"></i>
                            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">No showtimes scheduled yet</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="fixed bottom-0 left-0 -z-10 w-[500px] h-[500px] bg-emerald-600/5 rounded-full blur-[120px]"></div>
</body>
</html>