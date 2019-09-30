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

function formatPriceValue($value) {
    return number_format($value, 2, ',', '.');
}

function activeIfUri($uri) {
    return request()->is($uri) ? 'active' : '';
}