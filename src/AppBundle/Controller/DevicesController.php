<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Machine;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
*@Route("/dev/{id}")
* @ParamConverter("urzadzenie", class="AppBundle:Machine")
*/
class DevicesController extends Controller
{
  private $timeToUpdate = 1;
  /**
   * @param Request $request
   * @Route("/on", options = { "expose" = true }, name="on_dev")
   * @ParamConverter("urzadzenie", class="AppBundle:Machine")
   */
    public function onAction(Request $request, Machine $urzadzenie)
    {
      if ($request->isXmlHttpRequest()){
        $em = $this->getDoctrine()->getManager();
        $model = $request->request->get('model');
        if ($model == 'false') {
          $urzadzenie->setPower(true);
          $em->flush($urzadzenie);
          return new JsonResponse('success');
        }
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput(array(
            'command' => 'app:dev:on',
            'argument' => $urzadzenie->getMac()
        ));
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        if ($content == true) {
          $urzadzenie->setPower(true);
          $em->flush($urzadzenie);
          return new JsonResponse('success');
        } else {
          return new JsonResponse('error');
        }
      } else {
        return $this->redirectToRoute('index_dev', ['id' => $urzadzenie->getId()]);
      }
    }
  /**
   * @param Request $request
   * @Route("/off",  options = { "expose" = true }, name="off_dev")
   * @ParamConverter("urzadzenie", class="AppBundle:Machine")
   */
    public function offAction(Request $request, Machine $urzadzenie)
    {
      if ($request->isXmlHttpRequest()){
        $em = $this->getDoctrine()->getManager();
        $model = $request->request->get('model');
        if ($model == 'false') {
          $urzadzenie->setPower(false);
          $em->flush($urzadzenie);
          return new JsonResponse('success');
        }
        if(!$this->checkConnect($urzadzenie->getIpaddress())){
          $urzadzenie->setPower(false);
          $em->flush($urzadzenie);
          return new JsonResponse('isoff');
        }
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput(array(
            'command' => 'app:dev:off',
            'ipaddr' => $urzadzenie->getIpaddress(),
            'user' => $urzadzenie->getUsername(),
            'pass' => $urzadzenie->getPass()
        ));
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        if ($content == true) {
          $urzadzenie->setPower(false);
          $em->flush($urzadzenie);
          return new JsonResponse('success');
        } else {
          return new JsonResponse('error');
        }
      } else {
        return $this->redirectToRoute('index_dev', ['id' => $urzadzenie->getId()]);
      }
    }

  /**
   * @param Request $request
   * @Route("/update", options = { "expose" = true }, name="update_dev")
   * @ParamConverter("urzadzenie", class="AppBundle:Machine")
   */
    public function updateAction(Request $request, Machine $urzadzenie)
    {
      $em = $this->getDoctrine()->getManager();
      $last = $urzadzenie->getLastUpdate()->getTimestamp();
      $now = new \DateTime();
      $now = $now->getTimestamp();
      $diff = round(($now - $last)/60, 2);
      $i = 0;
      if (!$request->isXmlHttpRequest())$update = false;
       else $update = $diff > $this->timeToUpdate ? true : false;
      if ((($update && ($request->request->get('model') == 'true' || !$request->request->has('model'))) || $request->request->get('force') == 'true') && $urzadzenie->getPower() == true) {
        while (!$this->checkConnect($urzadzenie->getIpaddress()) && ++$i < 30) {
          sleep(1);
        }
        if($i < 30){
          $kernel = $this->get('kernel');
          $application = new Application($kernel);
          $application->setAutoExit(false);
          $input = new ArrayInput(array(
            'command' => 'app:dev:update',
            'ipaddr' => $urzadzenie->getIpaddress(),
            'user' => $urzadzenie->getUsername(),
            'pass' => $urzadzenie->getPass()
          ));
          $output = new BufferedOutput();
          $application->run($input, $output);
          $content = $output->fetch();
          $urzadzenie->setRaport($content);
          $urzadzenie->setLastUpdate(new \DateTime());
          $em->flush($urzadzenie);
        } else {
          $urzadzenie->setPower(false);
          $em->flush($urzadzenie);
        }
      } else {
        $content = $urzadzenie->getRaport();
      }
      $content = json_decode($content, true);
      if (!$update && $content != '') {
        $content['uptime']['arr'][2] += floor($diff);
        $content['uptime']['arr'][1] += floor($content['uptime']['arr'][2] / 60);
        $content['uptime']['arr'][2] = $content['uptime']['arr'][2] % 60;
        $content['uptime']['arr'][0] += floor($content['uptime']['arr'][1] / 60);
        $content['uptime']['arr'][1] = $content['uptime']['arr'][1] % 60;
      }
      if ($urzadzenie->getPower()) {
        $content['uptime']['text'] = ($content['uptime']['arr'][1] > 0 ? $content['uptime']['arr'][1]."h " : "").$content['uptime']['arr'][2]."min";
      } else {
        $content['uptime']['text'] = 'OFF';
      }
      if($i == 30){
        $content['status'] = '404';
      }
      else $content['status'] = '200';
      $contBuilded = $content;
      $content = json_encode($content, true);
      if ($request->isXmlHttpRequest()){
        return new JsonResponse($content);
      }
      else return array(
        'raport' => $contBuilded
      );
      }

  /**
   * @param Request $request
   * @Route("/shutdown",  options = { "expose" = true }, name="shutdown_dev")
   * @ParamConverter("urzadzenie", class="AppBundle:Machine")
   */
    public function shutdownAction(Request $request, Machine $urzadzenie)
    {
      if ($request->isXmlHttpRequest()){
        $em = $this->getDoctrine()->getManager();
        if ($request->request->get('model') == 'true') {
          if (!$this->checkConnect($urzadzenie->getIpaddress()))return new JsonResponse('error');
          $kernel = $this->get('kernel');
          $application = new Application($kernel);
          $application->setAutoExit(false);
          $input = new ArrayInput(array(
            'command' => 'app:dev:shutdown',
            'ipaddr' => $urzadzenie->getIpaddress(),
            'user' => $urzadzenie->getUsername(),
            'pass' => $urzadzenie->getPass(),
            'shArg' => $request->request->get('time')[0].":".$request->request->get('time')[1]
          ));
          $output = new BufferedOutput();
          $application->run($input, $output);
          $content = $output->fetch();
          if ($content == false)return new JsonResponse('error');
        }
        $timeToLock = new \DateTime();
        $timeToLock = $timeToLock->setTime($request->request->get('time')[0], $request->request->get('time')[1]);
        $urzadzenie->setShTime($timeToLock);
        return new JsonResponse('success');
      } else {
        return $this->redirectToRoute('index_dev', ['id' => $urzadzenie->getId()]);
      }
    }

    /**
     * @param Request $request
     * @Route("", name="index_dev")
     * @ParamConverter("urzadzenie", class="AppBundle:Machine")
     * @Template()
     */
      public function indexAction(Request $request, Machine $urzadzenie)
      {
        // die(var_dump($request->get('_controller')));
        $em = $this->getDoctrine()->getManager();
        if(!$this->checkConnect($urzadzenie->getIpaddress())){
          if ($urzadzenie->getPower() == true) {
            $urzadzenie->setPower(false);
            $em->flush($urzadzenie);
          }
        } else {
          if ($urzadzenie->getPower() == false) {
            $urzadzenie->setPower(true);
            $em->flush($urzadzenie);
          }
        }

        $updaterek = $this->updateAction($request, $urzadzenie);

        if($updaterek['raport'] == NULL || $updaterek['raport'] == "")$updaterek['raport'] = false;
        $commands = $em->getRepository('AppBundle:Command')->findBy(['urzadzenie' => $urzadzenie, 'enabled' => 1]);
        return ['commands' => $commands, 'machine' => $urzadzenie, 'raport' => $updaterek['raport']];
      }
      public function checkConnect($ip)
      {
        return @fsockopen($ip, '22', $errno, $errstr, 0.3);
      }
}
