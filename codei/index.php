<?php

declare(strict_types=1);

// Shared-hosting bootstrap for installations where requests arrive via /codei.
// The public front controller is executed internally so application routes can
// stay under /codei/... without requiring a visible redirect to /public/.

require __DIR__ . '/public/index.php';
