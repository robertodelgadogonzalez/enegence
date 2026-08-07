<?php

namespace App\Http\Controllers;

use App\Jobs\ImportEstadosJob;
use App\Models\Estado;
use App\Services\InegiCatalogoService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
            Log::error('Fallo al sincronizar el catálogo de estados', [
                'exception' => $exception->getMessage(),
            ]);

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

    public function municipios(string $cveEnt, InegiCatalogoService $inegi): JsonResponse
    {
        $cveEnt = str_pad($cveEnt, 2, '0', STR_PAD_LEFT);

        try {
            $municipios = $inegi->municipios($cveEnt)
                ->map(fn (array $municipio) => [
                    'cve_mun' => $municipio['cve_mun'],
                    'nomgeo' => $municipio['nomgeo'],
                    'pob_total' => array_key_exists('pob_total', $municipio)
                        ? number_format((int) $municipio['pob_total'])
                        : 'N/D',
                ])
                ->values();
        } catch (RequestException|Throwable $exception) {
            Log::error('Fallo al consultar municipios del INEGI', [
                'cve_ent' => $cveEnt,
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'No fue posible consultar los municipios de esta entidad.',
            ], 502);
        }

        return response()->json(['data' => $municipios]);
    }
}
