<?php
/**
 * Remove Post Type Slugs from Post URL.
 */
final class TS_Remove_Slug_From_CPT {
	private static $instance;

	/**
	 * Array of Post Types to remove slugs from.
	 */
	private $custom_post_types = [ 
		'landing_page',
	];

	/**
	 * Get an instance of the class.
	 *
	 * This method ensures that only one instance of the class is created,
	 * providing a global point of access to the class's functionality.
	 *
	 * @return TS_Remove_Slug_From_CPT The singleton instance of the class.
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * Initializes the class by adding necessary hooks to WordPress actions.
	 *
	 * @return void
	 */
	private function __construct() {
		// Add a filter to modify the post type link.
		add_filter( 'post_type_link', [ $this, 'filter_post_type_link' ], 10, 3 );

		// Add a filter to modify the rewrite rules.
		add_filter( 'rewrite_rules_array', [ $this, 'remove_custom_post_type_slug' ], PHP_INT_MAX );
	}

	/**
	 * Removes the custom post type slug from the rewrite rules.
	 *
	 * This function modifies the rewrite rules to remove the custom post type slug from the URL.
	 * It matches the custom post type 'landing_page' without the slug and replaces it with the appropriate query parameters.
	 *
	 * @param array $rules The existing rewrite rules.
	 *
	 * @return array The modified rewrite rules.
	 */
	public function remove_custom_post_type_slug( $rules ) {
		$new_rules = array();

		// Match the custom post type 'landing_page' without the slug.
		$new_rules['([^/]+)/?$'] = 'index.php?post_type=landing_page&name=$matches[1]';

		// Merge with existing rules
		// Least prioritize the new rules by placing the new rules at the end of the existing rules array.
		return $rules + $new_rules;
	}

	/**
	 * Filters the post type link to remove the post type slug.
	 *
	 * This function modifies the post type link to remove the post type slug from the URL.
	 * It checks if the post type is in the list of types to be modified and if the post status is 'publish'.
	 * If both conditions are met, it replaces the post type slug in the link with an empty string.
	 *
	 * @param string $post_link The original post type link.
	 * @param WP_Post $post The post object.
	 * @param bool $leavename Whether to keep the post name in the link.
	 *
	 * @return string The modified post type link.
	 */
	public function filter_post_type_link( $post_link, $post, $leavename ) {
		if ( ! in_array( $post->post_type, $this->custom_post_types, true ) || 'publish' !== $post->post_status ) {
			return $post_link;
		}
		if ( $slug = $this->get_post_type_slug( $post->post_type ) ) {
			$post_link = \str_replace( "/{$slug}/", '/', $post_link );
		}
		return $post_link;
	}

	/**
	 * Retrieves the slug for a given post type.
	 *
	 * This function retrieves the slug for a given post type. It first checks if the post type
	 * has a custom rewrite slug defined. If not, it falls back to the post type's name.
	 *
	 * @param string $type The post type for which to retrieve the slug.
	 *
	 * @return string The slug for the given post type.
	 */
	private function get_post_type_slug( $type ): string {
		$obj = get_post_type_object( $type );
		if ( isset( $obj->rewrite['slug'] ) && $obj->rewrite['slug'] ) {
			return $obj->rewrite['slug'];
		} elseif ( isset( $obj->name ) && $obj->name ) {
			return $obj->name;
		} else {
			return $type;
		}
	}

}

TS_Remove_Slug_From_CPT::get_instance();
