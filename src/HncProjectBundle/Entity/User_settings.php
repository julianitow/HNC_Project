<?php

namespace HncProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * user_settings
 *
 * @ORM\Table(name="user_settings")
 * @ORM\Entity(repositoryClass="HncProjectBundle\Repository\user_settingsRepository")
 */
class User_settings
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
     * @var int
     *
     * @ORM\Column(name="currency", type="integer")
     * @ORM\OneToOne(targetEntity="Currency")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $currency;

    /**
     * @var int
     *
     * @ORM\Column(name="colour", type="integer")
     * @ORM\OneToOne(targetEntity="Colour")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $colour;

    /**
     * @var bool
     *
     * @ORM\Column(name="email_notif", type="boolean")
     */
    private $emailNotif;

    /**
     * @var bool
     *
     * @ORM\Column(name="phone_notif", type="boolean")
     */
    private $phoneNotif;


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
     * Set currency
     *
     * @param integer $currency
     *
     * @return user_settings
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return int
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set colour
     *
     * @param integer $colour
     *
     * @return user_settings
     */
    public function setColour($colour)
    {
        $this->colour = $colour;

        return $this;
    }

    /**
     * Get colour
     *
     * @return int
     */
    public function getColour()
    {
        return $this->colour;
    }

    /**
     * Set emailNotif
     *
     * @param boolean $emailNotif
     *
     * @return user_settings
     */
    public function setEmailNotif($emailNotif)
    {
        $this->emailNotif = $emailNotif;

        return $this;
    }

    /**
     * Get emailNotif
     *
     * @return bool
     */
    public function getEmailNotif()
    {
        return $this->emailNotif;
    }

    /**
     * Set phoneNotif
     *
     * @param boolean $phoneNotif
     *
     * @return user_settings
     */
    public function setPhoneNotif($phoneNotif)
    {
        $this->phoneNotif = $phoneNotif;

        return $this;
    }

    /**
     * Get phoneNotif
     *
     * @return bool
     */
    public function getPhoneNotif()
    {
        return $this->phoneNotif;
    }
}

