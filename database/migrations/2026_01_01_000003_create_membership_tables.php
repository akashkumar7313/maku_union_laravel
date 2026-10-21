<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('membership_no', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('guardian_name')->nullable();
            $table->string('phone', 20)->index();
            $table->string('email')->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('dob')->nullable();
            $table->text('address');
            $table->string('district', 100)->default('मिर्जापुर');
            $table->string('block', 100)->nullable();
            $table->foreignId('worker_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo')->nullable();
            $table->string('id_document')->nullable();
            $table->string('aadhaar_last4', 4)->nullable();
            $table->date('joined_on');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable()->index();
            $table->string('status', 20)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('membership_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('order_no', 40)->unique();
            $table->string('receipt_no', 40)->nullable()->unique();
            $table->string('type', 20)->default('new');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('gateway', 30)->default('sandbox');
            $table->string('gateway_order_id')->nullable()->index();
            $table->string('gateway_payment_id')->nullable();
            $table->string('utr')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('membership_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('membership_payments')->nullOnDelete();
            $table->date('valid_from');
            $table->date('valid_until');
            $table->decimal('amount', 10, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_renewals');
        Schema::dropIfExists('membership_payments');
        Schema::dropIfExists('members');
    }
};
