<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InegiCatalogoService
{
    private readonly string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.inegi.base_url'), '/');
    }

    /**
     * Catálogo de entidades federativas.
     */
    public function estados(): Collection
    {
        return $this->fetch('/mgee/');
    }

    /**
     * Municipios de una entidad federativa.
     */
    public function municipios(string $cveEnt): Collection
    {
        return $this->fetch("/mgem/{$cveEnt}");
    }

    private function fetch(string $path): Collection
    {
        $response = Http::timeout(15)
            ->retry(3, 500, throw: false)
            ->get($this->baseUrl.$path);

        if ($response->failed()) {
            Log::error('Fallo al consultar el servicio del INEGI', [
                'url' => $this->baseUrl.$path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $response->throw();
        }

        return collect($response->json('datos', []));
    }
}
