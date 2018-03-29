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

class FagiResult
{
    protected $id;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $match;

    /**
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $goto;

    /**
     * Direct Join.
     */
    protected $fagi;

    public function __construct()
    {
        $this->fagi = new Fagi();
        $this->goto = new Destination();
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

    public function getMatch()
    {
        return $this->match;
    }

    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    public function getGoto()
    {
        return $this->goto;
    }

    public function getGotoAsArray()
    {
        return explode(',', $this->goto);
    }

    public function setGoto(Destination $goto)
    {
        $this->goto = $goto;

        return $this;
    }

    public function setFagi(Fagi $fagi)
    {
        $this->fagi = $fagi;

        return $this;
    }

    public function getFagi()
    {
        return $this->fagi;
    }
}
