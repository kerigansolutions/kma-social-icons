<?php

namespace KeriganSolutions\SocialMedia;

class SocialSettingsPage
{
    public $socialPlatforms;
    private $options;

    public function __construct()
    {
        $this->socialPlatforms = [
            // handle => label,
            'facebook'    => 'Facebook',
            'youtube'     => 'YouTube',
            'youtubealt'  => 'YouTube (version 2)',
            'linkedin'    => 'LinkedIn',
            'instagram'   => 'Instagram',
            'twitter'     => 'Twitter',
            'googleplus'  => 'Google+',
            'pinterest'   => 'Pinterest',
            'vimeo'       => 'Vimeo',
            'atom'        => 'Atom',
            'blogger'     => 'Blogger',
            'ios'         => 'iOS',
            'android'     => 'Android',
            'windows'     => 'Windows',
            'wordpress'   => 'WordPress',
            'stumbleupon' => 'StumbleUpon',
            'flickr'      => 'Flickr',
            'ebay'        => 'Ebay',
            'digg'        => 'Digg',
            'behance'     => 'Behance',
            'amazon'      => 'Amazon',
            'googledrive' => 'Google Drive',
            'dropbox'     => 'Dropbox',
            'skype'       => 'Skype',
            'rss'         => 'RSS'
        ];
    }

    public function createPage()
    {
        add_action('admin_menu', [$this, 'add_menu_item']);
        add_action('admin_init', [$this, 'page_fields_init']);
    }

    /**
     * Add options page menu item
     */
    public function add_menu_item()
    {
        // This page will be under "Settings"
        add_options_page(
            'Social Media Settings',
            'Social Media Links',
            'manage_options',
            'social-setting-admin',
            [$this, 'create_admin_page']
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('social_option_name'); ?>
		<div class="wrap">
			<h1>Social Media Settings Settings</h1>
			<form class="form form-horizontal" method="post" action="options.php">
                <?php 
                // print out all hidden setting fields
                settings_fields('social_option_group');

                //add form fields
                do_settings_sections('social-setting-admin');

                //add submit button
                submit_button(); 
                ?>
			</form>
		</div>
		<?php
    }

    /**
     * Register inputs
     */
    public function page_fields_init()
    {
        register_setting(
            'social_option_group', // Option group
            'social_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Manage Social Media Links', // Title
            [$this, 'print_link_section_info'], // Callback
            'social-setting-admin' // Page
        );

        foreach ($this->socialPlatforms as $handle => $label) {

            $args = [ //creates callback to print input field
                'name' => $handle,
                'type' => 'text',
                'label' => $label
            ];

            add_settings_field(
                $handle, // ID
                $label, // Title
                [$this, 'print_input_field'], // Callback
                'social-setting-admin', // Page
                'setting_section_id', // Section
                $args
            );
        }
    }

    /**
     * Print input field callback
     */
    public function print_input_field(array $args)
    {
        printf(
            '<input class="form-control" type="text" id="' . $args['name'] . '" name="social_option_name[' . $args['name'] . ']" value="%s" style="width:300px;" />',
            isset($this->options[$args['name']]) ? esc_attr($this->options[$args['name']]) : ''
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = [];
        foreach ($this->socialPlatforms as $handle => $label) {
            if (isset($input[$handle])) {
                $new_input[$handle] = sanitize_text_field($input[$handle]);
            }
        }

        return $new_input;
    }

        // Print the Section text
    public function print_link_section_info()
    {
        print '<p>Copy the entire URL to your profile page in the blanks below. Simply leave unused social media platforms blank.</p>';

        foreach ($this->getSocialLinks('svg', 'circle') as $socialIcon) {
            echo '<a style="display:inline-block; width:40px; margin:.2rem;" href="' . $socialIcon[0] . '" target="_blank">';
            echo $socialIcon[1];
            echo '</a>';
        }
    }

    public function getSocialLinks($format = 'svg', $shape = 'circle', $data = '')
    {
        $supportedPlatforms = ($data != '' ? $data : get_option('social_option_name'));

        $socialArray = [];
        if (is_array($supportedPlatforms)) {
            foreach ($supportedPlatforms as $plat => $platLink) {
                if ($platLink != '') {
                    $iconUrl = dirname(__FILE__) . '/icons/' . $format . '/' . $shape . '/' . $plat . '.svg';
                    $iconData = file_get_contents(wp_normalize_path($iconUrl));
                    $socialArray[$plat][0] = $platLink;
                    $socialArray[$plat][1] = $iconData;
                }
            }
        }

        return $socialArray;
    }
}
