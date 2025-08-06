@props(['id','items'])
<div class="dropdown">
    <button
        class="btn btn-primary dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu">
        @foreach($items as $uri => $title)
            @if($title === 'divider')
                <li>
                    <hr class="dropdown-divider">
                </li>
            @else
                <li>
                    <a href="{{ \Illuminate\Support\Str::replace(':id', $id, $uri) }}" class="dropdown-item">{{ $title }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
