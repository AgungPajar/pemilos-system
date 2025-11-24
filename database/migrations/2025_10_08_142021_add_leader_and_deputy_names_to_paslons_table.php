<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AddLeaderAndDeputyNamesToPaslonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paslons', function (Blueprint $table) {
            if (! Schema::hasColumn('paslons', 'leader_name')) {
                $table->string('leader_name')->after('order_number')->nullable();
            }

            if (! Schema::hasColumn('paslons', 'deputy_name')) {
                $table->string('deputy_name')->after('leader_name')->nullable();
            }
        });

        if (Schema::hasColumn('paslons', 'leader_name') && Schema::hasColumn('paslons', 'deputy_name')) {
            DB::table('paslons')->select('id', 'name', 'leader_name', 'deputy_name')
                ->whereNull('leader_name')
                ->orderBy('id')
                ->chunk(100, function ($paslons) {
                    foreach ($paslons as $paslon) {
                        [$leader, $deputy] = $this->splitName($paslon->name);

                        DB::table('paslons')
                            ->where('id', $paslon->id)
                            ->update([
                                'leader_name' => $leader,
                                'deputy_name' => $deputy,
                            ]);
                    }
                });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paslons', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('paslons', 'leader_name')) {
                $drops[] = 'leader_name';
            }

            if (Schema::hasColumn('paslons', 'deputy_name')) {
                $drops[] = 'deputy_name';
            }

            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }

    /**
     * Attempt to split existing combined paslon name into leader & deputy.
     *
     * @param  string|null  $name
     * @return array{0: string|null, 1: string|null}
     */
    protected function splitName(?string $name): array
    {
        if (! $name) {
            return [null, null];
        }

        $parts = preg_split('/\s*(?:&|dan|,)\s*/i', $name);

        if (!empty($parts[0]) && !empty($parts[1])) {
            return [
                Str::title(trim($parts[0])),
                Str::title(trim($parts[1])),
            ];
        }

        return [Str::title(trim($name)), null];
    }
}
