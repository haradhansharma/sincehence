<?php
class ControllerExtensionAnalyticsProgressiveWebApplication extends Controller {

	public function index() {
		$this->load->model('setting/setting');
		$get_pwa_settings = $this->model_setting_setting->getSetting('analytics_progressive_web_application', (int) $this->config->get('config_store_id'));
		$settings         = isset($get_pwa_settings['analytics_progressive_web_application_status'])?$get_pwa_settings['analytics_progressive_web_application_status']:false;
		$output           = '';

		if ($settings and $settings['status'] and isset($settings['long_name'][$this->config->get('config_language_id')]) and isset($settings['short_name'][$this->config->get('config_language_id')]) and isset($settings['description'][$this->config->get('config_language_id')])) {
			$this->load->model('tool/image');
			$this->document->addLink($this->url->link('extension/analytics/progressive_web_application/manifest', '', true), 'manifest');
			$output = '<script type="text/javascript">function isFacebookApp() {var ua = navigator.userAgent || navigator.vendor || window.opera; return (ua.indexOf("FBAN") > -1) || (ua.indexOf("FBAV") > -1);}if (window.location.protocol == "https:" && window.isSecureContext) {if(!isFacebookApp()){if ("serviceWorker" in navigator) {if (navigator.serviceWorker.controller) {console.log("[PWA Builder] active service worker found, no need to register");} else {navigator.serviceWorker.register("'.$this->url->link('extension/analytics/progressive_web_application/service_worker', '', true).'").then(function (reg) {console.log("[PWA Builder] Service worker has been registered for scope: " + reg.scope);});}}} else {console.log("[PWA Builder] The current browser doesn\'t support service workers.");}} else {console.log("[PWA Builder] The web site is not served from a secure (HTTPS) domain.");}</script>';
			$output .= '<meta name="theme-color" content="'.$settings['theme_color'].'" />';
			$output .= '<meta name="msapplication-TileColor" content="'.$settings['theme_color'].'" />';
			$output .= '<meta name="mobile-web-app-capable" content="yes" />';
			$output .= '<meta name="apple-mobile-web-app-capable" content="yes" />';
			$output .= '<meta name="apple-mobile-web-app-status-bar-style" content="'.$settings['theme_color'].'" />';
			$output .= '<meta name="apple-mobile-web-app-title" content="'.$this->clean_string($settings['long_name'][$this->config->get('config_language_id')]).'" />';
			$output .= '<meta name="application-name" content="'.$this->clean_string($settings['short_name'][$this->config->get('config_language_id')]).'" />';
			$output .= '<meta name="msapplication-TileImage" content="'.$this->model_tool_image->resize($settings['application_image'], 144, 144).'" />';
			$ati_sizes = array("57", "76", "114", "120", "144", "152", "167", "180");
			foreach ($ati_sizes as $key => $value) {
				$output .= '<link rel="apple-touch-icon" sizes="'.$value.'x'.$value.'" href="'.$this->model_tool_image->resize($settings['application_image'], $value, $value).'" />';
			}
			$msbcicons_sizes = array("70", "150", "310");
			foreach ($msbcicons_sizes as $key => $value) {
				$output .= '<meta name="msapplication-square'.$value.'x'.$value.'logo" content="'.$this->model_tool_image->resize($settings['application_image'], $value, $value).'" />';
			}
			$icon_sizes = array("128", "192");
			foreach ($icon_sizes as $key => $value) {
				$output .= '<link rel="icon" sizes="'.$value.'x'.$value.'" href="'.$this->model_tool_image->resize($settings['application_image'], $value, $value).'" />';
			}
			if ($settings['preloader_status']) {
				$output .= '<script type="text/javascript" async>async function load_jquery_not_exist() {if(!window.jQuery) {var script = document.createElement(\'script\');script.type = "text/javascript";script.async = true;script.src = "https://code.jquery.com/jquery-2.2.4.min.js";document.getElementsByTagName(\'head\')[0].appendChild(script);}}function start_jquery_check() {return load_jquery_not_exist();}(async() => {await start_jquery_check();})();</script><link href="'.$this->url->link('extension/analytics/progressive_web_application/preloader', '', true).'" rel="stylesheet" media="screen" /><script type="text/javascript" async>async function async_preloader() {if(window.jQuery) {$(document).ready(function(){$(window).load(function(){if ($(\'#pwa-holder\')) {$(\'#pwa-holder\').fadeOut('.$settings['preloader_fadeout'].', function() {$(\'body\').removeClass(\'pwa-loading\');});}});});}}function start_async_preloader() {return async_preloader();}(async() => {await start_async_preloader();})();</script>';
			}

		}
		return $output;
	}

	public function service_worker() {

		$this->load->model('setting/setting');
		$get_pwa_settings = $this->model_setting_setting->getSetting('analytics_progressive_web_application', (int) $this->config->get('config_store_id'));
		$settings         = isset($get_pwa_settings['analytics_progressive_web_application_status'])?$get_pwa_settings['analytics_progressive_web_application_status']:false;
		if ($settings and $settings['status'] and isset($settings['long_name'][$this->config->get('config_language_id')]) and isset($settings['short_name'][$this->config->get('config_language_id')]) and isset($settings['description'][$this->config->get('config_language_id')])) {

			$output = <<< EOSW
const CACHE = "pwabuilder-page";
const offlineFallbackPage = ["index.php"];
self.addEventListener("install", function (event) {
  console.log("[PWA Builder] Install Event processing");

  event.waitUntil(
    caches.open(CACHE).then(function (cache) {
      console.log("[PWA Builder] Cached offline page during install");

      if (offlineFallbackPage === "offline.html") {
        return cache.add(new Response("TODO: Update the value of the offlineFallbackPage constant in the serviceworker."));
      }

      return cache.add(offlineFallbackPage);
    })
  );
});

self.addEventListener("fetch", function (event) {
  if (event.request.method !== "GET") return;

  event.respondWith(
    fetch(event.request).catch(function (error) {

      if (
        event.request.destination !== "document" ||
        event.request.mode !== "navigate"
      ) {
        return;
      }

      console.error("[PWA Builder] Network request Failed. Serving offline page " + error);
      return caches.open(CACHE).then(function (cache) {
        return cache.match(offlineFallbackPage);
      });
    })
  );
});

self.addEventListener("refreshOffline", function () {
  const offlinePageRequest = new Request(offlineFallbackPage);

  return fetch(offlineFallbackPage).then(function (response) {
    return caches.open(CACHE).then(function (cache) {
      console.log("[PWA Builder] Offline page updated from refreshOffline event: " + response.url);
      return cache.put(offlinePageRequest, response);
    });
  });
});


self.addEventListener("backbutton", onBackKeyDown, false);
function onBackKeyDown() {
    var history = App.getHistory();
    if (history.length === 1) {
        var history_screen = history[0];
        if ( TemplateTags.getDefaultRouteLink().replace('#','') === history_screen.fragment ) {
            navigator.app.exitApp();
            return;
        }
    }
    navigator.app.backHistory();
}



EOSW

			;

			$this->response->addHeader("Content-Type: application/javascript");
			$this->response->addHeader("Access-Control-Allow-Origin: *");
			$output = str_replace(array("\r", "\n"), '', $output);
			$output = preg_replace('/\s+/', ' ', $output);
			$this->response->setOutput($output);

		}

	}

	public function preloader() {

		$this->load->model('setting/setting');
		$get_pwa_settings = $this->model_setting_setting->getSetting('analytics_progressive_web_application', (int) $this->config->get('config_store_id'));
		$settings         = isset($get_pwa_settings['analytics_progressive_web_application_status'])?$get_pwa_settings['analytics_progressive_web_application_status']:false;
		if ($settings and $settings['status'] and $settings['preloader_status'] and isset($settings['long_name'][$this->config->get('config_language_id')]) and isset($settings['short_name'][$this->config->get('config_language_id')]) and isset($settings['description'][$this->config->get('config_language_id')])) {

			$preloader_background  = $settings['preloader_background'].";";
			$preloader_size        = $settings['preloader_size']."%;";
			$preloader_balls_color = $settings['preloader_balls_color'].";";
			$preloader_balls_size  = $settings['preloader_balls_size']."px;";
			$display_types         = array(
				'4096px' => '4k_uhd', '3840px' => 'uhd', '1920px' => 'fhd',
				'1680px' => 'large_desktop', '1440px' => 'desktop', '1366px' => 'large_laptop',
				'1280px' => 'laptop', '1140px' => 'large_tablet', '1024px' => 'tablet',
				'760px'  => 'large_phone', '470px'  => 'normal_phone', '370px'  => 'small_phone',
			);

			$preloader_display = '';

			foreach ($display_types as $key => $value) {
				if (isset($settings['preloader_display'][$value])) {

					$preloader_display .= '
												@media screen and (max-width: '.$key.') {
													#pwa-holder { position: fixed; }
													#pwa-preloader { position: absolute; }
													#pwa-preloader div { position: absolute; }
													#pwa-preloader div:before { position: absolute; }
													#pwa-preloader { display: inline; }
													#pwa-holder { display: inline; }
												}
											';

				} else {

					$preloader_display .= '
												@media screen and (max-width: '.$key.') {
													#pwa-holder { position: unset; }
													#pwa-preloader { position: unset; }
													#pwa-preloader div { position: unset; }
													#pwa-preloader div:before { position: unset; }
													#pwa-preloader { display: none; }
													#pwa-holder { display: none; }
												}
											';
				}
			}

			$output = $preloader_display.'

											.pwa-loading {
												overflow: hidden;
												height: 100vh;
											}

											#pwa-holder {
												/*position: fixed;*/
												left: 0px;
												top: 0px;
												bottom: 0px;
												right: 0px;
												width: 100%;
												height: 100%;
												z-index:999999999999;
												overflow-y: hidden;
												background-color: '.$preloader_background.'
											}

											#pwa-preloader {
												width: '.$preloader_size.'
												height: '.$preloader_size.'
												/*position: absolute;*/
												left: 50%;
												top: 50%;
												transform: translateX(-50%) translateY(-50%);
												animation: rotatePreloader 2s infinite ease-in;
											}

											@keyframes rotatePreloader {
												0% {
													transform: translateX(-50%) translateY(-50%) rotateZ(0deg);
												}
												100% {
													transform: translateX(-50%) translateY(-50%) rotateZ(-360deg);
												}
											}

											#pwa-preloader div {
												/*position: absolute;*/
												width: 100%;
												height: 100%;
												opacity: 0;
											}

											#pwa-preloader div:before {
												content: "";
												/*position: absolute;*/
												left: 50%;
												top: 0%;
												width: '.$preloader_balls_size.'
												height: '.$preloader_balls_size.'
												background-color: '.$preloader_balls_color.'
												transform: translateX(-50%);
												border-radius: 50%;
											}

											#pwa-preloader div:nth-child(1) {
												transform: rotateZ(0deg);
												animation: rotateCircle1 2s infinite linear;
												z-index: 9;
											}

											@keyframes rotateCircle1 {
												0% {
													opacity: 0;
												}
												0% {
													opacity: 1;
													transform: rotateZ(36deg);
												}
												7% {
													transform: rotateZ(0deg);
												}
												57% {
													transform: rotateZ(0deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(2) {
												transform: rotateZ(36deg);
												animation: rotateCircle2 2s infinite linear;
												z-index: 8;
											}

											@keyframes rotateCircle2 {
												5% {
													opacity: 0;
												}
												5.0001% {
													opacity: 1;
													transform: rotateZ(0deg);
												}
												12% {
													transform: rotateZ(-36deg);
												}
												62% {
													transform: rotateZ(-36deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(3) {
												transform: rotateZ(72deg);
												animation: rotateCircle3 2s infinite linear;
												z-index: 7;
											}

											@keyframes rotateCircle3 {
												10% {
													opacity: 0;
												}
												10.0002% {
													opacity: 1;
													transform: rotateZ(-36deg);
												}
												17% {
													transform: rotateZ(-72deg);
												}
												67% {
													transform: rotateZ(-72deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(4) {
												transform: rotateZ(108deg);
												animation: rotateCircle4 2s infinite linear;
												z-index: 6;
											}

											@keyframes rotateCircle4 {
												15% {
													opacity: 0;
												}
												15.0003% {
													opacity: 1;
													transform: rotateZ(-72deg);
												}
												22% {
													transform: rotateZ(-108deg);
												}
												72% {
													transform: rotateZ(-108deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(5) {
												transform: rotateZ(144deg);
												animation: rotateCircle5 2s infinite linear;
												z-index: 5;
											}

											@keyframes rotateCircle5 {
												20% {
													opacity: 0;
												}
												20.0004% {
													opacity: 1;
													transform: rotateZ(-108deg);
												}
												27% {
													transform: rotateZ(-144deg);
												}
												77% {
													transform: rotateZ(-144deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(6) {
												transform: rotateZ(180deg);
												animation: rotateCircle6 2s infinite linear;
												z-index: 4;
											}

											@keyframes rotateCircle6 {
												25% {
													opacity: 0;
												}
												25.0005% {
													opacity: 1;
													transform: rotateZ(-144deg);
												}
												32% {
													transform: rotateZ(-180deg);
												}
												82% {
													transform: rotateZ(-180deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(7) {
												transform: rotateZ(216deg);
												animation: rotateCircle7 2s infinite linear;
												z-index: 3;
											}

											@keyframes rotateCircle7 {
												30% {
													opacity: 0;
												}
												30.0006% {
													opacity: 1;
													transform: rotateZ(-180deg);
												}
												37% {
													transform: rotateZ(-216deg);
												}
												87% {
													transform: rotateZ(-216deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(8) {
												transform: rotateZ(252deg);
												animation: rotateCircle8 2s infinite linear;
												z-index: 2;
											}

											@keyframes rotateCircle8 {
												35% {
													opacity: 0;
												}
												35.0007% {
													opacity: 1;
													transform: rotateZ(-216deg);
												}
												42% {
													transform: rotateZ(-252deg);
												}
												92% {
													transform: rotateZ(-252deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(9) {
												transform: rotateZ(288deg);
												animation: rotateCircle9 2s infinite linear;
												z-index: 1;
											}

											@keyframes rotateCircle9 {
												40% {
													opacity: 0;
												}
												40.0008% {
													opacity: 1;
													transform: rotateZ(-252deg);
												}
												47% {
													transform: rotateZ(-288deg);
												}
												97% {
													transform: rotateZ(-288deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

											#pwa-preloader div:nth-child(10) {
												transform: rotateZ(324deg);
												animation: rotateCircle10 2s infinite linear;
												z-index: 0;
											}

											@keyframes rotateCircle10 {
												45% {
													opacity: 0;
												}
												45.0009% {
													opacity: 1;
													transform: rotateZ(-288deg);
												}
												52% {
													transform: rotateZ(-324deg);
												}
												102% {
													transform: rotateZ(-324deg);
												}
												100% {
													transform: rotateZ(-324deg);
													opacity: 1;
												}
											}

										';

			$this->response->addHeader("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			$this->response->addHeader("Cache-Control: post-check=0, pre-check=0");
			$this->response->addHeader("Pragma: no-cache");
			$this->response->addHeader("Content-Type: text/css");
			$this->response->addHeader("Access-Control-Allow-Origin: *");
			$output = str_replace(array("\r", "\n"), '', $output);
			$output = preg_replace('/\s+/', ' ', $output);
			$this->response->setOutput($output);

		}

	}

	public function manifest() {

		$this->load->model('setting/setting');
		$get_pwa_settings = $this->model_setting_setting->getSetting('analytics_progressive_web_application', (int) $this->config->get('config_store_id'));
		$settings         = isset($get_pwa_settings['analytics_progressive_web_application_status'])?$get_pwa_settings['analytics_progressive_web_application_status']:false;
		if ($settings and $settings['status'] and isset($settings['long_name'][$this->config->get('config_language_id')]) and isset($settings['short_name'][$this->config->get('config_language_id')]) and isset($settings['description'][$this->config->get('config_language_id')])) {
			$this->load->model('tool/image');
			$endpoint = parse_url($this->url->link('common/home', '', true));

			$output = array(
				'name'                    => $this->clean_string($settings['long_name'][$this->config->get('config_language_id')]),
				'short_name'              => $this->clean_string($settings['short_name'][$this->config->get('config_language_id')]),
				'description'             => $this->clean_string($settings['description'][$this->config->get('config_language_id')]),
				'start_url'               => rtrim(dirname($endpoint['path']), '/\\').'/',
				'scope'                   => rtrim(dirname($endpoint['path']), '/\\').'/',
				'author'                  => 'Todor Donev',
				'developer'               => array('name'               => 'Todor Donev', 'url'               => 'https://donev.eu/'),
				'theme_color'             => $settings['theme_color'],
				'background_color'        => $settings['background_color'],
				'display'                 => $settings['display'],
				'orientation'             => $settings['orientation'],
				'dir'                     => $settings['direction'],
				'icons'                   => $this->manifest_icons($settings['application_image']),
				'version'                 => '1.0',
				'version_name'            => '1.0',
				'manifest_version'        => '2',
				'offline_enabled'         => boolval($settings['offline_enabled']),
				'content_security_policy' => 'upgrade-insecure-request',
				'lang'                    => $this->clean_string($settings['lang_code'][$this->config->get('config_language_id')])
			);

			$this->response->addHeader("Content-Type: text/plain; charset=utf-8");
			$this->response->addHeader("Access-Control-Allow-Origin: *");
			$this->response->setOutput(json_encode($output, JSON_UNESCAPED_SLASHES));
		}

	}

	protected function manifest_icons($image) {
		$output = array();
		$sizes  = array("36", "48", "64", "72", "96", "114", "144", "152", "168", "192", "256");
		foreach ($sizes as $key => $value) {
			$output[] = array(
				'sizes' => $value.'x'.$value,
				'src'   => $this->model_tool_image->resize($image, $value, $value)
			);
		}
		return $output;
	}

	protected function clean_string($string) {
		$string = htmlspecialchars_decode($string);
		$string = str_replace(array('\'', '"', ',', ';', '<', '>'), '', $string);
		$string = strip_tags($string);
		$string = trim(preg_replace('/ {2,}/', ' ', $string));
		return $string;
	}
}