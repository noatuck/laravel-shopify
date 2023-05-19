<?php
/*
 * Long story short, we need to support the old namespace of Osiset\BasicShopifyAPI
 *   which has since changed to Gnikyt\BasicShopifyAPI
 */
spl_autoload_register(function (string $className) {
    // Figure out the namespace

    // Only handle the old namspace
    if (! str_starts_with($className, 'Osiset')) {
        return;
    }

    $newClassName = str_replace('Osiset', 'Gnikyt', $className);
    if (class_exists($newClassName)) {
        class_alias($newClassName, $className);
    }
});
