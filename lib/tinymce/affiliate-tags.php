<?php //ビジュアルエディターのテンプレート挿入ドロップダウン

add_action('admin_init', 'add_affiliate_tags_dropdown');
add_action('admin_head', 'generate_affiliate_tags_is');

if ( !function_exists( 'add_affiliate_tags_dropdown' ) ):
function add_affiliate_tags_dropdown(){
  if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  {
    add_filter( 'mce_external_plugins',  'add_affiliate_tags_to_mce_external_plugins' );
    add_filter( 'mce_buttons_2',  'register_affiliate_tags' );
  }
}
endif;

//ボタン用スクリプトの登録
if ( !function_exists( 'add_affiliate_tags_to_mce_external_plugins' ) ):
function add_affiliate_tags_to_mce_external_plugins( $plugin_array ){
  $path=get_template_directory_uri() . '/js/affiliate-tags.js';
  $plugin_array['affiliate_tags'] = $path;
  return $plugin_array;
}
endif;

//吹き出しドロップダウンをTinyMCEに登録
if ( !function_exists( 'register_affiliate_tags' ) ):
function register_affiliate_tags( $buttons ){
  array_push( $buttons, 'separator', 'affiliate_tags' );
  return $buttons;
}
endif;

//吹き出しの値渡し用のJavaScriptを生成
if ( !function_exists( 'generate_affiliate_tags_is' ) ):
function generate_affiliate_tags_is($value){
  $records = get_affiliate_tags(null, 'title');

  echo '<script type="text/javascript">
  var affiliateTagsTitle = "'.__( 'アフィリエイトタグ', THEME_NAME ).'";
  var affiliateTags = new Array();';

  $count = 0;

  foreach($records as $record){
    //非表示の場合は跳ばす
    if (!$record->visible) {
      continue;
    }
    ?>

    var count = <?php echo $count; ?>;
    affiliateTags[count] = new Array();
    affiliateTags[count].title  = '<?php echo $record->title; ?>';
    affiliateTags[count].id     = '<?php echo $record->id; ?>';
    affiliateTags[count].shrotecode = '<?php echo get_affiliate_tag_shortcode($record->id); ?>';

    <?php
    $count++;
  }
  echo '</script>';
}
endif;

