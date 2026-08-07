<?php

namespace App\Http\Controllers;

use App\Jobs\ImportEstadosJob;
use App\Models\Estado;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class EstadoController extends Controller
{
    public function index(): View
    {
        return view('estados.index', [
            'total' => Estado::count(),
            'ultimaActualizacion' => Estado::max('updated_at'),
        ]);
    }

    public function data(): JsonResponse
    {
        $estados = Estado::query()->select(['id', 'cve_ent', 'nomgeo', 'pob_total']);

        return DataTables::of($estados)
            ->editColumn('pob_total', fn (Estado $estado) => number_format($estado->pob_total))
            ->toJson();
    }

    public function sync(): JsonResponse
    {
        try {
            ImportEstadosJob::dispatchSync();
        } catch (RequestException|Throwable $exception) {
            return response()->json([
                'message' => 'No fue posible sincronizar el catálogo del INEGI. Intenta de nuevo en unos minutos.',
            ], 502);
        }

        return response()->json([
            'message' => 'Catálogo de estados sincronizado correctamente.',
            'total' => Estado::count(),
            'ultima_actualizacion' => Estado::max('updated_at'),
        ]);
    }
}
