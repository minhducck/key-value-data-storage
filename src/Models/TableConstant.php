<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Models;

use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;

class TableConstant
{
    public const TABLE_NAME = 'key_value_storage';
    public const CHANGE_LOG_TABLE_NAME = 'key_value_storage_changelogs';

    public const TABLE_FIELD_KEY = KeyValueDataObjectInterface::KEY;
    public const TABLE_FIELD_VALUE = KeyValueDataObjectInterface::VALUE;
    public const TABLE_FIELD_TIMESTAMP = KeyValueDataObjectInterface::TIMESTAMP;
    public const TABLE_FIELD_METADATA = KeyValueDataObjectInterface::METADATA;

    public const IDX_KEY_NAME = 'KEY_VALUE_STORAGE_IDX_KEY';
    public const IDX_KEY_TIMESTAMP_NAME = 'KEY_VALUE_STORAGE_IDX_KEY_TIMESTAMP';
    public const IDX_KEY_NAME_UNIQUE = 'KEY_VALUE_STORAGE_IDX_KEY_UNIQ';

    public const TRG_ON_CREATE = 'TRG_KEY_VALUE_CREATE_ENTRY';
    public const TRG_ON_UPDATE = 'TRG_KEY_VALUE_UPDATE_ENTRY';

}
