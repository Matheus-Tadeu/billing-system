<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->table('files', function (Blueprint $collection) {
            $collection->index('path');
            $collection->index('status');;
            $collection->index('created_at');
            $collection->index('updated_at');
            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->table('files', function (Blueprint $collection) {
            $collection->dropIndex(['path']);
            $collection->dropIndex(['status']);
            $collection->dropIndex(['created_at']);
            $collection->dropIndex(['updated_at']);
            $collection->dropTimestamps();
        });
    }
};
