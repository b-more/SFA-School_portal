<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fee_categories')) {
            Schema::create('fee_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code', 40)->unique();
                $table->string('name');
                $table->enum('frequency', ['term', 'monthly', 'annual', 'one_off']);
                $table->decimal('default_amount', 10, 2)->default(0);
                $table->string('narration_template')->default('{category} ({period})');
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        $now = now();
        $seeds = [
            ['code' => 'tuition',          'name' => 'Tuition',          'frequency' => 'term',    'default_amount' => 0,   'sort_order' => 10],
            ['code' => 'bus',              'name' => 'Bus Fee',          'frequency' => 'monthly', 'default_amount' => 500, 'sort_order' => 20],
            ['code' => 'pta',              'name' => 'PTA Fee',          'frequency' => 'annual',  'default_amount' => 100, 'sort_order' => 30],
            ['code' => 'computer',         'name' => 'Computer Fee',     'frequency' => 'annual',  'default_amount' => 100, 'sort_order' => 40],
            ['code' => 'maintenance',      'name' => 'Maintenance Fee',  'frequency' => 'annual',  'default_amount' => 100, 'sort_order' => 50],
            ['code' => 'uniform',          'name' => 'Uniform',          'frequency' => 'one_off', 'default_amount' => 0,   'sort_order' => 60],
            ['code' => 'educational_tour', 'name' => 'Educational Tour', 'frequency' => 'one_off', 'default_amount' => 0,   'sort_order' => 70],
        ];
        foreach ($seeds as $s) {
            DB::table('fee_categories')->updateOrInsert(
                ['code' => $s['code']],
                array_merge($s, [
                    'narration_template' => '{category} ({period})',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_categories');
    }
};
