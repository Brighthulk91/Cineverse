<?php 
session_start(); 
include('dbcon.php');

$msg = "";
$status = "";

// Password policy: 8-16 characters, letters and numbers only,
// must contain at least one letter and at least one digit.
$password_pattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,16}$/';

if(isset($_POST['submit'])){
    $n = $_POST['name']; 
    $e = $_POST['email']; 
    $p = $_POST['pass'];

    // 1. Validate password against the policy before touching the database
    if(!preg_match($password_pattern, $p)){
        $msg = "Password must be 8-16 characters, using only letters and numbers, with at least one letter and one number.";
        $status = "error";
    } else {
        // 2. Check if email already exists
        $check = $con->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $e);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){
            $msg = "Email already registered!";
            $status = "error";
        } else {
            // 3. Insert new user securely, storing a hashed password
            $hashed_password = password_hash($p, PASSWORD_DEFAULT);
            $stmt = $con->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $n, $e, $hashed_password);

            if($stmt->execute()){
                $msg = "Account created! Redirecting to login...";
                $status = "success";
                header("refresh:2; url=index.php");
            } else {
                $msg = "Registration failed. Try again.";
                $status = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join CINEVERSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-violet-600/10 rounded-full blur-[120px] -z-10"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-cyan-600/10 rounded-full blur-[120px] -z-10"></div>

    <div class="bg-slate-900/60 backdrop-blur-2xl border border-slate-800 p-10 rounded-[3rem] w-full max-w-md shadow-2xl">
        <header class="mb-10">
            <h1 class="text-4xl font-black tracking-tighter text-white">JOIN THE <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">CLUB</span></h1>
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-2">Create your cinematic viewer account</p>
        </header>

        <form action="signup.php" method="post" class="space-y-5">
            <?php if($msg): ?>
                <div class="<?php echo $status == 'success' ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-red-500/10 border-red-500/20 text-red-400'; ?> border text-xs p-4 rounded-2xl flex items-center space-x-3 transition-all">
                    <i class="fa <?php echo $status == 'success' ? 'fa-check-circle' : 'fa-circle-exclamation'; ?>"></i>
                    <span class="font-bold"><?php echo htmlspecialchars($msg); ?></span>
                </div>
            <?php endif; ?>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Full Name</label>
                <div class="relative">
                    <i class="fa fa-id-card absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 text-sm"></i>
                    <input type="text" name="name" placeholder="John Doe" required 
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all placeholder:text-slate-700">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Email Address</label>
                <div class="relative">
                    <i class="fa fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 text-sm"></i>
                    <input type="email" name="email" placeholder="john@example.com" required 
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all placeholder:text-slate-700">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Password</label>
                <div class="relative">
                    <i class="fa fa-lock-open absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 text-sm"></i>
                    <input type="password" name="pass" id="pass" placeholder="••••••••" required
                        minlength="8" maxlength="16"
                        pattern="(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,16}"
                        title="8-16 characters, letters and numbers only, at least one letter and one number"
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 pl-12 text-white outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all placeholder:text-slate-700">
                </div>
                <p id="passHint" class="text-[10px] text-slate-500 mt-1.5 ml-1 flex items-start gap-1.5 transition-colors">
                    <i class="fa fa-circle-info mt-0.5"></i>
                    <span>Use <span class="font-bold">8–16 characters</span>: letters (A-Z, a-z) and numbers (0-9) only, with at least one letter and one number.</span>
                </p>
            </div>

            <button type="submit" name="submit" 
                class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 py-5 rounded-2xl font-black text-lg shadow-xl shadow-cyan-900/20 transition-all active:scale-[0.98] mt-4 uppercase tracking-tighter">
                Register Account
            </button>
        </form>

        <div class="mt-10 pt-8 border-t border-slate-800/50 text-center">
            <p class="text-sm text-slate-500 font-medium">
                Part of the family? 
                <a href="index.php" class="text-violet-400 font-bold hover:text-violet-300 transition-colors ml-1 underline decoration-violet-500/30 underline-offset-4">Sign In</a>
            </p>
        </div>
    </div>

    <script>
        const passInput = document.getElementById('pass');
        const passHint = document.getElementById('passHint');
        const passPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,16}$/;

        passInput.addEventListener('input', () => {
            passHint.classList.remove('text-slate-500', 'text-green-400', 'text-red-400');
            if(passInput.value.length === 0){
                passHint.classList.add('text-slate-500');
            } else if(passPattern.test(passInput.value)){
                passHint.classList.add('text-green-400');
            } else {
                passHint.classList.add('text-red-400');
            }
        });
    </script>
</body>
</html>