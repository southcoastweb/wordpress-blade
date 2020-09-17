<?php

namespace StarringJane\WordpressBlade\Wordpress;

use ClassNames\ClassNames;

class Hooks
{
    public function __construct()
    {
        $this->actions();
        $this->filters();
    }

    public static function register()
    {
        return new self;
    }

    public function actions()
    {
        add_action('template_include', [$this, 'action_template_include']);
    }

    public function filters()
    {
        $this->addTemplateDirectories();
    }

    /**
     * Render component if present in the template
     */
    public function action_template_include($template)
    {
        $extractor = new ClassNames;
        $classes = $extractor->getClassNames($template);
        $class = count($classes) ? $classes[0] : null;
        $component = $class ? new $class : null;

        if (!$component || !method_exists($component, 'render')) {
            return $template;
        }

        echo $component->toHtml();
    }

    /**
     * Also look for template files in template directories
     */
    public function addTemplateDirectories()
    {
        array_map(function ($type) {
            add_filter("{$type}_template_hierarchy", function ($templates) {
                foreach ($templates as $filename) {
                    $directories = apply_filters('wordpress-blade-template-directories', ['templates']);

                    foreach ($directories as $directory) {
                        $templates[] = $directory . DIRECTORY_SEPARATOR . $filename;
                    }
                }

                return $templates;
            });
        }, [
            'index',
            '404',
            'archive',
            'author',
            'category',
            'tag',
            'taxonomy',
            'date',
            'home',
            'frontpage',
            'page',
            'paged',
            'search',
            'single',
            'singular',
            'attachment'
        ]);
    }
}
