<?php

declare(strict_types=1);

/**
 * Returns the import map for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "preload" set to true for any modules that are loaded on the initial
 *     page load to help the browser download them earlier.
 *
 * The "importmap:require" command can be used to add new entries to this file.
 *
 * This file has been auto-generated by the importmap commands.
 */
return [
    'app' => [
        'entrypoint' => true,
        'path' => 'app.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => '@symfony/stimulus-bundle/loader.js',
    ],
];
