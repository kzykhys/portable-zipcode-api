<?php
/**
 * This file is a part of Portable ZipCode API
 *
 * @copyright 2013 Kazuyuki Hayashi
 * @license   MIT
 */

namespace KzykHys\ZipFinder\Entity;

/**
 * Entity: Address
 *
 * @Table(name="address")
 * @Entity()
 */
class Address
{

    /**
     * @Column(name="id", type="bigint")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string $code
     * @Column(name="code", type="string", length=7)
     */
    public $code;

    /**
     * @var string $pref
     * @Column(name="pref", type="string")
     */
    public $pref;

    /**
     * @var string $city
     * @Column(name="city", type="string")
     */
    public $city;

    /**
     * @var string $town
     * @Column(name="town", type="string")
     */
    public $town;

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPref()
    {
        return $this->pref;
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

}