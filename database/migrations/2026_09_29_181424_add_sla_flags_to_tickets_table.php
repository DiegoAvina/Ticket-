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
        Schema::table('tickets', function (Blueprint $table) {
            // Evitan volver a notificar el mismo aviso en cada corrida del job de SLA.
            $table->timestamp('sla_resolution_warned_at')->nullable()->after('sla_resolution_due_at');
            $table->timestamp('sla_resolution_breached_at')->nullable()->after('sla_resolution_warned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['sla_resolution_warned_at', 'sla_resolution_breached_at']);
        });
    }
};
