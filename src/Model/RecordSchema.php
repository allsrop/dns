<?php

namespace RDE\DNS\Model;

use LazyRecord\Schema;

class RecordSchema extends Schema
{
    public function schema()
    {
        $this->column('domain')->integer()->required()->refer('\RDE\DNS\Model\DomainSchema')->cononicalizer('\RDE\DNS\Model\Domain');
        $this->column('source')->varchar(128)->required();
        $this->column('type')->varchar(16)->required()->validValues(array('A', 'CNAME'));
        $this->column('dest')->text()->required();
        $this->belongsTo('domain_name', '\RDE\DNS\Model\DomainSchema', 'id', 'domain');
        $this->mixin('\RDE\DNS\Mixin\RecordMixin');
    }
}
