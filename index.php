<?php 
session_start(); 
include('dbcon.php');

$error = "";

if(isset($_POST['submit'])){
    $name = $_POST['name']; 
    $pass = $_POST['pass'];

    // Secure Prepared Statement to prevent SQL Injection
    $stmt = $con->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        $valid = false;

        if(password_verify($pass, $row['password'])){
            $valid = true;
        } elseif(hash_equals($row['password'], $pass)){
            // Transparently upgrade a legacy plaintext password to a hash
            // the first time it's used successfully.
            $valid = true;
            $new_hash = password_hash($pass, PASSWORD_DEFAULT);
            $upd = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->bind_param("si", $new_hash, $row['id']);
            $upd->execute();
        }

        if($valid){
            $_SESSION['user'] = $row['id'];
            $_SESSION['name'] = $row['username'];
            session_regenerate_id(true);
            header('location:userdashboard.php');
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEVERSE | Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-violet-600/10 rounded-full blur-[120px] -z-10"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-cyan-600/10 rounded-full blur-[120px] -z-10"></div>

    <div class="bg-slate-900/60 backdrop-blur-2xl border border-slate-800 p-10 rounded-[3rem] w-full max-w-md shadow-2xl relative">
        <header class="text-center mb-12">
            <div class="w-16 h-16 bg-gradient-to-tr from-violet-600 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg shadow-cyan-500/20 mx-auto mb-6 rotate-3 hover:rotate-0 transition-transform duration-500">
                <i class="fa fa-play text-white text-2xl ml-1"></i>
            </div>
            <h1 class="text-4xl font-black tracking-tighter text-white">CINE <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">VERSE</span></h1>
            <p class="text-slate-500 text-sm mt-2 font-medium uppercase tracking-[0.1em]">Audience Gateway</p>
        </header>

        <form action="index.php" method="post" class="space-y-5">
            <?php if($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs p-4 rounded-2xl text-center flex items-center justify-center space-x-2">
                    <i class="fa fa-circle-exclamation"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Username</label>
                <div class="relative">
                    <i class="fa fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 text-sm"></i>
                    <input type="text" name="name" placeholder="cinema_lover" required 
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all placeholder:text-slate-700">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Secret Key</label>
                <div class="relative">
                    <i class="fa fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 text-sm"></i>
                    <input type="password" name="pass" placeholder="••••••••" required 
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all placeholder:text-slate-700">
                </div>
            </div>

            <button type="submit" name="submit" 
                class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 py-4 rounded-2xl font-black text-lg shadow-lg shadow-cyan-900/20 transition-all active:scale-[0.98] mt-4 uppercase">
                Sign In
            </button>
        </form>

        <div class="mt-10 pt-8 border-t border-slate-800/50 text-center">
            <p class="text-sm text-slate-500">
                First time here? 
                <a href="signup.php" class="text-violet-400 font-bold hover:text-violet-300 transition-colors ml-1 underline decoration-violet-500/30 underline-offset-4">Create an Account</a>
            </p>
            <a href="admin.php" class="inline-block mt-6 text-[10px] font-bold text-slate-600 hover:text-slate-400 uppercase tracking-widest transition-colors">
                <i class="fa fa-user-shield mr-1"></i> Admin Portal
            </a>
        </div>
    </div>
</body>
</html>