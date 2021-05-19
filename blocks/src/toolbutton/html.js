/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../helpers.js';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, insert } from '@wordpress/rich-text';
import { RichTextToolbarButton, RichTextShortcut } from '@wordpress/block-editor';

registerFormatType( 'cocoon-blocks/html', {
  title: __( 'HTML挿入', THEME_NAME ),
  tagName: 'html',
  className: null,

  edit ({ isActive, value, onChange }) {
    const onToggle = () => {
      let html = '';
      html = window.prompt( __( 'HTMLを入力してください。', THEME_NAME ) ) || value.text.substr( value.start, value.end -value.start );
      value = insert( value, html, value.start, value.end );
      //console.log(value);
      return onChange( value );
    };

    // @see keycodes/src/index.js
    const shortcutType = 'primaryShift';
    const shortcutCharacter ='';
    return (
      <Fragment>
        <RichTextShortcut type={shortcutType} character={shortcutCharacter} onUse={onToggle}  />
        <RichTextToolbarButton icon={<FontAwesomeIcon icon={['fas', 'code']} />} title={__( 'HTML挿入', THEME_NAME )} onClick={onToggle}
                               isActive={isActive} shorcutType={shortcutType} shorcutCharacter={shortcutCharacter} />
      </Fragment>
    )
  }
} );


