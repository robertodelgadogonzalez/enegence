@extends('layouts.app')

@section('title', 'Estados')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Entidades federativas</h1>
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
                <table
                    id="estados-table"
                    class="table table-striped table-hover w-100"
                    data-source="{{ route('estados.data') }}"
                    data-municipios-template="{{ route('estados.municipios', ['cveEnt' => 'CVE_ENT']) }}"
                >
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

    @if ($total > 0)
        <div class="d-flex justify-content-between align-items-center mt-3">
            <p class="text-muted small mb-0">Haz clic en un estado para consultar sus municipios.</p>
            <p class="text-muted small mb-0" id="ultima-actualizacion">
                @if ($ultimaActualizacion)
                    Última actualización: {{ \Illuminate\Support\Carbon::parse($ultimaActualizacion)->format('d/m/Y H:i') }}
                @else
                    Sin datos sincronizados todavía.
                @endif
            </p>
        </div>
    @endif

    <div class="modal fade" id="municipios-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="municipios-modal-titulo">Municipios</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="municipios-loading" class="text-center text-muted py-4 d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span> Cargando municipios...
                    </div>
                    <div id="municipios-error" class="alert alert-danger d-none"></div>
                    <table id="municipios-table" class="table table-sm table-striped d-none">
                        <thead>
                            <tr>
                                <th>Clave</th>
                                <th>Municipio</th>
                                <th>Población total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
