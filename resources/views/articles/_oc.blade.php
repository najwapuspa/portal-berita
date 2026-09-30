@php $badge = $badge ?? null; @endphp
<a href="{{ route('articles.show', $a->slug) }}"
   class="oc g-{{ $a->category->slug }} {{ $size ?? '' }}"
   @if ($a->image) style="background-image:url('{{ asset('storage/' . $a->image) }}')" @endif>
  @if ($badge === 'star')<span class="badge rd"><svg viewBox="0 0 24 24"><path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.5 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"/></svg></span>@endif
  @if ($badge === 'image')<span class="badge sq"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM6 17l4-5 3 3 2-2 3 4z" fill-rule="evenodd"/></svg></span>@endif
  <div class="ocin">
    <span class="tag">{{ $a->category->name }}</span>
    <h3>{{ $a->title }}</h3>
    <div class="by"><i>{{ mb_substr($a->user->name, 0, 1) }}</i>oleh {{ $a->user->name }} &bull; {{ $a->created_at->format('d M Y') }} &bull; {{ $a->comments_count }} Komentar</div>
  </div>
</a>