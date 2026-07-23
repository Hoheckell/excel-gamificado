<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipe_missao_user_papeis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_missao_user_id')
                ->constrained('equipe_missao_user')
                ->cascadeOnDelete();
            $table->string('papel');
            $table->timestamps();
            $table->unique(['equipe_missao_user_id', 'papel'], 'emu_papeis_unique');
            $table->index('equipe_missao_user_id', 'emu_papeis_emu_id_index');
        });

        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->unsignedTinyInteger('tempo_extra_minutos')->default(0);
        });

        DB::table('equipe_missao_user')
            ->whereNotNull('papel')
            ->orderBy('id')
            ->each(function ($progresso): void {
                DB::table('equipe_missao_user_papeis')->insert([
                    'equipe_missao_user_id' => $progresso->id,
                    'papel' => $progresso->papel,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('equipe_missao_user', function (Blueprint $table) {
            $table->dropColumn('papel');
        });
    }

    public function down(): void
    {
        Schema::table('equipe_missao_user', function (Blueprint $table) {
            $table->string('papel')->nullable()->after('user_id');
        });

        DB::table('equipe_missao_user_papeis')
            ->orderBy('id')
            ->get()
            ->groupBy('equipe_missao_user_id')
            ->each(function ($papeis, $progressoId): void {
                DB::table('equipe_missao_user')
                    ->where('id', $progressoId)
                    ->update(['papel' => $papeis->first()->papel]);
            });

        Schema::dropIfExists('equipe_missao_user_papeis');

        Schema::table('equipes_missoes', function (Blueprint $table) {
            $table->dropColumn('tempo_extra_minutos');
        });
    }
};
