@php
    $flashTypes = ['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'status' => 'info', 'message' => 'info'];
@endphp

@foreach($flashTypes as $key => $class)
    @if(session($key))
        <div class="alert alert-{{ $class }} alert-dismissible fade show" role="alert">
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach

@if(session('notify'))
    @foreach((array) session('notify') as $notice)
        @php
            $type = $notice[0] ?? 'info';
            $message = $notice[1] ?? '';
            $class = $type === 'error' ? 'danger' : $type;
        @endphp
        <div class="alert alert-{{ $class }} alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif
