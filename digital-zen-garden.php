<?php
/*
Plugin Name: Digital Zen Garden
Description: ダッシュボードを「静寂の庭」へ。メニューのスクロール不具合を修正した最終形態。
Version: 1.0.1
Tested up to: 7.0.0
Requires PHP: 8.3.23
Author: masato shibuya (Image-box Co., Ltd.)
License: GPL2
*/

if (!defined('ABSPATH')) exit;

class DigitalZenGarden {
	public function __construct() {
		add_action('admin_head-index.php', [$this, 'apply_zen_style']);
		add_action('admin_footer-index.php', [$this, 'place_zen_stone']);
		add_action('wp_dashboard_setup', [$this, 'purify_dashboard'], 999);

		add_filter('admin_footer_text', '__return_empty_string', 999);
		add_filter('update_footer', '__return_empty_string', 999);
	}

	public function purify_dashboard() {
		global $wp_meta_boxes;
		$wp_meta_boxes['dashboard'] = [];
	}

	public function apply_zen_style() {
		echo '<style>
			/* 1. 標準要素の隠蔽 */
			#wpadminbar, #adminmenuback, #adminmenuwrap, #screen-meta-links {
				opacity: 0; visibility: hidden; transition: all 0.5s ease;
			}

			/* 2. 不要テキストの抹消 */
			.wrap h1, .wp-heading-inline, #dashboard-widgets-wrap .empty-container,
			#screen-options-link-wrap, #contextual-help-link-wrap, .notice, #message, #wpfooter {
				display: none !important;
			}

			/* 3. レイアウトと背景 */
			html.wp-toolbar { padding-top: 0 !important; }
			#wpcontent { margin-left: 0 !important; background: #fdfdfd !important; transition: margin 0.5s ease; padding-bottom: 0 !important; }
			#wpbody-content {
				padding-bottom: 0 !important;
				background: repeating-radial-gradient(circle at 50% 50%, #f4f4f4, #f4f4f4 1px, transparent 1px, transparent 40px);
				min-height: 100vh;
			}

			/* 4. 表示状態（石をクリックした後） */
			body.zen-revealed #wpadminbar,
			body.zen-revealed #adminmenuback,
			body.zen-revealed #adminmenuwrap {
				opacity: 1; visibility: visible;
			}

			/* 【修正】メニューをスクロール可能にする */
			body.zen-revealed #adminmenuwrap {
				top: 32px;
				height: calc(100% - 32px) !important;
				overflow-y: auto !important;
				overflow-x: hidden !important;
			}

			body.zen-revealed #wpfooter { display: block !important; }
			body.zen-revealed #wpcontent { margin-left: 160px !important; }

			/* 5. 隠しスイッチ（庭石） */
			#zen-stone {
				position: fixed; bottom: 30px; right: 30px;
				width: 14px; height: 11px; background: #bbb;
				border-radius: 53% 47% 51% 49% / 57% 41% 59% 43%;
				cursor: pointer; z-index: 99999; transition: all 0.4s;
				box-shadow: 2px 2px 5px rgba(0,0,0,0.05);
			}
			#zen-stone:hover { background: #888; transform: rotate(10deg) scale(1.3); }
		</style>';
	}

	public function place_zen_stone() {
		?>
		<div id="zen-stone" title="一念通天"></div>
		<script>
			document.getElementById('zen-stone').addEventListener('click', function() {
				document.body.classList.toggle('zen-revealed');
				const isRevealed = document.body.classList.contains('zen-revealed');
				document.documentElement.style.setProperty('padding-top', isRevealed ? '32px' : '0', 'important');
			});
		</script>
		<?php
	}
}
new DigitalZenGarden();


/**
 * Auto Update Settings
 */
require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/ms13th-cyber/digital-zen-garden/',
	__FILE__,
	'digital-zen-garden'
);
$updateChecker->setBranch('main');