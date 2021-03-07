<?php

namespace Arstech\Webshop\Classes;

use App;
use Arstech\Webshop\Models\Location;
use Arstech\Webshop\Models\Schedule;
use Event;
use OFFLINE\Mall\Controllers\Categories;
use OFFLINE\Mall\Controllers\Discounts;
use OFFLINE\Mall\Controllers\Orders;
use OFFLINE\Mall\Controllers\PaymentLogs;
use OFFLINE\Mall\Controllers\Products;
use OFFLINE\Mall\Models\Address;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\CustomField;
use OFFLINE\Mall\Models\Discount;
use OFFLINE\Mall\Models\OrderProduct;
use OFFLINE\Mall\Models\PaymentLog;
use OFFLINE\Mall\Models\Product;
use System\Classes\PluginManager;

trait BootExtensions
{
    protected function registerExtensions()
    {
        if (PluginManager::instance()->exists('OFFLINE.Mall')) {
            $this->extendMall();
        }
        $this->events();
    }

    public function events()
    {

        Event::listen('backend.form.extendFields', function ($formWidget) {


            // Only for the User model
            if (!$formWidget->model instanceof PaymentLog) {
                return;
            }

            // Add an extra birthday field
            //change message field type from text to partial
            $formWidget->addTabFields([
                'message' => [
                    'label' => 'offline.mall::lang.common.message',
                    'type' => 'partial',
                    'tab' => 'offline.mall::lang.payment_log.payment_data',
                    'path' => '$/offline/mall/controllers/paymentlogs/_data.htm',
                    'span' => 'auto',
                    'commentAbove' => 'offline.mall::lang.payment_log.message_comment'
                ]
            ]);
        });


        // create customer entry after user registration
        Event::listen('rainlab.user.activate', function ($user) {

            //create customer after signup
            $customer            = new Customer;
            $customer->firstname = $user->name;
            $customer->lastname  = $user->surname;
            $customer->user_id   = $user->id;
            $customer->is_guest  = 0;
            $customer->save();

            //create address if data available
            if ($user->address && $user->zip) {

                $billing = Address::findOrNew($customer->default_billing_address_id);

                $billing->name  = $customer->firstname;
                $billing->lines = $user->address;
                $billing->zip = $user->zip;
                $billing->city = $user->city;
                $billing->country_id = 159;
                $billing->customer_id = $customer->id;
                $billing->save();

                $customer->default_shipping_address_id = $billing->id;
                $customer->default_billing_address_id = $billing->id;
                $customer->save();
            }

            Cart::transferSessionCartToCustomer($customer);
        });

        //

        Event::listen('backend.menu.extendItems', function ($manager) {

            // Add Location sub menu under Catalouge main menu
            $manager->addSideMenuItems('OFFLINE.Mall', 'mall-catalogue', [
                'locations' => [
                    'label'       => 'Locations',
                    'url'         => \Backend::url('arstech/webshop/locations'),
                    'icon'        => 'icon-users',
                    'permissions' => ['arstech.webshop.manage_locations'],
                ],
            ]);

            //remove review and brands sub menu from catalog main menu sidebar
            $manager->removeSideMenuItems('OFFLINE.Mall', 'mall-catalogue', ['mall-reviews', 'mall-brands', 'mall-services']);
        }, 5);

        // update shedule max participants
        Event::listen('mall.checkout.succeeded', function ($result) {

            foreach ($result->order->products as $orderProduct) {

                if ($schedule = $orderProduct->getSchedule()) {

                    if (!$schedule->is_fully_booked) {

                        $schedule->decrement('max_participants');
                        if ($schedule->refresh()->max_participants == 0) {
                            $schedule->is_fully_booked = 1;
                            $schedule->save();
                        }
                    }
                }
            };
        });
    }
    protected function extendMall()
    {

        //models

        //discount

        Discount::extend(function ($model) {

            //add rules
            $model->rules['trigger'] = 'in:total,code,product,customer_group,products';
            $model->rules['products'] = 'required_if:trigger,products';


            $model->addJsonable('products');
            $model->bindEvent('model.beforeSave', function () use ($model) {
                if ($model->trigger !== 'products') {
                    $model->products = null;
                }
            });
        });

        // add description field tp category model
        Category::extend(function ($model) {
            $model->addFillable(['second_description']);
        });

        CartProduct::extend(function ($model) {

            $model->addDynamicMethod('getSchedule', function () use ($model) {

                $schedule = null;

                if ($model->data->isCourseProduct()) {

                    $customField = $model->custom_field_values()->whereHas('custom_field', function ($q) {
                        return $q->where('name', 'schedule_id');
                    })->first();

                    return $customField ? Schedule::find($customField->value) : null;
                }

                return $schedule;
            });
        });

        OrderProduct::extend(function ($model) {

            $model->addDynamicMethod('getSchedule', function () use ($model) {

                $schedule = null;

                if ($model->product and $model->product->isCourseProduct() and is_array($model->custom_field_values)) {

                    $customFields = $model->custom_field_values;
                    $customField = array_filter($customFields, function ($item) {
                        return $item['custom_field']['name'] == 'schedule_id';
                    });

                    if ($customField) {
                        $scheduleId = $customField[0]['display_value'];

                        return $customField ? Schedule::find($scheduleId) : null;
                    }
                }

                return $schedule;
            });
        });



        Product::extend(function ($model) {

            $model->hasMany['schedules'] = [

                Schedule::class,
                'key' => 'course_id',
                'otherKey' => 'id'
            ];

            //add dynamic method all scheules to fetch all date and locations
            $model->addDynamicMethod('allSchedules', function ($zip_city = null) use ($model) {

                $list = Schedule::with('location')
                    ->whereHas('product', function ($q) use ($model) {
                        $q->where('id', $model->id);
                    })->when($zip_city, function ($q, $zip_city) {
                        $q->whereHas('location', function ($q) use ($zip_city) {
                            $q->where('zipcode', $zip_city)->orWhere('city', trim($zip_city));
                        });
                    })
                    //->available()
                    ->get()
                    ->groupBy('location_id')
                    ->toArray();


                usort($list, function ($a, $b) {

                    $a1 = $a[0]['location']['zipcode'];
                    $b1 = $b[0]['location']['zipcode'];

                    return ($a1 < $b1) ? -1 : 1;
                });

                return collect($list);
            });

            $model->addDynamicMethod('findLocation', function ($id) {
                return Location::find($id);
            });

            $model->addDynamicMethod('isCourseProduct', function () use ($model) {

                $data = true;

                if ($course = $model->getPropertyValueBySlug('course-is-simple-product'))
                    $data = !$course->value;

                return $data;
            });

            // models events
            //create custom field schedule_id for each course product after create/save
            $model->bindEvent('model.afterSave', function () use ($model) {


                if ($model->isCourseProduct()) {

                    $customField = CustomField::whereHas('products', function ($q) use ($model) {
                        return $q->where('product_id', $model->id);
                    })->where('name', 'schedule_id')->first();


                    if (!$customField) {
                        $f = CustomField::where('name', 'schedule_id')->first();
                        $model->custom_fields()->attach($f);
                    }
                } else {
                    $model->custom_fields()->detach();
                }
            });

            // add stock field value before validate
            $model->bindEvent('model.beforeValidate', function () use ($model) {
                $model->stock = $model->stock ?: 9999;
                $model->is_virtual = true;
            });
        });

        // controllers


        //discount
        Discounts::extendFormFields(function ($form, $model, $context) {

            if (!$form->model instanceof Discount) {
                return;
            }

            $form->addFields([
                'products' => [
                    'label' => 'Producten',
                    'type' => 'repeater',
                    'span' => 'auto',
                    'prompt' => 'Product toevoegen',
                    'trigger' => [
                        'action' => 'show',
                        'field' => 'trigger',
                        'condition' => 'value[products]'
                    ],
                    'form' => [
                        'fields' => [
                            'product' => [
                                'label' => 'Producten',
                                'type' => 'recordfinder',
                                'nameFrom' => 'name',
                                'list' => '$/offline/mall/models/product/columns.yaml',
                                'span' => 'full',
                                'modelClass' => '\OFFLINE\Mall\Models\Product',
                                'useRelation' => false

                            ]
                        ]
                    ]
                ]
            ]);
        });

        //category
        Categories::extendFormFields(function ($form, $model, $context) {

            if (!$form->model instanceof Category) {
                return;
            }

            $form->addTabFields([
                'second_description' => [
                    'label' => 'Omschrijving (rechter kolom)',
                    'type' => 'richeditor',
                    'tab' => 'offline.mall::lang.product.description',
                    'size' => 'huge',
                    'span' => 'auto'
                ]
            ]);
        });

        //add location tab to product edit mode
        Products::extend(function ($controller) {

            //add relation manager for location
            $myConfigPath = '$/arstech/webshop/controllers/products/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );
        });


        Products::extendFormFields(function ($form, $model, $context) {

            if (!$form->model instanceof Product) {
                return;
            }

            //remove stock and is_virtual field from create screen
            $form->removeField('stock');
            $form->removeField('meta_title');
            $form->removeField('meta_description');
            $form->removeField('is_virtual');

            $form->addFields([
                'is_virtual' => [
                    'label' => 'Virtueel',
                    'type' => 'switch',
                    'default' => 1,
                    'cssClass' => 'hidden'
                ]
            ]);


            if (!$model->exists)
                return;

            $form->addTabFields([
                'locations' => [
                    'label' => 'Data en locaties',
                    'type' => 'partial',
                    'tab' => 'Data en locaties',
                    'path' => '$/arstech/webshop/controllers/products/_schedule.htm',
                    'span' => 'full',
                    'trigger' => [
                        'action' => 'hide',
                        'field' => 'PropertyValues[7]',
                        'condition' => 'value[1]'
                    ]
                ]
            ]);

            //remove tabs
            $disabledTabs = [
                'offline.mall::lang.product.details',
                'offline.mall::lang.common.shipping',
                'offline.mall::lang.common.cart',
                'offline.mall::lang.common.reviews',
                'offline.mall::lang.common.taxes',
				'offline.mall::lang.common.seo'
            ];
            foreach ($disabledTabs as $tab) {

                $form->removeTab($tab);
            }

            //remove fields

            $disabledFields = [
                'product_files_section',
                'file_session_required',
                'file_expires_after_days',
                'file_max_download_count',
                'missing_file_hint',
                'product_files',
                'gtin',
                'mpn',
                'brand',
                'downloads',
                'links',
                'embeds',
				'allow_out_of_stock_purchases'
            ];

            foreach ($disabledFields as $field) {

                $form->removeField($field);
            }
        });

        Orders::extend(function ($controller) {
            $partials_path = sprintf('$/arstech/webshop/views/partials/orders/');
            $controller->addViewPath($partials_path);
        });
    }
}
