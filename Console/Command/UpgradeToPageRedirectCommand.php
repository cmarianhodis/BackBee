<?php

/*
 * Copyright (c) 2011-2015 Lp digital system
 *
 * This file is part of BackBee.
 *
 * BackBee is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BackBee is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BackBee. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Marian Hodis <marian.hodis@lp-digital.fr>
 */

namespace BackBee\Console\Command;

use BackBee\BBApplication;
use BackBee\Console\AbstractCommand;
use BackBee\Exception\BBException;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Upgrade data structure for redirect field changed to type array
 *
 * @category    BackBee
 *
 * @copyright   Lp digital system
 * @author      Marian Hodis <marian.hodis@lp-digital.fr>
 */
class UpgradeToPageRedirectCommand extends AbstractCommand
{

    /**
     * Force database storage upgrade
     * @var boolean
     */
    private $overrideExisting;

    /**
     * Output interface
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * The current entity manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('migration:upgradeToPageRedirect')
                ->addOption('force', 'f', InputOption::VALUE_NONE, 'The database storage will be overrided against the existing one.')
                ->addOption('memory-limit', 'm', InputOption::VALUE_OPTIONAL, 'The memory limit to set.')
                ->setDescription('Upgrade BackBee page data structure for redirect')
                ->setHelp(<<<EOF
This command introduces redirect feature and updates data storage of pages:

   <info>php %command.name%</info>
EOF
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = -microtime(true);
        
        $this->overrideExisting = $input->getOption('force');
        $this->em = $this->getContainer()->get('em');
        $this->output = $output;
        
        if (null !== $input->getOption('memory-limit')) {
            ini_set('memory_limit', $input->getOption('memory-limit'));
        }

        $this->checksBackBeeVersion()
                ->updatePageTable();

        $time += microtime(true);

        $this->output->writeln(sprintf('<info>UPGRADE DONE IN %f s.</info>', $time));
        $this->output->writeln(sprintf(' - You should launch bbapp:update command.'));
    }

    /**
     * Checks for BackBee version, at least 1.1.0 is required
     *
     * @return \BackBee\Console\Command\UpgradeToPageSectionCommand
     * @throws BBException                                              Raises if version is previous to 1.1.0
     */
    private function checksBackBeeVersion()
    {
        $this->output->writeln('<info>Checking BackBee instance</info>');
        $this->output->write(sprintf(' - BackBee version: %s - ', BBApplication::VERSION));

        if (0 > version_compare(BBApplication::VERSION, '1.1')) {
            $this->output->writeln("<error>Failed</error>");
            throw new BBException(sprintf('This command needs at least BackBee v1.1.0 installed, gets BackBee v%s.%sPlease upgrade your distribution.', BBApplication::VERSION, PHP_EOL));
        }

        $this->output->writeln('<info>OK</info>');
        return $this;
    }

    private function updatePageTable()
    {
        $pageMeta = $this->em->getClassMetadata('BackBee\NestedNode\Page');
        $tablePage = $pageMeta->getTableName();
        $uidField = $pageMeta->getColumnName('_uid');
        $redirectField = $pageMeta->getColumnName('_redirect');
        $emptySerializedArray = serialize(array());
        $firstPart = 'a:1:{i:0;a:4:{s:3:"url";s:';
        $middlePart = ':"';
        $lastPart = '";s:5:"title";s:0:"";s:7:"pageUid";s:0:"";s:6:"target";s:5:"_self";}}';

        $query = "UPDATE %s p1, (SELECT DISTINCT %s, %s, LENGTH(%s) length FROM %s WHERE 1=1) p2"
            . " SET p1.%s = IF(p2.%s IS NULL, '".$emptySerializedArray."',"
            . " IF(p2.%s IS NOT NULL AND p2.%s != '', CONCAT('".$firstPart."', p2.length, '".$middlePart."', p2.%s, '".$lastPart."'), p2.%s))"
            . " WHERE p1.%s = p2.%s";
        $query = sprintf(
            $query,
            $tablePage,
            $uidField,
            $redirectField,
            $redirectField,
            $tablePage,
            $redirectField,
            $redirectField,
            $redirectField,
            $redirectField,
            $redirectField,
            $redirectField,
            $uidField,
            $uidField
        );
//        $this->output->writeln($query);
        $this->em->getConnection()->executeUpdate($query);

        return $this;
    }
}