<?php

namespace RDE\DNS\Controller;

use RDE\DNS\Model\DomainCollection;
use RDE\DNS\Model\RecordCollection;
use RDE\DNS\Model\Domain;
use RDE\DNS\Model\Record;
use Fruit\Tool\Input;
use Exception;

class DomainController extends \Fruit\AbstractController
{
    public function listAction()
    {
        $data = array();

        $domains = new DomainCollection;
        foreach ($domains as $d) {
            array_push($data, array('id' => $d->id, 'domain' => $d->domain, 'records' => $this->getRecords($d)));
        }

        return json_encode($data);
    }

    public function deleteAction()
    {
        $params = Input::allInOne();
        $id = $params['id'];
        $domain = new Domain($id);
        $ret = array(
            'result' => false,
            'message' => 'params=' . var_export($params, true),
        );

        // delete all records in this domain
        Record::delete()->where()->equal('domain', $id)->execute();
        $domain->delete();
        $ret['result'] = true;
        CLIHelper::update();
        return json_encode($ret);
    }

    public function createAction()
    {
        $ret = array('result' => false);
        $input = Input::allInOne();
        try {
            $domain = Domain::create(array('domain' => $input['domain']));
            if ($domain instanceof Domain and $domain->id) {
                $ret['result'] = true;
                $ret['data'] = array('id' => $domain->id, 'domain' => $domain->domain, 'records' => $this->getRecords($domain));
            }
        } catch (Exception $e) {
            $ret['message'] = $e->getMessage();
        }
        CLIHelper::update();
        return json_encode($ret);
    }

    private function getRecords(\RDE\DNS\Model\Domain $domain)
    {
        $records = new RecordCollection;
        $records->where()->equal('domain', $domain->id);
        $ret = array();
        foreach ($records as $r) {
            array_push(
                $ret,
                array(
                    'id' => $r->id,
                    'source' => $r->getName(),
                    'type' => $r->type,
                    'dest' => $r->dest
                )
            );
        }
        return $ret;
    }
}
