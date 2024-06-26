<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Models;

class KeyValueChangelog extends KeyValue
{
    public $timestamps = false;
    protected $table = TableConstant::CHANGE_LOG_TABLE_NAME;
}
