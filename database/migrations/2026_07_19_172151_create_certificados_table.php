<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->foreignId('equipe_id')->nullable()->constrained('equipes')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('nome_aluno');
            $table->string('cpf_aluno', 14);
            $table->string('nome_equipe')->nullable();
            $table->string('nome_categoria')->nullable();
            $table->string('titulo_certificado')->nullable();
            $table->string('cpf_professor', 14)->nullable();
            $table->string('nome_professor')->nullable();
            $table->date('dt_inicio');
            $table->date('dt_fim');
            $table->date('dt_ultima_aula');
            $table->string('codigo_validacao', 32)->unique();
            $table->timestamp('emitido_em')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
