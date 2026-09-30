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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();

            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requester_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('assigned_department_id')->constrained('departments')->restrictOnDelete();

            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('priority_id')->constrained()->restrictOnDelete();
            $table->foreignId('status_id')->constrained('ticket_statuses')->restrictOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->text('description');
            $table->string('source')->default('web');

            $table->foreignId('sla_policy_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('sla_response_due_at')->nullable();
            $table->timestamp('sla_resolution_due_at')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['assigned_department_id', 'status_id']);
            $table->index(['requester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
