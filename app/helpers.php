<?php

/**
 * @param $value
 * @return string
 */
function formatPrice($value) {
    return !empty($value) ? number_format(($value / 100), 2, ',', '.') . " &euro;" : '';
}