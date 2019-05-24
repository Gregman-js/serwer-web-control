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

class AppComExeRemotelyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:com:rem')
            ->setDescription('Exectute user commands remotely with ssh')
            ->addArgument('ipaddr', InputArgument::REQUIRED, 'IPv4 server adress')
            ->addArgument('user', InputArgument::REQUIRED, 'ssh authorization user')
            ->addArgument('pass', InputArgument::REQUIRED, 'ssh authorization user password')
            ->addArgument('comek', InputArgument::REQUIRED, 'command to execute')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ipaddr = $input->getArgument('ipaddr');
        $user = $input->getArgument('user');
        $pass = $input->getArgument('pass');
        $command = $input->getArgument('comek');
        $ssh = new SSH2($ipaddr);
        if (!$ssh->login($user, $pass)) {
            $output->write(false);
        } else {
          // $ssh->enablePTY();
          // $outek = $ssh->exec('nohup '.$command.' &>/dev/null &');
          // $outek = $ssh->exec('nohup '.$command.' 2>&1');
          $outek = $ssh->exec($command);
          // $ssh->exec($command);
          // $ssh->exec('disown');
          $output->write($outek);
        }
    }

}
