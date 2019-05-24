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

class AppDevUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:dev:update')
            ->setDescription('Update computer statistic and information with ssh')
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
          $alpha = 'a';
          $iter = 0;
          $isf = false;
          $drivers = array();
          while ($iter++ < 23){
            $isf = str_replace("\n", "", $ssh->exec('[ -e /dev/sd'.$alpha.' ] && echo true || echo false'));
            if($isf == 'true')
            array_push($drivers, $alpha);
            $alpha = chr(ord($alpha) +1);
          }
          $hddTemp = array();
          foreach ($drivers as $litera) {
            $tmp = str_replace("\n", "", $ssh->exec('hddtemp /dev/sd'.$litera));
            $array = explode(" ", $tmp);
            $array[0] = rtrim(explode("/", $array[0])[count(explode("/", $array[0])) - 1], ':');
            $array[1] = rtrim($array[1], ':');
            array_push($hddTemp, $array);
          }
          $out = [];
          $out['hdd'] = $hddTemp;
          $out['fan'] = [];
          $out['temp'] = [];

          $termo = explode("\n", $ssh->exec('sensors'));
          foreach ($termo as $line) {
            $line = trim($line);
            if (gettype(strpos($line, 'fan')) == "integer") {
              $cz = explode(':', $line);
              $cz[1] = explode(' ', $cz[1])[count(explode(' ', $cz[1])) - 2];
              if (ctype_digit($cz[1]))
              if((int)$cz[1])array_push($out['fan'], $cz);
            } elseif (gettype(strpos($line, 'temp')) == "integer") {
              $cz = explode(':', $line);
              $cz[1] = (float)explode(' ', $cz[1])[count(explode(' ', $cz[1])) - 1];
              if(gettype(strpos($line, 'temp1')) == "integer")$cz[0] = "CPU";
              if((float)$cz[1])array_push($out['temp'], $cz);
            }
          }

          $out['uptime']['arr'] = $this->uptimen(str_replace("\n", "", $ssh->exec('cat /proc/uptime')));
          // $out['uptime']['text'] = ($out['uptime']['arr'][1] > 0 ? $out['uptime']['arr'][1]."h " : "").$out['uptime']['arr'][2]."min";
          $out['ram'] = [];
          $ram = explode("\n", $ssh->exec('vmstat -s'));
          foreach ($ram as $ramlin) {
            $ramlin = trim($ramlin);
            if (gettype(strpos($ramlin, 'total memory')) == 'integer') {
              $ex = explode(" ", $ramlin);
              $val = round(((int)$ex[0]) / 1024);
              $out['ram']['total'] = $val;
            }
            if (gettype(strpos($ramlin, 'used memory')) == 'integer') {
              $ex = explode(" ", $ramlin);
              $val = round(((int)$ex[0]) / 1024);
              $out['ram']['used'] = $val;
            }
          }
          $out['ram']['procent'] = round($out['ram']['used'] / $out['ram']['total'] * 100);
          $output->write(json_encode($out));

        }
    }
    function uptimen($time) {
      $ut = strtok($time, "." );
      $days = sprintf( "%2d", ($ut/(3600*24)) );
      $hours = trim(sprintf( "%2d", ( ($ut % (3600*24)) / 3600) ));
      $min = sprintf( "%2d", ($ut % (3600*24) % 3600)/60  );
      $sec = trim(sprintf( "%2d", ($ut % (3600*24) % 3600)%60  ));
      return array( $days, $hours, $min, $sec );
    }

}
