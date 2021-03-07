<?php

namespace Arstech\Profile;

use App;
use Arstech\Profile\Models\Profile;
use Auth;
use Backend;
use Event;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;
use Redirect;

/**
 * Profile Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Profile',
            'description' => 'Extend Rainlab User',
            'author'      => 'Arstech',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        $this->extendUserModel();
        $this->extendUsersController();
        $this->events();
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Arstech\Profile\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'arstech.profile.some_permission' => [
                'tab' => 'Profile',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'profile' => [
                'label'       => 'Profile',
                'url'         => Backend::url('arstech/profile/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['arstech.profile.*'],
                'order'       => 500,
            ],
        ];
    }


    //custom functions

    public function events()
    {
    }

    protected function extendUserModel()
    {
        UserModel::extend(function ($model) {
            $model->addFillable([
                'salution',
                'initials',
                'infix',
                'address',
                'zip',
                'city',
                'phone',
                'ffp_number',
                'seh_number',
                'bsn_number'
            ]);

            //link to plugin

            $model->hasOne['profile'] = 'Arstech\Profile\Models\Profile';

            //add validation for names, surname and initials
            if (App::runningInBackend()) {
                $model->bindEvent('model.beforeValidate', function () use ($model) {
                    $model->rules['initials'] = 'required';
                    $model->rules['name'] = 'required';
                    $model->rules['surname'] = 'required';
                });
            }
        });
    }

    protected function extendUsersController()
    {

        UsersController::extendFormFields(function ($widget, $model, $contenx) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof UserModel) {
                return;
            }

            $nameField = $widget->getField('name');
            $surname = $widget->getField('surname');

            //remove name, surname fields
            $widget->removeField('name');
            $widget->removeField('surname');

            $fields = [

                'salution' => [
                    'label' => 'Salution',
                    'type' => 'dropdown',
                    'options' => [
                        'Mr.' => 'Mr.',
                        'Mrs.' => 'Mrs.'
                    ],
                    'span'  => 'left'
                ],
                'initials' => [
                    'label' => 'Initials',
                    'type' => 'text',
                    'span'  => 'left',
                    'required' => 1
                ],
                'name' => [
                    'label' => 'First Name',
                    'span'  => 'auto'
                ],

                'infix' => [
                    'label' => 'Surname Infix',
                    'type' => 'text',
                    'span'  => 'auto'
                ],

                'surname' => [
                    'label' => 'rainlab.user::lang.user.surname',
                    'span'  => 'auto'
                ],
                'phone' => [
                    'label' => 'Phone',
                    'span'  => 'auto'
                ],

                'address' => [
                    'label' => 'Address',
                    'type' => 'text',
                    'span'  => 'full'
                ],
                'zip' => [
                    'label' => 'Zip',
                    'type' => 'text',
                    'span'  => 'auto'
                ],
                'city' => [
                    'label' => 'City',
                    'type' => 'text',
                    'span'  => 'auto'
                ],
                'ffp_number' => [
                    'label' => 'FFP nummer',
                    'type' => 'text',
                    'span'  => 'auto'
                ],
                'seh_number' => [
                    'label' => 'SEH nummer',
                    'type' => 'text',
                    'span'  => 'auto'
                ],
                'bsn_number' => [
                    'label' => 'BSN nummer',
                    'type' => 'text',
                    'span'  => 'auto'
                ]
            ];

            $widget->addFields($fields);


            //add profiles fields in a tab

            if (!$model->exists)
                return;

            Profile::getFromUser($model);

            $widget->addTabFields([
                'profile[company]' => [
                    'label' => 'Company',
                    'tab' => 'Invoice Details',
                    'type' => 'text',
                    'span' => 'auto'
                ],
                'profile[department]' => [
                    'label' => 'Department',
                    'tab' => 'Invoice Details',
                    'type' => 'text',
                    'span' => 'auto'
                ],
                'profile[address]' => [
                    'label' => 'Address',
                    'tab' => 'Invoice Details',
                    'type' => 'text',
                    'span' => 'auto'
                ],
                'profile[zip]' => [
                    'label' => 'Zip',
                    'tab' => 'Invoice Details',
                    'type' => 'text',
                    'span' => 'auto'
                ],
                'profile[city]' => [
                    'label' => 'City',
                    'tab' => 'Invoice Details',
                    'type' => 'text',
                    'span' => 'auto'
                ],
                'profile[invoice_email]' => [
                    'label' => 'Invoice Email',
                    'tab' => 'Invoice Details',
                    'type' => 'text',
                    'span' => 'auto'
                ],
            ]);
        });
    }
}
