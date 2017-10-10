<?php declare(strict_types=1);

namespace Shopware\Framework\Writer\Resource;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\MultiEditBackupWrittenEvent;
use Shopware\Framework\Write\Field\DateField;
use Shopware\Framework\Write\Field\IntField;
use Shopware\Framework\Write\Field\LongTextField;
use Shopware\Framework\Write\Field\StringField;
use Shopware\Framework\Write\Flag\Required;
use Shopware\Framework\Write\WriteResource;

class MultiEditBackupWriteResource extends WriteResource
{
    protected const FILTER_STRING_FIELD = 'filterString';
    protected const OPERATION_STRING_FIELD = 'operationString';
    protected const ITEMS_FIELD = 'items';
    protected const DATE_FIELD = 'date';
    protected const SIZE_FIELD = 'size';
    protected const PATH_FIELD = 'path';
    protected const HASH_FIELD = 'hash';

    public function __construct()
    {
        parent::__construct('s_multi_edit_backup');

        $this->fields[self::FILTER_STRING_FIELD] = (new LongTextField('filter_string'))->setFlags(new Required());
        $this->fields[self::OPERATION_STRING_FIELD] = (new LongTextField('operation_string'))->setFlags(new Required());
        $this->fields[self::ITEMS_FIELD] = (new IntField('items'))->setFlags(new Required());
        $this->fields[self::DATE_FIELD] = new DateField('date');
        $this->fields[self::SIZE_FIELD] = (new IntField('size'))->setFlags(new Required());
        $this->fields[self::PATH_FIELD] = (new StringField('path'))->setFlags(new Required());
        $this->fields[self::HASH_FIELD] = (new StringField('hash'))->setFlags(new Required());
    }

    public function getWriteOrder(): array
    {
        return [
            self::class,
        ];
    }

    public static function createWrittenEvent(array $updates, TranslationContext $context, array $rawData = [], array $errors = []): MultiEditBackupWrittenEvent
    {
        $event = new MultiEditBackupWrittenEvent($updates[self::class] ?? [], $context, $rawData, $errors);

        unset($updates[self::class]);

        if (!empty($updates[self::class])) {
            $event->addEvent(self::createWrittenEvent($updates, $context));
        }

        return $event;
    }
}