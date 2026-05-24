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
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->string('foto_documento_verso')->nullable()->after('foto_documento');
            $table->string('foto_documento_consentimento')->nullable()->after('foto_documento_verso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropColumn(['foto_documento_verso', 'foto_documento_consentimento']);
        });
    }
};
