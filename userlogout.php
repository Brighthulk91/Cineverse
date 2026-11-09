<?php 
session_start(); 

// Clear all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    <meta http-equiv="refresh" content="1;url=index.php">
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center">
    
    <div class="text-center">
        <div class="relative w-20 h-20 mx-auto mb-8">
            <div class="absolute inset-0 border-4 border-violet-500/20 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-t-violet-500 rounded-full animate-spin"></div>
        </div>

        <h1 class="text-2xl font-black tracking-tighter uppercase italic mb-2">
            Signing <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">Out</span>
        </h1>
        <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.4em]">Disconnecting from CINEVERSE...</p>
    </div>

</body>
</html>