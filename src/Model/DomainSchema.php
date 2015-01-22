<?php

namespace RDE\DNS\Model;

use LazyRecord\Schema;

class DomainSchema extends Schema
{
    public function schema()
    {
        $this->column('domain')->unique()->varchar(128)->required();
        $this->column('ver')->integer()->required()->default(100);
    }
}
