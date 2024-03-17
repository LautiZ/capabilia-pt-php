<?php
function esPrimo($numero) {
    if ($numero <= 1) {
        return false;
    }
    for ($i = 2; $i <= sqrt($numero); $i++) {
        if ($numero % $i == 0) {
            return false;
        }
    }
    return true;
}

for ($i = 1; $i <= 100; $i++) {
    if (esPrimo($i)) {
        echo $i, " [ES PRIMO]\n";
    } else {
        echo $i, "\n";
    }
}
?>
