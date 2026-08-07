<?php

namespace App\Jobs;

use App\Models\Estado;
use App\Services\InegiCatalogoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Sincroniza el catálogo de entidades federativas del INEGI contra la tabla `estados`.
 *
 * Se despacha con dispatchSync() para que corra en el mismo proceso (comando o
 * request web) sin depender de un worker de colas, aunque al implementar
 * ShouldQueue queda lista para despacharse de forma asíncrona el día que se
 * necesite (por ejemplo, un resync programado).
 */
class ImportEstadosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @return int Número de estados procesados.
     */
    public function handle(InegiCatalogoService $inegi): int
    {
        $estados = $inegi->estados();

        $rows = $estados->map(fn (array $estado) => [
            'cve_ent' => $estado['cve_ent'],
            'nomgeo' => $estado['nomgeo'],
            'pob_total' => (int) $estado['pob_total'],
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        Estado::upsert($rows, uniqueBy: ['cve_ent'], update: ['nomgeo', 'pob_total', 'updated_at']);

        return count($rows);
    }
}
