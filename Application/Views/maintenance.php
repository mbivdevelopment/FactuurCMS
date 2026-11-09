<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deze site is niet bereikbaar</title>
    <?php $fav = branding_url('favicon'); ?>
    <?php if ($fav !== ''): ?><link rel="icon" href="<?= e($fav) ?>"><?php endif; ?>
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; color: #202124; background: #fff; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #fff; }
        main { width: min(600px, calc(100% - 48px)); margin: 0 auto; padding-top: 19vh; }
        h1 { margin: 0 0 18px; font-size: clamp(28px, 5vw, 36px); font-weight: 400; letter-spacing: -.5px; }
        p { margin: 0 0 12px; color: #5f6368; font-size: 16px; line-height: 1.55; }
        .hint { margin-top: 30px; font-size: 14px; }
        button { margin-top: 30px; border: 0; border-radius: 4px; padding: 11px 22px; color: #fff; background: #1a73e8; font-size: 14px; font-weight: 600; cursor: pointer; }
        button:hover { background: #185abc; }
        code { font-family: monospace; font-size: 13px; color: #5f6368; }
        @media (max-width: 600px) { main { padding-top: 12vh; } }
    </style>
</head>
<body>
<main>
    <h1>Deze site is niet bereikbaar</h1>
    <p>Deze website is tijdelijk niet beschikbaar omdat er onderhoud wordt uitgevoerd.</p>
    <p class="hint">Probeer het later opnieuw of laad de pagina opnieuw.</p>
    <button type="button" onclick="window.location.reload()">Opnieuw laden</button>
    <p class="hint"><code>ERR_MAINTENANCE_MODE</code></p>
</main>
</body>
</html>
