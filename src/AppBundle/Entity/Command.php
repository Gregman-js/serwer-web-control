<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Command
 *
 * @ORM\Table(name="command")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommandRepository")
 */
class Command
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Machine", inversedBy="clients")
     */
     private $urzadzenie;

     /**
      * @var string
      *
      * @ORM\Column(name="name", type="string")
      */
     private $name;

     /**
      * @var string
      *
      * @ORM\Column(name="command", type="string")
      */
     private $command;

     /**
      * @var boolean
      *
      * @ORM\Column(name="enabled", type="boolean")
      */
     private $enabled = true;
     /**
      * @var boolean
      *
      * @ORM\Column(name="isrem", type="boolean")
      */
     private $isrem = true;

     /**
      * @var boolean
      *
      * @ORM\Column(name="doOut", type="boolean")
      */
     private $doOut = false;


     /**
      * Set enabled
      *
      * @param boolean $enabled
      *
      * @return Command
      */
     public function setEnabled($enabled)
     {
         $this->enabled = $enabled;

         return $this;
     }

     /**
      * Get enabled
      *
      * @return boolean
      */
     public function getEnabled()
     {
         return $this->enabled;
     }

     /**
      * Set isrem
      *
      * @param boolean $isrem
      *
      * @return Command
      */
     public function setIsrem($isrem)
     {
         $this->isrem = $isrem;

         return $this;
     }

     /**
      * Get isrem
      *
      * @return boolean
      */
     public function getIsrem()
     {
         return $this->isrem;
     }

     /**
      * Set doOut
      *
      * @param boolean $doOut
      *
      * @return Command
      */
     public function setDoOut($doOut)
     {
         $this->doOut = $doOut;

         return $this;
     }

     /**
      * Get doOut
      *
      * @return boolean
      */
     public function getDoOut()
     {
         return $this->doOut;
     }

     /**
      * Set name
      *
      * @param string $name
      *
      * @return Command
      */
     public function setName($name)
     {
         $this->name = $name;

         return $this;
     }

     /**
      * Get name
      *
      * @return string
      */
     public function getName()
     {
         return $this->name;
     }
     /**
      * Set command
      *
      * @param string $command
      *
      * @return Command
      */
     public function setCommand($command)
     {
         $this->command = $command;

         return $this;
     }

     /**
      * Get command
      *
      * @return string
      */
     public function getCommand()
     {
         return $this->command;
     }

     /**
      * Set urzadzenie
      *
      * @param \AppBundle\Entity\Machine $urzadzenie
      * @return Command
      */
     public function setUrzadzenie(\AppBundle\Entity\Machine $urzadzenie = null)
     {
         $this->urzadzenie = $urzadzenie;

         return $this;
     }

     /**
      * Get urzadzenie
      *
      * @return \AppBundle\Entity\Machine
      */
     public function getUrzadzenie()
     {
         return $this->urzadzenie;
     }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
