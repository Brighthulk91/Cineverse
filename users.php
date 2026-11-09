<?php 
session_start(); 
if(!isset($_SESSION['user'])) {
    header('location:admin.php');
    exit();
}
include('dbcon.php'); 

// Fetch total count for the stat badge
$total_users = mysqli_num_rows(mysqli_query($con, "SELECT id FROM users"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Customer Database</title>
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
            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center shadow-lg shadow-orange-500/20">
                <i class="fa fa-users text-slate-950 text-xs"></i>
            </div>
            <h1 class="text-xl font-black text-white tracking-tighter italic">CINEVERSE <span class="text-orange-500 font-black">ADMIN</span></h1>
        </div>
        
        <div class="space-y-1">
            <a href="dash.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-chart-pie w-5 group-hover:text-orange-400"></i>
                <span class="text-sm">Overview</span>
            </a>
            
            <a href="movie.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-clapperboard w-5 group-hover:text-orange-400"></i>
                <span class="text-sm">Movie Library</span>
            </a>

            <a href="users.php" class="flex items-center space-x-3 p-4 rounded-2xl bg-orange-600 text-white shadow-lg shadow-orange-900/20 font-bold transition-all">
                <i class="fa fa-users w-5"></i>
                <span class="text-sm">Customers</span>
            </a>

            <a href="theatres.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-building w-5 group-hover:text-orange-400"></i>
                <span class="text-sm">Theatres</span>
            </a>

            <a href="bookings.php" class="flex items-center space-x-3 p-4 rounded-2xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all font-semibold group">
                <i class="fa fa-receipt w-5 group-hover:text-orange-400"></i>
                <span class="text-sm">Bookings</span>
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
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-orange-600/5 rounded-full blur-[120px] -z-10"></div>

        <header class="flex justify-between items-end mb-12">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-[10px] font-black uppercase tracking-widest">
                        CRM Database
                    </span>
                    <span class="text-slate-600 text-[10px] font-black uppercase tracking-widest">
                        Total: <?php echo htmlspecialchars($total_users); ?>
                    </span>
                </div>
                <h2 class="text-4xl font-extrabold text-white tracking-tight uppercase italic">Registered <span class="text-orange-500">Audience</span></h2>
            </div>
            <button onclick="window.print()" class="bg-slate-800 border border-slate-700 hover:bg-slate-700 text-white px-6 py-3 rounded-xl font-black text-xs transition-all flex items-center">
                <i class="fa fa-download mr-2 text-orange-400"></i> EXPORT LIST
            </button>
        </header>

        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-[2.5rem] overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/50 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
                    <tr>
                        <th class="p-8 border-b border-slate-800">Account ID</th>
                        <th class="p-8 border-b border-slate-800">User Profile</th>
                        <th class="p-8 border-b border-slate-800">Contact Address</th>
                        <th class="p-8 border-b border-slate-800 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    <?php 
                    $res = mysqli_query($con, "SELECT * FROM users ORDER BY id DESC");
                    if(mysqli_num_rows($res) > 0):
                        while($row = mysqli_fetch_array($res)): 
                    ?>
                    <tr class="hover:bg-slate-800/20 transition-colors group">
                        <td class="p-8">
                            <span class="font-mono text-xs text-slate-600 font-bold">#USR-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></span>
                        </td>
                        <td class="p-8">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center text-orange-400 border border-slate-800 font-black text-xs">
                                    <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                </div>
                                <span class="font-bold text-white"><?php echo htmlspecialchars($row['username']); ?></span>
                            </div>
                        </td>
                        <td class="p-8">
                            <span class="text-sm text-slate-400 italic"><?php echo htmlspecialchars($row['email']); ?></span>
                        </td>
                        <td class="p-8 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="user_delete.php?did=<?php echo htmlspecialchars($row['id']); ?>" 
                                   onclick="return confirm('Permanently remove this user account? All history will be lost.');"
                                   class="w-10 h-10 flex items-center justify-center bg-slate-800 rounded-xl text-slate-500 hover:bg-red-600 hover:text-white transition-all shadow-lg"
                                   title="Terminate Access">
                                    <i class="fa fa-user-x"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <i class="fa fa-user-secret text-5xl text-slate-800 mb-4 block"></i>
                            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">No users found in database</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>