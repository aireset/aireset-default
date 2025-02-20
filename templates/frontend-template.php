<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        
        <?php
        /**
         * Flexify before layout
         *
         * @since 1.0.0
         */
        do_action('aireset_default_before_layout'); ?>

        <div class="aireset-default aireset-default--modern aireset-default--has-sidebar" data-effect="pure-effect-slide">
            <main class="aireset-default__content">
                <?php while ( have_posts() ) :
                    the_post();
                    the_content();
                endwhile; ?>
            </main>

            <?php
            /**
             * Flexify after content
             *
             * @since 1.0.0
             */
            do_action('aireset_default_after_content'); ?>

            <div class="aireset-default__spinner"><img src="<?php echo esc_url( AIRESET_DEFAULT_ASSETS . 'frontend/img/loader.gif' ) ?>"/></div>
        </div>

        <?php
        /**
         * Flexify after layout
         *
         * @since 1.0.0
         */
        do_action('aireset_default_after_layout');

        wp_footer(); ?>
    </body>
</html>