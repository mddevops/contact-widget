<?php

namespace SiteApps\ContactWidget\Services\Social;

class SvgCleaner
{
    public static function clean(string $svg): string
    {
        $svg = trim($svg);

        if ($svg === '') {
            return $svg;
        }

        $document = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $document->loadXML($svg, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->documentElement;

        if (! $root instanceof \DOMElement || strtolower($root->tagName) !== 'svg') {
            return $svg;
        }

        self::removeViewportClearPaths($root);

        $isStrokeIcon = self::detectStrokeIcon($root);

        self::stripRootDimensions($root);
        $root->removeAttribute('class');

        if ($isStrokeIcon) {
            self::normalizeStrokeIcon($root);
        } else {
            self::normalizeFillIcon($root);
        }

        return $document->saveXML($root) ?: $svg;
    }

    protected static function removeViewportClearPaths(\DOMElement $root): void
    {
        $pathsToRemove = [];

        foreach ($root->getElementsByTagName('path') as $path) {
            if (! $path instanceof \DOMElement) {
                continue;
            }

            $d = self::normalizePathData($path->getAttribute('d'));

            if ($d === '' || self::isViewportClearPath($d, $path)) {
                $pathsToRemove[] = $path;
            }
        }

        foreach ($pathsToRemove as $path) {
            $path->parentNode?->removeChild($path);
        }
    }

    protected static function isViewportClearPath(string $d, \DOMElement $path): bool
    {
        if (preg_match('/^M\s*0\s+0\s*[hH]\s*24\s*[vV]\s*24\s*[hH]\s*0\s*[zZ]?\s*$/', $d)) {
            return true;
        }

        $stroke = strtolower(trim($path->getAttribute('stroke')));
        $fill = strtolower(trim($path->getAttribute('fill')));

        return $stroke === 'none' && ($fill === 'none' || $fill === '');
    }

    protected static function normalizePathData(string $d): string
    {
        return trim(preg_replace('/\s+/', ' ', $d) ?? '');
    }

    protected static function detectStrokeIcon(\DOMElement $root): bool
    {
        $rootStroke = strtolower(trim($root->getAttribute('stroke')));

        if ($rootStroke !== '' && $rootStroke !== 'none') {
            return true;
        }

        foreach ($root->getElementsByTagName('*') as $element) {
            if (! $element instanceof \DOMElement) {
                continue;
            }

            $stroke = strtolower(trim($element->getAttribute('stroke')));

            if ($stroke !== '' && $stroke !== 'none') {
                return true;
            }
        }

        return false;
    }

    protected static function stripRootDimensions(\DOMElement $root): void
    {
        foreach (['width', 'height'] as $attribute) {
            $root->removeAttribute($attribute);
        }
    }

    protected static function normalizeStrokeIcon(\DOMElement $root): void
    {
        $root->setAttribute('fill', 'none');
        $root->setAttribute('stroke', 'currentColor');

        if (! $root->hasAttribute('stroke-width')) {
            $root->setAttribute('stroke-width', '2');
        }

        foreach (['stroke-linecap', 'stroke-linejoin'] as $attribute) {
            if ($root->hasAttribute($attribute)) {
                continue;
            }

            $root->setAttribute($attribute, 'round');
        }

        self::normalizeShapeElements($root, stroke: true);
    }

    protected static function normalizeFillIcon(\DOMElement $root): void
    {
        $root->setAttribute('fill', 'none');

        self::normalizeShapeElements($root, stroke: false);
    }

    protected static function normalizeShapeElements(\DOMElement $root, bool $stroke): void
    {
        $shapeTags = ['path', 'circle', 'rect', 'ellipse', 'polygon', 'polyline', 'line'];

        foreach ($shapeTags as $tag) {
            foreach ($root->getElementsByTagName($tag) as $element) {
                if (! $element instanceof \DOMElement) {
                    continue;
                }

                foreach (['width', 'height', 'fill', 'stroke'] as $attribute) {
                    $element->removeAttribute($attribute);
                }

                if ($stroke) {
                    $element->setAttribute('fill', 'none');
                } else {
                    $element->setAttribute('fill', 'currentColor');
                }
            }
        }
    }
}
