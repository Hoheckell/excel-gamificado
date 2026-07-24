<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->text('feedback_reformulacao')->nullable()->after('reenvio_entregue_em');
            $table->timestamp('reformulacao_solicitada_em')->nullable()->after('feedback_reformulacao');
            $table->timestamp('reformulacao_entregue_em')->nullable()->after('reformulacao_solicitada_em');
        });
    }

    public function down(): void
    {
        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->dropColumn([
                'feedback_reformulacao',
                'reformulacao_solicitada_em',
                'reformulacao_entregue_em',
            ]);
        });
    }
};
