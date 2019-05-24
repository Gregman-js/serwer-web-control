<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenCrypt\OpenCrypt;
/**
 * Machine
 *
 * @ORM\Table(name="machine")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MachineRepository")
 */
class Machine
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
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mac", type="string")
     */
    private $mac;
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string")
     */
    private $pass;
    /**
     * @var binary
     *
     * @ORM\Column(name="vector", type="binary")
     */
    private $vector;
    /**
     * @var string
     *
     * @ORM\Column(name="ipaddress", type="string")
     */
    private $ipaddress;

    /**
     * @var bool
     *
     * @ORM\Column(name="power", type="boolean")
     */
    private $power;

    /**
     * @var string
     *
     * @ORM\Column(name="raport", type="string")
     */
    private $raport;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUpdate", type="datetime")
     */
    private $lastUpdate;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set power
     *
     * @param boolean $power
     *
     * @return Machine
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Get power
     *
     * @return bool
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Machine
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
     * Set raport
     *
     * @param string $raport
     *
     * @return Machine
     */
    public function setRaport($raport)
    {
        $this->raport = $raport;

        return $this;
    }

    /**
     * Get raport
     *
     * @return string
     */
    public function getRaport()
    {
        return $this->raport;
    }
    /**
     * Set username
     *
     * @param string $username
     *
     * @return Machine
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * Set vector
     *
     * @param binary $vector
     *
     * @return Machine
     */
    public function setVector()
    {
        $this->vector = OpenCrypt::generateIV();
        return $this;
    }

    /**
     * Get vector
     *
     * @return binary
     */
    public function getVector()
    {
      if(gettype($this->vector) == "resource")return stream_get_contents($this->vector);
      else return $this->vector;
    }

    /**
     * Set pass
     *
     * @param string $pass
     *
     * @return Machine
     */
    public function setPass($pass)
    {
        self::setVector();
        $openCrypt = new OpenCrypt('41fc89a19423122d2352e57881c4ad25dde353d5b150537e9af724488f91877ca330b02b4b23ae6e88d75707a59407cfa83c6bdbe4c914fe67b77cb2189877dc6638b4b3766c2cc5a2a77cb01297a211b52db8847a413081813e24c22cf46a43702f385e9e2a2e5dc74fd7305d3f953ae49126b261d9d0bf63f93dbd56c948121b421fb658012b8c0fc33e80ef1af44acc0d37115bc8962f6d88b70cc805aa2a7ed4f2182f3ac416698b1cdb35c7e28d076f5f65bf432427131867d8144be6afacf2ffab825266e9c9323c623401892ce1a45f1c808b2fd1708025d55baf6093b3971ffffa15170e7ab6f14eadcc1f57e6743fefb049731948aa12144fdcd229', self::getVector());
        $encryptedPassword = $openCrypt->encrypt($pass);
        $this->pass = $encryptedPassword;
        return $this;
    }

    /**
     * Get pass
     *
     * @return string
     */
    public function getPass()
    {
      if (!isset($this->pass) || trim($this->pass) === '') {
        return '';
      } else {
        $openCrypt = new OpenCrypt('41fc89a19423122d2352e57881c4ad25dde353d5b150537e9af724488f91877ca330b02b4b23ae6e88d75707a59407cfa83c6bdbe4c914fe67b77cb2189877dc6638b4b3766c2cc5a2a77cb01297a211b52db8847a413081813e24c22cf46a43702f385e9e2a2e5dc74fd7305d3f953ae49126b261d9d0bf63f93dbd56c948121b421fb658012b8c0fc33e80ef1af44acc0d37115bc8962f6d88b70cc805aa2a7ed4f2182f3ac416698b1cdb35c7e28d076f5f65bf432427131867d8144be6afacf2ffab825266e9c9323c623401892ce1a45f1c808b2fd1708025d55baf6093b3971ffffa15170e7ab6f14eadcc1f57e6743fefb049731948aa12144fdcd229', self::getVector());
        $decryptedPassword = $openCrypt->decrypt($this->pass);
        return $decryptedPassword;
      }
    }
    /**
     * Set ipaddress
     *
     * @param string $ipaddress
     *
     * @return Machine
     */
    public function setIpaddress($ipaddress)
    {
        $this->ipaddress = $ipaddress;

        return $this;
    }

    /**
     * Get ipaddress
     *
     * @return string
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }

    /**
     * Set mac
     *
     * @param string $mac
     *
     * @return Machine
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Get mac
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Machine
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }
}
