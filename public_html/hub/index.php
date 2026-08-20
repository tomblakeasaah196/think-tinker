<?php
/**
 * hub/index.php — Hub Login Page
 * Standalone (premium glassmorphism, animated environment, mobile-first).
 * Redirects to dashboard if already logged in.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Already logged in? Staff go to the dashboard; parents never belong in the Hub.
$already = getCurrentUser();
if ($already) {
    if (($already['user_type'] ?? '') === 'parent') {
        redirect(APP_URL . '/parent/');
    }
    header('Location: ' . HUB_URL . '/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Operations Centre — Think & Tinker</title>
    
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito+Sans:ital,opsz,wght@0,6..12,400;0,6..12,600;0,6..12,700;1,6..12,400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <style>
        /* --- GLOBAL CSS RESETS & VARIABLES --- */
        :root {
            --tinker-teal: #1AAFA0;
            --spark-orange: #F47B20;
            --idea-gold: #FDB813;
            --deep-navy: #1B2A4A;
            --charcoal: #2C3E50;
            
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 32px 64px rgba(0, 0, 0, 0.3);
            
            --radius-btn: 12px;
            --radius-card: 24px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body, html {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: 'Nunito Sans', sans-serif;
            color: var(--charcoal);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* --- ANIMATED LIVE BACKGROUND --- */
        body {
            background: linear-gradient(-45deg, #1B2A4A, #1AAFA0, #F47B20, #1B2A4A);
            background-size: 400% 400%;
            animation: gradientBG 20s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .bg-image-layer {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url('<?= APP_URL ?>/assets/img/logo/hub-background.png');
            background-size: cover;
            background-position: center;
            opacity: 0.3; 
            mix-blend-mode: overlay;
            z-index: 0;
            pointer-events: none;
        }

        /* Ambient Orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 10s infinite ease-in-out alternate;
            pointer-events: none;
        }
        .orb-1 { width: 400px; height: 400px; background: rgba(26, 175, 160, 0.5); top: -100px; left: -100px; }
        .orb-2 { width: 500px; height: 500px; background: rgba(244, 123, 32, 0.4); bottom: -150px; right: -100px; animation-delay: -5s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 50px) scale(1.1); }
        }

        /* --- PREMIUM GLASSMORPHISM CARD (MOBILE FIRST) --- */
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            position: relative;
            z-index: 10;
        }

        .glass-card {
            width: 100%;
            max-width: 500px; /* Constrains width on mobile/tablet */
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-top: 1px solid rgba(255, 255, 255, 0.8);
            border-left: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: var(--radius-card);
            box-shadow: var(--glass-shadow);
            display: flex;
            flex-direction: column; /* Stacked by default */
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .panel {
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* --- INTRO PANEL (Dark Glass) --- */
        .intro-panel {
            background: linear-gradient(135deg, rgba(27, 42, 74, 0.8) 0%, rgba(27, 42, 74, 0.5) 100%);
            color: #FFFFFF;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15); /* Separator for mobile */
        }

        .brand-header h1 {
            font-family: 'Fredoka One', cursive;
            font-size: 28px;
            margin: 0;
            letter-spacing: 1px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .brand-header h1 span { color: var(--spark-orange); }
        .brand-tagline {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--idea-gold);
            text-transform: uppercase;
            margin-top: 8px;
        }

        .live-environment { margin: 32px 0; }
        .dynamic-greeting {
            font-family: 'Fredoka One', cursive;
            font-size: 22px; 
            line-height: 1.4;
            margin-bottom: 12px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .time-location {
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.8);
        }
        .clock-pulse {
            width: 8px; height: 8px;
            background-color: var(--spark-orange);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--spark-orange);
            animation: pulse 2s infinite;
        }

        .brand-manifesto {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 16px;
            border-left: 4px solid var(--idea-gold);
        }
        .manifesto-quote {
            font-family: 'Georgia', serif;
            font-style: italic;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
            color: #FFFFFF;
            transition: opacity 0.8s ease-in-out;
            opacity: 1;
        }
        .manifesto-quote.fade-out { opacity: 0; }

        /* --- FORM PANEL (Light Glass) --- */
        .form-panel {
            background: rgba(255, 255, 255, 0.6);
            position: relative;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-bottom: 24px;
        }
        .logo-img {
            max-width: 120px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.15));
        }

        .form-header { text-align: center; margin-bottom: 24px; }
        .form-header h2 {
            font-family: 'Fredoka One', cursive;
            color: var(--charcoal);
            font-size: 22px;
            margin: 0 0 8px 0;
        }
        .form-header p {
            color: var(--charcoal);
            font-size: 14px;
            margin: 0;
            font-weight: 700;
        }

        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: var(--deep-navy);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-input {
            width: 100%;
            height: 50px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: var(--radius-btn);
            font-family: 'Nunito Sans', sans-serif;
            font-size: 15px;
            color: var(--deep-navy);
            font-weight: 600;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.4s ease;
        }
        .form-input::placeholder { color: rgba(44, 62, 80, 0.5); font-weight: 400; }
        .form-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.9);
            border-color: var(--tinker-teal);
            box-shadow: 0 0 0 4px rgba(26, 175, 160, 0.2), inset 0 2px 4px rgba(0,0,0,0.05);
            transform: translateY(-1px);
        }

        .password-wrapper { position: relative; display: flex; align-items: center; }
        .form-input[type="password"], .form-input[type="text"] { padding-right: 48px; }
        
        .toggle-password {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--charcoal);
            opacity: 0.6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: opacity 0.3s;
        }
        .toggle-password:hover { opacity: 1; }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: -8px; 
            margin-bottom: 24px;
        }
        .checkbox-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--charcoal);
            cursor: pointer;
            user-select: none;
        }
        input[type="checkbox"] {
            accent-color: var(--tinker-teal);
            width: 16px;
            height: 16px;
            cursor: pointer;
            margin: 0;
        }

        .btn {
            height: 50px;
            width: 100%;
            border-radius: var(--radius-btn);
            font-family: 'Nunito Sans', sans-serif;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            border: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--tinker-teal), #138D80);
            color: #FFFFFF;
            box-shadow: 0 10px 20px rgba(26, 175, 160, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(26, 175, 160, 0.5);
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            top: -50%; left: -60%;
            width: 20%; height: 200%;
            background: rgba(255,255,255,0.3);
            transform: rotate(30deg);
            transition: all 0.6s ease;
        }
        .btn-primary:hover::after { left: 140%; }

        .btn-outline {
            background: rgba(255,255,255,0.5);
            border: 2px solid var(--deep-navy);
            color: var(--deep-navy);
        }
        .btn-outline:hover { background: var(--deep-navy); color: #FFF; }

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--tinker-teal);
            text-decoration: none;
            font-weight: 800;
            transition: 0.3s;
        }
        .forgot-link:hover { color: var(--spark-orange); letter-spacing: 0.5px; }

        #forgotForm {
            display: none;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 123, 32, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(244, 123, 32, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 123, 32, 0); }
        }

        /* --- DESKTOP VIEW (Expanding the card horizontally) --- */
        @media (min-width: 900px) {
            .glass-card { 
                flex-direction: row; 
                max-width: 1100px;
                min-height: 650px; 
            }
            .panel { padding: 48px; }
            .intro-panel { 
                flex: 1.2; 
                border-bottom: none; 
                border-right: 1px solid rgba(255, 255, 255, 0.15); 
            }
            .form-panel { flex: 1; }
            
            /* Scale up typography for PC */
            .brand-header h1 { font-size: 36px; }
            .brand-tagline { font-size: 13px; }
            .dynamic-greeting { font-size: 28px; }
            .time-location { font-size: 18px; }
            .manifesto-quote { font-size: 20px; }
            .form-header h2 { font-size: 26px; }
            .form-header p { font-size: 15px; }
            .form-input, .btn { height: 54px; font-size: 15px; }
        }
    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="bg-image-layer"></div>

    <div class="login-wrapper">
        <div class="glass-card">
            
            <div class="panel intro-panel">
                <div class="brand-header">
                    <h1>THINK & <span>TINKER</span></h1>
                    <div class="brand-tagline">Learn. Build. Secure. Lead.</div>
                </div>

                <div class="live-environment">
                    <div class="dynamic-greeting" id="greetingText">Welcome.</div>
                    <div class="time-location">
                        <div class="clock-pulse"></div>
                        <span id="liveClock">00:00 AM</span>
                        <span style="opacity: 0.3; margin: 0 8px;">|</span>
                        <span id="userLocation">Operations Centre</span>
                    </div>
                </div>

                <div class="brand-manifesto">
                    <p class="manifesto-quote" id="quoteText">“Every child is a thinker. Every child is a tinker. Every child can shine.”</p>
                </div>
            </div>

            <div class="panel form-panel">
                
                <div class="logo-container">
                    <img src="<?= APP_URL ?>/assets/img/logo/logo_tnt_portrait.png" alt="Think & Tinker" class="logo-img" onerror="this.style.display='none'">
                </div>

                <div class="form-header">
                    <h2>Authorised Access</h2>
                    <p>Enter your credentials to continue.</p>
                </div>

                <form id="loginForm">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="emailInput" class="form-input" placeholder="educator@think-tinker.com" required autofocus>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Secure Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="passwordInput" class="form-input" placeholder="••••••••" required>
                            <button type="button" class="toggle-password" id="togglePasswordBtn" aria-label="Toggle password visibility">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="rememberMe" name="remember">
                        <label for="rememberMe" class="checkbox-label">Remember my email</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Sign In</button>
                </form>

                <a href="#" id="forgotLink" class="forgot-link">Forgot password?</a>

                <div id="forgotForm">
                    <p style="font-size: 13px; font-weight: 600; color: var(--charcoal); margin-bottom: 16px;">Enter your email to receive a secure reset link.</p>
                    <form id="resetForm">
                        <input type="hidden" name="action" value="forgot_password">
                        <input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                        <div class="form-group">
                            <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
                        </div>
                        <button type="submit" class="btn btn-outline">Send Reset Link</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // --- 1. LIVE CLOCK & GREETING ---
            const updateClockAndGreeting = () => {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                document.getElementById('liveClock').innerText = timeString;

                const hour = now.getHours();
                let greeting = "";

                if (hour >= 5 && hour < 12) {
                    const mornings = [
                        "Good Morning. The world is a blank canvas today—what will we help them build?",
                        "Rise and shine. Every great invention starts with a single, curious question asked in the morning light.",
                        "Good Morning. Let’s spark curiosity, encourage the messy mistakes, and shape the future today.",
                        "A brilliant morning ahead. Ready to guide the next generation of thinkers, tinkerers, and leaders?"
                    ];
                    greeting = mornings[Math.floor(Math.random() * mornings.length)];
                } else if (hour >= 12 && hour < 17) {
                    const afternoons = [
                        "Good Afternoon. The most profound discoveries happen when we lose track of time in the middle of a project.",
                        "Afternoon. Keep the momentum alive—every new idea explored today is a stepping stone to mastery.",
                        "Good Afternoon. Embrace the beautiful, creative chaos of midday tinkering. Let their imaginations run wild.",
                        "Midday check-in. This is the perfect hour to push boundaries, test hypotheses, and build something remarkable."
                    ];
                    greeting = afternoons[Math.floor(Math.random() * afternoons.length)];
                } else {
                    const evenings = [
                        "Good Evening. The space may be quieting down, but the ideas sparked today will echo into tomorrow.",
                        "Evening, night owl. Some of the most brilliant blueprints are drafted when the rest of the world is asleep.",
                        "Good Evening. Take a moment to reflect on today's breakthroughs before we reset for tomorrow's adventures.",
                        "The stars are out. A perfect time to recharge, review the day's creations, and dream up the next big challenge."
                    ];
                    greeting = evenings[Math.floor(Math.random() * evenings.length)];
                }
                
                document.getElementById('greetingText').innerText = greeting;
            };

            // Fetch User Location
            try {
                const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
                const city = tz.split('/').pop().replace(/_/g, ' '); 
                if (city) document.getElementById('userLocation').innerText = city;
            } catch (e) {}

            updateClockAndGreeting();
            setInterval(updateClockAndGreeting, 60000);

            // --- 2. 20-QUOTE ROTATOR (8s Interval) ---
            const quotes = [
                "“Every child is a thinker. Every child is a tinker. Every child can shine.”",
                "“Curiosity is the engine of achievement.”",
                "“Play is the highest form of research.”",
                "“The best way to predict the future is to invent it.”",
                "“Imagination is more important than knowledge.”",
                "“We learn by doing, and we grow by failing.”",
                "“To invent, you need a good imagination and a pile of junk.”",
                "“Creativity is intelligence having fun.”",
                "“Every great advance in science has issued from a new audacity of imagination.”",
                "“The mind is not a vessel to be filled, but a fire to be kindled.”",
                "“Mistakes are the portals of discovery.”",
                "“Logic will get you from A to B. Imagination will take you everywhere.”",
                "“The important thing is to not stop questioning.”",
                "“Tell me and I forget, teach me and I may remember, involve me and I learn.”",
                "“Innovation distinguishes between a leader and a follower.”",
                "“There are no rules of architecture for a castle in the clouds.”",
                "“Wonder is the beginning of wisdom.”",
                "“The beautiful thing about learning is that no one can take it away from you.”",
                "“Children are not things to be molded, but are people to be unfolded.”",
                "“Let them play, let them build, let them lead.”"
            ];
            
            const quoteElement = document.getElementById('quoteText');
            let quoteIndex = 0;

            setInterval(() => {
                quoteElement.classList.add('fade-out');
                setTimeout(() => {
                    quoteIndex = (quoteIndex + 1) % quotes.length;
                    quoteElement.innerText = quotes[quoteIndex];
                    quoteElement.classList.remove('fade-out');
                }, 800); 
            }, 8000); 

            // --- 3. TOGGLE PASSWORD VISIBILITY ---
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('passwordInput');
            
            togglePasswordBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    togglePasswordBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>`;
                } else {
                    togglePasswordBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>`;
                }
            });

            // --- 4. REMEMBER MY EMAIL ---
            const emailInput = document.getElementById('emailInput');
            const rememberMeCheckbox = document.getElementById('rememberMe');
            const savedEmail = localStorage.getItem('tnt_saved_email');
            
            if (savedEmail) {
                emailInput.value = savedEmail;
                rememberMeCheckbox.checked = true;
            }
        });

        // --- 5. API & FORM SUBMISSION ---
        window.TT = window.TT || { config: {} };
        TT.config.apiBase = '<?= HUB_URL ?>/api';
        TT.config.csrfToken = '<?= CSRF_TOKEN ?>';

        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            if (document.getElementById('rememberMe').checked) {
                localStorage.setItem('tnt_saved_email', document.getElementById('emailInput').value);
            } else {
                localStorage.removeItem('tnt_saved_email');
            }
            
            TT.submitForm('#loginForm', 'AuthController.php', {
                onSuccess: function(r) {
                    if (r.success) {
                        if(typeof TT.toast === 'function') TT.toast('Welcome to Operations Centre.', 'success');
                        setTimeout(() => window.location.href = (r.data && r.data.redirect) ? r.data.redirect : '<?= HUB_URL ?>/dashboard.php', 800);
                    }
                }
            });
        });

        $('#forgotLink').on('click', function(e) {
            e.preventDefault();
            $('#forgotForm').slideToggle(300);
        });

        $('#resetForm').on('submit', function(e) {
            e.preventDefault();
            TT.submitForm('#resetForm', 'AuthController.php', {
                onSuccess: function(r) { 
                    if (r.success && typeof TT.toast === 'function') {
                        TT.toast(r.message, 'success', 6000); 
                    }
                }
            });
        });
    </script>
</body>
</html>