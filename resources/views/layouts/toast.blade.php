<div class="toast-container-brutal">

    @foreach (['success', 'error', 'warning', 'info'] as $msgType)
        @if(session($msgType))
            @php
                $headerClass = '';
                $iconClass = '';
                $title = '';

                switch($msgType) {
                    case 'success':
                        $headerClass = 'toast-success';
                        $iconClass = 'fa-solid fa-circle-check';
                        $title = 'Éxito';
                        break;
                    case 'error':
                        $headerClass = 'toast-error';
                        $iconClass = 'fa-solid fa-circle-xmark';
                        $title = 'Error';
                        break;
                    case 'warning':
                        $headerClass = 'toast-warning';
                        $iconClass = 'fa-solid fa-triangle-exclamation';
                        $title = 'Advertencia';
                        break;
                    case 'info':
                        $headerClass = 'toast-info';
                        $iconClass = 'fa-solid fa-circle-info';
                        $title = 'Información';
                        break;
                }
            @endphp

            <div class="toast-brutal {{ $headerClass }}">
                <div class="toast-header">
                    <i class="{{ $iconClass }}"></i>
                    <strong>{{ $title }}</strong>
                    <small>Justo ahora</small>
                    <button type="button" class="btn-close" aria-label="Cerrar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="toast-body">
                    {{ session($msgType) }}
                </div>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="toast-brutal toast-error">
                <div class="toast-header">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <strong>Error de validación</strong>
                    <small>Justo ahora</small>
                    <button type="button" class="btn-close" aria-label="Cerrar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="toast-body">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    @endif
</div>
