<?php

namespace App\Http\Controllers;

use App\Core\Domain\Import\Services\ImportService;
use App\Http\Requests\ImportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class ImportController
 * @package App\Http\Controllers
 *
 * @OA\Post(
 *     path="/api/import-file",
 *     summary="Import a CSV file",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_id",
 *                     type="string",
 *                     example="673f4ce69ff867398807ba04"
 *                 ),
 *                 @OA\Property(
 *                     property="message",
 *                     type="string",
 *                     example="Processing in background"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Failed to process the file."
 *             ),
 *             @OA\Property(
 *                 property="details",
 *                 type="string",
 *                 example="Error details here"
 *             )
 *         )
 *     )
 * )
 */
class ImportController extends Controller
{
    /**
     * @var ImportService
     */
    private ImportService $importService;

    /**
     * @param ImportService $importService
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * @param ImportRequest $request
     * @return JsonResponse
     */
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
