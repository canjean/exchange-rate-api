<?php
/**
 * Plugin Name:       Canjean Currency Converter
 * Plugin URI:        https://canjean.com/widgets
 * Description:       A live currency converter for posts, pages and sidebars: 150+ currencies, updated daily, in 5 languages, light or dark. Add it with the [canjean_converter] shortcode or the "Canjean Currency Converter" widget.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Canjean
 * Author URI:        https://canjean.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       canjean-currency-converter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CANJEAN_CC_VERSION', '1.0.0' );
define( 'CANJEAN_CC_OPTION', 'canjean_cc_settings' );

/**
 * Languages the converter interface is available in.
 */
function canjean_cc_languages() {
	return array(
		'auto' => __( 'Same as the site', 'canjean-currency-converter' ),
		'en'   => 'English',
		'es'   => 'Español',
		'pt'   => 'Português',
		'de'   => 'Deutsch',
		'fr'   => 'Français',
	);
}

/**
 * Saved settings merged with the defaults. The credit link is off unless the site owner turns it on.
 */
function canjean_cc_settings() {
	$defaults = array(
		'from'   => 'USD',
		'to'     => 'EUR',
		'amount' => 100,
		'lang'   => 'auto',
		'theme'  => 'light',
		'credit' => 0,
	);
	$saved = get_option( CANJEAN_CC_OPTION, array() );
	return array_merge( $defaults, is_array( $saved ) ? $saved : array() );
}

/**
 * Cleans one set of converter options (from shortcode attributes, widget fields or the settings page).
 */
function canjean_cc_clean( $args, $defaults ) {
	$out = $defaults;
	foreach ( array( 'from', 'to' ) as $key ) {
		if ( isset( $args[ $key ] ) ) {
			$code = strtoupper( trim( (string) $args[ $key ] ) );
			if ( preg_match( '/^[A-Z]{3}$/', $code ) ) {
				$out[ $key ] = $code;
			}
		}
	}
	if ( isset( $args['amount'] ) ) {
		$amount = (float) $args['amount'];
		if ( $amount > 0 && $amount < 1e12 ) {
			$out['amount'] = $amount;
		}
	}
	if ( isset( $args['lang'] ) && array_key_exists( (string) $args['lang'], canjean_cc_languages() ) ) {
		$out['lang'] = (string) $args['lang'];
	}
	if ( isset( $args['theme'] ) && in_array( $args['theme'], array( 'light', 'dark' ), true ) ) {
		$out['theme'] = $args['theme'];
	}
	return $out;
}

/**
 * Interface language: the chosen one, or the site's language when it is one of the five available.
 */
function canjean_cc_resolve_lang( $lang ) {
	if ( 'auto' !== $lang ) {
		return $lang;
	}
	$site = substr( determine_locale(), 0, 2 );
	return in_array( $site, array( 'en', 'es', 'pt', 'de', 'fr' ), true ) ? $site : 'en';
}

/**
 * Link shown under the converter when the site owner has opted in.
 */
function canjean_cc_credit_link( $from, $to, $lang ) {
	$hosts = array(
		'en' => 'https://canjean.com/convert-%s-to-%s',
		'es' => 'https://es.canjean.com/convertir-%s-a-%s',
		'pt' => 'https://pt.canjean.com/converter-%s-em-%s',
		'de' => 'https://de.canjean.com/%s-in-%s-umrechnen',
		'fr' => 'https://fr.canjean.com/convertir-%s-en-%s',
	);
	$texts = array(
		'en' => 'Exchange rates by Canjean',
		'es' => 'Tipos de cambio por Canjean',
		'pt' => 'Cotações por Canjean',
		'de' => 'Wechselkurse von Canjean',
		'fr' => 'Taux de change par Canjean',
	);
	return '<a href="' . esc_url( sprintf( $hosts[ $lang ], $from, $to ) ) . '">' . esc_html( $texts[ $lang ] ) . '</a>';
}

/**
 * HTML for one converter. The bundled script turns the placeholder into the converter in the visitor's browser.
 */
function canjean_cc_render( $opts ) {
	$settings = canjean_cc_settings();
	$lang     = canjean_cc_resolve_lang( $opts['lang'] );

	wp_enqueue_script( 'canjean-cc-widget', plugins_url( 'assets/widget.js', __FILE__ ), array(), CANJEAN_CC_VERSION, true );

	$credit = ! empty( $settings['credit'] ) ? canjean_cc_credit_link( $opts['from'], $opts['to'], $lang ) : '';

	return sprintf(
		'<div class="canjean-widget" data-from="%s" data-to="%s" data-amount="%s" data-lang="%s" data-theme="%s">%s</div>',
		esc_attr( $opts['from'] ),
		esc_attr( $opts['to'] ),
		esc_attr( rtrim( rtrim( number_format( (float) $opts['amount'], 2, '.', '' ), '0' ), '.' ) ),
		esc_attr( $lang ),
		esc_attr( $opts['theme'] ),
		$credit
	);
}

/* ---------- Shortcode: [canjean_converter from="GBP" to="EUR" amount="50" lang="auto" theme="dark"] ---------- */

function canjean_cc_shortcode( $atts ) {
	$defaults = canjean_cc_settings();
	$atts     = is_array( $atts ) ? $atts : array();
	return canjean_cc_render( canjean_cc_clean( $atts, $defaults ) );
}
add_shortcode( 'canjean_converter', 'canjean_cc_shortcode' );

/* ---------- Widget for sidebars and footers ---------- */

class Canjean_CC_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'canjean_cc_widget',
			__( 'Canjean Currency Converter', 'canjean-currency-converter' ),
			array( 'description' => __( 'Live currency converter with 150+ currencies.', 'canjean-currency-converter' ) )
		);
	}

	private function values( $instance ) {
		$values          = canjean_cc_clean( is_array( $instance ) ? $instance : array(), canjean_cc_settings() );
		$values['title'] = isset( $instance['title'] ) ? $instance['title'] : '';
		return $values;
	}

	public function widget( $args, $instance ) {
		$values = $this->values( $instance );
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme.
		if ( '' !== $values['title'] ) {
			echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $values['title'], $instance, $this->id_base ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme.
		}
		echo canjean_cc_render( $values ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in canjean_cc_render().
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme.
	}

	public function form( $instance ) {
		$values = $this->values( $instance );
		$fields = array(
			'title'  => __( 'Title (optional)', 'canjean-currency-converter' ),
			'from'   => __( 'From currency (3-letter code, e.g. USD)', 'canjean-currency-converter' ),
			'to'     => __( 'To currency (3-letter code, e.g. EUR)', 'canjean-currency-converter' ),
			'amount' => __( 'Starting amount', 'canjean-currency-converter' ),
		);
		foreach ( $fields as $key => $label ) {
			printf(
				'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p>',
				esc_attr( $this->get_field_id( $key ) ),
				esc_html( $label ),
				esc_attr( $this->get_field_name( $key ) ),
				esc_attr( $values[ $key ] )
			);
		}
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'lang' ) ) . '">' . esc_html__( 'Language', 'canjean-currency-converter' ) . '</label> ';
		echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'lang' ) ) . '" name="' . esc_attr( $this->get_field_name( 'lang' ) ) . '">';
		foreach ( canjean_cc_languages() as $code => $name ) {
			echo '<option value="' . esc_attr( $code ) . '"' . selected( $values['lang'], $code, false ) . '>' . esc_html( $name ) . '</option>';
		}
		echo '</select></p>';
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'theme' ) ) . '">' . esc_html__( 'Theme', 'canjean-currency-converter' ) . '</label> ';
		echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'theme' ) ) . '" name="' . esc_attr( $this->get_field_name( 'theme' ) ) . '">';
		echo '<option value="light"' . selected( $values['theme'], 'light', false ) . '>' . esc_html__( 'Light', 'canjean-currency-converter' ) . '</option>';
		echo '<option value="dark"' . selected( $values['theme'], 'dark', false ) . '>' . esc_html__( 'Dark', 'canjean-currency-converter' ) . '</option>';
		echo '</select></p>';
	}

	public function update( $new_instance, $old_instance ) {
		$clean          = canjean_cc_clean( is_array( $new_instance ) ? $new_instance : array(), canjean_cc_settings() );
		$clean['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		unset( $clean['credit'] ); // The credit link is a site-wide setting, not a per-widget one.
		return $clean;
	}
}

add_action(
	'widgets_init',
	function () {
		register_widget( 'Canjean_CC_Widget' );
	}
);

/* ---------- Settings page: Settings → Currency Converter ---------- */

function canjean_cc_sanitize_settings( $input ) {
	$input           = is_array( $input ) ? $input : array();
	$clean           = canjean_cc_clean( $input, canjean_cc_settings() );
	$clean['credit'] = empty( $input['credit'] ) ? 0 : 1;
	return $clean;
}

add_action(
	'admin_init',
	function () {
		register_setting(
			'canjean_cc',
			CANJEAN_CC_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => 'canjean_cc_sanitize_settings',
			)
		);
	}
);

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'Canjean Currency Converter', 'canjean-currency-converter' ),
			__( 'Currency Converter', 'canjean-currency-converter' ),
			'manage_options',
			'canjean-currency-converter',
			'canjean_cc_settings_page'
		);
	}
);

function canjean_cc_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s    = canjean_cc_settings();
	$name = CANJEAN_CC_OPTION;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Canjean Currency Converter', 'canjean-currency-converter' ); ?></h1>
		<p>
			<?php esc_html_e( 'Add the converter to any post or page with the shortcode below, or to a sidebar from Appearance → Widgets. These are the defaults; you can change them for each converter.', 'canjean-currency-converter' ); ?>
		</p>
		<p><code>[canjean_converter]</code> &nbsp; <code>[canjean_converter from="GBP" to="EUR" amount="50" theme="dark"]</code></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'canjean_cc' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="canjean-cc-from"><?php esc_html_e( 'From currency', 'canjean-currency-converter' ); ?></label></th>
					<td><input id="canjean-cc-from" name="<?php echo esc_attr( $name ); ?>[from]" type="text" maxlength="3" class="small-text" value="<?php echo esc_attr( $s['from'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="canjean-cc-to"><?php esc_html_e( 'To currency', 'canjean-currency-converter' ); ?></label></th>
					<td>
						<input id="canjean-cc-to" name="<?php echo esc_attr( $name ); ?>[to]" type="text" maxlength="3" class="small-text" value="<?php echo esc_attr( $s['to'] ); ?>">
						<p class="description"><?php esc_html_e( 'Three-letter ISO codes such as USD, EUR, GBP, JPY or BTC. Visitors can pick any other currency in the converter.', 'canjean-currency-converter' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="canjean-cc-amount"><?php esc_html_e( 'Starting amount', 'canjean-currency-converter' ); ?></label></th>
					<td><input id="canjean-cc-amount" name="<?php echo esc_attr( $name ); ?>[amount]" type="number" min="0" step="any" class="small-text" value="<?php echo esc_attr( $s['amount'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="canjean-cc-lang"><?php esc_html_e( 'Language', 'canjean-currency-converter' ); ?></label></th>
					<td>
						<select id="canjean-cc-lang" name="<?php echo esc_attr( $name ); ?>[lang]">
							<?php foreach ( canjean_cc_languages() as $code => $label ) : ?>
								<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $s['lang'], $code ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="canjean-cc-theme"><?php esc_html_e( 'Theme', 'canjean-currency-converter' ); ?></label></th>
					<td>
						<select id="canjean-cc-theme" name="<?php echo esc_attr( $name ); ?>[theme]">
							<option value="light" <?php selected( $s['theme'], 'light' ); ?>><?php esc_html_e( 'Light', 'canjean-currency-converter' ); ?></option>
							<option value="dark" <?php selected( $s['theme'], 'dark' ); ?>><?php esc_html_e( 'Dark', 'canjean-currency-converter' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Credit link', 'canjean-currency-converter' ); ?></th>
					<td>
						<label>
							<input name="<?php echo esc_attr( $name ); ?>[credit]" type="checkbox" value="1" <?php checked( ! empty( $s['credit'] ) ); ?>>
							<?php esc_html_e( 'Show a small "Exchange rates by Canjean" link under the converter', 'canjean-currency-converter' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Optional and off by default. The converter works the same either way; the link is a way to say thanks if you find it useful.', 'canjean-currency-converter' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<h2><?php esc_html_e( 'Where the rates come from', 'canjean-currency-converter' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: 1: URL of the rates file, 2: URL of the privacy policy. */
				esc_html__( 'When a visitor opens a page with the converter, their browser downloads the exchange rates from %1$s and the currency flags from canjean.com. No cookies are set and nothing is tracked. Privacy policy: %2$s', 'canjean-currency-converter' ),
				'<code>https://canjean.com/api/rates.json</code>',
				'<a href="https://canjean.com/privacy-policy" target="_blank" rel="noopener">canjean.com/privacy-policy</a>'
			);
			?>
		</p>
	</div>
	<?php
}

/* ---------- "Settings" link on the Plugins screen ---------- */

add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	function ( $links ) {
		$url = admin_url( 'options-general.php?page=canjean-currency-converter' );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'canjean-currency-converter' ) . '</a>' );
		return $links;
	}
);

/* ---------- One-time question after activation: show the credit link or not ---------- */

define( 'CANJEAN_CC_ASKED', 'canjean_cc_credit_asked' );

register_activation_hook(
	__FILE__,
	function () {
		// Ask once, on the first activation only. Nothing is shown on the site until the owner says yes.
		add_option( CANJEAN_CC_ASKED, 'pending' );
	}
);

function canjean_cc_should_ask() {
	if ( 'pending' !== get_option( CANJEAN_CC_ASKED ) || ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	return $screen && in_array( $screen->id, array( 'dashboard', 'plugins', 'settings_page_canjean-currency-converter' ), true );
}

add_action(
	'admin_notices',
	function () {
		if ( ! canjean_cc_should_ask() ) {
			return;
		}
		$url = function ( $choice ) {
			return wp_nonce_url( admin_url( 'admin-post.php?action=canjean_cc_credit&choice=' . $choice ), 'canjean_cc_credit' );
		};
		?>
		<div class="notice notice-info is-dismissible" id="canjean-cc-credit-notice">
			<p><strong><?php esc_html_e( 'Thanks for installing Canjean Currency Converter!', 'canjean-currency-converter' ); ?></strong></p>
			<p><?php esc_html_e( 'Would you like to show a small "Exchange rates by Canjean" link under the converter? It is optional, the converter works exactly the same without it, and you can change it at any time in Settings → Currency Converter.', 'canjean-currency-converter' ); ?></p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( $url( 'yes' ) ); ?>"><?php esc_html_e( 'Yes, show the link', 'canjean-currency-converter' ); ?></a>
				<a class="button" href="<?php echo esc_url( $url( 'no' ) ); ?>"><?php esc_html_e( 'No thanks', 'canjean-currency-converter' ); ?></a>
			</p>
		</div>
		<?php
	}
);

// Closing the notice with its × counts as "No thanks", so it never comes back.
add_action(
	'admin_enqueue_scripts',
	function () {
		if ( ! canjean_cc_should_ask() ) {
			return;
		}
		$data = wp_json_encode(
			array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'canjean_cc_credit' ),
			)
		);
		wp_add_inline_script(
			'common',
			'jQuery(function($){var d=' . $data . ';$(document).on("click","#canjean-cc-credit-notice .notice-dismiss",function(){$.post(d.url,{action:"canjean_cc_credit_dismiss",_wpnonce:d.nonce});});});'
		);
	}
);

function canjean_cc_save_choice( $show ) {
	if ( $show ) {
		$settings           = canjean_cc_settings();
		$settings['credit'] = 1;
		update_option( CANJEAN_CC_OPTION, $settings );
	}
	update_option( CANJEAN_CC_ASKED, 'done' );
}

add_action(
	'admin_post_canjean_cc_credit',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'canjean-currency-converter' ), 403 );
		}
		check_admin_referer( 'canjean_cc_credit' );
		$show = isset( $_GET['choice'] ) && 'yes' === sanitize_key( wp_unslash( $_GET['choice'] ) );
		canjean_cc_save_choice( $show );
		$back = wp_get_referer();
		wp_safe_redirect( $back ? $back : admin_url( 'options-general.php?page=canjean-currency-converter' ) );
		exit;
	}
);

add_action(
	'wp_ajax_canjean_cc_credit_dismiss',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( null, 403 );
		}
		check_ajax_referer( 'canjean_cc_credit' );
		canjean_cc_save_choice( false );
		wp_send_json_success();
	}
);
