<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Minhducck\KeyValueDataStorage\Models\TableConstant;

return new class () extends Migration {
    public const TABLE_NAME = TableConstant::CHANGE_LOG_TABLE_NAME;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            $table->string(TableConstant::TABLE_FIELD_KEY, 250)
                ->index(TableConstant::IDX_KEY_NAME);

            $table->longText(TableConstant::TABLE_FIELD_VALUE)
                ->nullable(true)
                ->default(null);

            $table->timestamp(TableConstant::TABLE_FIELD_TIMESTAMP)
                ->nullable(false)
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->comment('Created timestamp');

            $table->json(TableConstant::TABLE_FIELD_METADATA);

            $table->index(
                [
                    TableConstant::TABLE_FIELD_KEY,
                    TableConstant::TABLE_FIELD_TIMESTAMP,
                ],
                TableConstant::IDX_KEY_TIMESTAMP_NAME
            );
        });

        $this->createTrigger();
    }

    protected function createTrigger(): void
    {
        $mainTable = TableConstant::TABLE_NAME;
        $keyField = TableConstant::TABLE_FIELD_KEY;
        $valField = TableConstant::TABLE_FIELD_VALUE;
        $timestampField = TableConstant::TABLE_FIELD_TIMESTAMP;
        $metaField = TableConstant::TABLE_FIELD_METADATA;
        $triggerNameOnCreate = TableConstant::TRG_ON_CREATE;
        $triggerNameOnUpdate = TableConstant::TRG_ON_UPDATE;
        $changelogTableName = TableConstant::CHANGE_LOG_TABLE_NAME;

        /** Create trigger */
        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS {$triggerNameOnCreate};
        CREATE TRIGGER {$triggerNameOnCreate} AFTER INSERT ON `{$mainTable}` FOR EACH ROW
            BEGIN
                INSERT IGNORE INTO `{$changelogTableName}` (`{$keyField}`, `{$valField}`, `{$timestampField}`, `{$metaField}`) SELECT * FROM `$mainTable` WHERE `{$keyField}`= NEW.`{$keyField}`;
            END
        SQL);

        /** Update trigger */
        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS {$triggerNameOnUpdate};
        CREATE TRIGGER {$triggerNameOnUpdate} AFTER UPDATE ON `{$mainTable}` FOR EACH ROW
            BEGIN
                INSERT IGNORE INTO `{$changelogTableName}` (`{$keyField}`, `{$valField}`, `{$timestampField}`, `{$metaField}`) SELECT * FROM `$mainTable` WHERE `{$keyField}`= NEW.`{$keyField}`;
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $trgOnCreate = TableConstant::TRG_ON_CREATE;
        $trgOnUpdate = TableConstant::TRG_ON_UPDATE;

        DB::unprepared("DROP TRIGGER IF EXISTS ${$trgOnCreate};");
        DB::unprepared("DROP TRIGGER IF EXISTS ${$trgOnUpdate};");

        Schema::dropIfExists(self::TABLE_NAME);
    }
};
