<?php
// includes/menu.php
// Håndterer hovedmeny, aktivt menyvalg og basisdata for brødsmuler.

if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

if (!function_exists('get_main_navigation_items')) {
    function get_main_navigation_items(): array {
        static $items = null;
        if ($items !== null) {
            return $items;
        }

        $items = [
            'home' => [
                'label' => 'Hjem',
                'href'  => url('index.php'),
                'match' => ['index.php'],
            ],
            'issues' => [
                'label' => 'Utgaver',
                'href'  => url('openfil/utgaver.php'),
                'match' => ['openfil/utgaver.php', 'openfil/detalj/utgave_detalj.php'],
            ],
            'topics' => [
                'label' => 'Tema',
                'href'  => url('openfil/tema_liste.php'),
                'match' => ['openfil/tema_liste.php', 'openfil/detalj/tema_detalj.php'],
            ],
            'articles' => [
                'label' => 'Artikler',
                'href'  => url('openfil/artikler.php'),
                'match' => ['openfil/artikler.php', 'openfil/detalj/artikkel_detalj.php'],
            ],
            'images' => [
                'label' => 'Bilder',
                'href'  => url('openfil/bilder.php'),
                'match' => ['openfil/bilder.php', 'openfil/detalj/bilde_detalj.php'],
            ],
            'people' => [
                'label' => 'Personer',
                'href'  => url('openfil/personer.php'),
                'match' => ['openfil/personer.php', 'openfil/detalj/person_detalj.php'],
            ],
            'vessels' => [
                'label' => 'Fartoyer',
                'href'  => url('openfil/fartoyer.php'),
                'match' => ['openfil/fartoyer.php', 'openfil/detalj/fartoy_detalj.php'],
            ],
            'authors' => [
                'label' => 'Forfattere',
                'href'  => url('openfil/forfattere.php'),
                'match' => ['openfil/forfattere.php', 'openfil/detalj/forfatter_detalj.php'],
            ],
            'photographers' => [
                'label' => 'Fotografer',
                'href'  => url('openfil/fotografer.php'),
                'match' => ['openfil/fotografer.php', 'openfil/detalj/fotograf_detalj.php'],
            ],
            'organizations' => [
                'label' => 'Forening/org',
                'href'  => url('openfil/forening_org.php'),
                'match' => ['openfil/forening_org.php', 'openfil/detalj/forening_org_detalj.php'],
            ],
            'yards' => [
                'label' => 'Verft',
                'href'  => url('openfil/verft.php'),
                'match' => ['openfil/verft.php', 'openfil/detalj/verft_detalj.php'],
            ],
            'shipping' => [
                'label' => 'Rederier',
                'href'  => url('openfil/rederier.php'),
                'match' => ['openfil/rederier.php', 'openfil/detalj/rederi_detalj.php'],
            ],
            'admin' => [
                'label' => 'Administrasjon',
                'href'  => url('protfil/param_admin.php'),
                'match' => ['protfil/param_admin.php'],
                'requires_admin' => true,
            ],
        ];

        return $items;
    }
}

if (!function_exists('determine_active_nav')) {
    function determine_active_nav(?string $explicit = null): ?string {
        if ($explicit !== null) {
            return $explicit;
        }

        $items = get_main_navigation_items();
        $currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
        $currentScript = str_replace('\\', '/', $currentScript);
        $currentNormalized = '/' . ltrim($currentScript, '/');

        foreach ($items as $key => $item) {
            foreach ($item['match'] as $match) {
                $matchNormalized = '/' . ltrim($match, '/');
                if (
                    $currentNormalized === $matchNormalized ||
                    (strlen($currentNormalized) >= strlen($matchNormalized) &&
                     substr($currentNormalized, -strlen($matchNormalized)) === $matchNormalized)
                ) {
                    return $key;
                }
            }
        }

        return null;
    }
}

if (!function_exists('render_main_menu')) {
    /**
     * Render hovedmenyen.
     *
     * @param string|null $activeNav Navn på aktiv menyseksjon. Null => automatisk.
     */
    function render_main_menu(?string $activeNav = null): void {
        $items = get_main_navigation_items();
        $resolved = determine_active_nav($activeNav);
        $isAdmin = function_exists('is_admin') ? is_admin() : false;

        echo '<nav class="site-nav nav tabs" aria-label="Hovedmeny">';
        echo '<div class="container">';
        echo '<ul class="tabs-list">';

        foreach ($items as $key => $item) {
            if (!empty($item['requires_admin']) && !$isAdmin) {
                continue;
            }

            $isActive = $resolved === $key;
            $href = $item['href'];
            $label = $item['label'];

            echo '<li class="tab-item">';
            echo '<a class="tab-link' . ($isActive ? ' is-active' : '') . '" href="' . h($href) . '"';
            if ($isActive) {
                echo ' aria-current="page"';
            }
            echo '>' . h($label) . '</a>';
            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
        echo '</nav>';
    }
}
