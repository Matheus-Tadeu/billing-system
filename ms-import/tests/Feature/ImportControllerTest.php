<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testImportCsv()
    {
        $csvData = "name,governmentId,email,debtAmount,debtDueDate,debtId\n"
            . "John Doe,11111111111,johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f\n"
            . "Jane Smith,22222222222,janesmith@kanastra.com.br,500000.00,2023-01-01,3b8fc8cc-ff16-467f-bea7-5f05d494280f\n"
            . "Charles Aguirre,15071478901,charles@kanastra.com.br,4640.00,2023-04-01,42f374d0-3491-498c-84c7-44038b45fab8\n"
            . "Alice Brown,33333333333,alicebrown@kanastra.com.br,1200000.00,2024-06-15,3c48d74d-98f5-4a56-b9a2-cba981f12d2c\n"
            . "Bob Johnson,44444444444,bobjohnson@kanastra.com.br,750000.00,2023-12-01,4a15b9e2-238f-498c-8bb7-e5857db07f3f\n";

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('import.csv', $csvData);

        $response = $this->post('/api/import', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'file_id' => true,
                    'success_count' => 5,
                    'error_count' => 0,
                    'errors' => [],
                ],
            ]);
    }
}
