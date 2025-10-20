@if (isset($href))
    <a href="{{ $href }}" class="btn btn-{{ $type ?? 'primary' }} {{ $class ?? '' }}" title="{{ $title ?? '' }}"
        {{ $attributes ?? '' }}>
        <i class="fas {{ $icon ?? 'fa-circle' }} mr-1"></i> {{ $slot }}
    </a>
@else
    <button type="submit" class="btn btn-{{ $type ?? 'primary' }} {{ $class ?? '' }}" title="{{ $title ?? '' }}"
        {{ $attributes ?? '' }}>
        <i class="fas {{ $icon ?? 'fa-circle' }} mr-1"></i> {{ $slot }}
    </button>
@endif
