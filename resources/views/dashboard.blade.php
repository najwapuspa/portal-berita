<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Dashboard') – wartanusa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="{{ url('/') }}">
            <span class="mark">W</span>
            <span>wartanusa</span>
        </a>
        <div class="actions">
            <a class="link-btn" href="{{ url('/') }}">Ke beranda</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-out">Keluar</button>
            </form>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-inner">
        <h1>@yield('hero')</h1>
        <p><time id="tgl"></time></p>
    </div>
</section>

<nav class="tabs" aria-label="Menu dashboard">
    <div class="tabs-inner">
        @yield('menu')
    </div>
</nav>

<main>
    @yield('konten')
</main>

<script>
// Hari, tanggal, dan jam otomatis; diperbarui tiap menit
function tick(){
    const now = new Date();
    const tgl = now.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
    const jam = now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}).replace('.',':');
    const el = document.getElementById('tgl');
    el.textContent = tgl + ', ' + jam;
    el.setAttribute('datetime', now.toISOString());
}
tick(); setInterval(tick, 60000);
</script>
</body>
</html>