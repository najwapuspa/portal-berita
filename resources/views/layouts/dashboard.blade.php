<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('judul', 'Dashboard') - NewsXPaper</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
<div class="top">
    <div class="wrap">
        <a class="logo" href="{{ url('/') }}">NEWS<i>X</i>PAPER<small>the art of publishing</small></a>
        <div class="who">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn pastel">Keluar</button>
            </form>
        </div>
    </div>
</div>

<nav class="menu" aria-label="Menu dashboard">
    <div class="wrap">
        @yield('menu')
    </div>
</nav>

<main class="wrap">
    <div class="bar-info">
        <div class="label">Hari ini</div>
        <time id="tgl"></time>
    </div>
    <h1 class="judul">@yield('hero')</h1>
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