<?php

namespace QuestionKeyBundle\Command;

use Doctrine\ORM\Mapping\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use QuestionKeyBundle\PurgeTree;


/**
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*/
class PurgeTreeCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('questionkey:purge-tree')
            ->setDescription('Purge a Tree from the DataBase')
            ->addOption(
                'publicid',
                null,
                InputArgument::OPTIONAL,
                'public ID of tree to purge'
            )
            ->addOption(
                'purge',
                null,
                InputArgument::OPTIONAL,
                'actually purge'
            );
    }

    protected $tree;

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $doctrine = $this->getContainer()->get('doctrine')->getManager();

        $treeRepo = $doctrine->getRepository('QuestionKeyBundle:Tree');

        $this->tree = $treeRepo->findOneBy(array('publicId'=>$input->getOption('publicid')));

        if (!$this->tree) {
            $output->writeln('No Tree Found');
            return;
        }

        $output->writeln('Tree ID: '.$this->tree->getId());
        $output->writeln('Tree Public ID: '.$this->tree->getPublicId());
        $output->writeln('Tree Title (Admin): '.$this->tree->getTitleAdmin());

        if ($input->getOption('purge') && filter_var($input->getOption('purge'), FILTER_VALIDATE_BOOLEAN)) {
            $output->writeln('Purging ...');

            $purgeTree = new PurgeTree($doctrine, $this->tree);
            $purgeTree->go();

            $output->writeln('Purged!');

        }

    }

}
