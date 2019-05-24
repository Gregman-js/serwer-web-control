<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AppDevOnCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:dev:on')
            ->setDescription('Turn on computer by mac from argument')
            ->addArgument('argument', InputArgument::REQUIRED, 'MAC Address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $process = new Process('wakeonlan '.$argument);
        $process->run();
        if (!$process->isSuccessful()) {
          $output->write(false);
        } else {
          $output->write(true);
        }
    }

}
