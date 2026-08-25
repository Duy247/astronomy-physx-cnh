<?php

declare(strict_types=1);

function astro_render_object(string $dataFile): void
{
    $data = astro_load_json($dataFile);
    $pageTitle = (string) ($data['title'] ?? astro_config('site_name'));
    $object = (string) ($data['object'] ?? 'Astronomy object');
    $image = is_array($data['image'] ?? null) ? $data['image'] : [];
    $details = is_array($data['details'] ?? null) ? $data['details'] : [];
    require ASTRO_ROOT . '/includes/header.php';
    require ASTRO_ROOT . '/includes/image_card.php';
    require ASTRO_ROOT . '/includes/footer.php';
}

function astro_render_gallery(string $dataFile, string $heading, string $subtitle = 'Click on a photo for a larger image and details.'): void
{
    $gallery = astro_load_json($dataFile);
    $pageTitle = $heading . ' — ' . astro_config('site_name');
    require ASTRO_ROOT . '/includes/header.php';
    require ASTRO_ROOT . '/includes/gallery.php';
    require ASTRO_ROOT . '/includes/footer.php';
}

function astro_render_legacy_page(string $page): void
{
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $page)) {
        astro_not_found();
    }
    if (in_array(strtolower($page), [
        '178ed',
        '500mm',
        'equipment',
        'equipment_drives',
        'equipment_ota',
        'equipment_photoguide',
        'equipment_rc32',
    ], true)) {
        astro_redirect_home();
    }
    $path = ASTRO_ROOT . '/legacy/pages/' . $page . '.htm';
    if (!is_file($path)) {
        astro_not_found();
    }
    $html = (string) file_get_contents($path);
    preg_match('~<title[^>]*>(.*?)</title>~is', $html, $titleMatch);
    preg_match('~<body[^>]*>(.*?)</body>~is', $html, $bodyMatch);
    $pageTitle = trim(strip_tags(html_entity_decode($titleMatch[1] ?? $page))) ?: $page;
    $content = $bodyMatch[1] ?? $html;
    $content = preg_replace('~<(?:script|style)[^>]*>.*?</(?:script|style)>~is', '', $content) ?? '';
    $content = preg_replace(
        '~<a\b[^>]*href=(["\'])[^"\']*(?:index_fits\.htm|fits/[^"\']+\.FIT|\.mov)\1[^>]*>(.*?)</a>~is',
        '$2',
        $content
    ) ?? '';
    $content = preg_replace_callback('~\b(href|src)=(["\'])(?!https?:|mailto:|tel:|#|/)([^"\']+)\2~i', static function (array $match): string {
        return $match[1] . '=' . $match[2] . astro_url('/' . $match[3]) . $match[2];
    }, $content) ?? '';
    require ASTRO_ROOT . '/includes/header.php';
    echo '<main class="astro-card legacy-content" id="main-content"><section class="astro-section">' . astro_safe_html($content) . '</section></main>';
    require ASTRO_ROOT . '/includes/footer.php';
}
