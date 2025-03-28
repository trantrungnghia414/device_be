<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{-- <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            padding: 20px;
            max-width: 570px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }
        h1 {
            color: #e3342f;
            text-align: center;
            margin-bottom: 30px;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
           // color: #333;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            //color: #666;
            font-style: italic;
        }
    </style> --}}
</head>
<body>
    <div class="container">
        {{ Illuminate\Mail\Markdown::parse($slot) }}
    </div>
</body>
</html>
