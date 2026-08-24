<?php
// Modern header with dynamic nav and dark mode toggle
// Load navLinks and title from JSON data
$siteTitle = $data['title'] ?? 'AstroGallery';
$navLinks = $data['navLinks'] ?? [
    ["href" => "/Galaxies.php", "label" => "Galaxies"],
    ["href" => "/Nebulae.php", "label" => "Nebulae"],
    ["href" => "/Clusters.php", "label" => "Star Clusters"],
    ["href" => "/SolarSystem.php", "label" => "Solar System"],
    ["href" => "/178ED.php", "label" => "7\" Refractor"],
    ["href" => "/index_fits.php", "label" => "FITS Files"],
    ["href" => "/index.php", "label" => "Home"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($siteTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/astro-modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="https://physx-cnh.com/image/favicon.ico">
    <script>
    // Dark mode toggle
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    }
    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme');
        setTheme(current === 'dark' ? 'light' : 'dark');
    }
    (function() {
        const saved = localStorage.getItem('theme');
        if(saved) setTheme(saved);
    })();
    // Dimming overlay logic
    function setDimOverlay(active) {
        var overlay = document.querySelector('.astro-dim-overlay');
        if(overlay) {
            if(active) overlay.classList.add('active');
            else overlay.classList.remove('active');
        }
    }
    // Mobile nav toggle
    function toggleNav() {
        var nav = document.querySelector('.astro-nav');
        var btn = document.querySelector('.astro-nav-toggle');
        var body = document.body;
        if(nav) {
            var expanded = nav.classList.toggle('show');
            btn.setAttribute('aria-expanded', expanded);
            setDimOverlay(expanded);
            if(expanded) {
                btn.style.display = 'none';
                body.classList.add('astro-nav-open');
            } else {
                btn.style.display = 'block';
                body.classList.remove('astro-nav-open');
            }
        }
    }
    window.addEventListener('DOMContentLoaded', function() {
        var nav = document.querySelector('.astro-nav');
        var btn = document.querySelector('.astro-nav-toggle');
        var links = document.querySelectorAll('.astro-nav a');
        var overlay = document.querySelector('.astro-dim-overlay');
        if(window.innerWidth <= 600 && nav && btn) {
            nav.classList.remove('show');
            btn.style.display = 'block';
            btn.setAttribute('aria-expanded', 'false');
            setDimOverlay(false);
            links.forEach(function(link){
                link.addEventListener('click', function(){
                    nav.classList.remove('show');
                    btn.style.display = 'block';
                    btn.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('astro-nav-open');
                    setDimOverlay(false);
                });
            });
        }
        // Hide nav on outside click
        document.addEventListener('click', function(e) {
            if(window.innerWidth > 600) return;
            if(nav && nav.classList.contains('show')) {
                if(!nav.contains(e.target) && e.target !== btn) {
                    nav.classList.remove('show');
                    btn.style.display = 'block';
                    btn.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('astro-nav-open');
                    setDimOverlay(false);
                }
            }
        });
        // Desktop: dim on header hover
        var header = document.querySelector('.astro-header');
        if(window.innerWidth > 600 && header && overlay) {
            header.addEventListener('mouseenter', function(){ setDimOverlay(true); });
            header.addEventListener('mouseleave', function(){ setDimOverlay(false); });
        }
    });
    </script>
</head>
<body>
<div class="astro-dim-overlay"></div>
<header class="astro-header">
    <button class="astro-dark-toggle" onclick="toggleTheme()" title="Toggle dark mode"><i class="fas fa-moon"></i></button>
    <div class="astro-header-brand">
        <img src="https://www.physx-cnh.com/image/logo.png" alt="Logo" class="astro-logo">
        <div class="astro-header-title"><?= htmlspecialchars($siteTitle) ?></div>
    </div>
    <span class="astro-nav-toggle" onclick="toggleNav()" aria-expanded="false" style="display:none;">Expand navigation</span>
    <nav class="astro-nav">
        <?php foreach ($navLinks as $link) : ?>
            <a href="<?= htmlspecialchars($link['href']) ?>"><?= htmlspecialchars($link['label']) ?></a>
        <?php endforeach; ?>
    </nav>
</header>
