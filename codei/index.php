<?php

declare(strict_types=1);

// Shared-hosting bootstrap when document root points to the codei directory.
// The public front controller is executed internally so public URLs remain
// clean (without exposing /public/ in the browser).

require __DIR__ . '/public/index.php';
