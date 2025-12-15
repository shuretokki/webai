<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $chat->title }}</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .meta {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
        .message {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .message-header {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
            color: #444;
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .role-user {
            color: #2563eb;
        }
        .role-assistant {
            color: #059669;
        }
        .content {
            padding: 0 10px;
            white-space: pre-wrap;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $chat->title }}</h1>
        <div class="meta">
            Exported on {{ now()->toDayDateTimeString() }}
        </div>
    </div>

    @foreach($chat->messages as $message)
        <div class="message">
            <div class="message-header">
                <span class="role-{{ $message->role }}">
                    {{ ucfirst($message->role) }}
                </span>
                <span style="float: right; font-weight: normal; color: #888;">
                    {{ $message->created_at->format('M d, Y H:i') }}
                </span>
            </div>
            <div class="content">{{ $message->content }}</div>
        </div>
    @endforeach
</body>
</html>
