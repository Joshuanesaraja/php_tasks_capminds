in .htaccess we have
**********************

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

<!-- If the requested path is NOT a real file f AND NOT a real directory d, send the request to public/index.php. -->

RewriteRule ^(.*)$ public/index.php [QSA,L]

