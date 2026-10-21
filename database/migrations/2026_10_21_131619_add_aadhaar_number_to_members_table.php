<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->text('aadhaar_number')->nullable()->after('aadhaar_last4');
            $table->string('aadhaar_hash', 64)->nullable()->index()->after('aadhaar_number');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['aadhaar_number', 'aadhaar_hash']);
        });
    }
};
