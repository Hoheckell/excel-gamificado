<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipes_missoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_id')->constrained('equipes')->onDelete('cascade');
            $table->foreignId('missao_id')->constrained('missoes')->onDelete('cascade');
            
            // Opcional: Se quiser registrar o status da missão (ex: 'em_andamento', 'concluida')
            // $table->string('status')->default('concluida');
            
            $table->timestamps();

            // Garante que uma equipe não receba a mesma missão duplicada no banco
            $table->unique(['equipe_id', 'missao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipes_missoes');
    }
};