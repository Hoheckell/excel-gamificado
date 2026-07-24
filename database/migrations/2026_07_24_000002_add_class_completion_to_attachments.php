<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->timestamp('concluida_em')->nullable()->after('dt_fim');
        });

        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->timestamp('anexo_removido_em')->nullable()->after('anexo_nome_original');
        });

        Schema::table('missao_anexos', function (Blueprint $table) {
            $table->timestamp('removido_em')->nullable()->after('tamanho');
        });
    }

    public function down(): void
    {
        Schema::table('missao_anexos', function (Blueprint $table) {
            $table->dropColumn('removido_em');
        });

        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->dropColumn('anexo_removido_em');
        });

        Schema::table('turmas', function (Blueprint $table) {
            $table->dropColumn('concluida_em');
        });
    }
};
