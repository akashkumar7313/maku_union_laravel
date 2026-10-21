<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->decimal('fee_amount', 10, 2)->default(0)->after('amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('fee_amount');
            $table->decimal('total_amount', 10, 2)->default(0)->after('tax_amount');
        });

        DB::table('membership_payments')->update(['total_amount' => DB::raw('amount')]);
    }

    public function down(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'tax_amount', 'total_amount']);
        });
    }
};
