<?php

namespace ACF_Gutenberg\Lib;
use ACF_Gutenberg\Includes;;



function convert_to_class_name($str)
{
    $str = ucwords(str_replace('-', ' ', $str));
    $str = ucwords(str_replace('_', ' ', $str));
    return str_replace(' ', '', $str);
}


function my_acf_block_render_callback($block)
{
    $slug = str_replace('acf/', '', $block['name']);
    $class_name = 'ACF_Gutenberg\\Blocks\\' . convert_to_class_name($slug);
    $block_instance = new $class_name($slug);

    // Set Position
    $block_instance->set_block_id();

    $plugin_blade_file = glob(ACFGB_PATH . "/resources/blocks/{$block_instance->slug}/{,*/}{*}blade.php", GLOB_BRACE);
    $theme_blade_file = glob(get_template_directory() . "/acf-gutenberg/blocks/{$block_instance->slug}/{,*/}{*}blade.php", GLOB_BRACE);

    if (isset($plugin_blade_file[0]) && file_exists($plugin_blade_file[0]) || isset($theme_blade_file[0]) && file_exists($theme_blade_file[0]) ) {
        echo Includes\ACF_Gutenberg::getInstance()->builder()->blade()
            ->view()->make("blocks.{$block_instance->slug}.{$block_instance->slug}", [
                'block' => $block_instance,
                'content' => $block_instance->content,
                'design' => $block_instance->design,
                'custom_classes' => $block_instance->custom_classes
            ]);
    } else {
        wp_die("Blade view not exist for $class_name Block");
    }
}

if (file_exists(get_template_directory() . '/acf-gutenberg/settings.php')) {
    include get_template_directory() . '/acf-gutenberg/settings.php';
}
