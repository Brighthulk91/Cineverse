<?php
session_start();
include('dbcon.php');

if(!isset($_SESSION['name'])) {
    header('location:index.php');
    exit();
}

if(
    !isset($_POST['movie_id']) ||
    !isset($_POST['timing_id']) ||
    !isset($_POST['seat_class'])
){
    header('location:booking.php');
    exit();
}

$movie_id = intval($_POST['movie_id']);
$timing_id = intval($_POST['timing_id']);
$seat_class = $_POST['seat_class'] === 'Gold' ? 'Gold' : 'Silver';

$stmt = $con->prepare("SELECT Movie_Name FROM movies WHERE Movie_id=?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

$t_stmt = $con->prepare("SELECT * FROM timings WHERE id=?");
$t_stmt->bind_param("i", $timing_id);
$t_stmt->execute();
$timing = $t_stmt->get_result()->fetch_assoc();

if(!$movie || !$timing || $timing['seats_remaining'] <= 0){
    header('location:booking.php?err=unavailable');
    exit();
}

$movie_name = $movie['Movie_Name'];
$theatre_name = $timing['Theatre_Name'];
$showtime = $timing['showtime'];
$show_date = $timing['show_date'];
$price = $seat_class === 'Gold' ? $timing['ticket_rate_Gold'] : $timing['ticket_rate_Silver'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirm Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-200 flex items-center justify-center min-h-screen p-6">

    <div class="bg-slate-900 border border-slate-800 p-10 rounded-[3rem] w-full max-w-md shadow-2xl">

        <div class="text-center mb-8">
            <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2">
                Selected Movie
            </p>

            <h2 class="text-3xl font-black">
                <?php echo htmlspecialchars($movie_name); ?>
            </h2>
        </div>

        <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-4 mb-8">

            <div class="flex justify-between text-sm font-bold">
                <span class="text-slate-500 uppercase">Hall</span>
                <span class="text-cyan-400">
                    <?php echo htmlspecialchars($theatre_name); ?>
                </span>
            </div>

            <div class="flex justify-between text-sm font-bold">
                <span class="text-slate-500 uppercase">Date</span>
                <span class="text-cyan-400 font-mono">
                    <?php echo htmlspecialchars(date('D, M d, Y', strtotime($show_date))); ?>
                </span>
            </div>

            <div class="flex justify-between text-sm font-bold">
                <span class="text-slate-500 uppercase">Showtime</span>
                <span class="text-cyan-400 font-mono">
                    <?php echo htmlspecialchars($showtime); ?>
                </span>
            </div>

            <div class="flex justify-between text-sm font-bold">
                <span class="text-slate-500 uppercase">Seat Class</span>
                <span class="<?php echo $seat_class === 'Gold' ? 'text-amber-400' : 'text-slate-300'; ?>">
                    <?php echo htmlspecialchars($seat_class); ?>
                </span>
            </div>

            <div class="flex justify-between text-sm font-bold pt-4 border-t border-slate-800">
                <span class="text-slate-500 uppercase">Price</span>
                <span class="text-white text-lg">$<?php echo htmlspecialchars($price); ?></span>
            </div>

            <div class="flex justify-between text-sm font-bold">
                <span class="text-slate-500 uppercase">Status</span>
                <span class="text-green-500">Reserved</span>
            </div>

        </div>

        <form action="receipt.php" method="post">

            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
            <input type="hidden" name="timing_id" value="<?php echo $timing_id; ?>">
            <input type="hidden" name="seat_class" value="<?php echo htmlspecialchars($seat_class); ?>">

            <button type="submit" name="confirm"
                class="w-full bg-violet-600 hover:bg-violet-500 py-4 rounded-2xl font-black text-lg transition-all shadow-lg shadow-violet-600/30">
                PAY & GET TICKET
            </button>

        </form>

    </div>

</body>
</html>
