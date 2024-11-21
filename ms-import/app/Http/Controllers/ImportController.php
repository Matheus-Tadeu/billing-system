<?php

namespace App\Http\Controllers;

use App\Core\Domain\Import\Services\ImportService;
use App\Http\Requests\ImportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    private ImportService $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function __invoke(ImportRequest $request): JsonResponse
    {
        $traceId = $request->header('X-Trace-ID');
        $file = $request->file('file');

        Log::info('Received import request', [
            'trace_id' => $traceId,
            'filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);

        try {
            $result = $this->importService->process($file);

            return response()->json([
                ['success' => true ,'data' => $result],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Import failed', [
                'trace_id' => $traceId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to process the file.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
