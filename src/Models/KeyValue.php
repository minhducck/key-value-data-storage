<?php
declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Minhducck\KeyValueDataStorage\Helpers\DataTypeResolver;
use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;

/**
 * @property string $metadata
 * @property mixed $value
 * @property int $timestamp
 */
class KeyValue extends Model implements KeyValueDataObjectInterface
{
    const CREATED_AT = TableConstant::TABLE_FIELD_TIMESTAMP;

    const METADATA_DATA_TYPE = 'dataType';

    protected $primaryKey = TableConstant::TABLE_FIELD_KEY;

    // To ignore converting object key to int
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = TableConstant::TABLE_NAME;

    protected $fillable = [
        TableConstant::TABLE_FIELD_KEY,
        TableConstant::TABLE_FIELD_VALUE,
        TableConstant::TABLE_FIELD_TIMESTAMP,
    ];

    public $timestamps = false;

    protected $dateFormat = 'U';

    private function _populateMetadata(): void
    {
        $metadata = $this->{self::METADATA};

        if (!is_array($this->{self::METADATA})) {
            $metadata = json_decode($this->{self::METADATA} ?? '{}', true);
        }

        if (!array_key_exists(self::METADATA_DATA_TYPE, $metadata)) {
            $metadata[self::METADATA_DATA_TYPE] = DataTypeResolver::resolve($this->getValue());
        }

        $this->{self::METADATA} = $metadata;
    }

    public function getMetadata(string $key = ''): mixed
    {
        $this->_populateMetadata();

        if ($key === '') {
            return $this->{self::METADATA};
        }

        return $this->{self::METADATA}[$key] ?? null;
    }

    public function setMetadata(string $key, mixed $value): KeyValueDataObjectInterface
    {
        $this->_populateMetadata();
        $metadata               = $this->{self::METADATA};
        $metadata[$key]         = $value;
        $this->{self::METADATA} = $metadata;
        return $this;
    }

    public function setObjectKey(string $key): KeyValueDataObjectInterface
    {
        $this->{self::KEY} = $key;
        return $this;
    }

    public function getObjectKey(): string
    {
        return $this->{self::KEY};
    }

    public function setValue(mixed $value): KeyValueDataObjectInterface
    {
        $this->{self::VALUE} = $value;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->{self::VALUE};
    }

    public function setTimestamp(int $timestamp): KeyValueDataObjectInterface
    {
        $this->{self::TIMESTAMP} = $timestamp;
        return $this;
    }

    public function getTimestamp(): int
    {
        return (int)$this->{self::TIMESTAMP};
    }

    public static function createKeyValueDataObject(string $key, mixed $value): KeyValueDataObjectInterface
    {
        $newObject = new self();
        return $newObject->setObjectKey($key)
            ->setValue($value)
            ->setTimestamp((int)Carbon::now()->timestamp);
    }

    public function getDataType(): string
    {
        return $this->getMetadata(self::METADATA_DATA_TYPE);
    }

    public function serialize(): array
    {
        return [
            self::METADATA  => json_encode($this->getMetadata()),
            self::TIMESTAMP => Carbon::createFromTimestampUTC($this->getTimestamp())->toDate(),
            self::KEY       => $this->getObjectKey(),
            self::VALUE     => DataTypeResolver::serializeValue($this->getValue()),
        ];
    }
}
