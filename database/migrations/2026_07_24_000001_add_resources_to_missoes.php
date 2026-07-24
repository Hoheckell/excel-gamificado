<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missoes', function (Blueprint $table) {
            $table->string('url', 2048)->nullable()->after('descricao');
        });

        Schema::create('missao_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('missao_id')->constrained('missoes')->cascadeOnDelete();
            $table->string('path');
            $table->string('nome_original');
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('tamanho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missao_anexos');

        Schema::table('missoes', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
};
