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

namespace ch\tutteli\speedtest\template;

abstract class ASpeedTest {

    /**
     * Implement here the test
     */
    abstract protected function run();

    /**
     * Overwrite method if you have a setup for your test
     * (same for both tests)
     */
    protected function setup(){
        
    }
    
     /**
     * Overwrite method if you have a setup for your test
     * (same for both tests)
     */
    protected function teardown(){
        
    }

    public function test() {
        $this->setup();
		$c = new \HRTime\StopWatch;
		$c->start();
		$this->run();
        $c->stop();
        $this->teardown();
		return $c->getElapsedTime(\HRTime\Unit::NANOSECOND);
    }

}

?>
