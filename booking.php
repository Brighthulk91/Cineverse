<?php
session_start();
include('dbcon.php');

if(!isset($_SESSION['name'])) {
    header('location:index.php');
    exit();
}

$movie_query = mysqli_query($con, "SELECT Movie_id, Movie_Name FROM movies ORDER BY Movie_Name ASC");
$preselected_movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

// Pull every screening that still has open seats. The Hall -> Date -> Time
// cascade below is driven entirely from this dataset in JS so the user can
// only ever pick combinations that actually exist.
$timings_res = mysqli_query($con, "SELECT id, Theatre_Name, showtime, show_date, ticket_rate_Gold, ticket_rate_Silver, seats_remaining FROM timings WHERE seats_remaining > 0 AND show_date IS NOT NULL ORDER BY Theatre_Name, show_date, showtime");
$timings = [];
$halls = [];
while($t = mysqli_fetch_assoc($timings_res)) {
    $timings[] = $t;
    $halls[$t['Theatre_Name']] = true;
}
$timings_json = json_encode(array_values($timings), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

$sold_out = count($timings) === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE | Seat Reservation</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&display=swap');
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
        select:disabled { opacity: 0.4; cursor: not-allowed; }
    </style>
</head>

<body class="bg-[#020617] text-slate-200 min-h-screen">

<div class="max-w-2xl mx-auto p-6 md:p-12">

    <header class="flex justify-between items-center mb-12">
        <a href="userdashboard.php"
           class="group flex items-center text-slate-500 hover:text-cyan-400 transition-colors text-xs font-bold uppercase tracking-widest">
            <i class="fa fa-chevron-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
            Back to Dashboard
        </a>

        <div class="h-1 w-12 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full"></div>
    </header>

    <section class="mb-10">
        <h1 class="text-5xl font-extrabold tracking-tighter mb-4">
            RESERVE
            <span class="text-transparent bg-clip-text bg-gradient-to-br from-violet-400 to-cyan-400">
                SEATS
            </span>
        </h1>

        <p class="text-slate-400 text-sm">
            Select your movie, then hall, date, time and seat class.
        </p>
    </section>

    <?php if(isset($_GET['err'])): ?>
        <div class="mb-8 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-bold flex items-center">
            <i class="fa fa-circle-exclamation mr-3"></i>
            <?php echo $_GET['err'] === 'soldout' ? 'That showtime just sold out — please pick another.' : 'That showtime is no longer available — please pick another.'; ?>
        </div>
    <?php endif; ?>

    <?php if($sold_out): ?>
        <div class="text-center py-20 bg-slate-900/30 rounded-[3rem] border-2 border-dashed border-slate-800">
            <i class="fa fa-calendar-xmark text-5xl text-slate-800 mb-6 block"></i>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">No screenings available right now</p>
            <p class="text-slate-600 text-xs mt-2">Please check back soon.</p>
        </div>
    <?php else: ?>

    <form id="bookingForm" action="booking_confirm.php" method="post" class="space-y-10">

        <!-- MOVIE -->
        <div class="space-y-4">
            <label class="flex items-center text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                <i class="fa fa-film mr-2 text-violet-500"></i>
                Select Movie
            </label>

            <div class="relative group">
                <select name="movie_id" required
                    class="w-full bg-slate-900/50 border border-slate-800 p-5 rounded-2xl outline-none focus:ring-2 focus:ring-violet-500/50 appearance-none text-lg font-medium">

                    <option value="" disabled <?php echo $preselected_movie_id ? '' : 'selected'; ?>>
                        Choose a featured film...
                    </option>

                    <?php while($r = mysqli_fetch_array($movie_query)): ?>
                        <option value="<?php echo htmlspecialchars($r['Movie_id']); ?>" <?php echo ($preselected_movie_id && intval($r['Movie_id']) === $preselected_movie_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($r['Movie_Name']); ?>
                        </option>
                    <?php endwhile; ?>

                </select>
                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- HALL -->
        <div class="space-y-4">
            <label class="flex items-center text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                <i class="fa fa-building mr-2 text-cyan-400"></i>
                Select Hall
            </label>

            <div class="relative group">
                <select id="hallSelect" required
                    class="w-full bg-slate-900/50 border border-slate-800 p-5 rounded-2xl outline-none focus:ring-2 focus:ring-cyan-500/50 appearance-none text-lg font-medium">
                    <option value="" disabled selected>Choose a hall...</option>
                    <?php foreach(array_keys($halls) as $hall): ?>
                        <option value="<?php echo htmlspecialchars($hall); ?>"><?php echo htmlspecialchars($hall); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- DATE -->
        <div class="space-y-4">
            <label class="flex items-center text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                <i class="fa fa-calendar-day mr-2 text-cyan-400"></i>
                Select Date
            </label>

            <div class="relative group">
                <select id="dateSelect" disabled required
                    class="w-full bg-slate-900/50 border border-slate-800 p-5 rounded-2xl outline-none focus:ring-2 focus:ring-cyan-500/50 appearance-none text-lg font-medium">
                    <option value="" disabled selected>Pick a hall first...</option>
                </select>
                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- TIME -->
        <div class="space-y-4">
            <label class="flex items-center text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                <i class="fa fa-clock mr-2 text-cyan-400"></i>
                Select Showtime
            </label>

            <div class="relative group">
                <select id="timeSelect" disabled required
                    class="w-full bg-slate-900/50 border border-slate-800 p-5 rounded-2xl outline-none focus:ring-2 focus:ring-cyan-500/50 appearance-none text-lg font-medium">
                    <option value="" disabled selected>Pick a date first...</option>
                </select>
                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- SEAT CLASS -->
        <div id="classWrap" class="space-y-4 hidden">
            <label class="flex items-center text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                <i class="fa fa-chair mr-2 text-cyan-400"></i>
                Select Seat Class
            </label>

            <div class="grid grid-cols-2 gap-4">
                <label class="group cursor-pointer">
                    <input type="radio" name="seat_class" value="Gold" class="peer hidden" required>
                    <div class="py-6 bg-slate-900/30 border border-slate-800 rounded-2xl text-center transition-all
                        peer-checked:bg-gradient-to-br peer-checked:from-amber-500 peer-checked:to-yellow-600
                        peer-checked:border-transparent peer-checked:shadow-[0_0_20px_rgba(245,158,11,0.3)]
                        hover:border-slate-600">
                        <span class="block text-[10px] font-black uppercase tracking-widest text-amber-400 peer-checked:text-slate-950 mb-1">Gold</span>
                        <span id="goldPrice" class="block text-2xl font-black">$0</span>
                    </div>
                </label>
                <label class="group cursor-pointer">
                    <input type="radio" name="seat_class" value="Silver" class="peer hidden" required>
                    <div class="py-6 bg-slate-900/30 border border-slate-800 rounded-2xl text-center transition-all
                        peer-checked:bg-gradient-to-br peer-checked:from-slate-400 peer-checked:to-slate-500
                        peer-checked:border-transparent peer-checked:shadow-[0_0_20px_rgba(148,163,184,0.3)]
                        hover:border-slate-600">
                        <span class="block text-[10px] font-black uppercase tracking-widest text-slate-300 peer-checked:text-slate-950 mb-1">Silver</span>
                        <span id="silverPrice" class="block text-2xl font-black">$0</span>
                    </div>
                </label>
            </div>
            <p class="text-[10px] text-slate-500 ml-1"><span id="seatsLeft"></span> seats remaining for this showtime.</p>
        </div>

        <input type="hidden" name="timing_id" id="timingId" value="">

        <!-- SUBMIT -->
        <div class="pt-6">

            <button type="submit"
                    name="submit"
                    class="w-full group bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-slate-950 py-6 rounded-[2rem] font-black text-xl transition-all shadow-xl shadow-cyan-900/20 active:scale-[0.98]">

                CONTINUE TO TICKET

                <i class="fa fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>

            </button>

            <p class="text-center text-slate-600 text-[10px] uppercase font-bold mt-6 tracking-widest">
                Secure checkout powered by CINEVERSE Systems
            </p>

        </div>

    </form>
    <?php endif; ?>

</div>

<div class="fixed top-0 right-0 -z-10 w-[500px] h-[500px] bg-violet-600/5 rounded-full blur-[120px]"></div>
<div class="fixed bottom-0 left-0 -z-10 w-[500px] h-[500px] bg-cyan-600/5 rounded-full blur-[120px]"></div>

<script>
    const timingsData = <?php echo $timings_json; ?>;

    const hallSelect = document.getElementById('hallSelect');
    const dateSelect = document.getElementById('dateSelect');
    const timeSelect = document.getElementById('timeSelect');
    const classWrap = document.getElementById('classWrap');
    const timingIdInput = document.getElementById('timingId');

    function resetSelect(sel, placeholder) {
        sel.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
        sel.disabled = true;
    }

    function formatDate(iso) {
        const d = new Date(iso + 'T00:00:00');
        return d.toLocaleDateString(undefined, { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    }

    hallSelect.addEventListener('change', () => {
        resetSelect(dateSelect, 'Select a date...');
        resetSelect(timeSelect, 'Pick a date first...');
        classWrap.classList.add('hidden');
        timingIdInput.value = '';

        const hall = hallSelect.value;
        const dates = [...new Set(timingsData.filter(t => t.Theatre_Name === hall).map(t => t.show_date))].sort();

        dates.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d;
            opt.textContent = formatDate(d);
            dateSelect.appendChild(opt);
        });
        dateSelect.disabled = false;
    });

    dateSelect.addEventListener('change', () => {
        resetSelect(timeSelect, 'Select a time...');
        classWrap.classList.add('hidden');
        timingIdInput.value = '';

        const hall = hallSelect.value;
        const date = dateSelect.value;
        const slots = timingsData.filter(t => t.Theatre_Name === hall && t.show_date === date);

        slots.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.showtime + ' — ' + t.seats_remaining + ' seats left';
            opt.dataset.gold = t.ticket_rate_Gold;
            opt.dataset.silver = t.ticket_rate_Silver;
            opt.dataset.seats = t.seats_remaining;
            timeSelect.appendChild(opt);
        });
        timeSelect.disabled = false;
    });

    timeSelect.addEventListener('change', () => {
        const opt = timeSelect.selectedOptions[0];
        timingIdInput.value = opt.value;
        document.getElementById('goldPrice').textContent = '$' + opt.dataset.gold;
        document.getElementById('silverPrice').textContent = '$' + opt.dataset.silver;
        document.getElementById('seatsLeft').textContent = opt.dataset.seats;
        classWrap.classList.remove('hidden');

        // Clear any previously chosen class so price/seat mismatches can't linger
        document.querySelectorAll('input[name="seat_class"]').forEach(r => r.checked = false);
    });
</script>

</body>
</html>
