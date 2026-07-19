<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turmas_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Evita que o mesmo usuário seja matriculado mais de uma vez na mesma turma
            $table->unique(['turma_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turmas_users');
    }
};