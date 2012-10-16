<?php
/**
 * Opus Primus Post Structures
 * Controls for the organization and layout of the site and its content.
 *
 * @package     OpusPrimus
 * @since       0.1
 *
 * @author      Opus Primus <in.opus.primus@gmail.com>
 * @copyright   Copyright (c) 2012, Opus Primus
 *
 * This file is part of Opus Primus.
 *
 * Opus Primus is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2, as published by the
 * Free Software Foundation.
 *
 * You may NOT assume that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to:
 *
 *      Free Software Foundation, Inc.
 *      51 Franklin St, Fifth Floor
 *      Boston, MA  02110-1301  USA
 *
 * The license for this software can also likely be found here:
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

class OpusPrimusAuthors {
    /** Constructor */
    function __construct() {

    }

    /**
     * Post Author
     * Outputs the author details: web address, email, and biography from the
     * user profile information - not designed for use in the post meta section.
     *
     * @package OpusPrimus
     * @since   0.1
     *
     * @param   $post_author_args
     * @internal @param show_mod_author
     * @internal @param show_mod_url
     * @internal @param show_mod_email
     * @internal @param show_mod_desc
     *
     * @uses    (global) $opus_author_id
     * @uses    (global) $post
     * @uses    do_action
     * @uses    get_post_meta
     * @uses    get_the_date
     * @uses    get_the_modified_date
     * @uses    author_details
     */
    function post_author( $post_author_args ) {
        /** Defaults */
        $defaults = array(
            'show_mod_author'   => true,
            'show_author_url'   => true,
            'show_author_email' => true,
            'show_author_desc'  => true
        );
        $post_author_args = wp_parse_args( (array) $post_author_args, $defaults );

        /** Get global variables */
        global $opus_author_id, $post;
        if ( ! isset( $opus_author_id ) ) {
            $opus_author_id = '';
        }

        /** Add empty hook before post author section */
        do_action( 'opus_post_author_top' );

        /** Add empty hook before first author details */
        do_action( 'opus_before_author_details' );

        /** Output author details */
        echo '<div class="first-author-details">';
            $this->author_details( $opus_author_id, $post_author_args['show_author_url'], $post_author_args['show_author_email'], $post_author_args['show_author_desc'] );
        echo '</div>';
        $this->author_coda();

        /** Add empty hook after first author details */
        do_action( 'opus_after_author_details' );

        /** Modified Author Details */
        /** @var $last_id - set last user ID */
        $last_id = get_post_meta( $post->ID, '_edit_last', true );
        /**
         * If the modified dates are different; and, the modified author is not
         * the same as the original author; and, showing the modified author is
         * set to true
         */
        if ( ( get_the_date() <> get_the_modified_date() ) && ( $opus_author_id <> $last_id )&& $post_author_args['show_mod_author'] ) {
            /** Add empty hook before modified author details */
            do_action( 'opus_before_modified_author_details' );

            /** Output author details based on the last one to edit the post */
            echo '<div class="modified-author-details">';
                printf(
                    '<div class="modified-author-details-text">%1$s</div>',
                    apply_filters( 'opus_modified_author_by_text', __( 'Modified by:', 'opusprimus' ) )
                );
                $this->author_details( $last_id, $post_author_args['show_author_url'], $post_author_args['show_author_email'], $post_author_args['show_author_desc'] );
                echo '</div>';
            $this->author_coda();

            /** Add empty hook after modified author details */
            do_action( 'opus_after_modified_author_details' );
        }

        /** Add empty hook after post author section */
        do_action( 'opus_post_author_bottom' );

    }

    /**
     * Author Details
     * Takes the passed author ID parameter and creates / collects various
     * details to be used when outputting author information, by default,
     * at the end of the post or page single view.
     *
     * @package OpusPrimus
     * @since   0.1
     *
     * @param   $author_id
     * @param   $show_author_url
     * @param   $show_author_email
     * @param   $show_author_desc
     *
     * @uses    get_avatar
     * @uses    get_the_author_meta
     * @uses    user_can
     */
    function author_details( $author_id, $show_author_url, $show_author_email, $show_author_desc ){
        /** Collect details from the author's profile */
        $author_display_name   = get_the_author_meta( 'display_name', $author_id );
        $author_url            = get_the_author_meta( 'user_url', $author_id );
        $author_email          = get_the_author_meta( 'user_email', $author_id );
        $author_desc           = get_the_author_meta( 'user_description', $author_id );

        /** Add empty hook before author details */
        do_action( 'opus_before_author_details' ); ?>

        <div class="author-details <?php $this->author_classes( $author_id ); ?>">
            <h2>
                <?php
                /** Sanity check - an author id should always be present */
                if ( ! empty( $author_id ) )
                    echo get_avatar( $author_id );
                printf( '<span class="opus-author-about">' . __( 'About %1$s', 'opusprimus' ) . '</span>', $author_display_name ); ?>
            </h2>
            <ul>
                <?php
                /** Check for the author URL; if show Author URL is true; and, show Author email is true */
                if ( ! empty( $author_url ) && $show_author_url && $show_author_email ) { ?>
                    <li>
                        <?php
                        printf( '<span class="opus-author-contact">' . __( 'Visit the web site of %1$s or email %2$s.', 'opusprimus' ) . '</span>',
                            '<a class="opus-author-url" href="' . $author_url . '">' . $author_display_name . '</a>',
                            '<a class="opus-author-email" href="mailto:' .  $author_email . '">' . $author_display_name . '</a>'
                        ); ?>
                    </li>
                    <?php
                    /** Check for the author URL; show Author URL is true; and, show Author email is false */
                } elseif ( ! empty( $author_url ) && $show_author_url && ! $show_author_email ) { ?>
                    <li>
                        <?php
                        printf( '<span class="opus-author-contact">' . __( 'Visit the web site of %1$s.', 'opusprimus' ) . '</span>',
                            '<a class="opus-author-url" href="' . $author_url . '">' . $author_display_name . '</a>'
                        ); ?>
                    </li>
                    <?php
                    /** Check for the author URL; show Author URL is false; and, show Author email is true */
                } elseif ( ! empty( $author_url ) && ! $show_author_url && $show_author_email ) { ?>
                    <li>
                        <?php
                        printf( '<span class="opus-author-contact">' . __( 'Email %1$s.', 'opusprimus' ) . '</span>',
                            '<a class="opus-author-email" href="mailto:' .  $author_email . '">' . $author_display_name . '</a>'
                        ); ?>
                    </li>
                    <?php
                    /** The last option available in this logic chain: no Author URL and show Author email is true */
                } elseif ( $show_author_email ) { ?>
                    <li>
                        <?php
                        printf( '<span class="opus-author-contact">' . __( 'Email %1$s.', 'opusprimus' ) . '</span>',
                            '<a class="opus-author-email" href="mailto:' .  $author_email . '">' . $author_display_name . '</a>'
                        ); ?>
                    </li>
                    <?php }
                /** Check for the author bio; and, if show Author Desc is true */
                if ( ! empty( $author_desc ) && $show_author_desc ) { ?>
                    <li>
                        <?php printf( '<span class="opus-author-biography">' . __( 'Biography: %1$s', 'opusprimus' ) . '</span>', $author_desc ); ?>
                    </li>
                    <?php } ?>
            </ul>
        </div>

        <?php
        /** Add empty hook after author details */
        do_action( 'opus_after_author_details' );

        return;

    }

    /**
     * Author Classes
     * Additional author classes related to the use and their capabilities
     *
     * @package OpusPrimus
     * @since   0.1
     *
     * @param   $author_id
     *
     * @uses    get_the_author_meta
     * @uses    replace_spaces
     */
    function author_classes( $author_id ) {
        /** Call the structure class to use replace spaces */
        global $opus_structure;
        /** Add class as related to the user role (see 'Role:' drop-down in User options) */
        if ( user_can( $author_id, 'administrator' ) ) {
            echo 'administrator';
        } elseif ( user_can( $author_id, 'editor' ) ) {
            echo 'editor';
        } elseif ( user_can( $author_id, 'contributor' ) ) {
            echo 'contributor';
        } elseif ( user_can( $author_id, 'subscriber' ) ) {
            echo 'subscriber';
        } else {
            echo 'guest';
        };
        /** Check if this is the first user */
        if ( ( $author_id ) == '1' ) {
            echo ' administrator-prime';
        }
        echo ' author-' . $author_id;
        echo ' author-' . $opus_structure->replace_spaces( get_the_author_meta( 'display_name', $author_id ) );
    }

    /**
     * Author Coda
     * Adds text art after the author details to signify the end of the output
     * block
     *
     * @package OpusPrimus
     * @since   0.1
     *
     * @uses    apply_filters
     * @uses    do_action
     */
    function author_coda(){
        /** Add empty hook before post coda */
        do_action( 'opus_before_author_coda' );

        /** Create the text art */
        $author_coda = '|=|=|=|=|';
        printf( '<div class="author-coda">%1$s</div>', apply_filters( 'opus_author_coda', $author_coda )  );

        /** Add empty hook after the post coda */
        do_action( 'opus_after_author_coda' );

    }

}
$opus_authors = new OpusPrimusAuthors();