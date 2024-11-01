<?php
    /*
        Plugin Name: Twitget Media Add on
        Plugin URI: http://www.admirecreative.co.uk
        Description: Adds Twitget Twitter feed image media as seperate widget
        Version: 1.0.1
        Author: Tom Hopcraft
        Author URI: http://www.chewx.co.uk
        License: GNU General Public License v2.0
        License URI: http://www.opensource.org/licenses/gpl-license.php
    */

    class Admire_Twitget_Media extends WP_Widget {

        /**
         * Constants
         */

        // Name
        const name = 'Twitget Media Add on';

        /**
         * Constructor
         *
         * @access public
         * return Admire_Twitget_Media
         */
        public function __construct() {

            /**
             * Sets up the widgets name etc
             */
            parent::__construct(
                'admire_twitget_media', // Base ID
                __( 'Twitget Media', self::name ), // Name
                array( 'description' => __( 'Display your recent tweet images.', self::name ), ) // Args
            );

            $this->check_requirements();

        }

        /**
         * Checks that the WordPress setup meets the plugin requirements.
         *
         * @access private
         * @return boolean
         */
        private function check_requirements() {

            if( function_exists( 'is_plugin_active' ) ) {
                if( !is_plugin_active( 'twitget/twitget.php' )) {

                    add_action('admin_notices', array( &$this, 'display_activate_parent_plugin' ) );
                    return false;

                }
            }

        }

        /**
         * Display the plugin requirements.
         *
         * @access static
         */
        static function display_activate_parent_plugin() {

            echo '<div id="message" class="error"><p>';
            echo sprintf( __('Sorry, <strong>%s</strong> requires Twitget. Please search \'Twitget\' and activate.</a>', self::name), self::name );
            echo '</p></div>';

        }

        /**
         * Get Twitget feed
         *
         * @access private
         * @return array
         */
        private function admire_get_twitget_feed() {

            $options = get_option('twitget_settings');

            if(!is_array($options["twitter_data"])) {
                $tweets = json_decode($options['twitter_data'], true);
            } else {
                $tweets = $options['twitter_data'];
            }

            return $tweets;

        }

        /**
         * Get twitter image url
         *
         * @access static
         * @return string
         */
        public static function admire_get_twitget_media_url() {

            $tweets = Admire_Twitget_Media::admire_get_twitget_feed();

            $image_url = $tweets[0]['entities']['media'][0]['media_url'];

            if($image_url) {
                return $image_url . ':large';
            } else {
                return false;
                //return 'http://admire.agency/placeholder.jpg';
            }

        }

        /**
         * Get twitter image url
         *
         * @access static
         */
        private function admire_twitget_media() {

            $tweets = $this->admire_get_twitget_feed();
            $twitget_settings = get_option('twitget_settings');
            $tweets_count = $twitget_settings['number_of_tweets'];
            $i = 0;

            for($count = 0; $tweets_count > $count; $count++) {

                $img_src = $tweets[$i]['entities']['media'][0]['media_url'];

                if($img_src)
                    echo '<img src="' . $img_src . '" alt="Twitter Media - ' . $i . '"/>';

                $i++;

            }


        }

        /**
         * Outputs the content of the widget
         *
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance ) {

            extract( $args );

            // outputs the content of the widget
            echo $args['before_widget'];

                echo $this->admire_twitget_media();

            echo $args['after_widget'];

        }

    }

    add_action( 'widgets_init', function() {

        register_widget( 'Admire_Twitget_Media' );

    } );