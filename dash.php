<?php 
session_start(); 
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php');

// Helper function for quick counts
function getCount($con, $table) {
    $res = mysqli_query($con, "SELECT * FROM $table");
    return mysqli_num_rows($res);
}

// Helper function for total revenue collected from ticket sales
function getRevenue($con) {
    $res = mysqli_query($con, "SELECT SUM(price) AS total FROM bookings");
    $row = mysqli_fetch_assoc($res);
    return $row['total'] ? $row['total'] : 0;
}

$total_revenue = getRevenue($con);

// Recent ticket sales, most recent first, for the sales ledger below
$sales_res = mysqli_query($con, "SELECT * FROM bookings ORDER BY booking_time DESC LIMIT 15");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Intelligence Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-sidebar { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 flex min-h-screen">

    <nav class="w-72 glass-sidebar border-r border-slate-800/50 sticky top-0 h-screen p-8 flex flex-col justify-between">
        <div class="space-y-10">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-tr from-violet-600 to-cyan-500 rounded-lg flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <i class="fa fa-shield-halved text-white text-xs"></i>
                </div>
                <h1 class="text-xl font-extrabold text-white tracking-tighter">CINEVERSE <span class="text-violet-500">ADMIN</span></h1>
            </div>

            <div class="space-y-1">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4 ml-2">Core Management</p>
                
                <a href="dash.php" class="flex items-center space-x-3 p-4 rounded-2xl bg-gradient-to-r from-violet-600/20 to-transparent border border-violet-500/20 text-violet-400 transition-all font-semibold">
                    <i class="fa fa-chart-pie w-5"></i>
                    <span class="text-sm tracking-tight">Overview</span>
                </a>
                
                <a href="movie.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800/50 transition-all font-semibold group">
                    <i class="fa fa-film w-5 group-hover:text-cyan-400"></i>
                    <span class="text-sm tracking-tight">Movies</span>
                </a>

                <a href="timings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800/50 transition-all font-semibold group">
                    <i class="fa fa-clock w-5 group-hover:text-cyan-400"></i>
                    <span class="text-sm tracking-tight">Showtimes</span>
                </a>

                <a href="theatres.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800/50 transition-all font-semibold group">
                    <i class="fa fa-building w-5 group-hover:text-cyan-400"></i>
                    <span class="text-sm tracking-tight">Theatres</span>
                </a>

                <a href="bookings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800/50 transition-all font-semibold group">
                    <i class="fa fa-receipt w-5 group-hover:text-cyan-400"></i>
                    <span class="text-sm tracking-tight">Bookings</span>
                </a>

                <a href="users.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800/50 transition-all font-semibold group">
                    <i class="fa fa-users w-5 group-hover:text-cyan-400"></i>
                    <span class="text-sm tracking-tight">User Base</span>
                </a>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-800">
            <a href="logout.php" class="flex items-center space-x-3 p-4 rounded-2xl text-red-400 hover:bg-red-500/10 transition-all font-bold text-xs uppercase tracking-widest">
                <i class="fa fa-power-off"></i>
                <span>Secure Sign Out</span>
            </a>
        </div>
    </nav>

    <main class="flex-grow p-12">
        <header class="flex justify-between items-start mb-12">
            <div>
                <h2 class="text-4xl font-extrabold text-white tracking-tight">Overview</h2>
                <p class="text-slate-500 mt-1">Operational status for <?php echo date('F d, Y'); ?></p>
            </div>
            <div class="bg-slate-900 border border-slate-800 px-6 py-3 rounded-2xl flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Current Admin</p>
                    <p class="text-sm font-bold text-violet-400"><?php echo htmlspecialchars($_SESSION['user']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center">
                    <i class="fa fa-user-tie text-slate-400 text-sm"></i>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
            <div class="group relative bg-slate-900 p-8 rounded-[2.5rem] border border-slate-800 hover:border-cyan-500/50 transition-all duration-500 shadow-2xl">
                <div class="absolute top-8 right-8 w-12 h-12 bg-cyan-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa fa-clapperboard text-cyan-400"></i>
                </div>
                <p class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.2em]">Movies Active</p>
                <h3 class="text-6xl font-black mt-4 text-white tracking-tighter">
                    <?php echo getCount($con, 'movies'); ?>
                </h3>
                <div class="mt-6 flex items-center text-xs text-cyan-400 font-bold">
                    <span class="mr-2 underline">Manage Catalog</span>
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>

            <div class="group relative bg-slate-900 p-8 rounded-[2.5rem] border border-slate-800 hover:border-violet-500/50 transition-all duration-500 shadow-2xl">
                <div class="absolute top-8 right-8 w-12 h-12 bg-violet-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa fa-ticket-simple text-violet-400"></i>
                </div>
                <p class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.2em]">Tickets Sold</p>
                <h3 class="text-6xl font-black mt-4 text-white tracking-tighter">
                    <?php echo getCount($con, 'bookings'); ?>
                </h3>
                <div class="mt-6 flex items-center text-xs text-violet-400 font-bold">
                    <span class="mr-2 underline">View Ledger Below</span>
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>

            <div class="group relative bg-slate-900 p-8 rounded-[2.5rem] border border-slate-800 hover:border-emerald-500/50 transition-all duration-500 shadow-2xl">
                <div class="absolute top-8 right-8 w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa fa-sack-dollar text-emerald-400"></i>
                </div>
                <p class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.2em]">Revenue Collected</p>
                <h3 class="text-6xl font-black mt-4 text-white tracking-tighter">
                    $<?php echo number_format($total_revenue, 2); ?>
                </h3>
                <div class="mt-6 flex items-center text-xs text-emerald-400 font-bold">
                    <span class="mr-2 underline">From Ticket Sales</span>
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>

            <div class="group relative bg-slate-900 p-8 rounded-[2.5rem] border border-slate-800 hover:border-amber-500/50 transition-all duration-500 shadow-2xl">
                <div class="absolute top-8 right-8 w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa fa-user-group text-amber-400"></i>
                </div>
                <p class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.2em]">Users</p>
                <h3 class="text-6xl font-black mt-4 text-white tracking-tighter">
                    <?php echo getCount($con, 'users'); ?>
                </h3>
                <div class="mt-6 flex items-center text-xs text-amber-400 font-bold">
                    <span class="mr-2 underline">Analyze Database</span>
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-slate-900/50 border border-slate-800 p-10 rounded-[2.5rem]">
            <h4 class="font-bold text-lg mb-6">System Health</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-slate-950/50 rounded-2xl border border-slate-800">
                    <div class="flex items-center space-x-4">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="text-sm font-semibold">Database Connection</span>
                    </div>
                    <span class="text-[10px] font-black text-green-500 uppercase tracking-widest">Active</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-950/50 rounded-2xl border border-slate-800">
                    <div class="flex items-center space-x-4">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                        <span class="text-sm font-semibold">Tailwind CDN Engine</span>
                    </div>
                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Online</span>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-slate-900/50 border border-slate-800 rounded-[2.5rem] overflow-hidden">
            <div class="flex justify-between items-center p-10 pb-6">
                <div>
                    <h4 class="font-bold text-lg">Ticket Sales Ledger</h4>
                    <p class="text-slate-500 text-xs mt-1">Who bought a ticket, for which show, and how much it earned.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-xl uppercase tracking-widest">
                        Total: $<?php echo number_format($total_revenue, 2); ?>
                    </span>
                    <a href="bookings.php" class="text-[10px] font-black text-slate-950 bg-white hover:bg-slate-200 px-4 py-2 rounded-xl uppercase tracking-widest transition-all">
                        View All <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-950/40 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
                        <tr>
                            <th class="px-10 py-5 border-y border-slate-800">Customer</th>
                            <th class="px-10 py-5 border-y border-slate-800">Movie</th>
                            <th class="px-10 py-5 border-y border-slate-800">Theatre / Showtime</th>
                            <th class="px-10 py-5 border-y border-slate-800">Date</th>
                            <th class="px-10 py-5 border-y border-slate-800 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <?php if(mysqli_num_rows($sales_res) > 0): ?>
                            <?php while($sale = mysqli_fetch_assoc($sales_res)): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="px-10 py-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-slate-950 rounded-lg flex items-center justify-center text-violet-400 border border-slate-800 font-black text-[10px]">
                                            <?php echo strtoupper(substr($sale['username'] ?? '?', 0, 1)); ?>
                                        </div>
                                        <span class="font-bold text-white text-sm"><?php echo htmlspecialchars($sale['username']); ?></span>
                                    </div>
                                </td>
                                <td class="px-10 py-5 text-sm text-slate-300 font-semibold"><?php echo htmlspecialchars($sale['movie']); ?></td>
                                <td class="px-10 py-5">
                                    <span class="text-sm text-slate-300"><?php echo htmlspecialchars($sale['theatre']); ?></span>
                                    <span class="block text-[10px] text-slate-600 font-bold uppercase tracking-widest mt-0.5"><?php echo htmlspecialchars($sale['showtime']); ?></span>
                                </td>
                                <td class="px-10 py-5 text-xs text-slate-500 font-bold"><?php echo htmlspecialchars($sale['booking_date']); ?></td>
                                <td class="px-10 py-5 text-right">
                                    <span class="bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-lg text-xs font-black border border-emerald-500/20">
                                        $<?php echo number_format($sale['price'], 2); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-16 text-center">
                                    <i class="fa fa-receipt text-4xl text-slate-800 mb-4 block"></i>
                                    <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">No ticket sales recorded yet</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="fixed top-0 right-0 -z-10 w-[800px] h-[800px] bg-violet-600/5 rounded-full blur-[140px]"></div>
</body>
</html>