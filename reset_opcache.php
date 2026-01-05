<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Opcache flushed successfully.";
} else {
    echo "Opcache reset not available.";
}
