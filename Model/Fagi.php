<?php

namespace TelNowEdge\Module\modfagi\Model;

use Symfony\Component\Validator\Constraints as Assert;
use TelNowEdge\FreePBX\Base\Form\Model\Destination;

class Fagi
{
    protected $id;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $displayName;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $host;

    /**
     * @Assert\Type("integer")
     * @Assert\NotNull()
     */
    protected $port;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $path;

    /**
     * @Assert\Type("string")
     */
    protected $query;

    /**
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $trueGoto;

    /**
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $falseGoto;

    public function __construct()
    {
        $this->trueGoto = new Destination();
        $this->falseGoto = new Destination();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    public function getTrueGoto()
    {
        return $this->trueGoto;
    }

    public function setTrueGoto($trueGoto)
    {
        $this->trueGoto = $trueGoto;

        return $this;
    }

    public function getFalseGoto()
    {
        return $this->falseGoto;
    }

    public function setFalseGoto($falseGoto)
    {
        $this->falseGoto = $falseGoto;

        return $this;
    }
}
