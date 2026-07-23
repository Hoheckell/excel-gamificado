<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipe_missao_user', function (Blueprint $table) {
            $table->string('competencia_formulas')->nullable()->after('pontuacao_obtida');
            $table->string('competencia_qualidade')->nullable()->after('competencia_formulas');
            $table->string('competencia_visual')->nullable()->after('competencia_qualidade');
            $table->string('competencia_colaboracao')->nullable()->after('competencia_visual');
            $table->text('feedback_professor')->nullable()->after('competencia_colaboracao');
            $table->text('proximo_passo')->nullable()->after('feedback_professor');
        });
    }

    public function down(): void
    {
        Schema::table('equipe_missao_user', function (Blueprint $table) {
            $table->dropColumn([
                'competencia_formulas',
                'competencia_qualidade',
                'competencia_visual',
                'competencia_colaboracao',
                'feedback_professor',
                'proximo_passo',
            ]);
        });
    }
};
