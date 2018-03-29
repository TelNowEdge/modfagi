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

namespace TelNowEdge\Module\modfagi\Event;

use Symfony\Component\EventDispatcher\Event;
use TelNowEdge\Module\modfagi\Model\Fagi;

class FagiEvent extends Event
{
    const CREATE_PRE_BIND = 'fagi.create.pre_bind';
    const CREATE_PRE_SAVE = 'fagi.create.pre_save';
    const CREATE_POST_SAVE = 'fagi.create.post_save';

    const UPDATE_PRE_BIND = 'fagi.update.pre_bind';
    const UPDATE_PRE_SAVE = 'fagi.update.pre_save';
    const UPDATE_POST_SAVE = 'fagi.update.post_save';

    const DELETE_PRE_SAVE = 'fagi.delete.pre_save';
    const DELETE_POST_SAVE = 'fagi.delete.post_save';

    const DUPLICATE_PRE_SAVE = 'fagi.duplicate.pre_save';
    const DUPLICATE_POST_SAVE = 'fagi.duplicate.post_save';

    private $fagi;

    public function __construct(Fagi $fagi)
    {
        $this->fagi = $fagi;
    }

    public function getFagi()
    {
        return $this->fagi;
    }
}
