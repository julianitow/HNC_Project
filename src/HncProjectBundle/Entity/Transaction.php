<?php

namespace HncProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity(repositoryClass="HncProjectBundle\Repository\TransactionRepository")
 */
class Transaction
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="share_name", type="string", length=8)
     */
    private $shareName;

    /**
     * @var int
     *
     * @ORM\Column(name="volume_amount", type="integer")
     */
    private $volumeAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="portfolio_id", type="integer")
     */
    private $portfolioId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    private $price;

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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Transaction
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set shareName
     *
     * @param string $shareName
     *
     * @return Transaction
     */
    public function setShareName($shareName)
    {
        $this->shareName = $shareName;

        return $this;
    }

    /**
     * Get shareName
     *
     * @return string
     */
    public function getShareName()
    {
        return $this->shareName;
    }

    /**
     * Set volumeAmount
     *
     * @param integer $volumeAmount
     *
     * @return Transaction
     */
    public function setVolumeAmount($volumeAmount)
    {
        $this->volumeAmount = $volumeAmount;

        return $this;
    }

    /**
     * Get volumeAmount
     *
     * @return int
     */
    public function getVolumeAmount()
    {
        return $this->volumeAmount;
    }

    /**
     * Set portfolioId
     *
     * @param integer $portfolioId
     *
     * @return Transaction
     */
    public function setPortfolioId($portfolioId)
    {
        $this->portfolioId = $portfolioId;

        return $this;
    }

    /**
     * Get portfolioId
     *
     * @return int
     */
    public function getPortfolioId()
    {
        return $this->portfolioId;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */

    public function setPrice($price)
    {
        $this->price = $price;
    }
}

