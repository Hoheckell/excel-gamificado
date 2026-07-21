<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missoes', function (Blueprint $table) {
            $table->string('titulo')->default('Missão Prática')->after('id');
            $table->unsignedInteger('ordem')->default(1)->after('titulo');
        });

        Schema::table('equipe_missao_user', function (Blueprint $table) {
            $table->string('papel')->nullable()->after('user_id');
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('icone');
            $table->text('descricao');
            $table->unsignedInteger('pontos_bonus')->default(15);
            $table->timestamps();
        });

        Schema::create('equipe_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_id')->constrained('equipes')->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained('badges')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['equipe_id', 'badge_id']);
        });

        DB::table('badges')->insert([
            ['nome' => 'Zero Mouse', 'icone' => '⌨️', 'descricao' => 'Dominou os atalhos de teclado.', 'pontos_bonus' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Código Limpo', 'icone' => '🧹', 'descricao' => 'Criou fórmulas claras e sem erros.', 'pontos_bonus' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Visual Executivo', 'icone' => '🎨', 'descricao' => 'Entregou uma planilha com acabamento profissional.', 'pontos_bonus' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Salva-Vidas', 'icone' => '🤝', 'descricao' => 'Ajudou outra equipe sem assumir o computador.', 'pontos_bonus' => 15, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('equipe_badge');
        Schema::dropIfExists('badges');

        Schema::table('equipe_missao_user', function (Blueprint $table) {
            $table->dropColumn('papel');
        });

        Schema::table('missoes', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'ordem']);
        });
    }
};
