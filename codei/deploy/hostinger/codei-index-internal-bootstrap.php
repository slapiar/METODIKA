<?php

declare(strict_types=1);

// Optional no-redirect bootstrap for document root pointing to /codei.
// Use this as /codei/index.php when you want to avoid HTTP redirects to /public.

require __DIR__ . '/public/index.php';
