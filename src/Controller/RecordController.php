<?php

namespace RDE\DNS\Controller;

use RDE\DNS\Model\DomainCollection;
use RDE\DNS\Model\RecordCollection;
use RDE\DNS\Model\Domain;
use RDE\DNS\Model\Record;
use Fruit\Tool\Input;
use Fruit\AbstractController;
use Exception;

class RecordController extends AbstractController
{
    public function createAction()
    {
        $ret = array('result' => false);
        $input = Input::allInOne();
        $domain = new Domain($input['domain']);
        if (
            $domain instanceof Domain and
            $domain->id and
            isset($input['source']) and
            isset($input['type']) and
            isset($input['dest'])
        ) {
            $data = array(
                'domain' => $domain->id,
                'source' => $input['source'],
                'type' => $input['type'],
                'dest' => $input['dest']
            );
            $record = Record::create($data);
            if ($record->id) {
                $domain->update(array('ver' => $domain->ver+1));
                $ret['result'] = true;
                $ret['data'] = array(
                    'id' => $record->id,
                    'source' => $record->getName(),
                    'type' => $record->type,
                    'dest' => $record->dest
                );
            }
        }
        CLIHelper::update();

        return json_encode($ret);
    }

    public function deleteAction()
    {
        $ret = array('result' => false);
        $input = Input::allInOne();
        $record = new Record($input['id']);
        if ($record->id) {
            $record->domain->update(array('ver' => $record->domain->ver + 1));
            $record->delete();
            $ret['result'] = true;
        }
        CLIHelper::update();
        return json_encode($ret);
    }
}
