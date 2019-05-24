<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Machine;
use AppBundle\Entity\Command;
use AppBundle\Form\CommandType;
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
class CommandsController extends Controller
{
  /**
  * @param Request $request
  * @Route("/commands/{command_id}/execom", options = { "expose" = true }, name="exec_com")
  * @ParamConverter("command", class="AppBundle:Command", options={"id" = "command_id"})
   */
  public function execCommAction(Request $request, Machine $urzadzenie, Command $command){
    if ($request->isXmlHttpRequest()){
      $em = $this->getDoctrine()->getManager();
      $model = $request->request->get('model');
      if ($model == 'false') return new JsonResponse('success');
      if(!$this->checkConnect($urzadzenie->getIpaddress()) && $command->getIsrem()){
        $urzadzenie->setPower(false);
        $em->flush($urzadzenie);
        return new JsonResponse('isoff');
      }
      $kernel = $this->get('kernel');
      $application = new Application($kernel);
      $application->setAutoExit(false);
      if ($command->getIsrem()) {
        $input = new ArrayInput(array(
            'command' => 'app:com:rem',
            'ipaddr' => $urzadzenie->getIpaddress(),
            'user' => $urzadzenie->getUsername(),
            'pass' => $urzadzenie->getPass(),
            'comek' => $command->getCommand()
        ));
      } else {
        $input = new ArrayInput(array(
            'command' => 'app:com:loc',
            'comek' => $command->getCommand(),
            'shout' => $command->getDoOut()
        ));
      }
      $output = new BufferedOutput();
      $application->run($input, $output);
      $content = $output->fetch();
        return new JsonResponse($content);
    } else {
      return $this->redirectToRoute('index_com', ['id' => $urzadzenie->getId()]);
    }
  }

  /**
   * @param Request $request
   * @Route("/commands", name="index_com")
   * @Template()
   */
    public function indexAction(Request $request, Machine $urzadzenie)
    {
      $em = $this->getDoctrine()->getManager();
      $commands=$em->getRepository("AppBundle:Command")->findByUrzadzenie($urzadzenie->getId());
      return ['commands'=>$commands, 'machine' => $urzadzenie];
    }
    /**
    * @Route("/commands/{command_id}/edit", name="edit_com")
     * @ParamConverter("command", class="AppBundle:Command", options={"id" = "command_id"})
     * @Template()
     */
    public function editAction(Request $request, Machine $urzadzenie, Command $command){
        $em = $this->getDoctrine()->getManager();
        $form=$this->createForm(CommandType::class,$command);
        $formularz=$form->createView();
        if($request->getMethod()=="POST"){
            $form->handleRequest($request);
            if($form->isValid()){
                $em->flush();
                return $this->redirectToRoute('index_com', array('id' => $urzadzenie->getId()));
            }

        }
        return ['form'=>$form->createView()];
    }

    /**
     * @Route("/commands/{command_id}/del", name="del_com")
     * @ParamConverter("command", class="AppBundle:Command", options={"id" = "command_id"})
     */
    public function delAction(Request $request, Machine $urzadzenie, Command $command){
        $em = $this->getDoctrine()->getManager();
        $em->remove($command);
        $em->flush();
        return $this->redirectToRoute('index_com', array('id' => $urzadzenie->getId()));
    }

    /**
     * @Route("/commands/add", name="add_com")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function addAction(Request $request, Machine $urzadzenie){
        $em = $this->getDoctrine()->getManager();
        $command = new Command();
        $form = $this->createForm(CommandType::class, $command);//, array('action'=>$this->generateUrl('add_com', array('id' => $urzadzenie->getId())))
        $formularz=$form->createView();
        if($request->getMethod()=="POST"){
          $form->handleRequest($request);
              if($form->isValid()) {
                  $command->setUrzadzenie($urzadzenie);
                  $em->persist($command);
                  $em->flush();
                  return $this->redirectToRoute('index_com', array('id' => $urzadzenie->getId()));

              }
        }
        return ['form' => $formularz];

    }

    /**
    * @Route("/commands/{command_id}/encom", name="en_com")
    * @ParamConverter("command", class="AppBundle:Command", options={"id" = "command_id"})
     */
    public function enabledCommAction(Request $request, Machine $urzadzenie, Command $command){
        $em = $this->getDoctrine()->getManager();
        $command->setEnabled(!$command->getEnabled());
        $em->flush();
        return $this->redirectToRoute('index_com', array('id' => $urzadzenie->getId()));
    }

    /**
    * @Route("/commands/{command_id}/remcom", name="rem_com")
    * @ParamConverter("command", class="AppBundle:Command", options={"id" = "command_id"})
     */
    public function remCommAction(Request $request, Machine $urzadzenie, Command $command){
        $em = $this->getDoctrine()->getManager();
        $command->setIsrem(!$command->getIsrem());
        $em->flush();
        return $this->redirectToRoute('index_com', array('id' => $urzadzenie->getId()));
    }

    /**
    * @Route("/commands/{command_id}/outek", name="do_out_com")
    * @ParamConverter("command", class="AppBundle:Command", options={"id" = "command_id"})
     */
    public function doOuterAction(Request $request, Machine $urzadzenie, Command $command){
        $em = $this->getDoctrine()->getManager();
        $command->setDoOut(!$command->getDoOut());
        $em->flush();
        return $this->redirectToRoute('index_com', array('id' => $urzadzenie->getId()));
    }
    public function checkConnect($ip)
    {
      return @fsockopen($ip, '22', $errno, $errstr, 0.3);
    }
}
