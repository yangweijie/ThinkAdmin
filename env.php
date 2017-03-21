<?php
header('Content-Type: text/plain; charset=UTF-8');

echo "System Environment:\n\n";
foreach ($_ENV as $key => $value) {
    echo "{$key}: {$value}\n";
}
