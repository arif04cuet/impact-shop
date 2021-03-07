<?php namespace Mmid\Cms;

use File;
use Event;
use Cms\Classes\Theme;
use System\Classes\PluginBase;

class Plugin extends PluginBase{

    public function registerComponents(){
    	return [
    		'Mmid\Cms\Components\ContactForm' => 'ContactForm',
    		'Mmid\Cms\Components\FooterContactForm' => 'FooterContactForm',
			'Mmid\Cms\Components\CmsSettings' => 'CmsSettings',
			'Mmid\Cms\Components\Webshop' => 'Webshop',
    	];
    }

	public function registerMailTemplates(){
	    return [
	        'mmid.cms::mail.contact-form' => 'Contact Form',
	        'mmid.cms::mail.footer-contact-form' => 'Footer Contact Form',
	    ];
	}

    public function registerSettings(){
		return [
            'settings' => [
                'label' => 'CMS instellingen',
                'description' => 'Beheer website instellingen',
                'category' => 'IMPACT',
                'icon' => 'oc-icon-adjust',
                'class' => 'Mmid\Cms\Models\Settings',
                'order' => 10,
            ],
        ];
    }


    public function registerMarkupTags() {
        return [
            'filters' => [
                // A global function, i.e str_plural()
                'urlencode' => [$this, 'urlEncode'],
				'md5' => [$this, 'md5Doen'],
            ],
        ];
    }

    public function urlEncode($text)
    {
        return urlencode($text);
    }
	
	 public function md5Doen($text)
    {
        return md5($text);
    }

	public function boot(){
		\RainLab\Pages\Classes\Page::extend(function($model) {
			$model->addDynamicMethod('getTemplateList', function() {
				$items = array(0 => '-- Geen template --');
				$theme_dir = Theme::getActiveTheme()->getDirName();
				foreach (glob( themes_path( $theme_dir ) . '/partials/features/*.{htm}', GLOB_BRACE ) as $item) {
					$file = str_replace( themes_path() . '/' . $theme_dir . '/partials/features/' , '', $item );
					$items[$file] = $file;
				}
				return $items;
			});
			$model->addDynamicMethod('getContentList', function() {
				$items = array(0 => '-- Geen content --');
				$theme_dir = Theme::getActiveTheme()->getDirName();
				foreach (glob( themes_path( $theme_dir ) . '/content/*.{htm}', GLOB_BRACE ) as $item) {
					$file = str_replace( themes_path() . '/' . $theme_dir . '/content/' , '', $item );
					$items[$file] = $file;
				}
				return $items;
			});
			$model->addDynamicMethod('getButtons', function() {
				 return ['primary' => 'Primair', 'link' => 'Link'];
			});
		});

	}

}