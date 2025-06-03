<?php

/**
 * Original authored by @andrefelipe
 * https://github.com/andrefelipe/vite-php-setup
 *

/**
 * VUE_MODE 
 * The 'development' will use directly of main.js
 * 
 * VUE_PORT
 * Change this value according to the server port in vite.config.js
 */

function vite($entry)
{
    return js_tag($entry)
        . js_preload_imports($entry)
        . css_tag($entry);
}

function is_development()
{
    return env('VITE_MODE') == 'development';
}

// ----------------------
// Helpers to print tags
// ----------------------

function js_tag($entry)
{
    $url = is_development() 
            ? 'http://127.0.0.1:' . env('VITE_PORT') . '/' . $entry 
            : asset_url($entry);

    if (!$url) return '';
    return '<script type="module" crossorigin src="' . $url . '"></script>';
}

function js_preload_imports($entry)
{
    if (is_development()) return '';

    $urls = imports_urls($entry);
    
    $tags = '';
    foreach ($urls as $url) {
        $tags .= '<link rel="modulepreload" href="' . $url . '">';
    }

    return $tags;
}

function css_tag($entry)
{
    // not needed on dev, it's inject by Vite
    if (is_development()) return '';

    $urls = css_urls($entry);

    $tags = '';
    foreach ($urls as $url) {
        $tags .= '<link rel="stylesheet" href="' . $url . '">';
    }

    return $tags;
}

// ------------------------
// Helpers to locate files
// ------------------------

function get_manifest()
{
    $content = is_development() ?
        file_get_contents(APPPATH . '../public/assets/vue/manifest.json') :
        file_get_contents(APPPATH . '../../../httpdocs/diarimu/assets/vue/.vite/manifest.json') ;

    return json_decode($content, true);
}

function asset_url($entry)
{
    $manifest = get_manifest();

    return isset($manifest[$entry])
        ? base_url('assets/vue/' . $manifest[$entry]['file'])
        : '';
}

function imports_urls($entry)
{
    $urls = [];
    $manifest = get_manifest();

    if (!empty($manifest[$entry]['imports'])) {
        foreach ($manifest[$entry]['imports'] as $imports) {
            $urls[] = base_url('assets/vue/' . $manifest[$imports]['file']);
        }
    }

    return $urls;
}

function css_urls($entry)
{
    $urls = [];
    $manifest = get_manifest();

    if (!empty($manifest[$entry]['css'])) {
        foreach ($manifest[$entry]['css'] as $file) {
            $urls[] = base_url('assets/vue/' . $file);
        }
    }
    
    return $urls;
}