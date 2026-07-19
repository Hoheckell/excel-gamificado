<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipe_missao_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_id')->constrained('equipes')->onDelete('cascade');
            $table->foreignId('missao_id')->constrained('missoes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pendente');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('pontuacao_obtida')->nullable();
            $table->timestamps();

            $table->unique(['equipe_id', 'missao_id', 'user_id'], 'equipe_missao_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipe_missao_user');
    }
};
