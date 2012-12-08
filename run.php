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

$howManyRuns = (isset($_POST['howManyRuns']) && $_POST['howManyRuns'] > 0) ? $_POST['howManyRuns'] : 10;
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
        $jsTests .= '"' . \str_replace('\\', '\\\\', $test) . '"';
        $i = 1;
        $tmpTests[]=$test;
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
        <script type="text/javascript">           
            var i=0;
            var j=0;
            var tests = [<?php echo $jsTests; ?>];
        var length = tests.length;
        function run(){
            $.get('test.php', 'test='+tests[j], function(data){
                $('#output'+j).html($('#output'+j).html()+data+'\n');
                ++j;
                if(j >= length){
                    ++i;
                    j=0;
                }
                if(i >=  <?php echo $howManyRuns; ?>){
                    $('#done').css('display','block');
                    return;
                }
                window.setTimeout(run,1);
            });
        }
        $(document).ready(function(){
            run();
        });
        </script>
        <style type="text/css">
            body{
                overflow-x:auto;

            }
            body, table{
                font-family:"arial";
                font-size:12px;
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
            div.copyright{
                color:#CCC;
            }
            div.copyright a{
                color:#CCC;
            }
            div.examine{
                text-align:right;
                margin-top:10px;
                margin-right:10px;
            }
        </style>
    </head>
    <body>
        <div class="copyright">Copyright by <a href="mailto:rstoll@tutteli.ch">Robert Stoll</a> - licensed under the <a href="http://www.apache.org/licenses/LICENSE-2.0">Apache License 2.0</a></div>
        <h1><span id="done" style="display:none">done!!!</span></h1>
        <?php
        $length = \count($tests);
        $count=0;
        for ($i = 0; $i < $length; ++$i) {
            if($count%2==0){?>
                <div class="output">
                    <form target="_blank" action="http://www.physics.csbsju.edu/cgi-bin/stats/t-test_paste.n.plot" method="post">
            <?php }
        ?>
                        <div class="test">
            <?php echo $tests[$i]; ?><br/>
                <textarea name="<?php echo ($count%2==0) ? 'A':'B'?>" id="output<?php echo $i; ?>"></textarea>
                        </div>
        <?php     
            if($count%2==1){?>
                        <div style="clear:both"></div>
                        <div class="examine"><input type="submit" value="analyse"/></div>
                    </form>
                </div>
            <?php }
            ++$count;
        } ?>
    </body>
</html>
