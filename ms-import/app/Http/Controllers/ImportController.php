<?php

namespace App\Http\Controllers;

use App\Core\Domain\Import\Services\ImportService;
use App\Http\Requests\ImportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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
 *                     example="6740156e39e019e0bf08f3a2"
 *                 ),
 *                 @OA\Property(
 *                     property="success_count",
 *                     type="integer",
 *                     example=1100000
 *                 ),
 *                 @OA\Property(
 *                     property="error_count",
 *                     type="integer",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="errors",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(
 *                             property="record",
 *                             type="object",
 *                             @OA\Property(property="fileId", type="string", example="6740096f4e3cf3ac16044e5a"),
 *                             @OA\Property(property="name", type="string", example="Charles Aguirre"),
 *                             @OA\Property(property="governmentId", type="string", example="1507"),
 *                             @OA\Property(property="email", type="string", example="westjeremyexample.com"),
 *                             @OA\Property(property="debtAmount", type="string", example="4640"),
 *                             @OA\Property(property="debtDueDate", type="string", example="2023-04-01"),
 *                             @OA\Property(property="debtID", type="string", example="42f374d0-3491-498c-84c7-44038b45fab8"),
 *                             @OA\Property(property="status", type="string", example="processing")
 *                         ),
 *                         @OA\Property(
 *                             property="errors",
 *                             type="array",
 *                             @OA\Items(type="string", example="Invalid email address.")
 *                         )
 *                     )
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
        try {
            $file = $request->file('file');
            $result = $this->importService->processFile($file);
            return response()->json(['success' => true ,'data' => $result]);

        } catch (\Throwable $e) {
            Log::error('Import failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

//            Notification::route('slack', env('SLACK_WEBHOOK_URL'))
//                ->notify(new ImportFailedSlackNotification($e->getMessage()));

            return response()->json([
                'success' => false,
                'error' => 'Failed to process the file.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
