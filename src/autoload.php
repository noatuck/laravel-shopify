<?php
/*
 * Long story short, we need to support the old namespace of Osiset\BasicShopifyAPI
 *   which has since changed to Gnikyt\BasicShopifyAPI
 *
 * Inspired by https://stackoverflow.com/a/74236530/8292439
 */
spl_autoload_register(function (string $className) {
    // Only handle the old namespace
    if (! str_starts_with($className, 'Osiset\\')) {
        return;
    }

    $newClassName = str_replace('Osiset\\', 'Gnikyt\\', $className);

    if (class_exists($newClassName)) {
        class_alias($newClassName, $className);
    }
});
