<?php

namespace RDE\DNS\Bin;

use CLIFramework\Command;
use Fruit\Config\IniConfig;
use RDE\DNS\Model\Domain;
use RDE\DNS\Model\DomainCollection;
use RDE\DNS\Model\Record;
use RDE\DNS\Model\RecordCollection;

class UpdateCommand extends Command
{
    public function brief()
    {
        return 'Update configs.';
    }

    public function execute()
    {
        $l = $this->logger;

        $config = new IniConfig(PROJ_DIR . '/config.ini');
        $db = $config->getPath('dns', 'dir');
        $ns = $config->get('dns', 'ns');

        $l->info(sprintf('Writing config to %s with default nameserver %s.', $db, $ns));

        if (! is_dir($db)) {
            mkdir($db, 0755, true);
        }

        $zone_tmpl = "zone \"%s\" {\n  type master;\n  file \"%s\";\n};\n";
        $db_tmpl = "%s. IN SOA %s. root.%s. ( %d 604800 86400 2419200 604800 )\n";
        $rec_tmpl = "%-32s IN %-16s %s\n";
        $conf = '';
        $domains = new DomainCollection;
        foreach ($domains as $domain) {
            $db_file = $db . '/' . $domain->domain . '.db';
            $conf .= sprintf($zone_tmpl, $domain->domain, $db_file);
            $db_content = sprintf(
                $db_tmpl,
                $domain->domain, //%s
                $domain->domain, //IS SOA %s.
                $domain->domain, //root.%s.
                $domain->ver     //( %d ....)
            );
            $db_content .= sprintf($rec_tmpl, '@', 'NS', 'NS.' . $domain->domain . '.');
            $db_content .= sprintf($rec_tmpl, 'NS.' . $domain->domain . '.', 'A', $ns);
            $db_content .= "\n";

            $records = new RecordCollection;
            $records->where()->equal('domain', $domain->id);
            foreach ($records as $record) {
                $db_content .= sprintf(
                    $rec_tmpl,
                    $record->source,
                    $record->type,
                    $record->dest
                );
            }
            file_put_contents($db_file, $db_content);
            $l->info($db_content);
        }
        file_put_contents($db . '/conf.rde', $conf);
        $l->info($conf);
        shell_exec('/usr/bin/sudo /usr/sbin/rndc reload');
    }
}
