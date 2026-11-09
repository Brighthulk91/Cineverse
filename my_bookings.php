<?php 
session_start(); 
include('dbcon.php');

if(!isset($_SESSION['user'])) {
    header('location:index.php');
    exit();
}

$uid = $_SESSION['user'];
$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | CINEVERSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#020617] text-slate-100 p-6 md:p-12">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <h1 class="text-3xl font-black italic uppercase tracking-tighter">My <span class="text-violet-500">Bookings</span></h1>
            <a href="userdashboard.php" class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-white">← Back to Explore</a>
        </div>

        <div class="space-y-6">
            <?php
            // Securely fetch this user's bookings using a prepared statement
            $stmt = $con->prepare("SELECT * FROM bookings WHERE username = ? ORDER BY id DESC");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $res = $stmt->get_result();

            if(mysqli_num_rows($res) > 0):
                while($row = mysqli_fetch_array($res)): ?>
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-[2rem] flex justify-between items-center">
                    <div>
                        <p class="text-cyan-400 text-[10px] font-black uppercase tracking-widest mb-1"><?php echo htmlspecialchars(date('M d, Y', strtotime($row['booking_date']))); ?></p>
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($row['movie']); ?></h3>
                        <p class="text-slate-500 text-xs mt-1"><?php echo htmlspecialchars($row['theatre']); ?> • <?php echo htmlspecialchars($row['showtime']); ?></p>
                    </div>
                    <div class="text-right">
                        <span class="bg-violet-500/10 text-violet-400 px-4 py-2 rounded-xl text-xs font-black border border-violet-500/20">
                            $<?php echo number_format($row['price'], 2); ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div class="text-center py-20 bg-slate-900/30 rounded-[3rem] border-2 border-dashed border-slate-800">
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">No tickets found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>