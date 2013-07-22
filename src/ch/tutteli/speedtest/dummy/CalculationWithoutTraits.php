<?php

/*
 * Copyright 2013 Robert Stoll <rstoll@tutteli.ch>
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

namespace ch\tutteli\speedtest\dummy;

class CalculationWithoutTraits extends ACalculation {

    public function addition($num, $num2) {
        return $num + $num2;
    }

    public function subtract($num, $num2) {
        return $num - $num2;
    }

    public function multiply($num, $num2) {
        return $num * $num2;
    }

    public function divide($num, $num2) {
        return $num / $num2;
    }

}

?>
