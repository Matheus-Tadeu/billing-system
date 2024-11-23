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
        Schema::connection('mongodb')->table('records', function (Blueprint $collection) {
            $collection->index('file_id');
            $collection->index('status');
            $collection->index('governmentId');
            $collection->index('debtDueDate');
            $collection->index('debtID');
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
        Schema::connection('mongodb')->table('records', function (Blueprint $collection) {
            $collection->dropIndex(['file_id']);
            $collection->dropIndex(['status']);
            $collection->dropIndex(['governmentId']);
            $collection->dropIndex(['debtDueDate']);
            $collection->dropIndex(['debtID']);
            $collection->dropIndex(['created_at']);
            $collection->dropIndex(['updated_at']);
            $collection->dropTimestamps();
        });
    }
};
