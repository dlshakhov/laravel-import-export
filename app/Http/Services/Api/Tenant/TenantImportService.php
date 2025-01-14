<?php

namespace App\Http\Services\Api\Tenant;

use App\Http\Requests\Api\Tenant\TenantImportRulesRequest;
use App\Jobs\Tenant\TenantImportJob;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TenantImportService
{
    /**
     * @param User $user
     * @param array $data
     * @return string
     * @throws ValidationException
     * @throws \Throwable
     */
    public function importData(User $user, array $data): string
    {
        $validation = $this->validateData($data);

        if ($validation['fails']) {
            throw ValidationException::withMessages($validation['errors']);
        }

        $batch = Bus::batch(
            $this->generateJobs($data, $user->id)
        )->dispatch();

        return $batch->id;
    }

    /**
     * Generate jobs for batch
     *
     * @param  array  $totalRows
     * @param  int  $userId
     * @return array
     */
    private function generateJobs(array $totalRows, int $userId): array
    {
        $dataTmp = [];
        foreach (array_chunk(range(1, count($totalRows) - 1), 100) as $chunk) {
            $dataTmp[] = new TenantImportJob($userId, $totalRows, $chunk);
        }

        return $dataTmp;
    }

    /**
     * @param array $tenants
     * @return array
     */
    private function validateData(array $tenants): array
    {
        $errors = [];
        $request = new TenantImportRulesRequest();

        foreach (range(1, count($tenants) - 1) as $key) {
            $validator = Validator::make($tenants[$key], ($request)->rules(), ($request)->messages());
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $item) {
                    $errors[] = $item;
                }
            }
        }

        return empty($errors) ? ['fails' => false, 'errors' => null] : ['fails' => true, 'errors' => array_unique($errors)];
    }

}
