<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Minhducck\KeyValueDataStorage\Models\TableConstant;

return new class () extends Migration {
    public const TABLE_NAME = TableConstant::TABLE_NAME;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string(TableConstant::TABLE_FIELD_KEY, 250)
                ->unique(TableConstant::IDX_KEY_NAME_UNIQUE)
                ->primary()
                ->comment('Key');

            $table->longText(TableConstant::TABLE_FIELD_VALUE)
                ->nullable(true)
                ->default(null)
                ->comment('Value');

            $table->timestamp(TableConstant::TABLE_FIELD_TIMESTAMP)
                ->nullable(false)
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->comment('Created timestamp');

            $table->json(TableConstant::TABLE_FIELD_METADATA)
                ->comment('Store value metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
