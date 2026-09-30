<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $article->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 16px; }
        .meta { color: #777; font-size: 14px; }
        .comment { background: #f3f4f6; padding: 10px 14px; border-radius: 6px; margin-bottom: 8px; }
        a { color: #1a56db; text-decoration: none; }
    </style>
</head>
<body>
    <a href="{{ route('articles.index') }}">← Kembali</a>

    <h1>{{ $article->title }}</h1>
    <p class="meta">{{ $article->category->name }} · {{ $article->user->name }} · {{ $article->created_at->format('d M Y') }}</p>
    <p>{{ $article->content }}</p>

    <h3>Komentar ({{ $article->comments->count() }})</h3>
    @foreach ($article->comments as $comment)
        <div class="comment">
            <strong>{{ $comment->user->name }}</strong>
            <p>{{ $comment->body }}</p>
        </div>
    @endforeach
</body>
</html>