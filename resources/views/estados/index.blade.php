@extends('layouts.app')

@section('title', 'Estados')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Entidades federativas</h1>
            <p class="text-muted mb-0" id="ultima-actualizacion">
                @if ($ultimaActualizacion)
                    Última actualización: {{ \Illuminate\Support\Carbon::parse($ultimaActualizacion)->format('d/m/Y H:i') }}
                @else
                    Sin datos sincronizados todavía.
                @endif
            </p>
        </div>
        <button type="button" class="btn btn-enegence" data-sync-estados="{{ route('estados.sync') }}">
            <i class="fas fa-sync-alt me-1"></i> Sincronizar
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($total === 0)
                <div class="text-center py-5">
                    <p class="text-muted mb-3">Todavía no hay estados cargados.</p>
                    <button type="button" class="btn btn-enegence" data-sync-estados="{{ route('estados.sync') }}">
                        <i class="fas fa-cloud-download-alt me-1"></i> Sincronizar catálogo del INEGI
                    </button>
                </div>
            @else
                <table id="estados-table" class="table table-striped table-hover w-100" data-source="{{ route('estados.data') }}">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Estado</th>
                            <th>Población total</th>
                        </tr>
                    </thead>
                </table>
            @endif
        </div>
    </div>
@endsection
