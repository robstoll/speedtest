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

namespace ch\tutteli\speedtest\template;

abstract class AMatrixAddition extends ASpeedTest {

    private $matrix1;
    private $matrix2;

    abstract protected function getValue();

    protected function setup() {
        $this->matrix1 = array();
        $this->matrix2 = array();
        $value = $this->getValue();

        for ($i = 0; $i < 50; ++$i) {
            $this->matrix1[$i] = array();
            $this->matrix2[$i] = array();

            for ($j = 0; $j < 50; ++$j) {
                $this->matrix1[$i][$j] = $value;
                $this->matrix2[$i][$j] = $value;
            }
        }
    }

    protected function run() {
        $matrix3 = array();
        for ($i = 0; $i < 50; ++$i) {
            $matrix3[$i] = array();
            for ($j = 0; $j < 50; ++$j) {
                $matrix3[$i][$j] = $this->matrix1[$i][$j] + $this->matrix2[$i][$j];
            }
        }
    }

}

?>
