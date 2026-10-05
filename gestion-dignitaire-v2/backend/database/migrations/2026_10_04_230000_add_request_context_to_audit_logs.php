<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->uuid('request_id')->nullable()->after('id')->index();
            $table->string('http_method', 10)->nullable()->after('new_values');
            $table->string('request_path', 500)->nullable()->after('http_method');
            $table->unsignedSmallInteger('response_status')->nullable()->after('request_path');
            $table->string('ip_address', 45)->nullable()->after('response_status');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['request_id']);
            $table->dropColumn([
                'request_id',
                'http_method',
                'request_path',
                'response_status',
                'ip_address',
                'user_agent',
            ]);
        });
    }
};
