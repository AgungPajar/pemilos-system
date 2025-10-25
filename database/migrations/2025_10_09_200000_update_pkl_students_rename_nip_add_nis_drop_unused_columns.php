<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pkl_students', function (Blueprint $table) {
            // Add new column 'nis' and copy values from 'nip'
            $table->string('nis')->nullable()->after('name');
        });

        // Copy data from nip to nis
        DB::statement('UPDATE pkl_students SET nis = nip');

        // Make nis not nullable and add unique index. Some DB drivers may not
        // support change() without doctrine/dbal; do alter via raw SQL when needed.
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE pkl_students MODIFY nis VARCHAR(255) NOT NULL');
        } elseif (in_array($driver, ['pgsql', 'postgresql'])) {
            DB::statement('ALTER TABLE pkl_students ALTER COLUMN nis SET NOT NULL');
        }

        Schema::table('pkl_students', function (Blueprint $table) {
            $table->unique('nis');

            // drop old columns
            if (Schema::hasColumn('pkl_students', 'nip')) {
                $table->dropColumn('nip');
            }
            if (Schema::hasColumn('pkl_students', 'nisn')) {
                $table->dropColumn('nisn');
            }
            if (Schema::hasColumn('pkl_students', 'tmp_lahir')) {
                $table->dropColumn('tmp_lahir');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pkl_students', function (Blueprint $table) {
            // add back columns
            $table->string('nip')->nullable()->after('name');
            $table->string('nisn')->nullable()->after('jk');
            $table->string('tmp_lahir')->nullable()->after('nisn');
        });

        // copy back
        DB::statement('UPDATE pkl_students SET nip = nis');

        Schema::table('pkl_students', function (Blueprint $table) {
            $table->dropUnique(['nis']);
            $table->dropColumn('nis');
        });
    }
};
