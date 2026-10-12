<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('passport_client_id')
                ->nullable()
                ->unique()
                ->after('client_id');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique([
                'applications_passport_client_id_unique'
            ]);

            $table->dropColumn('passport_client_id');
        });
    }
};