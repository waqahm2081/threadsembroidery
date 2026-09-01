<?php

namespace App\Providers;

use App\Services\ProductCatalogService;
use App\Services\ShopCartService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('smtp_relaxed', function (array $config) {
            $factory = new EsmtpTransportFactory;

            $scheme = $config['scheme'] ?? null;
            if (! $scheme) {
                $scheme = ($config['encryption'] ?? null) === 'ssl' ? 'smtps' : 'smtp';
            }

            $transport = $factory->create(new Dsn(
                $scheme,
                $config['host'],
                $config['username'] ?? null,
                $config['password'] ?? null,
                isset($config['port']) ? (int) $config['port'] : null,
                $config
            ));

            $stream = $transport->getStream();
            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                if (! empty($config['timeout'])) {
                    $stream->setTimeout((float) $config['timeout']);
                }
            }

            if (array_key_exists('auto_tls', $config) && method_exists($transport, 'setAutoTls')) {
                $transport->setAutoTls((bool) $config['auto_tls']);
            }

            return $transport;
        });

        View::composer('partials.header', function ($view) {
            $shopCategories = [];
            $shopCartCount = 0;

            try {
                $shopCategories = app(ProductCatalogService::class)->getCategories();
                $shopCartCount = app(ShopCartService::class)->itemCount();
            } catch (\Throwable) {
                // pc_products may be unavailable in some environments
            }

            $view->with([
                'shopCategories' => $shopCategories,
                'shopCartCount' => $shopCartCount,
            ]);
        });
    }
}
