<?php

namespace Arstech\Webshop;

use Arstech\Webshop\Classes\BootExtensions;
use Arstech\Webshop\Classes\Jobs\MySendVirtualProductFiles;
use Arstech\Webshop\Classes\MollieProvider;
use OFFLINE\Mall\Classes\Jobs\SendVirtualProductFiles;
use OFFLINE\Mall\Classes\Payments\PaymentGateway;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    use BootExtensions;

    public function registerComponents()
    {
        return [
            'Arstech\Webshop\Components\ProductsFilter' => 'productsFilter'
        ];
    }

    public function registerSettings()
    {
    }

    public function boot()
    {
        $this->registerExtensions();

        //register moolie payment provider
        $gateway = $this->app->get(PaymentGateway::class);
        $gateway->registerProvider(new MollieProvider());

        // replace

        $this->app->bind(SendVirtualProductFiles::class, function () {
            return new MySendVirtualProductFiles();
        });
    }
}
