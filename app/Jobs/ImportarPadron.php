<?php

namespace App\Jobs;

use App\Interfaces\Services\PadronElectoral\IImportadorService;
use App\Models\Elecciones;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;

class ImportarPadron implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, Queueable, SerializesModels;

    protected string $path;
    protected Elecciones $eleccion;

    /**
     * Create a new job instance.
     */
    public function __construct(string $path, Elecciones $eleccion)
    {
        $this->path = $path;
        $this->eleccion = $eleccion;
    }

    public function uniqueId(): string
    {
        return $this->eleccion->getKey();
    }

    public function uniqueFor(): int
    {
        return 300;
    }

    /**
     * Execute the job.
     */
    public function handle(IImportadorService $importadorService): void
    {
        $importadorService->importar($this->path, $this->eleccion);

        unlink($this->path);
    }

    public function failed(\Throwable $exception): void
    {
        unlink($this->path);

        \Log::error('Falló la importación del padrón', [
            'path'        => $this->path,
            'eleccion_id' => $this->eleccion->getKey(),
            'error'       => $exception->getMessage(),
        ]);
    }
}
