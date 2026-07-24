<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('educational_emails_consent')->default(false)->after('autorizado');
            $table->timestamp('educational_emails_consented_at')->nullable()->after('educational_emails_consent');
            $table->timestamp('educational_emails_consent_revoked_at')->nullable()->after('educational_emails_consented_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'educational_emails_consent',
                'educational_emails_consented_at',
                'educational_emails_consent_revoked_at',
            ]);
        });
    }
};
