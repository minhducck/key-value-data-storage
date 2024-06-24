<?php
declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Models;

use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;

class TableConstant
{
    const TABLE_NAME = 'key_value_storage';
    const CHANGE_LOG_TABLE_NAME = 'key_value_storage_changelogs';

    const TABLE_FIELD_KEY = KeyValueDataObjectInterface::KEY;
    const TABLE_FIELD_VALUE = KeyValueDataObjectInterface::VALUE;
    const TABLE_FIELD_TIMESTAMP = KeyValueDataObjectInterface::TIMESTAMP;
    const TABLE_FIELD_METADATA = KeyValueDataObjectInterface::METADATA;

    const IDX_KEY_NAME = 'KEY_VALUE_STORAGE_IDX_KEY';
    const IDX_KEY_TIMESTAMP_NAME = 'KEY_VALUE_STORAGE_IDX_KEY_TIMESTAMP';
    const IDX_KEY_NAME_UNIQUE = 'KEY_VALUE_STORAGE_IDX_KEY_UNIQ';

    const TRG_ON_CREATE = 'TRG_KEY_VALUE_CREATE_ENTRY';
    const TRG_ON_UPDATE = 'TRG_KEY_VALUE_UPDATE_ENTRY';

}
