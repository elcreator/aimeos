<?php

$root = dirname(__DIR__);
$files = [
    $root . '/vendor/aimeos/ai-client-html/themes/client/html/default/aimeos.js',
    $root . '/public/vendor/shop/themes/default/aimeos.js',
];

foreach ($files as $file) {
    if (!is_file($file)) {
        throw new RuntimeException('Aimeos theme asset was not published: ' . $file);
    }

    $content = file_get_contents($file);
    $eol = str_contains($content, "\r\n") ? "\r\n" : "\n";
    $needle = implode($eol, [
        "\t\tconst url = this.sameOriginUrl(value);",
        "\t\tif(!url) throw new Error('Invalid widget endpoint');",
        '',
        "\t\tconst follow = type === 'html' && followRedirects === true;",
    ]);
    $replacement = implode($eol, [
        "\t\tconst url = this.sameOriginUrl(value);",
        "\t\tif(!url) throw new Error('Invalid widget endpoint');",
        "\t\tif(type === 'html') {",
        "\t\t\turl.pathname = url.pathname.replace(/\\/shop\\/basket$/, '/basket-fragment');",
        "\t\t}",
        '',
        "\t\tconst follow = type === 'html' && followRedirects === true;",
    ]);

    if (str_contains($content, "url.pathname = url.pathname.replace(/\\/shop\\/basket$/")) {
        continue;
    }

    if (substr_count($content, $needle) !== 1) {
        throw new RuntimeException('Could not safely patch Aimeos basket AJAX requests in ' . $file);
    }

    file_put_contents($file, str_replace($needle, $replacement, $content));
}
