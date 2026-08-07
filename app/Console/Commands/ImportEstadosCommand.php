<?php

namespace App\Console\Commands;

use App\Jobs\ImportEstadosJob;
use App\Models\Estado;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Throwable;

#[Signature('estados:import')]
#[Description('Sincroniza el catálogo de entidades federativas del INEGI (idempotente)')]
class ImportEstadosCommand extends Command
{
    public function handle(): int
    {
        $this->info('Consultando el catálogo de entidades federativas del INEGI...');

        try {
            ImportEstadosJob::dispatchSync();
        } catch (RequestException|Throwable $exception) {
            $this->error("No fue posible sincronizar el catálogo: {$exception->getMessage()}");

            return self::FAILURE;
        }

        $this->info(sprintf('Se sincronizaron %d estados sin generar duplicados.', Estado::count()));

        return self::SUCCESS;
    }
}
