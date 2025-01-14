<?php

namespace App\Jobs\Tenant;

use App\Imports\TenantsImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TenantImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var array
     */
    private $rowsData;

    /**
     * @var array
     */
    private $chunkData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $userId, array $rowsData, $chunkData)
    {
        $this->rowsData = $rowsData;
        $this->userId = $userId;
        $this->chunkData = $chunkData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tenants = new TenantsImport($this->userId, $this->rowsData);
        $tenants->importData($this->chunkData);
    }
}
