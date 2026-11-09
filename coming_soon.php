<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon | CINEVERSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .glitch-text {
            position: relative;
            text-shadow: 0.05em 0 0 #00fffc, -0.03em -0.04em 0 #fc00ff,
                         0.025em 0.04em 0 #fffc00;
            animation: glitch 725ms infinite;
        }

        @keyframes glitch {
            0% { text-shadow: 0.05em 0 0 #00fffc, -0.03em -0.04em 0 #fc00ff, 0.025em 0.04em 0 #fffc00; }
            15% { text-shadow: 0.05em 0 0 #00fffc, -0.03em -0.04em 0 #fc00ff, 0.025em 0.04em 0 #fffc00; }
            16% { text-shadow: -0.05em -0.025em 0 #00fffc, 0.025em 0.035em 0 #fc00ff, -0.05em -0.05em 0 #fffc00; }
            100% { text-shadow: -0.025em 0 0 #00fffc, -0.025em -0.0125em 0 #fc00ff, -0.025em -0.05em 0 #fffc00; }
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen overflow-x-hidden">
    
    <div class="fixed top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 pointer-events-none"></div>
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-violet-600/10 rounded-full blur-[120px]"></div>

    <div class="max-w-7xl mx-auto p-8 md:p-20 relative z-10">
        <header class="mb-20">
            <a href="userdashboard.php" class="group text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-12 inline-flex items-center hover:text-white transition-colors">
                <i class="fa fa-arrow-left mr-3 group-hover:-translate-x-2 transition-transform"></i> Return to Dashboard
            </a>
            
            <div class="mt-8">
                <h1 class="text-6xl md:text-8xl font-black mb-4 tracking-tighter italic glitch-text uppercase">
                    Coming <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">Soon</span>
                </h1>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-[0.5em] max-w-xl">
                    The next generation of cinematic experiences is currently being encrypted.
                </p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <div class="group relative bg-slate-900/40 border border-slate-800 p-8 rounded-[3rem] hover:bg-slate-900/60 transition-all duration-500 hover:border-violet-500/30">
                <div class="relative h-80 bg-slate-950 rounded-[2rem] mb-6 overflow-hidden flex flex-col items-center justify-center border border-slate-800 group-hover:border-violet-500/20">
                    <i class="fa fa-lock text-4xl text-slate-800 mb-4 group-hover:text-violet-500/50 transition-colors"></i>
                    <div class="text-[10px] font-black text-slate-700 uppercase tracking-widest">Data Encrypted</div>
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-violet-500/5 to-transparent h-1/2 w-full animate-pulse"></div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-start">
                        <h3 class="font-black text-2xl uppercase italic tracking-tighter group-hover:text-white">The Last Horizon</h3>
                        <span class="text-[10px] bg-slate-800 px-3 py-1 rounded-full text-slate-400 font-bold">2026</span>
                    </div>
                    <p class="text-xs text-cyan-400 font-black uppercase tracking-widest flex items-center">
                        <i class="fa fa-calendar-check mr-2"></i> June Release
                    </p>
                    <button class="w-full py-4 rounded-2xl bg-slate-950 border border-slate-800 text-[10px] font-black uppercase tracking-widest hover:bg-violet-600 hover:text-white hover:border-violet-600 transition-all">
                        Notify Me
                    </button>
                </div>
            </div>

            <div class="group relative bg-slate-900/40 border border-slate-800 p-8 rounded-[3rem] hover:bg-slate-900/60 transition-all duration-500 hover:border-cyan-500/30">
                <div class="relative h-80 bg-slate-950 rounded-[2rem] mb-6 overflow-hidden flex flex-col items-center justify-center border border-slate-800 group-hover:border-cyan-500/20">
                    <i class="fa fa-shuttle-space text-4xl text-slate-800 mb-4 group-hover:text-cyan-500/50 transition-colors"></i>
                    <div class="text-[10px] font-black text-slate-700 uppercase tracking-widest">Protocol Pending</div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-start">
                        <h3 class="font-black text-2xl uppercase italic tracking-tighter group-hover:text-white">Cyber Night</h3>
                        <span class="text-[10px] bg-slate-800 px-3 py-1 rounded-full text-slate-400 font-bold">2026</span>
                    </div>
                    <p class="text-xs text-cyan-400 font-black uppercase tracking-widest flex items-center">
                        <i class="fa fa-calendar-check mr-2"></i> July Release
                    </p>
                    <button class="w-full py-4 rounded-2xl bg-slate-950 border border-slate-800 text-[10px] font-black uppercase tracking-widest hover:bg-cyan-500 hover:text-slate-950 hover:border-cyan-500 transition-all">
                        Notify Me
                    </button>
                </div>
            </div>

            <div class="border-2 border-dashed border-slate-800 rounded-[3rem] flex items-center justify-center p-12 opacity-40">
                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.4em] text-center leading-loose">
                    More titles<br>unveiling soon
                </p>
            </div>
        </div>
    </div>

    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-600/10 rounded-full blur-[120px]"></div>
</body>
</html>