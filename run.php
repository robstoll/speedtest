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

$howManyRuns = (isset($_POST['howManyRuns']) && $_POST['howManyRuns'] > 0) ? $_POST['howManyRuns'] : 10;
if ($howManyRuns % 2 != 0) {
    ++$howManyRuns;
}
$halfOfTheRuns = $howManyRuns / 2;
$output = isset($_POST['output']) ? $_POST['output'] : 'output';
$tests = (isset($_POST['tests']) && is_array($_POST['tests'])) ? $_POST['tests'] : array();
$i = 0;
$jsTests = '';
$tmpTests = array();
foreach ($tests as $test) {
    if ($test != '0') {
        if ($i != 0) {
            $jsTests .= ',';
        }
        $jsTests .= '"'.\str_replace('\\', '\\\\', $test).'"';
        $i = 1;
        $tmpTests[] = $test;
    }
}
$tests = $tmpTests;
if ($i == 0) {
    die('No tests without tests ;) - ensure you have choosen at least on test.<br/><a href="index.php">Go Back!</a>');
}
?>
<html>
    <head>
        <title>Speed Test - Tutteli.ch</title>
        <script type="text/javascript" src="jquery.min.js"></script>
        <script type="text/javascript" src="chiCalc.js"></script>
        <script type="text/javascript">
            
            var tests = [<?php echo $jsTests; ?>];
            var totalRuns = <?php echo $howManyRuns; ?>;
            var halfOftheRuns = <?php echo $halfOfTheRuns; ?>;
            var length = tests.length;
            var runCounter1 = 0;
            var runCounter2 = 0;
        </script>
        <script type="text/javascript" src="run.js"></script>
        <style type="text/css">
            body{
                overflow-x:auto;

            }
            body, table{
                font-family:"arial";
                font-size:12px;
            }
            table{
                border-spacing: 0;
            }
            th{
                text-align: left;
                padding-right:20px;
            }
            th,
            td{
                margin:0;
                border:0;
                padding-bottom: 4px;
                padding-top: 4px;
                padding-left:5px;
            }
            .output{
                float:left;
                height:640px;
                border:1px dotted black;
            }
            .output textarea{
                width: 245px;
                height: 580px;
            }
            .output .test{
                float:left;
                margin-right:10px;
            }
            .copyright{
                color:#CCC;
            }
            .copyright a{
                color:#CCC;
            }
            .examine{
                text-align:right;
                margin-top:10px;
                margin-right:10px;
            }
            .analysis{
                margin-top:20px;
                line-height:1.5em;
            }
            .better{
                background-color:#009933;
            }
            .gap th,
            .gap td{
                padding-top:20px;
            }

        </style>
    </head>
    <body>
        <div class="copyright">Copyright by <a href="mailto:rstoll@tutteli.ch">Robert Stoll</a> - licensed under the <a href="http://www.apache.org/licenses/LICENSE-2.0">Apache License 2.0</a></div>
        <h1><span id="done" style="display:none">done!!!</span></h1>
        <?php
        $length = \count($tests);
        $count = 0;
        for ($i = 0; $i < $length; ++$i) {
            if ($count % 2 == 0) {
                ?>
                <div class="output">
                <?php }
                ?>
                <div class="test">
                    <?php echo $tests[$i]; ?><br/>
                    <textarea name="<?php echo ($count % 2 == 0) ? 'A' : 'B' ?>" id="output<?php echo $i; ?>"></textarea>
                </div>
                <?php if ($count % 2 == 1) { ?>
                    <div class="test">
                        <div class="analysis" id="analysis<?php echo ($i - 1).'-'.$i; ?>"></div>
                    </div>
                    <div style="clear:both"></div>
                    <div class="examine"><input type="button" onclick="examineValuesAnalyseAndShowResults('<?php echo $i - 1; ?>',<?php echo $i; ?>)" value="analyse"/></div>
                </div>
                <?php
            }
            ++$count;
        }
        ?>
    </body>
</html>
