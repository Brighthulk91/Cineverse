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

$username = $_SESSION['name'] ?? 'Guest User';

$stmt = $con->prepare("SELECT Movie_Name FROM movies WHERE Movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$movie_name = $movie['Movie_Name'] ?? "Unknown Movie";

$t_stmt = $con->prepare("SELECT * FROM timings WHERE id = ?");
$t_stmt->bind_param("i", $timing_id);
$t_stmt->execute();
$timing = $t_stmt->get_result()->fetch_assoc();

if(!$timing){
    header('location:booking.php?err=unavailable');
    exit();
}

$theatre = $timing['Theatre_Name'];
$showtime = $timing['showtime'];
$booking_date = $timing['show_date'];
$price = $seat_class === 'Gold' ? $timing['ticket_rate_Gold'] : $timing['ticket_rate_Silver'];

// Atomically claim a seat: only decrement if one is actually still available,
// which prevents two people racing for the last seat from both succeeding.
$seat_stmt = $con->prepare("UPDATE timings SET seats_remaining = seats_remaining - 1 WHERE id = ? AND seats_remaining > 0");
$seat_stmt->bind_param("i", $timing_id);
$seat_stmt->execute();

if($seat_stmt->affected_rows === 0){
    // Someone else took the last seat between page loads
    header('location:booking.php?err=soldout');
    exit();
}

$insert_stmt = $con->prepare(
"INSERT INTO bookings
(username, movie, theatre, booking_date, showtime, seat_class, price)
VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$insert_stmt->bind_param(
"ssssssi",
$username,
$movie_name,
$theatre,
$booking_date,
$showtime,
$seat_class,
$price
);

$insert_stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CINEVERSE Ticket</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

body{
    font-family:'Plus Jakarta Sans',sans-serif;
}

.barcode{
    font-family:'Libre Barcode 128',cursive;
    font-size:50px;
}

@media print{
    .no-print{
        display:none;
    }

    body{
        background:white;
    }
}
</style>
</head>

<body class="bg-[#020617] text-slate-200 min-h-screen flex flex-col items-center justify-center p-6">

<div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10 w-[500px] h-[500px] bg-violet-600/10 rounded-full blur-[120px]"></div>

<div class="bg-white text-slate-950 w-full max-w-sm rounded-[2.5rem] overflow-hidden shadow-2xl">

    <div class="p-8 text-center bg-slate-50 border-b-2 border-dashed border-slate-200">
        <h1 class="text-2xl font-black italic tracking-tighter">
            CINE <span class="text-violet-600">VERSE</span>
        </h1>

        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
            Official Admission Pass
        </p>
    </div>

    <div class="p-10 space-y-8">

        <div class="text-center">
            <span class="text-[10px] font-black text-violet-600 bg-violet-50 px-3 py-1 rounded-full uppercase">
                Confirmed Booking
            </span>

            <h2 class="text-3xl font-extrabold uppercase mt-4 tracking-tighter">
                <?php echo htmlspecialchars($movie_name); ?>
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-y-6 pt-4">

            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase">Date</p>
                <p class="text-sm font-bold"><?php echo htmlspecialchars(date('M d, Y', strtotime($booking_date))); ?></p>
            </div>

            <div class="text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase">Time</p>
                <p class="text-sm font-bold"><?php echo htmlspecialchars($showtime); ?></p>
            </div>

            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase">Theatre</p>
                <p class="text-sm font-bold"><?php echo htmlspecialchars($theatre); ?></p>
            </div>

            <div class="text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase">Class</p>
                <p class="text-sm font-bold"><?php echo htmlspecialchars($seat_class); ?></p>
            </div>

            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase">Price</p>
                <p class="text-sm font-bold">$<?php echo htmlspecialchars($price); ?></p>
            </div>

        </div>

        <div class="pt-6 border-t border-slate-100 text-center">

            <div class="barcode opacity-80 leading-none">
                CINEVERSE-<?php echo rand(1000,9999); ?>
            </div>

            <p class="text-[8px] font-bold text-slate-400 tracking-[0.5em]">
                SCAN AT ENTRANCE
            </p>

        </div>

    </div>

</div>

<div class="mt-10 flex gap-4 no-print">

    <button onclick="window.print()"
        class="bg-slate-800 hover:bg-slate-700 px-8 py-4 rounded-2xl text-sm font-black">
        PRINT
    </button>

    <a href="userdashboard.php"
        class="bg-violet-600 hover:bg-violet-500 px-8 py-4 rounded-2xl text-sm font-black">
        DONE
    </a>

</div>

</body>
</html>
