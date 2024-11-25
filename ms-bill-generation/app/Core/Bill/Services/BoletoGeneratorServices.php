<?php

namespace App\Core\Bill\Services;

use Illuminate\Support\Facades\Log;

class BoletoGeneratorServices
{
    /**
     * @param array $data
     * @return void
     */
    public function generate(array $data): void
    {
        Log::info('Boleto gerado com sucesso', ['data' => $data]);
    }
}
