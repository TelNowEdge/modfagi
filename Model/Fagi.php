<?php

/*
 * Copyright [2016] [TelNowEdge]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
     * @Assert\Type("\TelNowEdge\FreePBX\Base\Form\Model\Destination")
     * @Assert\NotNull()
     * @Assert\Valid(traverse=true)
     */
    protected $fallback;

    /**
     * @Assert\Type("\Doctrine\Common\Collections\ArrayCollection")
     * @Assert\Valid(traverse=true)
     * @Assert\All({
     *   @Assert\Type("\TelNowEdge\Module\modfagi\Model\FagiResult")
     * })
     */
    protected $fagiResults;

    public function __construct()
    {
        $this->fagiResults = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fallback = new \TelNowEdge\FreePBX\Base\Form\Model\Destination();
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

    public function getFagiResults()
    {
        return $this->fagiResults;
    }

    public function setFagiResults(array $fagiResults)
    {
        foreach ($fagiResults as $fagiResult) {
            $this->addFagiResult($fagiResult);
        }

        return $this;
    }

    public function addFagiResult(FagiResult $fagiResult)
    {
        if (true === $this->fagiResults->exists(function ($key, $object) use ($fagiResult) {
            return $object->getId() === $fagiResult->getId();
        }) && null !== $fagiResult->getId()) {
            return $this;
        }

        $fagiResult->setFagi($this);

        $this->fagiResults->add($fagiResult);

        return $this;
    }

    public function removeFagiResult(FagiResult $fagiResult)
    {
        $this->fagiResults->removeElement($fagiResult);

        return $this;
    }

    public function getFallbackAsArray()
    {
        return explode(',', $this->fallback);
    }

    public function getFallback()
    {
        return $this->fallback;
    }

    public function setFallback(Destination $fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    public function getUrl()
    {
        return sprintf(
            'agi://%s:%s/%s',
            $this->getHost(),
            $this->getPort(),
            $this->getPath()
        );
    }

    public function getUri()
    {
        parse_str($this->getQuery(), $query);

        return sprintf(
            '%s?%s',
            $this->getUrl(),
            http_build_query($query, '', '&', \PHP_QUERY_RFC3986)
        );
    }
}
