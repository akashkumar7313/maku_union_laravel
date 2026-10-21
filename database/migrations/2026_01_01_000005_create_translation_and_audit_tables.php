<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 8)->default('hi');
            $table->string('key');
            $table->text('value');
            $table->string('source', 20)->default('manual');
            $table->boolean('reviewed')->default(false)->index();
            $table->timestamps();
            $table->unique(['locale', 'key']);
        });

        Schema::create('translation_usage', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30)->default('google');
            $table->string('month', 7)->index();
            $table->unsignedBigInteger('characters')->default(0);
            $table->unsignedInteger('calls')->default(0);
            $table->timestamps();
            $table->unique(['provider', 'month']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60)->index();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('translation_usage');
        Schema::dropIfExists('translations');
    }
};
