<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Machine;
use AppBundle\Form\MachineType;

/**
*@Route("/settings")
*/

class SettingsController extends Controller
{
    /**
     */
     /**
      * @param Request $request
      * @Route("/mach/index", name="index_set_mach")
      * @Template()
      */
    public function indexMachAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $machines=$em->getRepository("AppBundle:Machine")->findAll();
      return ['machines' => $machines];
    }

    /**
    * @Route("/mach/{id}/edit", name="edit_set_mach")
     * @ParamConverter("urzadzenie", class="AppBundle:Machine")
     * @Template()
     */
    public function editMachAction(Request $request, Machine $urzadzenie){
        $em = $this->getDoctrine()->getManager();
        $form=$this->createForm(MachineType::class, $urzadzenie);
        $formularz=$form->createView();
        if($request->getMethod()=="POST"){
            $form->handleRequest($request);
            if($form->isValid()){
                $em->flush();
                return $this->redirectToRoute('index_set_mach');
            }

        }
        return ['form'=>$form->createView()];
    }

    /**
     * @Route("/mach/{id}/del", name="del_set_mach")
     * @ParamConverter("urzadzenie", class="AppBundle:Machine")
     */
    public function delMachAction(Request $request, Machine $urzadzenie){
        $em = $this->getDoctrine()->getManager();
        $em->remove($urzadzenie);
        $em->flush();
        return $this->redirectToRoute('index_set_mach');
    }

    /**
     * @Route("/mach/add", name="add_set_mach")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function addMachAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine);//, array('action'=>$this->generateUrl('add_com', array('id' => $urzadzenie->getId())))
        $formularz=$form->createView();
        if($request->getMethod()=="POST"){
          $form->handleRequest($request);
              if($form->isValid()) {
                  $machine->setPower(false);
                  $machine->setRaport('');
                  $machine->setLastUpdate(new \DateTime());
                  $em->persist($machine);
                  $em->flush();
                  return $this->redirectToRoute('index_set_mach');

              }
        }
        return ['form' => $formularz];

    }

}
