<?php

namespace Arstech\Webshop\Classes\Jobs;

use Cms\Classes\Controller;
use DB;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Mail;
use OFFLINE\Mall\Classes\Jobs\SendVirtualProductFiles;
use OFFLINE\Mall\Models\GeneralSettings;
use OFFLINE\Mall\Models\Order;
use OFFLINE\Mall\Models\OrderProduct;
use OFFLINE\Mall\Models\ProductFileGrant;

/**
 * This Job generates ProductFileGrants for each purchased product.
 * It also sends the email containing the download links to the customer.
 */
class MySendVirtualProductFiles extends SendVirtualProductFiles
{

    public function fire(Job $job, $data)
    {
        //traceLog('ok my child class');

        if ($job->attempts() > 5) {
            logger()->error('Failed to send virtual product files for order.', ['data' => $data]);
            $job->delete();
        }

        $order = Order::with(['virtual_products.product.latest_file'])->findOrFail($data['order']);

        DB::transaction(function () use ($order) {
            // Create download grants for each order product.
            $order->virtual_products->each(function (OrderProduct $product) {
                ProductFileGrant::fromOrderProduct($product);
            });

            // If the file notification has been disabled exit here.
            if (!$this->enabledNotifications->has('offline.mall::product.file_download')) {
                return;
            }

            // Re-fetch the products with all relevant relationships.
            $products = $order->virtual_products->fresh([
                'product_file_grants.order_product.product.latest_file',
                'product.latest_file',
            ]);

            $data = [
                'order'       => $order,
                'products'    => $products,
                'account_url' => $this->getAccountUrl(),
            ];

            Mail::send(
                $this->enabledNotifications->get('offline.mall::product.file_download'),
                $data,
                function ($message) use ($order) {
                    $message->to('abs@gmail.com', $order->customer->name);
                }
            );
        });

        $job->delete();
    }
}
