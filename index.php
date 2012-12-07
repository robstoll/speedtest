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

function getFiles($path, $namespace) {
    $arr = array();
    if ($handle = \opendir($path)) {
        /* This is the correct way to loop over the directory. */
        while (($entry = readdir($handle)) !== false) {
            if ($entry != "." && $entry != ".." && \substr($entry, -4) == '.php') {
                $arr[$path . $entry] = $namespace . \substr($entry, 0, -4);
            }
        }
        closedir($handle);
    }
    return $arr;
}

$arr = getFiles(__DIR__ . '/src/ch/tutteli/speedtest/', 'ch\tutteli\speedtest\\');
unset($arr[__DIR__ . '/src/ch/tutteli/speedtest/ASpeedTest.php']);

$options = '<option value="0">Please select</option>';
foreach ($arr as $file) {
    $options .= '<option>' . $file . '</option>';
}
$jsOptions = str_replace('\\','\\\\',$options);
?>
<html>
    <head>
        <title>Speed Test - tutteli.ch</title>
        <script type="text/javascript" src="jquery.min.js"></script>
        <script type="text/javascript">
            var testCounter=3;
            function addTest(){
                $('#tests').append(
                    $('<tr><td>Test '+testCounter +'.1</td><td><select name="tests[]"><?php echo $jsOptions ?></select></td></tr>'+
                    '<tr><td>Test '+testCounter+'.2</td><td><select name="tests[]"><?php echo $jsOptions; ?></select></td></tr>')
                );
                ++testCounter;
            }
        </script>
        <style type="text/css">
            body, table{
                font-family:"arial";
                font-size:12px;
            }
        </style>
    </head>
    <body>
        <h1>Speed Test</h1>
        <form method="post" action="run.php">
            <div style="float:left;margin-right: 20px;">
                <table id="tests" style="border:0">
                    <tr>
                        <td>How many runs:</td><td> <input type="text" name="howManyRuns" value="10"/></td>
                    </tr>
                    <tr><td>Test 1.1</td><td><select name="tests[]"><?php echo $options; ?></select></td></tr>
                    <tr><td>Test 1.2</td><td><select name="tests[]"><?php echo $options; ?></select></td></tr>
                    <tr><td>Test 2.1</td><td><select name="tests[]"><?php echo $options; ?></select></td></tr>
                    <tr><td>Test 2.2</td><td><select name="tests[]"><?php echo $options; ?></select></td></tr>    
                </table>
                <br/>
            </div>
            <div>
                <input type="submit" value="Run!" style="width:100px;height:50px;"/>
                <button style="padding:2px;display: block;margin-top:10px;" onclick="addTest();return false;"><img src="add.png" onclick='' style="padding:0;padding-top:2px;"/>Add a test</button>
            </div>
            <div style="clear:both"></div>
        </form>
    </body>
</html>
