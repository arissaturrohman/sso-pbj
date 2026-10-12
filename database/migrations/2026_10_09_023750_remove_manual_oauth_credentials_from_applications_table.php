<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'client_id')) {
                $table->dropUnique(['client_id']);
                $table->dropColumn('client_id');
            }

            if (Schema::hasColumn('applications', 'client_secret')) {
                $table->dropColumn('client_secret');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('client_id')->nullable()->unique()->after('description');
            $table->text('client_secret')->nullable()->after('client_id');
        });
    }
};