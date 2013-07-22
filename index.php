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

function getFiles($namespaces) {
    $arr = array();
    foreach ($namespaces as $namespace) {
        $path = __DIR__ . '/src/' . $namespace;

        if ($handle = \opendir($path)) {
            /* This is the correct way to loop over the directory. */
            while (($entry = readdir($handle)) !== false) {
                if ($entry != "." && $entry != ".." && \substr($entry, -4) == '.php') {
                    $arr[] = $namespace .'\\'. \substr($entry, 0, -4);
                }
            }
            closedir($handle);
        }
    }
    return $arr;
}

$tests = getFiles(
        array(
            'ch\tutteli\speedtest',
        )
);

$options = '<option value="0">Please select</option>';
foreach ($tests as $test) {
    $className = str_pad(\substr($test,  \strrpos($test, "\\")+1),45, "_");
    $options .= '<option value="'.$test.'">' .$className.' '. $test . '</option>';
}
$jsOptions = str_replace('\\', '\\\\', $options);

$numberTest = isset($_GET['num']) && $_GET['num'] > 0 ? $_GET['num'] : 1;
?>
<html>
    <head>
        <title>Speed Test - tutteli.ch</title>
        <script type="text/javascript" src="jquery.min.js"></script>
        <script type="text/javascript">
            var testCounter=<?php echo $numberTest + 1 ?>;
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
            input[type=submit]{
                width:100px;
                height:50px;
            }
            button{
                display: block;
                margin-top:10px;
                padding:2px;
                width:100px
            }
            img{
                vertical-align:top;padding-right:5px;
            }
            div.copyright{
                color:#CCC;
            }
            div.copyright a{
                color:#CCC;
            }
            select{
                font-family: Courier New;
            }
        </style>
    </head>
    <body>
        <div class="copyright">Copyright by <a href="mailto:rstoll@tutteli.ch">Robert Stoll</a> - licensed under the <a href="http://www.apache.org/licenses/LICENSE-2.0">Apache License 2.0</a></div>
        <h1>Speedtest - powered by <a href="http://tutteli.ch">tutteli.ch</a></h1>
        <form method="post" action="run.php">
            <div style="float:left;margin-right: 20px;">
                <table id="tests" style="border:0">
                    <tr>
                        <td>How many runs:</td><td> <input type="text" name="howManyRuns" value="100"/></td>
                    </tr>
                    <?php for ($i = 1; $i <= $numberTest; ++$i) { ?>
                        <tr><td>Test <?php echo $i; ?>.1</td><td><select name="tests[]"><?php echo $options; ?></select></td></tr>
                        <tr><td>Test <?php echo $i; ?>.2</td><td><select name="tests[]"><?php echo $options; ?></select></td></tr>
                    <?php } ?>
                </table>
                <br/>
            </div>
            <div>
                <input type="submit" value="Run!"/>
                <button onclick="addTest();return false;"><img src="add.png"/>Add a test</button>
            </div>
            <div style="clear:both"></div>
        </form>
        
    </body>
</html>
