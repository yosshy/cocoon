<?php //目次関数

//最初のH2タグの前に目次を挿入する
//ref:https://qiita.com/wkwkrnht/items/c2ee485ff1bbd81325f9
if (is_toc_visible()) {
  //優先順位の設定
  if (is_toc_before_ads()) {
    $priority = 9;
  } else {
    $priority = 10;
  }
  add_filter('the_content', 'add_toc_before_1st_h2', $priority);
}
if ( !function_exists( 'add_toc_before_1st_h2' ) ):
function add_toc_before_1st_h2($the_content){

  $content     = $the_content;
  $headers     = array();
  $html        = '';
  $toc_list    = '';
  $id          = '';
  $toggle      = '';
  $counter     = 0;
  $counters    = array(0,0,0,0,0,0);
  $harray      = array();

  $class       = 'toc';
  $title       = get_toc_title(); //目次タイトル
  $showcount   = 0;
  $depth       = intval(get_toc_depth()); //2-6 0で全て
  $top_level   = 2; //h2がトップレベル
  $targetclass = 'entry-content'; //目次対象となるHTML要素
  $number_visible   = is_toc_number_visible(); //見出しの数字を表示するか
  if ($number_visible) {
    $list_tag = 'ol';
  } else {
    $list_tag = 'ul';
  }


  if($targetclass===''){$targetclass = get_post_type();}
  for($h = $top_level; $h <= 6; $h++){$harray[] = 'h' . $h . '';}
  //$harray = implode(',',$harray);

  preg_match_all('/<([hH][1-6]).*?>(.*?)<\/[hH][1-6].*?>/u', $content, $headers);
  $header_count = count($headers[0]);
  if($header_count > 0){
    $level = strtolower($headers[1][0]);
    if($top_level < $level){$top_level = $level;}
  }
  if($top_level < 1){$top_level = 1;}
  if($top_level > 6){$top_level = 6;}
  $top_level = $top_level;
  $current_depth          = $top_level - 1;
  $prev_depth             = $top_level - 1;
  $max_depth              = (($depth == 0) ? 6 : intval($depth)) - $top_level + 1;


  if($header_count > 0){
    $toc_list .= '<' . $list_tag . (($current_depth == $top_level - 1) ? ' class="toc-list open"' : '') . '>';
  }
  for($i=0;$i < $header_count;$i++){
    $depth = 0;
    switch(strtolower($headers[1][$i])){
      case 'h1': $depth = 1 - $top_level + 1; break;
      case 'h2': $depth = 2 - $top_level + 1; break;
      case 'h3': $depth = 3 - $top_level + 1; break;
      case 'h4': $depth = 4 - $top_level + 1; break;
      case 'h5': $depth = 5 - $top_level + 1; break;
      case 'h6': $depth = 6 - $top_level + 1; break;
    }
    //var_dump($depth);
    if($depth >= 1 && $depth <= $max_depth){
      if($current_depth == $depth){$toc_list .= '</li>';}
      while($current_depth > $depth){
        $toc_list .= '</li></'.$list_tag.'>';
        $current_depth--;
        $counters[$current_depth] = 0;
      }
      if($current_depth != $prev_depth){$toc_list .= '</li>';}
      if($current_depth < $depth){
        $toc_list .= '<'.$list_tag.'>';
        $current_depth++;
      }
      $counters[$current_depth - 1] ++;
      $counter++;
      $toc_list .= '<li><a href="#toc' . $counter . '" tabindex="0">' . $headers[2][$i] . '</a>';
      $prev_depth = $depth;
    }
  }
  while($current_depth >= 1 ){
    $toc_list .= '</li></'.$list_tag.'>';
    $current_depth--;
  }
  if($counter >= $showcount){
    if($id!==''){$id = ' id="' . $id . '"';}else{$id = '';}
    $html .= '
    <div' . $id . ' class="' . $class . get_additional_toc_classes() . '">
      <div class="toc-title">' . $title . '</div>
      ' . $toc_list .'
    </div>';
    ///////////////////////////////////////
    // jQueryの見出し処理（PHPの置換処理と比べてこちらの方が信頼度高い）
    ///////////////////////////////////////
    // $script = '
    // (function($){
    //   $(document).ready(function(){
    //     var hxs = $(".'.$targetclass.'").find("' . implode(',', $harray) . '");
    //     //console.log(hxs);
    //     hxs.each(function(i, e) {
    //       //console.log(e);
    //       //console.log(i+1);
    //       $(e).attr("id", "toc"+(i+1));
    //     });
    //   });
    // })(jQuery);';
    // //JavaScriptの縮小化
    // $script_min = minify_js($script);
    // //javascript.jsの後に読み込む
    // wp_add_inline_script( THEME_JS, $script_min, 'after' ) ;

    ///////////////////////////////////////
    // PHPの見出し処理（条件によっては失敗するかも）
    ///////////////////////////////////////
    $res = preg_match_all('/<('.implode('|', $harray).')[^>]*?>.*?<\/h[2-6]>/i', $the_content, $m);
    // var_dump($harray);
    // var_dump($res);
    //var_dump($m);
    if ($res && $m[0] && $m[1]) {
      $i = 0;
      foreach ($m[0] as $value) {
        //var_dump($m[0][$i]);
        $h_tag = $m[1][$i];
        $new = str_replace('<'.$h_tag, '<'.$h_tag.' id="toc'.strval($i+1).'"', $value);
        // var_dump($value);
        // var_dump($new);

        $the_content = str_replace($value, $new, $the_content);

        $i++;
      }
    }

  }
  $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
  $the_content = preg_replace(H2_REG, $html.$h2result, $the_content, 1);
  //var_dump($the_content);
  return $the_content;
}
endif;
