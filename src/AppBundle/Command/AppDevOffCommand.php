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

class AppDevOffCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:dev:off')
            ->setDescription('Turn off computer with ssh')
            ->addArgument('ipaddr', InputArgument::REQUIRED, 'IPv4 server adress')
            ->addArgument('user', InputArgument::REQUIRED, 'ssh authorization user')
            ->addArgument('pass', InputArgument::REQUIRED, 'ssh authorization user password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ipaddr = $input->getArgument('ipaddr');
        $user = $input->getArgument('user');
        $pass = $input->getArgument('pass');
        $ssh = new SSH2($ipaddr);
        if (!$ssh->login($user, $pass)) {
            $output->write(false);
        } else {
          $ssh->exec('poweroff');
          $output->write(true);
        }
    }

}
