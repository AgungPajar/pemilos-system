<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Token Pemilos</title>
    <style>
        @page { size: A4; margin: 12mm; }
        body { font-family: Arial, Helvetica, sans-serif; color: #000; }
        .tokens-grid {
            margin: 0;
            padding: 0;
        }
        /* Three columns per row using floats for Dompdf compatibility */
        .token-box {
            box-sizing: border-box;
            float: left;
            width: 33.3333%;
            padding: 4px;
            border: 1px solid #000;
            height: 60px; /* total box height */
            text-align: center;
            page-break-inside: avoid;
            font-family: 'Courier New', Courier, monospace;
            font-size: 28px;
            letter-spacing: 6px;
            line-height: 52px; /* centers the single-line token vertically */
        }
        /* Ensure rows align when printing */
        .row-break { width: 100%; }
    </style>
</head>
<body>
    <div class="tokens-grid">
        @foreach($tokens as $token)
            <div class="token-box">{{ $token->code }}</div>
            @if($loop->iteration % 3 == 0)
                <div style="clear: both;"></div>
            @endif
        @endforeach
    </div>
</body>
</html>
