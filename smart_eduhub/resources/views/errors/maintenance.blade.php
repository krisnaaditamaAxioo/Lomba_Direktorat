<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - Sistem Dalam Perbaikan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1e293b;
        }
        .card {
            background: white;
            border-radius: 32px;
            padding: 50px 40px;
            max-width: 560px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0,0,0,0.25);
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo {
            max-height: 80px;
            margin-bottom: 28px;
        }
        .icon-wrap {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); }
            50% { box-shadow: 0 0 0 20px rgba(251, 191, 36, 0); }
        }
        .icon-wrap svg {
            width: 48px;
            height: 48px;
            color: #b45309;
        }
        h1 {
            font-size: 30px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .message-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            margin: 24px 0;
            text-align: left;
        }
        .message-box p {
            font-size: 14px;
            color: #334155;
            line-height: 1.7;
        }
        .message-box p:empty { display: none; }
        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .info-card {
            flex: 1;
            min-width: 140px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-radius: 14px;
            padding: 16px;
        }
        .info-card .label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.6px;
            color: #6366f1;
            margin-bottom: 6px;
        }
        .info-card .value {
            font-size: 14px;
            font-weight: 700;
            color: #3730a3;
        }
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 24px 0;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 8px;
        }
        .btn-refresh {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 28px;
            background: #4f46e5;
            color: white;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-refresh:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        .admin-hint {
            margin-top: 16px;
            padding: 10px 14px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            font-size: 12px;
            color: #065f46;
            display: none;
        }
    </style>
</head>
<body>
    <div class="card">
        @if($logoDataUri)
        <img src="{{ $logoDataUri }}" alt="Logo {{ $appName }}" class="logo">
        @endif

        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>

        <h1>Sistem Sedang Dalam Perbaikan</h1>
        <p class="subtitle">
            Mohon maaf atas ketidaknyamanannya. Tim {{ $appName }} sedang melakukan perawatan dan pemeliharaan sistem.
        </p>

        @if($message)
        <div class="message-box">
            <p>{{ $message }}</p>
        </div>
        @endif

        <div class="info-row">
            <div class="info-card">
                <div class="label">Status</div>
                <div class="value">Maintenance</div>
            </div>
            <div class="info-card">
                <div class="label">Waktu Server</div>
                <div class="value">{{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
            </div>
            <div class="info-card">
                <div class="label">Coba Lagi</div>
                <div class="value">Beberapa saat lagi</div>
            </div>
        </div>

        <button class="btn-refresh" onclick="window.location.reload()">
            🔄 Coba Muat Ulang
        </button>

        <div class="divider"></div>
        <p class="footer-text">
            © {{ \Carbon\Carbon::now()->format('Y') }} {{ $appName }}. All rights reserved.
        </p>
    </div>
</body>
</html>
