<?php

declare(strict_types=1);

const ASTRO_ROOT = __DIR__ . '/..';

function astro_config(?string $key = null): mixed
{
    static $config;
    $config ??= require ASTRO_ROOT . '/config/site.php';
    return $key === null ? $config : ($config[$key] ?? null);
}

function astro_base_path(): string
{
    return rtrim('/' . trim((string) astro_config('base_path'), '/'), '/');
}

function astro_url(string $path): string
{
    if ($path === '' || preg_match('~^(?:https?:|mailto:|tel:|#|data:)~i', $path)) {
        return $path;
    }
    return astro_base_path() . '/' . ltrim(str_replace('\\', '/', $path), '/');
}

function astro_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function astro_load_json(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException('Content file is unavailable.');
    }
    $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($data)) {
        throw new RuntimeException('Content file has an invalid root value.');
    }
    return $data;
}

function astro_safe_html(string $html): string
{
    $html = preg_replace('~<(?:script|iframe|object|embed)[^>]*>.*?</(?:script|iframe|object|embed)>~is', '', $html) ?? '';
    $html = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
    $html = preg_replace('/(?:href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\1/i', 'href="#"', $html) ?? '';
    return strip_tags($html, '<a><br><p><ul><ol><li><strong><b><em><i><span><small><code><pre><blockquote><table><thead><tbody><tr><th><td><img><figure><figcaption>');
}

function astro_render_detail(array $row): string
{
    if (array_key_exists('text', $row) || array_key_exists('links', $row)) {
        $output = nl2br(astro_escape((string) ($row['text'] ?? '')));
        $links = is_array($row['links'] ?? null) ? $row['links'] : [];
        $links = array_values(array_filter($links, static fn (array $link): bool => !preg_match(
            '~^https?://~i',
            (string) ($link['href'] ?? '')
        )));
        if ($links) {
            $output .= '<ul class="astro-detail-links">';
            foreach ($links as $link) {
                $rawHref = (string) ($link['href'] ?? '');
                $href = astro_url($rawHref);
                $label = (string) ($link['label'] ?? $href);
                $output .= '<li><a href="' . astro_escape($href) . '">' . astro_escape($label) . '</a></li>';
            }
            $output .= '</ul>';
        }
        return $output;
    }
    return astro_safe_html((string) ($row['value'] ?? ''));
}

function astro_is_equipment_detail(array $row): bool
{
    return in_array(strtolower(trim((string) ($row['label'] ?? ''))), [
        'instrument',
        'focal ratio',
        'camera',
        'guiding',
        'film',
    ], true);
}

function astro_redirect_home(): void
{
    header('Location: ' . astro_url('/index.php'), true, 302);
    exit;
}

function astro_log(Throwable $error): void
{
    $directory = (string) astro_config('storage_path') . '/logs';
    if (!is_dir($directory)) {
        @mkdir($directory, 0775, true);
    }
    @error_log(sprintf("[%s] %s\n", date(DATE_ATOM), $error), 3, $directory . '/application.log');
}

function astro_not_found(string $message = 'The requested astronomy resource was not found.'): never
{
    http_response_code(404);
    $pageTitle = 'Not Found — ' . astro_config('site_name');
    require ASTRO_ROOT . '/includes/header.php';
    echo '<main class="astro-card" id="main-content"><section class="astro-section"><h1>Not Found</h1><p>' . astro_escape($message) . '</p></section></main>';
    require ASTRO_ROOT . '/includes/footer.php';
    exit;
}

set_exception_handler(static function (Throwable $error): void {
    astro_log($error);
    http_response_code(500);
    $pageTitle = 'Application Error — ' . astro_config('site_name');
    require ASTRO_ROOT . '/includes/header.php';
    echo '<main class="astro-card" id="main-content"><section class="astro-section"><h1>Unable to display this page</h1><p>Please try again later.</p></section></main>';
    require ASTRO_ROOT . '/includes/footer.php';
});

require_once ASTRO_ROOT . '/app/renderers.php';
