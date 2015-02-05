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
    public function dumpAction()
    {
        $this->plugin('lazy')->init();
        $this->getRouter()->disableOutputInterpolate();
        $config = $this->getSingleton()->getConfig();
        $records = new RecordCollection;
        $ret = array();
        header('Content-Type: text/plain; charset=utf8');
        header('Content-Disposition: attachment; filename="hosts.txt"');
        foreach ($records as $r) {
            $name = $r->getName();
            $ns = $config->get('dns', 'ns');
            $cmd = sprintf("/usr/bin/dig %s @%s|grep -P 'IN\\s+A\\s+([0-9]+\\.){3}[0-9]+'|grep --color=never -oP '([0-9]+\\.){3}[0-9]+'", $name, $ns);
            $output = explode("\n", shell_exec($cmd));
            $output = $output[0];
            if (preg_match('/([0-9]+\.){3}[0-9]+/', $output) == 1) {
                array_push($ret, sprintf('%s %s', $output, $name));
            }
        }
        return implode("\n", $ret);
    }

    public function createAction()
    {
        $this->plugin('lazy')->init();
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

        return $ret;
    }

    public function deleteAction()
    {
        $this->plugin('lazy')->init();
        $ret = array('result' => false);
        $input = Input::allInOne();
        $record = new Record($input['id']);
        if ($record->id) {
            $record->domain->update(array('ver' => $record->domain->ver + 1));
            $record->delete();
            $ret['result'] = true;
        }
        CLIHelper::update();
        return $ret;
    }
}
