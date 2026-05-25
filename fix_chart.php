<?php
$f = 'd:\OSPanel\domains\localhost\nblogistics\public\lib\chart\chart.min.js';
$c = file_get_contents($f);
$c = str_replace(' */const r=', 'const r=', $c);
file_put_contents($f, $c);
echo "Fixed";
