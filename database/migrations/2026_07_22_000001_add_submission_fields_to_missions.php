<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missoes', function (Blueprint $table) {
            $table->boolean('permite_resposta')->default(false)->after('pontuacao');
            $table->boolean('permite_anexo')->default(false)->after('permite_resposta');
        });

        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->text('resposta')->nullable()->after('missao_id');
            $table->string('anexo_path')->nullable()->after('resposta');
            $table->string('anexo_nome_original')->nullable()->after('anexo_path');
        });
    }

    public function down(): void
    {
        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->dropColumn(['resposta', 'anexo_path', 'anexo_nome_original']);
        });

        Schema::table('missoes', function (Blueprint $table) {
            $table->dropColumn(['permite_resposta', 'permite_anexo']);
        });
    }
};
