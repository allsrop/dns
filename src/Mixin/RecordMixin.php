<?php

namespace RDE\DNS\Mixin;

use LazyRecord\Schema\MixinSchemaDeclare;

class RecordMixin extends MixinSchemaDeclare
{
    public function getName($record)
    {
        return $record->source . '.' . $record->domain->domain;
    }
}
