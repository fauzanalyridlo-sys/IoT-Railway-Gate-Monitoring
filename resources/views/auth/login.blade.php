<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Railway Control</title>

<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Rajdhani', sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

/* ================= BACKGROUND ================= */
.scene {
    position: fixed;
    inset: 0;
}

.scene::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('{{ asset('kereta.jpg') }}') center/cover no-repeat;
    animation: bgMove 18s ease-in-out infinite alternate;
    filter: brightness(1.1) contrast(1.1) saturate(1.05);
}

.scene::after {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 50% 40%, rgba(255,255,200,0.15), transparent 60%),
        linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));
}

@keyframes bgMove {
    0% { transform: scale(1); }
    100% { transform: scale(1.07); }
}

/* ================= CARD ================= */
.card-wrap {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 420px;
    padding: 2rem;
}

.card {
    background: linear-gradient(
        145deg,
        rgba(20, 25, 40, 0.85),
        rgba(10, 12, 25, 0.9)
    );

    backdrop-filter: blur(20px);
    border-radius: 12px;
    padding: 2.5rem;

    border: 1px solid rgba(250,204,21,0.25);

    box-shadow:
        0 20px 60px rgba(0,0,0,0.6),
        inset 0 0 25px rgba(250,204,21,0.05),
        0 0 10px rgba(250,204,21,0.15);

    position: relative;
    overflow: hidden;

    animation: panelBlink 3s ease-in-out infinite;
}

/* garis atas ala panel */
.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;

    background: linear-gradient(
        90deg,
        transparent,
        #facc15,
        transparent
    );
}

/* efek dalam */
.card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 12px;
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.03),
        inset 0 0 15px rgba(0,0,0,0.4);
    pointer-events: none;
}

/* animasi panel */
@keyframes panelBlink {
    0%,100% { opacity: 1; }
    50% { opacity: 0.92; }
}

.card:hover {
    transform: translateY(-6px) scale(1.01);
}

/* ================= TITLE ================= */
.title-block {
    text-align: center;
    margin-bottom: 2rem;
}

.title {
    font-family: 'Share Tech Mono', monospace;
    font-size: 1.8rem;
    color: #facc15;
    letter-spacing: 6px;

    text-shadow:
        0 0 8px rgba(250,204,21,0.7),
        0 0 18px rgba(250,204,21,0.4);
}

/* garis bawah */
.title::after {
    content: '';
    display: block;
    margin: 10px auto 0;
    width: 60%;
    height: 2px;
    background: linear-gradient(to right, transparent, #facc15, transparent);
}

/* ================= INPUT ================= */
.field {
    position: relative;
    margin-bottom: 1.4rem;
}

.field i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.4);
}

input {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2.2rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 8px;
    color: white;
    transition: 0.3s;
}

input:focus {
    border-color: #facc15;
    box-shadow: 0 0 12px rgba(250,204,21,0.4);
    background: rgba(255,255,255,0.08);
    outline: none;
}

/* ================= BUTTON ================= */
.btn {
    width: 100%;
    padding: 0.9rem;
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #facc15, #f59e0b);
    color: #111;
    font-weight: bold;
    letter-spacing: 3px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: 0.3s;
}

.btn:hover {
    transform: scale(1.04);
    box-shadow: 0 0 20px rgba(250,204,21,0.7);
}

.btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transform: translateX(-100%);
    animation: shimmer 2.2s infinite;
}

@keyframes shimmer {
    100% { transform: translateX(100%); }
}

/* ================= FOOTER ================= */
.footer {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 11px;
    letter-spacing: 2px;
    color: rgba(255,255,255,0.6);
}

.scanlines {
    position: fixed;
    inset: 0;
    pointer-events: none;
    background: repeating-linear-gradient(
        0deg,
        transparent,
        transparent 2px,
        rgba(0,0,0,0.03) 2px,
        rgba(0,0,0,0.03) 4px
    );
}
</style>
</head>

<body>

<div class="scene"></div>
<div class="scanlines"></div>

<div class="card-wrap">
<div class="card">

    <div class="title-block">
        <div class="title">Railway Control</div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <i>📧</i>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="field">
            <i>🔒</i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button class="btn">LOGIN</button>
    </form>

</div>
</div>

</body>
</html>