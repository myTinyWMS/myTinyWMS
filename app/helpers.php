<?php

/**
 * @param $value
 * @return string
 */
function formatPrice($value) {
    return !empty($value) ? number_format($value, 2, ',', '.') . " &euro;" : '';
}

function parsePrice($value) {
    return floatval(str_replace(',', '.', $value)) * 100;
}