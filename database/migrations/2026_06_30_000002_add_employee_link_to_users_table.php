<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->string('phone', 20)->nullable()->after('employee_id');
        });

        DB::table('users')->where('role', 'security')->update(['role' => 'employee']);

        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
            $table->dropColumn('phone');
        });
    }
};
