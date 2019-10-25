<?php
function directory($directory) {
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
        if ($file != '.' && $file != '..') {
            $results[] = $file;
        }
    }
    closedir($handler);
    return $results;
}
?>