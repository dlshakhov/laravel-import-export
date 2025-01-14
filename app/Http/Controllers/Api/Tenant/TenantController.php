<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Exports\TenantsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tenant\TenantImportRequest;
use App\Http\Resources\Api\Tenant\TenantResource;
use App\Http\Services\Api\Tenant\TenantImportService;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TenantController extends Controller
{
    public function __construct(
        private TenantImportService $importService
    ) {}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $users = Tenant::where('user_id', $request->user()->id)
        ->when(! empty($request->get('email')), function (Builder $query) use ($request) {
            $query->where('email', 'like', '%'.$request->get('email').'%');
        })->when(! empty($request->get('first_name')), function (Builder $query) use ($request) {
            $query->where('first_name', 'like', '%'.$request->get('first_name').'%');
        })->when(! empty($request->get('last_name')), function (Builder $query) use ($request) {
            $query->where('last_name', 'like', '%'.$request->get('last_name').'%');
        });

        $totalAll = $users->count();
        $users = $users->offset($request->get('offset', 0))
            ->limit($request->get('limit', 20))
            ->get();

        return response()->json([
            'tenants' => TenantResource::collection($users),
            'total_all' => $totalAll,
            'total' => $users->count(),
            'message' => __('Successfully retrieved Tenants'),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        if ($request->get('tenant_id') === null) {
            return response()->json([
                'error' => __('Tenant ID cannot be empty'),
                'code' => 422,
            ]);
        }

        $tenant = Tenant::where('user_id', $request->user()->id)
            ->where('id', $request->get('tenant_id'))
            ->first();

        if (! $tenant) {
            return response()->json([
                'error' => __('Tenant does not exists'),
                'code' => 422,
            ]);
        }

        return response()->json([
            'tenants' => (new TenantResource($tenant))->resolve(),
            'message' => __('Successfully retrieved Tenant'),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        if ($request->get('tenant_id') === null) {
            return response()->json([
                'error' => __('Tenant ID cannot be empty'),
                'code' => 422,
            ]);
        }

        $tenant = Tenant::where('user_id', $request->user()->id)
            ->where('id', $request->get('tenant_id'))
            ->first();

        if (! $tenant) {
            return response()->json([
                'error' => __('Tenant does not related to your User'),
                'code' => 422,
            ]);
        }

        $tenant->delete();
        return response()->json([
            'message' => __('Successfully deleted Tenant'),
        ]);
    }

    /**
     * @param TenantImportRequest $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Throwable
     */
    public function importTenants(TenantImportRequest $request): JsonResponse
    {
        $totalRows = Excel::toArray([], $request->file('import_file'));

        $batchId = $this->importService->importData($request->user(), $totalRows[0]);

        return response()->json([
            'progress_id' => $batchId,
        ]);
    }

    /**
     * @param TenantImportRequest $request
     * @return JsonResponse
     */
    public function importProcess(Request $request): JsonResponse
    {
        $batch = null;
        if ($request->batch_id) {
            $batch = Bus::findBatch($request->batch_id);
        }

        return response()->json([
            'batch_id' => $request->batch_id,
            'batch_progress' => $batch->progress(),
            'batch_processed' => $batch->processedJobs(),
            'total_jobs' => $batch->totalJobs,
        ]);
    }

    /**
     * @param TenantImportRequest $request
     * @return BinaryFileResponse|JsonResponse
     */
    public function exportTenants(Request $request): BinaryFileResponse|JsonResponse
    {
        try {
            return Excel::download(new TenantsExport($request->user()->id), 'tenants.xlsx');
        } catch (Exception $exception) {
            \Log::error('TenantController->export', ['message' => $exception->getMessage()]);
        }

        return response()->json([
            'error' => __('Something went wrong, please try again.')
        ]);
    }
}
