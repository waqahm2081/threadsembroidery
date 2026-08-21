<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

abstract class SiteMailable extends Mailable
{
    protected function sharedViewData(array $extra = []): array
    {
        return array_merge([
            // Fallback remote URL if embed is unavailable
            'logoUrl' => url('assets/img/logo.png'),
            'siteName' => config('site.name'),
            'siteUrl' => rtrim((string) config('app.url'), '/'),
        ], $extra);
    }
}
