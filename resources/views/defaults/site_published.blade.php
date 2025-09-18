<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Siteniz Yayında 🎉</title>
    <style>
        :root{
            --bg: #0f1221;
            --grad1:#6ee7f9; --grad2:#a78bfa; --grad3:#f472b6;
            --card:#111529; --muted:#96a0c2; --ok:#22c55e;
        }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{
            margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
            color:#e5e7f5; background: radial-gradient(1200px 800px at 20% 10%,#1b2040 0%,transparent 60%),
        radial-gradient(1000px 700px at 80% 90%,#1a1537 0%,transparent 55%),
        var(--bg);
            display:grid; place-items:center; padding:24px;
        }
        .wrap{max-width:720px; width:100%}
        .badge{
            display:inline-flex; align-items:center; gap:8px; font-weight:600;
            padding:8px 12px; border-radius:999px; background:rgba(34,197,94,.12); color:#bbf7d0;
            border:1px solid rgba(34,197,94,.25); backdrop-filter: blur(4px);
            animation: pop .6s ease-out both;
        }
        @keyframes pop{0%{transform:scale(.9); opacity:0}100%{transform:scale(1); opacity:1}}
        .card{
            margin-top:16px; background:linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
            border:1px solid rgba(255,255,255,.08);
            border-radius:20px; padding:28px; box-shadow: 0 10px 40px rgba(0,0,0,.35);
            backdrop-filter: blur(8px);
        }
        h1{
            margin:12px 0 6px; font-size: clamp(28px, 4vw, 40px); line-height:1.15;
            background: linear-gradient(90deg, var(--grad1), var(--grad2), var(--grad3));
            -webkit-background-clip: text; background-clip:text; color: transparent;
        }
        p{margin:8px 0; color:var(--muted)}
        .row{display:flex; flex-wrap:wrap; gap:12px; margin-top:18px}
        .btn{
            appearance:none; border:1px solid rgba(255,255,255,.14);
            background:#161a33; color:#e8ecff; padding:12px 16px; border-radius:12px;
            text-decoration:none; font-weight:600; transition:.18s transform,.18s opacity,.18s border-color;
        }
        .btn:hover{transform: translateY(-1px); border-color: rgba(255,255,255,.3)}
        .btn.primary{
            background: linear-gradient(90deg, var(--grad1), var(--grad2));
            color:#0b1030; border-color: transparent;
        }
        .tips{
            margin-top:18px; padding:14px 16px; border-radius:14px;
            border:1px dashed rgba(255,255,255,.18); background: rgba(255,255,255,.03);
            font-size:15px;
        }
        .ok{color:var(--ok); font-weight:700}
        .footer{margin-top:18px; font-size:13px; color:#7f8ab3}
        .check{
            width:56px;height:56px; border-radius:50%; display:inline-grid; place-items:center;
            background:radial-gradient(120% 120% at 30% 30%, #34d399 0%, #16a34a 70%);
            box-shadow: 0 10px 22px rgba(34,197,94,.35), inset 0 0 8px rgba(255,255,255,.25);
            border:1px solid rgba(255,255,255,.2);
        }
        .check svg{fill:#071418}
        .center{display:flex; align-items:center; gap:14px}
        .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace; font-size:14px}
    </style>
</head>
<body>
<div class="wrap">
    <span class="badge">
      <span class="check" aria-hidden="true">
        <svg width="28" height="28" viewBox="0 0 24 24" role="img" aria-label="başarılı">
          <path d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/>
        </svg>
      </span>
      Yayın durumu: <span class="ok">Başarılı</span>
    </span>

    <div class="card">
        <h1>🎉 Siteniz başarıyla yayına alındı!</h1>
        <p class="mono">Alan adı: <strong>{{ $domain }}</strong></p>
        <p>Kurulum tamamlandı. DNS, SSL ve yönlendirmelerinizin aktif olması birkaç dakika sürebilir.</p>

        <div class="row">
            <a class="btn primary" href="/" target="_self">Siteye Git</a>
        </div>

        <div class="tips">
            <ul style="margin:0; padding-left:18px; line-height:1.6">
                <li>DNS değişiklikleri 5–10 dk sürebilir. Cloudflare kullanıyorsanız <em>SSL Mode: Full (Strict)</em> önerilir.</li>
                <li>Laravel için <code class="mono">APP_URL</code> ve <code class="mono">APP_ENV</code> değerlerini kontrol edin.</li>
                <li>Önbellek sorunlarında: <code class="mono">php artisan optimize:clear</code></li>
            </ul>
        </div>

        <div class="footer">
            © <span id="y"></span> WebKedi • Tüm hakları saklıdır.
        </div>
    </div>
</div>

<script>
    document.getElementById('y').textContent = new Date().getFullYear();
</script>
</body>
</html>
