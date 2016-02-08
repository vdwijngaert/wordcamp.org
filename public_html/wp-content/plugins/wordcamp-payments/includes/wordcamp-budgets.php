<?php

/*
 * Main class to provide functionality common to all other classes
 */
class WordCamp_Budgets {
	const VERSION = '0.1.4';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu',             array( $this, 'register_budgets_menu' )     );
		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_common_assets' ), 11 );
	}

	/**
	 * Register the Budgets menu
	 *
	 * This is just an empty page so that a top-level menu can be created to hold the various post types and pages.
	 *
	 * @todo This may no longer be needed once the Budgets post type and Overview pages are added
	 */
	public function register_budgets_menu() {
		add_menu_page(
			__( 'WordCamp Budget', 'wordcamporg' ),
			__( 'Budget',          'wordcamporg' ),
			'manage_options',
			'wordcamp-budget',
			'__return_empty_string',
			plugins_url( 'images/dollar-sign-icon.svg', dirname( __FILE__ ) ),
			30
		);
	}

	/**
	 * Enqueue scripts and stylesheets common to all modules
	 */
	public function enqueue_common_assets() {
		// todo setup grunt to concat/minify js

		wp_enqueue_script(
			'wordcamp-budgets',
			plugins_url( 'javascript/wordcamp-budgets.js', __DIR__ ),
			array( 'jquery', 'jquery-ui-datepicker', 'media-upload', 'media-views' ),
			1,
			true
		);

		wp_register_script(
			'wcb-attached-files',
			plugins_url( 'javascript/attached-files.js', __DIR__ ),
			array( 'wordcamp-budgets', 'backbone', 'wp-util' ),
			1,
			true
		);

		wp_localize_script(
			'wordcamp-budgets',
			'wcbLocalizedStrings',		// todo merge into WordCampBudgets var
			array(
				'uploadModalTitle'  => __( 'Attach Supporting Documentation', 'wordcamporg' ),
				'uploadModalButton' => __( 'Attach Files', 'wordcamporg' ),
			)
		);

		// Let's still include our .css file even if these are unavailable.
		$soft_deps = array( 'jquery-ui', 'wp-datepicker-skins' );
		foreach ( $soft_deps as $key => $handle )
			if ( ! wp_style_is( $handle, 'registered' ) )
				unset( $soft_deps[ $key ] );

		// Enqueue it on every screen, because it styles the menu icon
		wp_enqueue_style(
			'wordcamp-budgets',
			plugins_url( 'css/wordcamp-budgets.css', __DIR__ ),
			$soft_deps,
			2
		);
	}

	/**
	 * Validate an amount value
	 *
	 * @param string $amount
	 *
	 * @return float
	 */
	public static function validate_amount( $amount ) {
		$amount = sanitize_text_field( $amount );
		$amount = preg_replace( '#[^\d.-]+#', '', $amount );
		$amount = round( floatval( $amount ), 2 );

		return $amount;
	}

	/**
	 * Return an array of countries where the key is an ISO 3166 code
	 * and the value is a label.
	 *
	 * @see https://gist.github.com/vxnick/380904
	 *
	 * @return array
	 */
	public static function get_valid_countries_iso3166() {
		$countries = array(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua And Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia And Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (Keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo',
			'CD' => 'Congo, Democratic Republic',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Cote D\'Ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands (Malvinas)',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island & Mcdonald Islands',
			'VA' => 'Holy See (Vatican City State)',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran, Islamic Republic Of',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle Of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KR' => 'Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao People\'s Democratic Republic',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libyan Arab Jamahiriya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia, Federated States Of',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'AN' => 'Netherlands Antilles',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory, Occupied',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts And Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre And Miquelon',
			'VC' => 'Saint Vincent And Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome And Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia And Sandwich Isl.',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard And Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad And Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks And Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Viet Nam',
			'VG' => 'Virgin Islands, British',
			'VI' => 'Virgin Islands, U.S.',
			'WF' => 'Wallis And Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);

		return $countries;
	}

	/**
	 * Get a list of valid payment methods
	 *
	 * @return array
	 */
	public static function get_valid_payment_methods() {
		return array( 'Direct Deposit', 'Check', 'Credit Card', 'Wire' );
	}

	/**
	 * Validate and save payment method fields
	 *
	 * @param int $post_id
	 */
	public static function validate_save_payment_method_fields( $post_id, $meta_key_prefix ) {
		foreach ( $_POST as $key => $unsafe_value ) {
			$unsafe_value = wp_unslash( $unsafe_value );

			switch ( $key ) {
				case 'bank_name':
				case 'bank_street_address':
				case 'bank_city':
				case 'bank_state':
				case 'bank_zip_code':
				case 'bank_country':
				case 'bank_bic':

				case 'interm_bank_name':
				case 'interm_bank_street_address':
				case 'interm_bank_city':
				case 'interm_bank_state':
				case 'interm_bank_zip_code':
				case 'interm_bank_swift':
				case 'interm_bank_account':

				case 'beneficiary_account_number':
				case 'beneficiary_name':
				case 'beneficiary_street_address':
				case 'beneficiary_city':
				case 'beneficiary_state':
				case 'beneficiary_zip_code':
				case 'beneficiary_country':
				case 'payable_to':

				case 'ach_bank_name':
				case 'ach_routing_number':
				case 'ach_account_number':
				case 'ach_account_holder_name':
					$safe_value = sanitize_text_field( $unsafe_value );
					break;

				case 'ach_account_type':
					if ( in_array( $unsafe_value, array( 'Personal', 'Company' ) ) ) {
						$safe_value = $unsafe_value;
					} else {
						$safe_value = false;
					}
					break;

				case 'payment_method':
					if ( in_array( $unsafe_value, self::get_valid_payment_methods(), true ) ) {
						$safe_value = $unsafe_value;
					} else {
						$safe_value = false;
					}
					break;

				case 'bank_country_iso3166':
				case 'interm_bank_country_iso3166':
				case 'beneficiary_country_iso3166':
					if ( array_key_exists( $unsafe_value, self::get_valid_countries_iso3166() ) ) {
						$safe_value = $unsafe_value;
					}
					break;

				default:
					$safe_value = null;
					break;
			}

			if ( is_null( $safe_value ) ) {
				continue;
			}

			if ( in_array( $key, self::get_encrypted_fields() ) ) {
				$encrypted_value = WCP_Encryption::encrypt( $safe_value );

				if ( ! is_wp_error( $encrypted_value ) ) {
					$safe_value = $encrypted_value;
				}
			}

			update_post_meta( $post_id, "_{$meta_key_prefix}_" . $key, $safe_value );
		}
	}

	/**
	 * Get the names of all the fields that should be encrypted
	 *
	 * @return array
	 */
	public static function get_encrypted_fields() {
		return array(
			'payable_to',
			'beneficiary_name',
			'beneficiary_account_number',
			'beneficiary_street_address',
			'beneficiary_city',
			'beneficiary_state',
			'beneficiary_zip_code',
			'beneficiary_country',
			'beneficiary_country_iso3166',
			'ach_bank_name',
			'ach_routing_number',
			'ach_account_number',
			'ach_account_holder_name',
		);
	}

	/**
	 * Get a list of all world currencies, with the most frequently used at the top.
	 *
	 * @return array
	 */
	public static function get_currencies() {
		$currencies = array (
			'null-most-frequently-used' => 'Most Frequently Used:',
			'USD' => 'United States Dollar',
			'EUR' => 'Euro Member Countries',

			'null-separator2' => '',

			'null-all' => 'All:',
			'ALL' => 'Albania Lek',
			'AFN' => 'Afghanistan Afghani',
			'ARS' => 'Argentina Peso',
			'AWG' => 'Aruba Guilder',
			'AUD' => 'Australia Dollar',
			'AZN' => 'Azerbaijan New Manat',
			'BSD' => 'Bahamas Dollar',
			'BBD' => 'Barbados Dollar',
			'BDT' => 'Bangladeshi taka',
			'BYR' => 'Belarus Ruble',
			'BZD' => 'Belize Dollar',
			'BMD' => 'Bermuda Dollar',
			'BOB' => 'Bolivia Boliviano',
			'BAM' => 'Bosnia and Herzegovina Convertible Marka',
			'BWP' => 'Botswana Pula',
			'BGN' => 'Bulgaria Lev',
			'BRL' => 'Brazil Real',
			'BND' => 'Brunei Darussalam Dollar',
			'KHR' => 'Cambodia Riel',
			'CAD' => 'Canada Dollar',
			'KYD' => 'Cayman Islands Dollar',
			'CLP' => 'Chile Peso',
			'CNY' => 'China Yuan Renminbi',
			'COP' => 'Colombia Peso',
			'CRC' => 'Costa Rica Colon',
			'HRK' => 'Croatia Kuna',
			'CUP' => 'Cuba Peso',
			'CZK' => 'Czech Republic Koruna',
			'DKK' => 'Denmark Krone',
			'DOP' => 'Dominican Republic Peso',
			'XCD' => 'East Caribbean Dollar',
			'EGP' => 'Egypt Pound',
			'SVC' => 'El Salvador Colon',
			'EEK' => 'Estonia Kroon',
			'FKP' => 'Falkland Islands (Malvinas) Pound',
			'FJD' => 'Fiji Dollar',
			'GHC' => 'Ghana Cedis',
			'GIP' => 'Gibraltar Pound',
			'GTQ' => 'Guatemala Quetzal',
			'GGP' => 'Guernsey Pound',
			'GYD' => 'Guyana Dollar',
			'HNL' => 'Honduras Lempira',
			'HKD' => 'Hong Kong Dollar',
			'HUF' => 'Hungary Forint',
			'ISK' => 'Iceland Krona',
			'INR' => 'India Rupee',
			'IDR' => 'Indonesia Rupiah',
			'IRR' => 'Iran Rial',
			'IMP' => 'Isle of Man Pound',
			'ILS' => 'Israel Shekel',
			'JMD' => 'Jamaica Dollar',
			'JPY' => 'Japan Yen',
			'JEP' => 'Jersey Pound',
			'KZT' => 'Kazakhstan Tenge',
			'KPW' => 'Korea (North) Won',
			'KRW' => 'Korea (South) Won',
			'KGS' => 'Kyrgyzstan Som',
			'LAK' => 'Laos Kip',
			'LVL' => 'Latvia Lat',
			'LBP' => 'Lebanon Pound',
			'LRD' => 'Liberia Dollar',
			'LTL' => 'Lithuania Litas',
			'MKD' => 'Macedonia Denar',
			'MYR' => 'Malaysia Ringgit',
			'MUR' => 'Mauritius Rupee',
			'MXN' => 'Mexico Peso',
			'MNT' => 'Mongolia Tughrik',
			'MZN' => 'Mozambique Metical',
			'NAD' => 'Namibia Dollar',
			'NPR' => 'Nepal Rupee',
			'ANG' => 'Netherlands Antilles Guilder',
			'NZD' => 'New Zealand Dollar',
			'NIO' => 'Nicaragua Cordoba',
			'NGN' => 'Nigeria Naira',
			'NOK' => 'Norway Krone',
			'OMR' => 'Oman Rial',
			'PKR' => 'Pakistan Rupee',
			'PAB' => 'Panama Balboa',
			'PYG' => 'Paraguay Guarani',
			'PEN' => 'Peru Nuevo Sol',
			'PHP' => 'Philippines Peso',
			'PLN' => 'Poland Zloty',
			'QAR' => 'Qatar Riyal',
			'RON' => 'Romania New Leu',
			'RUB' => 'Russia Ruble',
			'SHP' => 'Saint Helena Pound',
			'SAR' => 'Saudi Arabia Riyal',
			'RSD' => 'Serbia Dinar',
			'SCR' => 'Seychelles Rupee',
			'SGD' => 'Singapore Dollar',
			'SBD' => 'Solomon Islands Dollar',
			'SOS' => 'Somalia Shilling',
			'ZAR' => 'South Africa Rand',
			'LKR' => 'Sri Lanka Rupee',
			'SEK' => 'Sweden Krona',
			'CHF' => 'Switzerland Franc',
			'SRD' => 'Suriname Dollar',
			'SYP' => 'Syria Pound',
			'TWD' => 'Taiwan New Dollar',
			'THB' => 'Thailand Baht',
			'TTD' => 'Trinidad and Tobago Dollar',
			'TRY' => 'Turkey Lira',
			'TRL' => 'Turkey Lira',
			'TVD' => 'Tuvalu Dollar',
			'UAH' => 'Ukraine Hryvna',
			'GBP' => 'United Kingdom Pound',
			'UYU' => 'Uruguay Peso',
			'UZS' => 'Uzbekistan Som',
			'VEF' => 'Venezuela Bolivar',
			'VND' => 'Viet Nam Dong',
			'YER' => 'Yemen Rial',
			'ZWD' => 'Zimbabwe Dollar'
		);

		return $currencies;
	}

	/**
	 * Define the payment categories
	 *
	 * The slugs are explicitly registered in English so they will match across sites that use different locales,
	 * which facilitates aggregating the data into reports.
	 *
	 * @return array
	 */
	public static function get_payment_categories() {
		return array(
			'after-party'     => __( 'After Party',                    'wordcamporg' ),
			'audio-visual'    => __( 'Audio Visual',                   'wordcamporg' ),
			'camera-shipping' => __( 'Camera Shipping',                'wordcamporg' ),
			'food-beverages'  => __( 'Food & Beverage',                'wordcamporg' ),
			'office-supplies' => __( 'Office Supplies',                'wordcamporg' ),
			'signage-badges'  => __( 'Signage & Badges',               'wordcamporg' ),
			'speaker-event'   => __( 'Speaker Event',                  'wordcamporg' ),
			'swag'            => __( 'Swag (t-shirts, stickers, etc)', 'wordcamporg' ),
			'venue'           => __( 'Venue',                          'wordcamporg' ),
			'other'           => __( 'Other',                          'wordcamporg' ), // This one is intentionally last, regardless of alphabetical order
		);
	}

	/**
	 * Determines whether we want to perform actions on the given post based on the current context.
	 *
	 * Examples of actions we might perform are saving the meta fields during the `save_post` hook, or send out an
	 * e-mail notification during the `transition_post_status` hook.
	 *
	 * This function is called by several other functions, each of which may require additional checks that are
	 * specific to their circumstances. This function only covers checks that are common to all of its callers.
	 *
	 * @param WP_Post|array $post
	 * @param string        $valid_post_type
	 *
	 * @return bool
	 */
	public static function post_edit_is_actionable( $post, $valid_post_type ) {
		if ( is_array( $post ) ) {
			$post = (object) $post;
		}

		$is_actionable   = true;
		$ignored_actions = array( 'trash', 'untrash', 'restore', 'bulk_edit' ); // todo ignore bulk deletion too

		// Don't take action on other post types
		if ( ! $post || $post->post_type != $valid_post_type ) {
			$is_actionable = false;
		}

		// Don't take action if the user isn't allowed. The ID will be missing from new posts during `wp_insert_post_data`, though, so skip it then.
		if ( $is_actionable && isset( $post->ID ) && ! current_user_can( 'edit_post', $post->ID ) ) {
			$is_actionable = false;
		}

		// Don't take action while trashing the post, etc
		if ( $is_actionable && isset( $_GET['action'] ) && in_array( $_GET['action'], $ignored_actions ) ) {
			$is_actionable = false;
		}

		// Don't take action during autosaves
		if ( $is_actionable && ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $post->post_status == 'auto-draft' ) ) {
			$is_actionable = false;
		}

		return $is_actionable;
	}

	/**
	 * Get the name of the requester
	 *
	 * @param int $post_author_id
	 *
	 * @return string
	 */
	public static function get_requester_name( $post_author_id ) {
		$requester_name = '';

		$author = get_user_by( 'id', $post_author_id );

		if ( is_a( $author, 'WP_User' ) ) {
			$requester_name = $author->get( 'display_name' );
		}

		return $requester_name;
	}

	/**
	 * Get the e-mail address of the requester in `Name <address>` format
	 *
	 * @param int $post_author_id
	 *
	 * @return false|string
	 */
	public static function get_requester_formatted_email( $post_author_id ) {
		$address   = false;
		$requester = get_user_by( 'id', $post_author_id );

		if ( is_a( $requester, 'WP_User' ) ) {
			$address = sprintf( '%s <%s>', $requester->get( 'display_name' ), $requester->get( 'user_email' ) );
		}

		return $address;
	}

	/**
	 * Get the files attached to a post
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public static function get_attached_files( $post ) {
		$files = get_posts( array(
			'post_parent'    => $post->ID,
			'post_type'      => 'attachment',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		foreach ( $files as &$file ) {
			$file->filename = wp_basename( $file->guid );
			$file->url      = wp_get_attachment_url( $file->ID );
		}

		return $files;
	}

	/**
	 * Attach unattached files to the Vendor Payment post
	 *
	 * Sometimes users will upload the files manually to the Media Library, instead of using the Add Files button,
	 * and we need to attach them to the request so that they show up in the metabox.
	 *
	 * NOTE: The calling function must remove any of its save_post callbacks before calling this, in order to
	 * avoid infinite recursion
	 *
	 * @param int   $post_id
	 * @param array $request
	 */
	public static function attach_existing_files( $post_id, $request ) {
		if ( empty( $request['wcb_existing_files_to_attach'] ) ) {
			return;
		}

		if ( ! $files = json_decode( $request['wcb_existing_files_to_attach'] ) ) {
			return;
		}

		foreach( $files as $file_id ) {
			wp_update_post( array(
				'ID'          => $file_id,
				'post_parent' => $post_id,
			) );
		}
	}

	/**
	 * Insert an entry into a log for one of the custom post types
	 *
	 * @param int    $post_id The post ID.
	 * @param string $message A log message.
	 * @param array  $data    Optional data.
	 */
	public static function log( $post_id, $user_id, $message, $data = array() ) {
		global $wpdb;

		$data['user_id'] = $user_id; // for backwards-compatibility

		$entry = array(
			'timestamp' => time(),
			'message' => $message,
			'data' => $data,
		);

		$log = get_post_meta( $post_id, '_wcp_log', true );
		if ( empty( $log ) )
			$log = '[]';

		$log = json_decode( $log, true );
		$log[] = $entry;
		$log = json_encode( $log );

		update_post_meta( $post_id, '_wcp_log', wp_slash( $log ) );
	}
}
