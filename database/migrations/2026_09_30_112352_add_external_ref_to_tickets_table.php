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
            // Id del registro de origen en el sistema externo (p. ej. el chatbot
            // de sgiDasavena), para poder reintentar la llamada sin duplicar el ticket.
            $table->string('external_ref')->nullable()->after('source');
            $table->unique(['source', 'external_ref']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique(['source', 'external_ref']);
            $table->dropColumn('external_ref');
        });
    }
};
