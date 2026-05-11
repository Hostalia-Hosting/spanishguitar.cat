<?php
$base_url = "https://www.spanishguitar.cat/";

// Carpetes o fitxers que vols ignorar completament
$ignorar = [
    '.github',
    'common-php',
    'php',
    'generar-sitemap.php'
];

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Creem un iterador per recórrer totes les carpetes i fitxers
$directory = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $info) {
    $ruta_relativa = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $info->getPathname());
    
    // Normalitzem les barres per a Windows (canviem \ per /)
    $ruta_relativa = str_replace('\\', '/', $ruta_relativa);

    // Comprovem si el fitxer està dins d'una carpeta a ignorar
    $ha_d_ignorar = false;
    foreach ($ignorar as $pauta) {
        if (strpos($ruta_relativa, $pauta) === 0) {
            $ha_d_ignorar = true;
            break;
        }
    }

    if (!$ha_d_ignorar && $info->isFile()) {
        $extensio = $info->getExtension();

        if ($extensio == 'php' || $extensio == 'html') {
            
            // Si el fitxer és un index.php, netegem el nom per a la URL
            // Exemple: casaments/index.php -> casaments/
            $url_path = $ruta_relativa;
            if (basename($url_path) == 'index.php') {
                $url_path = dirname($url_path);
                if ($url_path == '.') $url_path = '';
                else $url_path .= '/';
            }

            $ultima_modificacio = date("Y-m-d", $info->getMTime());
            $prioritat = ($url_path == '') ? '1.0' : '0.8';

            echo '<url>';
            echo '  <loc>' . $base_url . $url_path . '</loc>';
            echo '  <lastmod>' . $ultima_modificacio . '</lastmod>';
            echo '  <changefreq>monthly</changefreq>';
            echo '  <priority>' . $prioritat . '</priority>';
            echo '</url>';
        }
    }
}

echo '</urlset>';
?>