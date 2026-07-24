<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->text('feedback_reenvio')->nullable()->after('anexo_removido_em');
            $table->timestamp('reenvio_solicitado_em')->nullable()->after('feedback_reenvio');
            $table->timestamp('reenvio_entregue_em')->nullable()->after('reenvio_solicitado_em');
        });
    }

    public function down(): void
    {
        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->dropColumn([
                'feedback_reenvio',
                'reenvio_solicitado_em',
                'reenvio_entregue_em',
            ]);
        });
    }
};
