<?php

namespace Magelan\PdfMergerBundle\Command;

use Magelan\PdfMergerBundle\Service\PdfMerger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MagelanMergeCommand extends Command
{
    protected static $defaultName = 'magelan:merge';

    /**
     * @var PdfMerger
     */
    private $pdfMerger;

    /**
     * MagelanMergeCommand constructor.
     *
     * @param PdfMerger $pdfMerger
     */
    public function __construct(PdfMerger $pdfMerger)
    {
        $this->pdfMerger = $pdfMerger;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Merge pdfs')
            ->addArgument('files', InputArgument::IS_ARRAY, 'Files to merges (pdf or images)')
            ->addOption('output', 'O', InputOption::VALUE_REQUIRED, 'Output file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $files = $input->getArgument('files');

        $result = $this->pdfMerger->merge($files, $input->getOption('output'));
        $io->success(sprintf('Exit code : %s', $result[0]));
        $io->success(sprintf('Output : %s', $result[1]));
        $io->success(sprintf('ErrorOutput : %s', $result[2]));
    }
}
