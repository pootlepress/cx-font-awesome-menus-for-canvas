<?php

if (!function_exists('check_main_heading')) {
    function check_main_heading() {
        $options = get_option('woo_template');
        if (!in_array("Canvas Extensions", $options)) {
            function woo_options_add($options){
                $i = count($options);
                $options[$i++] = array(
                    'name' => __('Canvas Extensions', 'pootlepress-canvas-extensions' ),
                    'icon' => 'favorite',
                    'type' => 'heading'
                );
                return $options;
            }
        }
    }
}
?>