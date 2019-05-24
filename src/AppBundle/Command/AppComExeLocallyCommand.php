<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;

class AppComExeLocallyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('app:com:loc')
        ->setDescription('Exectute user commands locally')
        ->addArgument('comek', InputArgument::REQUIRED, 'command to execute')
        ->addArgument('shout', InputArgument::REQUIRED, 'wait for output')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getArgument('comek');
        $wait = $input->getArgument('shout');
        $outputter = '';
        if (substr(php_uname(), 0, 7) == "Windows")$outputter = shell_exec($command);
        else $outputter = shell_exec($command.($wait ? "" : " > /dev/null 2>&1 &"));
       $output->write($outputter);
    }

}
