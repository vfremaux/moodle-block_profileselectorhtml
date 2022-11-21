<?php

function block_profileselectorhtml_eval($expression, $inputs, &$result) {

    if (!is_array($inputs)) {
        return;
    }

    extract($inputs);

    /*
     * Add a real $ to expression so if expression is f.e.
     * imagecount > 2
     * then we evaluate :
     * "$result = $imagecount > 2"
     */
    $evaled = "\$result = {$expression};";
    eval($evaled);
}