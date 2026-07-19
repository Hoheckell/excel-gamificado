<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('pt_classificacao');
            $table->string('titulo_certificado')->nullable()->after('descricao');
            $table->string('cor', 7)->default('#107c41')->after('titulo_certificado');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'titulo_certificado', 'cor']);
        });
    }
};
