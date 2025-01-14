<?php

namespace App\Imports;

use App\Http\Requests\Api\Tenant\TenantImportRulesRequest;
use App\Models\Tenant;

class TenantsImport
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var array
     */
    private $rowsData;

    /**
     * @var TenantImportRulesRequest
     */
    private $rules;

    /**
     * @param int $userId
     * @param array $rowsData
     */
    public function __construct(int $userId, array $rowsData)
    {
        $this->userId = $userId;
        $this->rowsData = $rowsData;

        $this->rules = new TenantImportRulesRequest();
    }

    /**
     * Creates or updates users
     *
     * @param  array  $chunkedData
     * @return Tenant|null
     */
    public function importData(array $chunkedData): ?Tenant
    {
        foreach ($chunkedData as $el) {
            $data = [
                'first_name' => $this->rowsData[$el][0],
                'last_name' => $this->rowsData[$el][1],
                'email' => strtolower(trim($this->rowsData[$el][2])),
                'address' => $this->rowsData[$el][3],
                'city' => $this->rowsData[$el][4],
                'post_code' => $this->rowsData[$el][5],
                'country' => $this->rowsData[$el][6],
            ];

            $user = Tenant::where('email', $data['email'])
                ->where('user_id', $user->id)
                ->first();
            if ($user) {
                $user->update($data);
                continue;
            }

            $data['user_id'] = $this->userId;

            $user = Tenant::create($data);
        }

        return null;
    }
}
