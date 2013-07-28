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

function run() {
    $.get('test.php', 'test=' + tests[runCounter2], function(data) {
        $('#output' + runCounter2).html($('#output' + runCounter2).html() + data + '\n');
        if (runCounter1 < halfOftheRuns) {
            ++runCounter2;
            if (runCounter2 >= length) {
                ++runCounter1;
                if (runCounter1 < halfOftheRuns) {
                    runCounter2 = 0;
                } else {
                    runCounter2 = length - 1;
                }
            }
        } else {
            --runCounter2;
            if (runCounter2 < 0) {
                ++runCounter1;
                runCounter2 = length - 1;
            }
        }
        $("#status").html('Run #' + runCounter1);
        if (runCounter1 >= totalRuns) {
            $('#status').html(runCounter1 + ' done :-) &nbsp; &nbsp; <button onclick="analyseAll()">analyse all</button>');
            if (shallAnalyseAllAfterRun) {
                analyseAll();
            }
            return;
        }
        run();
    });
}
$(document).ready(function() {
    run();
});

var TestData = function(id) {
    this.id = id;
    this.values = [];
    this.rankSum = 0;
    this.totalTime = 0;
    this.medianTime = 0;
    this.averageTime = 0;
    this.averageTimePlusMinus5PercentAwayFromMedian = 0;
    this.count = 0;
};

var KruskalWallisData = function() {
    this.ranks = 0;
    this.rankSumTotal = 0;
    this.tieCoefficient = 0;
    this.expectedAverageRankSum = 0;
    this.H = 0;
    this.D = 0;
    this.adjustedH = 0;
    this.significanceLevel = 0;

    this.A = new TestData("A");
    this.B = new TestData("B");
}

function analyseAll() {
    $(".examine input[type=button]").click();
}

function examineValuesAnalyseAndShowResults(idTestA, idTestB) {
    var valuesA = $("#output" + idTestA).val().split("\n");
    var valuesB = $("#output" + idTestB).val().split("\n");
    valuesA.sort(function(a, b) {
        return a - b
    });
    valuesB.sort(function(a, b) {
        return a - b
    });

    var data = new KruskalWallisData();
    data.A.values = extractArrayFromToConvertValuesToFloat(valuesA, 1, valuesA.length);
    data.B.values = extractArrayFromToConvertValuesToFloat(valuesB, 1, valuesB.length);

    data = analyseAndAugmentData(data);

    $("#analysis" + idTestA + '-' + idTestB).html(formatAnalysisResult(data));
}

/**
 * Get the array from an existing array, no checks for out of bound etc.
 * @param {array} array
 * @param {int} from
 * @param {int} to
 * @returns {Array}
 */
function extractArrayFromToConvertValuesToFloat(array, from, to) {
    var arr = new Array();
    for (var i = from; i < to; ++i) {
        arr.push(parseFloat(array[i]));
    }
    return arr;
}

/**
 * @param {KruskalWallisData} kruskalWallisData
 * @returns {KruskalWallisData}
 */
function analyseAndAugmentData(kruskalWallisData) {
    var rank = 1;
    var isTie = false;
    var tieValue = 0;

    var A = kruskalWallisData.A;
    var B = kruskalWallisData.B;

    while (A.count < A.values.length || B.count < B.values.length) {
        var valueA = A.count < A.values.length ? A.values[A.count] : 10000000;
        var valueB = B.count < B.values.length ? B.values[B.count] : 10000000;
        if (valueA < valueB) {
            isTie = analyseNext(A, rank);
            tieValue = A.values[A.count];
        } else if (valueA > valueB) {
            isTie = analyseNext(B, rank);
            tieValue = B.values[B.count];
        } else {
            tieValue = A.values[A.count];
            isTie = true;
        }
        if (isTie) {
            rank = analyseTieRanks(kruskalWallisData, tieValue, rank);
        }
        ++rank;
    }

    kruskalWallisData.ranks = --rank;
    kruskalWallisData.rankSumTotal = kruskalWallisData.ranks * (kruskalWallisData.ranks + 1) / 2;
    kruskalWallisData.expectedAverageRankSum = kruskalWallisData.rankSumTotal / kruskalWallisData.ranks;

    var a = Math.pow(A.rankSum, 2) / A.values.length;
    var b = Math.pow(B.rankSum, 2) / B.values.length;

    kruskalWallisData.H = (12 / (kruskalWallisData.ranks * (kruskalWallisData.ranks + 1))) * (a + b) - 3 * (kruskalWallisData.ranks + 1);

    kruskalWallisData.D = 1 - (kruskalWallisData.tieCoefficient) / ((kruskalWallisData.ranks - 1) * (kruskalWallisData.ranks) * (kruskalWallisData.ranks + 1));

    kruskalWallisData.adjustedH = kruskalWallisData.H / kruskalWallisData.D;
    kruskalWallisData.significanceLevel = pochisq(kruskalWallisData.adjustedH, 1);

    calculateTestDataSpecifics(A);
    calculateTestDataSpecifics(B);

    return kruskalWallisData;
}

/**
 * @param {TestData} testData
 * @param {int} rank The next rank
 */
function analyseNext(testData, rank) {
    if (isNextValueNotTheSame(testData)) {
        testData.rankSum += rank;
        testData.totalTime += testData.values[testData.count];
        ++testData.count;
        return false;
    }
    return true;
}

/**
 * @param {TestData} testData
 * @returns {bool}
 */
function isNextValueNotTheSame(testData) {
    return testData.count + 1 >= testData.values.length
            || testData.values[testData.count] != testData.values[testData.count + 1];
}


/**
 * @param {KruskalWallisData} kruskalWallisData
 * @param {float} tieValue
 * @param {int} rank
 * @returns {int} lastRank
 */
function analyseTieRanks(kruskalWallisData, tieValue, rank) {

    var res = calculateTie(kruskalWallisData.A, tieValue, rank);
    rank = res.rank;
    var rankSum = res.rankSum;
    var count = res.count;
    var tmpCountA = res.tmpCount;
    kruskalWallisData.A.count += tmpCountA;

    res = calculateTie(kruskalWallisData.B, tieValue, rank);
    rank = res.rank;
    rankSum += res.rankSum;
    count += res.count;
    var tmpCountB = res.tmpCount;
    kruskalWallisData.B.count += tmpCountB;

    kruskalWallisData.A.rankSum += tmpCountA * rankSum / count;
    kruskalWallisData.B.rankSum += tmpCountB * rankSum / count;
    kruskalWallisData.tieCoefficient += (Math.pow(count, 2) - 1) * count;
    return rank - 1;
}

var TieData = function(theCount, theRankSum, theRank, theTmpCount) {
    this.count = theCount;
    this.rankSum = theRankSum;
    this.rank = theRank;
    this.tmpCount = theTmpCount;
}

/**
 * 
 * @param {TestData} testData
 * @param {float} tieValue
 * @param {int} rank
 * @returns {TieData}
 */
function calculateTie(testData, tieValue, rank) {
    var tmpCount = 0;
    var count = 0;
    var rankSum = 0;
    while (testData.count + tmpCount < testData.values.length && testData.values[testData.count + tmpCount] === tieValue) {
        testData.totalTime += testData.values[testData.count + tmpCount];
        ++tmpCount;
        ++count;
        rankSum += rank;
        ++rank;
    }
    return new TieData(count, rankSum, rank, tmpCount);
}

/**
 * @param {TestData} testData
 */
function calculateTestDataSpecifics(testData) {
    testData.averageTime = testData.totalTime / testData.values.length;
    testData.medianTime = calculateMedian(testData);
    testData.averageTimePlusMinus5PercentAwayFromMedian = calculateAverageOfPlusMinus5PercentMedian(testData);
}

/**
 * @param {TestData} testData
 */
function calculateMedian(testData) {
    var middle = testData.values.length / 2 - 1; //-1 since index starts from 0 
    var isEven = (testData.values.length) % 2 == 0; //is not really needed, since we enforce an even number of tests
    if (isEven) {
        return (testData.values[middle] + testData.values[middle + 1]) / 2.0;
    } else {
        middle += 0.5;
        return testData.values[middle];
    }
}
/**
 * @param {TestData} testData
 */
function calculateAverageOfPlusMinus5PercentMedian(testData) {
    var middle = testData.values.length / 2 - 1; //-1 since index starts from 0 
    var fivePercent = Math.floor(testData.values.length / 100 * 5);
    if (fivePercent != 0) {
        var timeSum = 0;
        for (var i = middle - fivePercent; i < middle + fivePercent; ++i) {
            timeSum += testData.values[i];
        }
        return timeSum / (fivePercent * 2);
    }
    return -1;
}


/**
 * @param {KruskalWallisData} kruskalWallisData
 * @returns {String}
 */
function formatAnalysisResult(kruskalWallisData) {
    return '<h2>Kruskal-Wallis Test</h2>\
            <table>\
                <tr ' + (kruskalWallisData.significanceLevel < 0.05 ? 'class="better"' : '') + '>\
                    <th>Significance level</sup></th><td>' + formatSignificanceLevel(kruskalWallisData.significanceLevel) + '</td>\
                </tr>\
                <tr><th><a onclick="$(this).hide();$(\'.runDetail\').show()">Further test details</th></tr>\
                <tr class="runDetail"><th>Total Samples</th><td>\
                    ' + kruskalWallisData.A.values.length + ' + ' + kruskalWallisData.B.values.length + ' = ' + (kruskalWallisData.A.values.length + kruskalWallisData.B.values.length) + '\
                </td></tr>\
                <tr class="runDetail"><th>Total Points</th><td>' + kruskalWallisData.rankSumTotal + '</td></tr>\
                <tr class="runDetail"><th>Average Rank</th><td>' + kruskalWallisData.expectedAverageRankSum + '</td></tr>\
                <tr class="runDetail"><th>H</th><td>' + toExpIfSmall(kruskalWallisData.H) + '</td></tr>\
                <tr class="runDetail"><th>D</th><td>' + toExpIfSmall(kruskalWallisData.D) + '</td></tr>\
                <tr class="runDetail"><th>Adjusted H</th><td>' + toExpIfSmall(kruskalWallisData.adjustedH) + '</td></tr>\
                <tr class="runDetail"><th>df</th><td>1</td></tr>\
                ' + formatFasterOne(kruskalWallisData) + '\
                <tr><th>&nbsp;</th><td>&nbsp;</td></tr>\
                ' + formatTestData(kruskalWallisData.A, kruskalWallisData.B) + '\
                <tr><th>&nbsp;</th><td>&nbsp;</td></tr>\
                ' + formatTestData(kruskalWallisData.B, kruskalWallisData.A) + '\
            </table>';
}
/**
 * @param {KruskalWallisData} kruskalWallisData
 */
function formatFasterOne(kruskalWallisData) {
    var res = calculateFaster(kruskalWallisData);
    return '<tr><th>' + res.idFast + '\'s median is x sec. faster than ' + res.idSlow + '\'s</th><td>' + toExpIfSmall(res.diff) + '</td></tr>\
            <tr><th>' + res.idFast + '\'s median is x as fast as ' + res.idSlow + '\'s</th><td>' + res.timesFaster + '</td></tr>';
}

/**
 * @param {KruskalWallisData} kruskalWallisData
 */
function calculateFaster(kruskalWallisData) {
    if (kruskalWallisData.A.rankSum < kruskalWallisData.B.rankSum) {
        var diff = kruskalWallisData.B.medianTime - kruskalWallisData.A.medianTime;
        return {
            idFast: kruskalWallisData.A.id,
            idSlow: kruskalWallisData.B.id,
            diff: diff,
            timesFaster: kruskalWallisData.B.medianTime / kruskalWallisData.A.medianTime
        };
    } else {
        var diff = kruskalWallisData.A.medianTime - kruskalWallisData.B.medianTime;
        return {
            idFast: kruskalWallisData.B.id,
            idSlow: kruskalWallisData.A.id,
            diff: diff,
            timesFaster: kruskalWallisData.A.medianTime / kruskalWallisData.B.medianTime
        };
    }
}



/**
 * @param {TestData} testData
 * @param {TestData} compareData
 * @returns {String}
 */
function formatTestData(testData, compareData) {
    return  '<tr ' + (testData.rankSum < compareData.rankSum ? 'class="better"' : '') + '>\
                <th>Rank Sum ' + testData.id + ' :</th><td>' + testData.rankSum + '</td>\
            </tr>\
            <tr ' + (testData.medianTime < compareData.medianTime ? 'class="better"' : '') + '>\
                <th>Median Time ' + testData.id + ' :</th><td>' + toExpIfSmall(testData.medianTime) + '</td>\
            </tr>\
            <tr ' + (testData.values[0] < compareData.values[0] ? 'class="better"' : '') + '>\
                <th>fastest run ' + testData.id + ' :</th><td>' + toExpIfSmall(testData.values[0]) + '</td>\
            </tr>\
            <tr ' + (testData.values[testData.values.length-1] < compareData.values[testData.values.length-1] ? 'class="better"' : '') + '>\
                <th>slowest run ' + testData.id + ' :</th><td>' + toExpIfSmall(testData.values[testData.values.length-1]) + '</td>\
            </tr>\
            <tr ' + (testData.totalTime < compareData.totalTime ? 'class="better"' : '') + '>\
                <th>Total Time ' + testData.id + ' :</th><td>' + toExpIfSmall(testData.totalTime) + '</td>\
            </tr>\
            <tr ' + (testData.averageTime < compareData.averageTime ? 'class="better"' : '') + '>\
                <th>Average Time ' + testData.id + ' :</th><td>' + toExpIfSmall(testData.averageTime) + '</td>\
            </tr>\
            <tr ' + (testData.averageTimePlusMinus5PercentAwayFromMedian < compareData.averageTimePlusMinus5PercentAwayFromMedian ? 'class="better"' : '') + '>\
                <th><span title="Average time of the values +/- 5% away from the median">Average Time +/- 5% median</span> ' + testData.id + ' :</th>\
                <td>' + (testData.averageTimePlusMinus5PercentAwayFromMedian != -1 ? toExpIfSmall(testData.averageTimePlusMinus5PercentAwayFromMedian) : 'Not enought samples') + '</td>\
            </tr>';
}

function toExpIfSmall(number) {
    if (number < 0.001) {
        return number.toExponential();
    }
    return number;
}

function formatSignificanceLevel(number) {
    if (number == 0) {
        return '0 (< 2e-9)';
    }
    return toExpIfSmall(number);
}
