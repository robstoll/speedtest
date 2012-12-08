Speedtest - a PHP framework to conduct speed tests.
===================================================
| Simple but very useful GUI to conduct speed tests with PHP and it is open source :)
| Do not use it in a productive environment since it can slow down your system and it has no security features at all.

| 

.. image:: https://bitbucket.org/rstoll/speedtest/downloads/Speedtest%20in%20action.png

| 

Wou want to write your own test? Simply add your source code of your test class to the src folder whereby the structure has to follow the convention one folder for each namespace and the filename must be the same as the class name.
For instance, ::

    com\exmaple\PreIncrement -> src\com\example\PreIncrement.php
    com\exmaple\PostIncrement -> src\com\exmaple\PostIncrement.php 
    
PreIncrement.php would contain ::

    class PreIncrement extends \ch\tutteli\speedtest\template\ASpeedTest{
        protected function run(){
            $i=0;
            ++$i;
        }
    }
	
And PostIncrement.php would contain ::
	
    class PostIncrement extends \ch\tutteli\speedtest\template\ASpeedTest{
        protected function run(){
            $i=0;
            $i++;
        }
    }

In order that your new test classes show up you have to modify index.php. Add your namespace to $tests as follows ::

    $tests = getFiles(
        array(
            'ch\tutteli\speedtest',
            'com\example\speedtest'
        )
    );

That's it, you should now see the classes on the start page.

Btw. Preincrement is slightly faster ca. 0.2 microseconds with ::

	t= 2.59
	sdev= 0.548E-06
	degrees of freedom =198
	The probability of this result, assuming the null hypothesis, is 0.010 


| 


| Copyright 2012 Robert Stoll <rstoll@tutteli.ch>
| 
| Licensed under the Apache License, Version 2.0 (the "License");
| you may not use this file except in compliance with the License.
| You may obtain a copy of the License at
| 
| http://www.apache.org/licenses/LICENSE-2.0
| 
| Unless required by applicable law or agreed to in writing, software
| distributed under the License is distributed on an "AS IS" BASIS,
| WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
| See the License for the specific language governing permissions and
| limitations under the License.