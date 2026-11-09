<?php 
session_start();
include('dbcon.php');

$error = "";

if(!isset($_SESSION['admin_attempts'])) $_SESSION['admin_attempts'] = 0;
if(!isset($_SESSION['admin_lock_until'])) $_SESSION['admin_lock_until'] = 0;

$locked = time() < $_SESSION['admin_lock_until'];

if(isset($_POST['login']) && !$locked){
    $u = $_POST['uname']; 
    $p = $_POST['pass'];

    $stmt = $con->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $admin_row = $stmt->get_result()->fetch_assoc();

    $valid = false;
    if($admin_row){
        // Supports hashed passwords, and transparently upgrades a legacy
        // plaintext password (like the default seed account) to a hash
        // the first time it's used successfully.
        if(password_verify($p, $admin_row['password'])){
            $valid = true;
        } elseif(hash_equals($admin_row['password'], $p)){
            $valid = true;
            $new_hash = password_hash($p, PASSWORD_DEFAULT);
            $upd = $con->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $upd->bind_param("si", $new_hash, $admin_row['id']);
            $upd->execute();
        }
    }

    if($valid){
        $_SESSION['user'] = $u;
        $_SESSION['admin_attempts'] = 0;
        session_regenerate_id(true);
        header('location:dash.php');
        exit();
    } else {
        $_SESSION['admin_attempts']++;
        if($_SESSION['admin_attempts'] >= 5){
            $_SESSION['admin_lock_until'] = time() + 60;
            $_SESSION['admin_attempts'] = 0;
            $locked = true;
        } else {
            $error = "Invalid Administrative Credentials";
        }
    }
}

if($locked){
    $error = "Too many failed attempts. Please try again in a minute.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE Admin | Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#050505] text-slate-200 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <div class="absolute top-0 -left-20 w-96 h-96 bg-violet-600/20 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-0 -right-20 w-96 h-96 bg-cyan-600/10 rounded-full blur-[120px]"></div>

    <div class="relative w-full max-w-md">
        <div class="absolute -top-6 -left-6 w-12 h-12 border-t-2 border-l-2 border-violet-500 rounded-tl-2xl"></div>
        <div class="absolute -bottom-6 -right-6 w-12 h-12 border-b-2 border-r-2 border-cyan-500 rounded-br-2xl"></div>

        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-10 rounded-[2.5rem] shadow-2xl relative z-10">
            <header class="mb-10">
                <div class="inline-block px-3 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                    Secure Area
                </div>
                <h1 class="text-4xl font-extrabold tracking-tighter text-white">
                    CINEVERSE <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">ADMIN</span>
                </h1>
                <p class="text-slate-500 text-sm mt-2">Enter credentials to access the management portal.</p>
            </header>

            <form action="admin.php" method="post" class="space-y-5">
                <?php if($error): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs p-4 rounded-xl text-center">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 ml-1 uppercase">Username</label>
                    <input type="text" name="uname" placeholder="admin_user" required 
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-white outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition-all placeholder:text-slate-700">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 ml-1 uppercase">Password</label>
                    <input type="password" name="pass" placeholder="••••••••" required 
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-white outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition-all placeholder:text-slate-700">
                </div>

                <button type="submit" name="login" <?php echo $locked ? 'disabled' : ''; ?>
                    class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 py-4 rounded-2xl font-bold text-white shadow-lg shadow-violet-900/20 transition-all active:scale-[0.98] mt-4 uppercase tracking-widest text-sm disabled:opacity-40 disabled:cursor-not-allowed">
                    <?php echo $locked ? 'Locked' : 'Authenticate'; ?>
                </button>
            </form>

            <footer class="mt-8 text-center">
                <a href="index.php" class="text-slate-500 text-xs hover:text-violet-400 transition">← Back to Audience Portal</a>
            </footer>
        </div>
    </div>
</body>
</html>