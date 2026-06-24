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

function sanitizeSignatureHtml($html) {
    if (empty($html)) {
        return '';
    }

    $allowedTags = [
        'a' => ['href', 'target', 'rel'],
        'b' => [],
        'br' => [],
        'div' => [],
        'em' => [],
        'i' => [],
        'li' => [],
        'ol' => [],
        'p' => [],
        'span' => [],
        'strong' => [],
        'u' => [],
        'ul' => [],
    ];

    $document = new DOMDocument();
    $previousState = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?><div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previousState);

    $sanitizeNode = function (DOMNode $node) use (&$sanitizeNode, $allowedTags) {
        if (!($node instanceof DOMElement)) {
            return;
        }

        while ($node->firstChild) {
            $sanitizeNode($node->firstChild);
        }

        $tagName = strtolower($node->tagName);
        if (!array_key_exists($tagName, $allowedTags)) {
            $parent = $node->parentNode;
            if ($parent) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
            }

            return;
        }

        if ($node->hasAttributes()) {
            $attributes = [];
            foreach ($node->attributes as $attribute) {
                $attributes[] = $attribute->name;
            }

            foreach ($attributes as $attributeName) {
                if (!in_array($attributeName, $allowedTags[$tagName], true)) {
                    $node->removeAttribute($attributeName);
                }
            }
        }

        if ($tagName === 'a' && $node->hasAttribute('href')) {
            $href = trim($node->getAttribute('href'));
            if (!preg_match('/^(https?:|mailto:)/i', $href)) {
                $node->removeAttribute('href');
            }
        }

        if ($tagName === 'a' && $node->hasAttribute('target')) {
            $target = strtolower($node->getAttribute('target'));
            if (!in_array($target, ['_blank', '_self'], true)) {
                $node->removeAttribute('target');
            } elseif ($target === '_blank') {
                $node->setAttribute('rel', 'noopener noreferrer');
            }
        }
    };

    $root = $document->documentElement;
    if (!$root) {
        return '';
    }

    $sanitizeNode($root);

    $html = '';
    foreach ($root->childNodes as $childNode) {
        $html .= $document->saveHTML($childNode);
    }

    return $html;
}
