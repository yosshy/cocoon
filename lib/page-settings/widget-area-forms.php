<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<!-- ウィジェット設定 -->
<div id="widget-area-page" class="postbox">
  <h2 class="hndle"><?php _e( 'ウィジェットエリア表示', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '使用しないウィジェットエリアを表示しないようにする設定です。', THEME_NAME ); ?></p>

    <table class="form-table">
      <tbody>

        <!-- 除外ウィジェットエリア -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXCLUDE_WIDGET_AREA_IDS, __( '除外ウィジェットエリア', THEME_NAME )); ?>
          </th>
          <td>
            <?php
              $widgets = $GLOBALS['wp_widget_factory']->widgets;
              //_v($widgets);
              // echo '<pre>';
              // var_dump($widgets);
              // echo '</pre>';
            ?>
            <ul>
              <?php
              foreach ($widgets as $class => $widget) {
                $checked = null;
                //_v($widget->widget_options);
                if (in_array($class, get_exclude_widget_classes())) {
                  $checked = ' checked="checked"';
                }
                // _v($class);
                // _v($widget);
                echo '<li><input type="checkbox" name="'.OP_EXCLUDE_WIDGET_CLASSES.'[]" value="'.$class.'"'.$checked.'><b>' . $widget->name.'</b>：'.$widget->widget_options['description'].'</li>';
              }
              ?>
            </ul>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>
