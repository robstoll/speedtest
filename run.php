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
if($howManyRuns % 2 !=0){
    ++$howManyRuns;
}
$halfOfTheRuns = $howManyRuns/2;
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
        <script type="text/javascript">           
            var i=0;
            var j=0;
            var tests = [<?php echo $jsTests; ?>];
            var length = tests.length;
            function run(){
                $.get('test.php', 'test='+tests[j], function(data){
                    $('#output'+j).html($('#output'+j).html()+data+'\n');
                    if(i < <?php echo $halfOfTheRuns; ?>){
                        ++j;
                        if(j >= length){
                            ++i;
                            if(i < <?php echo $halfOfTheRuns; ?>){
                                j=0;
                            }else{
                                j=length-1;
                            }
                        }
                    }else{
                        --j;
                        if(j < 0){
                            ++i;
                            j=length-1;
                        }
                    }
                    if(i >=  <?php echo $howManyRuns; ?>){
                        $('#done').css('display','block');
                        return;
                    }
                    run();
                    
                });
            }
            $(document).ready(function(){
                run();
            });
            
            function analyse(idTestA, idTestB){
                var valuesA = $("#output"+idTestA).val().split("\n");
                var valuesB= $("#output"+idTestB).val().split("\n");
                valuesA.sort();
                valuesB.sort();
                
                var max = valuesA.length <= valuesB.length ? valuesA.length : valuesB.length
                
                var pointsA = 0;
                var pointsB = 0;
                var countA = 1; //index 0 is ""
                var countB = 1; //index 0 is ""
                var rank = 1;
                
                var totalA=0;
                var totalB=0;
                
                //todo calculate median
                while(countA < max || countB < max){
                    var valueA = countA < max ? parseFloat(valuesA[countA]) : 10000000; 
                    var valueB = countB < max ? parseFloat(valuesB[countB]) : 10000000;
                    if(valueA < valueB){
                        pointsA += rank;
                        totalA += valueA;
                        ++countA;
                    }else if(valueA >  valueB){
                        pointsB += rank;
                        totalB += valueB;
                        ++countB;
                    }else{
                        pointsA += rank;
                        pointsB += rank;
                        totalA += valueA;
                        totalB += valueB;
                        ++countA;
                        ++countB;
                    }
                    ++rank;
                }
                var averageA = totalA / max;
                var averageB = totalB / max;
                var medianA = calculateMedian(valuesA,max);
                var medianB = calculateMedian(valuesB, max);
                
                $("#analysis"+idTestA).html(formatAnalysisResult('A', pointsA, totalA, averageA, medianA));
                $("#analysis"+idTestB).html(formatAnalysisResult('B', pointsB, totalB, averageB, medianB));                
            }
            function calculateMedian(values, max){
                var middle = (max-1)/2; //max - 1 since index 0 is ""
                var isEven = (max-1) % 2 == 0; //is not really needed, since we enforce an even number of tests
                if(isEven){
                    return (parseFloat(values[middle]) + parseFloat(values[middle+1]))/2.0;
                }else{
                    return parseFloat(values[middle]);
                }
            }
            function formatAnalysisResult(test, points, total, average, median){
                return '<table>'
                        + '<tr><th>Points ' + test + ':</th><td>' + points + '</td></tr>'
                        + '<tr><th>Total '+ test + ':</th><td>' + total.toExponential() + '</td></tr>'
                        + '<tr><th>Average '+ test + ':</th><td>' + average.toExponential() + '</td></tr>'
                        + '<tr><th>Median '+ test + ':</th><td>' + median.toExponential() + '</td></tr>'
                    +'</table>';
            }

        </script>
        <style type="text/css">
            body{
                overflow-x:auto;

            }
            body, table{
                font-family:"arial";
                font-size:12px;
            }
            th{
                text-align: left;
                width:65px;
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
            div.analysis{
                margin-top:20px;
                line-height:1.5em;
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
                        <div class="analysis" id="analysis<?php echo $i-1; ?>"></div>
                        <div class="analysis" id="analysis<?php echo $i; ?>"></div>
                    </div>
                    <div style="clear:both"></div>
                    <div class="examine"><input type="button" onclick="analyse('<?php echo $i-1; ?>',<?php echo $i; ?>)" value="analyse"/></div>
                </div>
            <?php
            }
            ++$count;
        }
        ?>
    </body>
</html>
