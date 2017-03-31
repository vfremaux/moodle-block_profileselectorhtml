<?php

block_profileselector_eval($expression, $inputs, &$result) {

    extract($input);

    /*
     * Add a real $ to expression so if expression is f.e.
     * imagecount > 2
     * then we evaluate :
     * "$result = $imagecount > 2"
     */
    eval("\$result = {$expression};");

}