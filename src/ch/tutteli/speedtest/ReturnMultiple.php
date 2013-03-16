<?php

/*
 * Copyright 2012 Robert Stoll <rstoll@tutteli.ch>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 */

namespace ch\tutteli\speedtest;

class ReturnMultiple extends template\ASpeedTest
{

    protected function run() {
        $this->foo(5);
        $this->foo(20);
    }

    private function foo($a) {
        for ($i = 0; $i < 10; ++$i) {
            if ($i == $a) {
                return true;
            }
        }
        return false;
    }

}

?>
