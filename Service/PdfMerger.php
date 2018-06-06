<?php

namespace Magelan\PdfMergerBundle\Service;

use Symfony\Component\Process\Process;

class PdfMerger
{
    /**
     * @var array
     */
    private $config;

    /**
     * PdfMerger constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param array       $files
     * @param string|null $output
     *
     * @return array
     */
    public function merge(array $files, string $output)
    {
        foreach ($files as $file) {
            if (!is_file($file)) {
                throw new \RuntimeException(sprintf('%s is not a file', $file));
            }
        }

        if (!preg_match("#\.pdf$#", $output)) {
            throw new \RuntimeException(sprintf('%s is not a pdf file', $output));
        }

        $command = $this->buildCommand(
            sprintf(
                '%s -q -sPAPERSIZE=a3 -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=%s',
                $this->config['binary'],
                escapeshellarg($output)
            ),
            $files,
            $output
        );

        return $this->executeCommand($command);
    }

    protected function buildCommand($binary, $files, $output)
    {
        $command = $binary;

        foreach ($files as $file) {
            $command .= ' '.escapeshellarg($file);
        }

        $command .= ' '.escapeshellarg(dirname(__FILE__).'/../pdfmarks');

        return $command;
    }

    /**
     * Executes the given command via shell and returns the complete output as
     * a string.
     *
     * @param string $command
     *
     * @return array(status, stdout, stderr)
     */
    protected function executeCommand($command)
    {
        $process = new Process($command);
        $process->setTimeout(null);

        $process->run();

        return [
            $process->getExitCode(),
            $process->getOutput(),
            $process->getErrorOutput(),
        ];
    }
}
