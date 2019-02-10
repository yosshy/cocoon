<?php //PWA設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//PWAを有効にする
update_theme_option(OP_PWA_ENABLE);

//PWAアプリ名
update_theme_option(OP_PWA_NAME);

//PWAホーム画面に表示されるアプリ名
update_theme_option(OP_PWA_SHORT_NAME);

//PWAアプリの説明
update_theme_option(OP_PWA_DESCRIPTION);

//PWAテーマカラー
update_theme_option(OP_PWA_THEME_COLOR);

//PWA背景色
update_theme_option(OP_PWA_BACKGROUND_COLOR);

//PWA表示モード
update_theme_option(OP_PWA_DISPLAY);

//PWA画面の向き
update_theme_option(OP_PWA_ORIENTATION);

//PWAが有効な時
if (is_pwa_enable()) {
  $name = get_double_quotation_escape(get_pwa_name());
  $short_name = get_double_quotation_escape(get_pwa_short_name());
  $description = get_double_quotation_escape(get_pwa_description());
  $start_url = home_url().'/?utm_source=homescreen&utm_medium=pwa';
  $display = get_pwa_display();
  $orientation = get_pwa_orientation();
  $theme_color = get_pwa_theme_color();
  $background_color = get_pwa_background_color();
  $icon_url_192 =  get_site_icon_url(192);
  $icon_url_512 =  get_site_icon_url(512);
  $manifest =
  "
  {
    \"name\": \"{$name}\",
    \"short_name\": \"{$short_name}\",
    \"description\": \"{$description}\",
    \"start_url\": \"{$start_url}\",
    \"display\": \"{$display}\",
    \"lang\": \"ja\",
    \"dir\": \"auto\",
    \"orientation\": \"{$orientation}\",
    \"theme_color\": \"{$theme_color}\",
    \"background_color\": \"{$background_color}\",
    \"icons\": [
        {
            \"src\": \"{$icon_url_192}\",
            \"type\": \"image/png\",
            \"sizes\": \"192x192\"
        },
        {
            \"src\": \"{$icon_url_512}\",
            \"type\": \"image/png\",
            \"sizes\": \"512x512\"
        }
    ]
  }";
  //マニフェストファイルの作成
  $manifest_file = get_theme_pwa_cache_dir().'manifest.json';
  wp_filesystem_put_contents($manifest_file, $manifest, 0);

  // _v($manifest);
  $site_logo = get_the_site_logo_url();
  $jquery_core_url = get_jquery_core_url(get_jquery_version());
  $jquery_migrate_url = get_jquery_migrate_url(get_jquery_migrate_version());
  $theme_js_url = THEME_JS_URL;
  $theme_child_js_url = THEME_CHILD_JS_URL;
  $theme_name = THEME_NAME;
  $service_worker =
  "
  const CACHE_NAME = '{$theme_name}_ver_1';
  const urlsToCache = [
      '/',
      '{$icon_url_192}',
      '{$icon_url_512}',
      '{$site_logo}',
      '{$jquery_core_url}',
      '{$jquery_migrate_url}',
      '{$theme_js_url}',
      '{$theme_child_js_url}',
  ];

  self.addEventListener('install', function(event) {
      // インストール処理
      event.waitUntil(
          caches.open(CACHE_NAME)
          .then(function(cache) {
              console.log('Opened cache');
              return cache.addAll(urlsToCache);
          })
      );
  });

  self.addEventListener('activate', function(event) {
      const cacheWhitelist = [CACHE_NAME];

      event.waitUntil(
          caches.keys().then(function(cacheNames) {
              return Promise.all(
                  cacheNames.map(function(cacheName) {
                      if (cacheWhitelist.indexOf(cacheName) === -1) {
                          return caches.delete(cacheName);
                      }
                  })
              );
          })
      );
  });

  self.addEventListener('fetch', function(event) {

      // 管理画面はキャッシュを使用しない
      if (/\/wp-admin|\/wp-login|preview=true/.test(event.request.url)) {
          return;
      }

      // POSTの場合はキャッシュを使用しない
      if ('POST' === event.request.method) {
          return;
      }

      // 管理画面にログイン時はキャッシュを使用しない
      console.log(event);

      event.respondWith(
          caches.match(event.request)
          .then(function(response) {
              // キャッシュがあったら、そのレスポンスを返す
              if (response) {
                  return response;
              }

              // 重要：リクエストをcloneする。リクエストはStreamなので
              // 一度しか処理できない。ここではキャッシュ用、fetch用と2回
              // 必要なのでリクエストはcloneしないといけない
              const fetchRequest = event.request.clone();

              return fetch(fetchRequest,{credentials: 'include'}).then(
                  function(response) {
                      // レスポンスが正しいかをチェック
                      if (!response || response.status !== 200 || response.type !== 'basic') {
                          return response;
                      }

                      // 重要：レスポンスを clone する。レスポンスは Stream で
                      // ブラウザ用とキャッシュ用の2回必要。なので clone して
                      // 2つの Stream があるようにする
                      const responseToCache = response.clone();

                      caches.open(CACHE_NAME)
                          .then(function(cache) {
                              cache.put(event.request, responseToCache);
                          });

                      return response;
                  }
              );
          })
      );
  });
  ";
  //_v($service_worker);
}
