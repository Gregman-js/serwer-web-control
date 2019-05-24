<?php
namespace AppBundle\EventListener;

// ...

use Avanzu\AdminThemeBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Machines;

class MyMenuItemListListener {

  private $em;

	public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

	public function onSetupMenu(SidebarMenuEvent $event) {

		$request = $event->getRequest();

        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }

	}

	protected function getMenu(Request $request) {
		// Build your menu here by constructing a MenuItemModel array
    if (strpos($request->getUri(), "/dev/") !== false) {
      $machId = $request->attributes->get('id');
      $urzadzenie = $this->em->getRepository('AppBundle:Machine')->find($machId);
      $color = ($urzadzenie->getPower() ? 'green' : 'red');
      $menuItems = array(
      $retTOmen = new MenuItemModel('1', 'Back', 'index', array(/* options */), 'iconclasses fa fa-long-arrow-alt-left'),
      $main = new MenuItemModel('2', $urzadzenie->getName(), 'index_dev', array('id' => $machId), 'iconclasses fa fa-home '.$color),
      $commends = new MenuItemModel('3', 'Commands', 'index_com', array('id' => $machId), 'iconclasses fa fa-terminal')
      );
    } else {
      $menuItems = array(
      $menmach = new MenuItemModel('1', 'Urządzenia', 'item_symfony_route', array(/* options */), 'iconclasses fa fa-server'),
      $settings = new MenuItemModel('2', 'Settings', 'item_symfony_route', array(/* options */), 'iconclasses fa fa-cog')
      );
      $machines = $this->em->getRepository('AppBundle:Machine')->findAll();
      for ($i=0; $i < count($machines); $i++) {
        $color = ($machines[$i]->getPower() ? 'green' : 'red');
        $menmach->addChild(new MenuItemModel('1'.$i, $machines[$i]->getName(), 'index_dev', array('id' => $machines[$i]->getId()), 'fa fa-laptop '.$color));
      }
      $settings->addChild(new MenuItemModel('20', "Servers", 'index_set_mach', array(), 'fa fa-toolbox'));
    }
		return $this->activateByRoute($request->get('_route'), $menuItems);
	}

	protected function activateByRoute($route, $items) {

        foreach($items as $item) {
            if($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            }
            else {
                if($item->getRoute() == $route) {
                    $item->setIsActive(true);
                }
            }
        }

        return $items;
    }

}
