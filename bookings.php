<?php
session_start();
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php');

// Optional search by customer username or movie name
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if($search !== ''){
    $like = "%{$search}%";
    $stmt = $con->prepare("SELECT * FROM bookings WHERE username LIKE ? OR movie LIKE ? ORDER BY booking_time DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = mysqli_query($con, "SELECT * FROM bookings ORDER BY booking_time DESC");
}

$total_bookings = mysqli_num_rows($res);

$rev_res = mysqli_query($con, "SELECT SUM(price) AS total FROM bookings");
$total_revenue = mysqli_fetch_assoc($rev_res)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Bookings</title>
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
            <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center shadow-lg shadow-rose-500/20">
                <i class="fa fa-receipt text-slate-950 text-xs"></i>
            </div>
            <h1 class="text-xl font-black text-white tracking-tighter italic">CINEVERSE <span class="text-rose-500 font-black">ADMIN</span></h1>
        </div>

        <div class="space-y-1">
            <a href="dash.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-chart-pie w-5 group-hover:text-rose-400"></i>
                <span class="text-sm">Overview</span>
            </a>

            <a href="movie.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-clapperboard w-5 group-hover:text-rose-400"></i>
                <span class="text-sm">Movie Library</span>
            </a>

            <a href="timings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-clock w-5 group-hover:text-rose-400"></i>
                <span class="text-sm">Showtimes</span>
            </a>

            <a href="theatres.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-building w-5 group-hover:text-rose-400"></i>
                <span class="text-sm">Theatres</span>
            </a>

            <a href="bookings.php" class="flex items-center space-x-3 p-4 rounded-2xl bg-rose-600 text-white shadow-lg shadow-rose-900/20 font-bold transition-all">
                <i class="fa fa-receipt w-5"></i>
                <span class="text-sm">Bookings</span>
            </a>

            <a href="users.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-users w-5 group-hover:text-rose-400"></i>
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
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-rose-600/5 rounded-full blur-[120px] -z-10"></div>

        <header class="flex flex-col md:flex-row justify-between md:items-end gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-black uppercase tracking-widest">
                        Ticketing
                    </span>
                    <span class="text-slate-600 text-[10px] font-black uppercase tracking-widest">
                        <?php echo $total_bookings; ?> Bookings
                    </span>
                </div>
                <h2 class="text-4xl font-extrabold text-white tracking-tight uppercase italic">Who Booked <span class="text-rose-500">What</span></h2>
            </div>

            <div class="flex items-center gap-4">
                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest">
                    Total Revenue: $<?php echo number_format($total_revenue, 2); ?>
                </span>
            </div>
        </header>

        <form action="bookings.php" method="get" class="mb-8">
            <div class="relative max-w-md">
                <i class="fa fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-600"></i>
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by customer or movie..."
                    class="w-full bg-slate-900/50 border border-slate-800 rounded-2xl p-4 pl-12 pr-28 text-white outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition-all">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-black uppercase px-5 py-2.5 rounded-xl transition-all">
                    Search
                </button>
            </div>
            <?php if($search !== ''): ?>
                <a href="bookings.php" class="inline-block mt-3 text-[10px] font-bold text-slate-500 hover:text-white uppercase tracking-widest">
                    <i class="fa fa-xmark mr-1"></i> Clear search
                </a>
            <?php endif; ?>
        </form>

        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800/50 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
                        <tr>
                            <th class="p-8 border-b border-slate-800">Customer</th>
                            <th class="p-8 border-b border-slate-800">Movie</th>
                            <th class="p-8 border-b border-slate-800">Hall / Showtime</th>
                            <th class="p-8 border-b border-slate-800">Date</th>
                            <th class="p-8 border-b border-slate-800">Class</th>
                            <th class="p-8 border-b border-slate-800 text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <?php if($total_bookings > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors group">
                                <td class="p-8">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center text-rose-400 border border-slate-800 font-black text-xs">
                                            <?php echo strtoupper(substr($row['username'] ?? '?', 0, 1)); ?>
                                        </div>
                                        <span class="font-bold text-white"><?php echo htmlspecialchars($row['username']); ?></span>
                                    </div>
                                </td>
                                <td class="p-8">
                                    <span class="font-semibold text-slate-200"><?php echo htmlspecialchars($row['movie']); ?></span>
                                </td>
                                <td class="p-8">
                                    <span class="text-slate-300 text-sm"><?php echo htmlspecialchars($row['theatre']); ?></span>
                                    <span class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mt-1"><?php echo htmlspecialchars($row['showtime']); ?></span>
                                </td>
                                <td class="p-8 text-xs text-slate-500 font-bold">
                                    <?php echo htmlspecialchars(date('M d, Y', strtotime($row['booking_date']))); ?>
                                </td>
                                <td class="p-8">
                                    <span class="px-3 py-1 rounded-lg text-xs font-black border <?php echo ($row['seat_class'] ?? '') === 'Gold' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-slate-700/20 text-slate-300 border-slate-600/30'; ?>">
                                        <?php echo htmlspecialchars($row['seat_class'] ?? '—'); ?>
                                    </span>
                                </td>
                                <td class="p-8 text-right">
                                    <span class="bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-lg text-xs font-black border border-emerald-500/20">
                                        $<?php echo number_format($row['price'], 2); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-20 text-center">
                                    <i class="fa fa-receipt text-5xl text-slate-800 mb-4 block"></i>
                                    <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">
                                        <?php echo $search !== '' ? 'No bookings match that search' : 'No bookings recorded yet'; ?>
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
