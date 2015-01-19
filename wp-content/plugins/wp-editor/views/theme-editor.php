<div id="save-result"></div>
<div class="wrap">
  <?php screen_icon(); ?>
  <h2><?php _e('Edit Themes', 'wpeditor'); ?></h2>
  <?php
  $theme = wp_get_theme();
  if(is_object($data['wp_theme']) && $data['wp_theme']->name == $theme->name) { ?>
    <div class="updated">
      <p><?php _e('<strong>This theme is currently activated!<br />Warning:</strong> Making changes to active themes is not recommended.', 'wpeditor'); ?></p>
    </div>
  <?php } ?>
  <?php if(isset($_GET['create-theme']) && $_GET['create-theme'] == 'success'): ?>
    <div class="updated">
      <p><?php _e('<strong>Your theme was successfully created!</strong>', 'wpeditor'); ?></p>
    </div>
  <?php endif; ?>
  <?php if(isset($_GET['error'])): ?>
    <div class="error">
      <?php if($_GET['error'] == 1): ?>
        <p><strong><?php _e('You do not have sufficient permissions to download this plugin.', 'wpeditor'); ?></strong></p>
      <?php elseif($_GET['error'] == 2): ?>
        <p><strong><?php _e('There was an error locating the file to download. Please try again later.', 'wpeditor'); ?></strong></p>
      <?php elseif($_GET['error'] == 3): ?>
        <p><strong><?php _e('There was an error compressing the plugin files. Please try again later.', 'wpeditor'); ?></strong></p>
      <?php elseif($_GET['error'] == 4): ?>
        <p><strong><?php _e('You do not have sufficient permissions to download this file.', 'wpeditor'); ?></strong></p>
      <?php elseif($_GET['error'] == 5): ?>
        <p><strong><?php _e('Your theme details were invalid. Please try again.', 'wpeditor'); ?></strong></p>
      <?php elseif($_GET['error'] == 6): ?>
        <p><strong><?php _e('There was an error creating the theme. Please try again later.', 'wpeditor'); ?></strong></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="fileedit-sub">
    <div class="alignleft">
      <h3>
        <?php if(WP_34): ?>
          <?php echo $data['wp_theme']->display('Name') . ': '; ?>
        <?php else: ?>
          <?php echo $data['themes'][$data['theme']]['Name'] . ': '; ?>
        <?php endif; ?>
        <?php
          if(is_writable($data['real_file'])) {
            echo '<span class="writable_status">' . __('Editing', 'wpeditor') . '</span> <span class="current_file">' . $data['file'] . '</span>';
          }
          else {
            echo '<span class="writable_status">' . __('Browsing', 'wpeditor') . '</span> <span class="current_file">' . $data['file'] . '</span>';
          }
        ?>
      </h3>
    </div>
    <div class="alignright">
      <form action="themes.php?page=wpeditor_themes" method="post">
        <strong><label for="plugin"><?php _e('Select theme to edit:', 'wpeditor'); ?></label></strong>
        <select name="theme" id="theme">
          <?php if(WP_34): ?>
            <?php
              foreach(wp_get_themes(array('errors' => null)) as $a_stylesheet => $a_theme) {
                if($a_theme->errors() && 'theme_no_stylesheet' == $a_theme->errors()->get_error_code()) {
                  continue;
                }
                $selected = $a_stylesheet == strtolower($data['stylesheet']) ? ' selected="selected"' : '';
                echo "\n\t" . '<option value="' . esc_attr($a_stylesheet) . '"' . $selected . '>' . $a_theme->display('Name') . '</option>';
              }
            ?>
          <?php else: ?>
            <?php
              foreach($data['themes'] as $a_theme) {
                $theme_name = $a_theme['Name'];
                if($theme_name == $data['theme']) {
                  $selected = ' selected="selected"';
                }
                else {
                  $selected = '';
                }
                $theme_name = esc_attr($theme_name); ?>
                <option value="<?php echo $theme_name; ?>" <?php echo $selected; ?>><?php echo $theme_name; ?></option>
              <?php
              }
            ?>
          <?php endif; ?>
        </select>
        <input type='submit' name='submit' class="button-secondary" value="<?php _e('Select', 'wpeditor'); ?>" />
      </form>
    </div>
    <br class="clear" />
  </div>
  
  <div id="templateside">
    <?php if(WPEditorSetting::getValue('theme_file_upload')): ?>
      <h3><?php _e('Upload Files', 'wpeditor'); ?></h3>
      <div id="theme-upload-files">
        <?php if(is_writable($data['real_file'])): ?>
          <form enctype="multipart/form-data" id="theme_upload_form" method="POST">
              <!-- MAX_FILE_SIZE must precede the file input field -->
              <!--input type="hidden" name="MAX_FILE_SIZE" value="30000" /-->
              <p class="description">
                <?php _e('To', 'wpeditor'); ?>: <?php echo basename(dirname($data['current_theme_root'])) . '/' . basename($data['current_theme_root']) . '/'; ?>
              </p>
              <input type="hidden" name="current_theme_root" value="<?php echo $data['current_theme_root']; ?>" id="current_theme_root" />
              <input type="text" name="directory" id="file_directory" style="width:190px" placeholder="<?php _e('Optional: Sub-Directory', 'wpeditor'); ?>" />
              <!-- Name of input element determines name in $_FILES array -->
              <input name="file" type="file" id="upload_file" style="width:180px" />
              <div class="ajax-button-loader">
                <?php submit_button(__('Upload File', 'wpeditor'), 'primary', 'submit', false); ?>
                <div class="ajax-loader"></div>
              </div>
          </form>
        <?php else: ?>
          <p>
            <em><?php _e('You need to make this folder writable before you can upload any files. See <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">the Codex</a> for more information.'); ?></em>
          </p>
        <?php endif; ?>
      </div>
      <div id="upload_message"></div>
    <?php endif; ?>
    
    <h3><?php _e('Theme Files', 'wpeditor'); ?></h3>
    <div id="theme-editor-files">
      <ul id="theme-folders" class="theme-folders"></ul>
    </div>
  </div>
  
  <form name="template" id="template_form" action="" method="post" class="ajax-editor-update" style="float:left;width:auto;overflow:hidden;">
    <?php wp_nonce_field('edit-theme_' . $data['real_file']); ?>
    <div>
      <textarea cols="70" rows="25" name="new-content" id="new-content" tabindex="1"><?php echo $data['content'] ?></textarea>
      <input type="hidden" name="action" value="save_files" />
      <input type="hidden" name="_success" id="_success" value="<?php _e('The file has been updated successfully.', 'wpeditor'); ?>" />
      <input type="hidden" id="file" name="file" value="<?php echo esc_attr($data['file']); ?>" />
      <input type="hidden" id="theme-name" name="theme" value="<?php echo esc_attr($data['theme']); ?>" />
      <input type="hidden" id="path" name="path" value="<?php echo esc_attr($data['real_file']); ?>" />
      <input type="hidden" name="scroll_to" id="scroll_to" value="<?php echo $data['scroll_to']; ?>" />
      <input type="hidden" name="content-type" id="content-type" value="<?php echo $data['content-type']; ?>" />
      <?php
        $pathinfo = pathinfo($data['file']);
      ?>
      <input type="hidden" name="extension" id="extension" value="<?php echo $pathinfo['extension']; ?>" />
    </div>
    <p class="submit">
      <?php if(isset($_GET['phperror'])): ?>
        <input type="hidden" name="phperror" value="1" />
        <input type="submit" name="submit" class="button-primary" value="<?php _e('Update File and Attempt to Reactivate', 'wpeditor'); ?>" />
      <?php else: ?>
        <input type="submit" name='submit' class="button-primary" value="<?php _e('Update File', 'wpeditor'); ?>" />
      <?php endif; ?>
      <?php if(WPEditorSetting::getValue('theme_create_new')): ?>
        <input type="button" name="theme-create-new" class="button-primary theme-create-new" value="<?php _e('Create New Theme', 'wpeditor'); ?>" />
      <?php endif; ?>
      <input type="button" class="button-secondary download-file" value="<?php _e('Download File', 'wpeditor'); ?>" />
      <input type="button" class="button-secondary download-theme" value="<?php _e('Download Theme', 'wpeditor'); ?>" />
    </p>
    <?php if(!is_writable($data['real_file'])): ?>
      <div class="error writable-error">
        <p>
          <em><?php _e('You need to make this file writable before you can save your changes. See <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">the Codex</a> for more information.'); ?></em>
        </p>
      </div>
    <?php endif; ?>
  </form>
  <form name="theme_create_form" id="theme_create_form" style="display:none;" action="themes.php?page=wpeditor_themes" method="post">
    <?php wp_nonce_field('create_theme_new', 'create_theme_new'); ?>
    <div>
      <?php if(is_writable(get_theme_root())): ?>
        <table class="form-table">
          <tbody>
            <tr valign="top">
              <th scope="row"><?php _e('Theme Name', 'wpeditor'); ?></th>
              <td>
                <input type="text" name="theme-name" />
                <p class="description"><?php _e('Enter the name that you want to use for your new theme.', 'wpeditor'); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Theme Folder', 'wpeditor'); ?></th>
              <td>
                <input type="text" name="theme-folder" />
                <p class="description"><?php _e('Enter the folder name that you want to use to create your new theme. This will be the name of the new folder that is created and added to your themes directory.', 'wpeditor'); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"></th>
              <td>
                <?php submit_button(__('Create Theme', 'wpeditor'), 'primary', 'submit', false); ?>
                <input type="button" name="cancel-theme-create" class="cancel-theme-create button-primary" value="<?php _e('Cancel', 'wpeditor'); ?>" />
              </td>
            </tr>
          </tbody>
        </table>
      <?php else: ?>
        <p><?php _e('Your theme folder is not writable.  In order to add a new theme, this folder needs to be writable.', 'wpeditor'); ?></p>
        <input type="button" name="cancel-theme-create" class="cancel-theme-create button-primary" value="<?php _e('Cancel', 'wpeditor'); ?>" />
      <?php endif; ?>
    </div>
  </form>
  <?php if(isset($_GET['create_tab'])): ?>
    <script type="text/javascript">
      (function($){
        $(document).ready(function() {
          $('#template_form, #templateside, .updated.below-h2, .fileedit-sub').hide();
          $('#theme_create_form').show();
        })
      })(jQuery);
    </script>  
  <?php endif; ?>
  <form action="" method="post" id="download_theme_form">
    <input type="hidden" name="file" value="<?php echo esc_attr($data['file']); ?>" />
    <input type="hidden" name="download_theme" value="true" />
  </form>
  <form action="" method="post" id="download_file_form">
    <input type="hidden" name="file_path" id="file_path" value="<?php echo esc_attr($data['real_file']); ?>" />
    <input type="hidden" name="download_theme_file" value="true" />
  </form>
  <script type="text/javascript">
    (function($){
      $(document).ready(function(){
        $('.cancel-theme-create').click(function() {
          $('#template_form, #templateside, .updated.below-h2, .fileedit-sub').show();
          $('#theme_create_form').hide();
        });
        $('.theme-create-new').click(function() {
          $('#template_form, #templateside, .updated.below-h2, .fileedit-sub').hide();
          $('#theme_create_form').show();
        });
        $('#template_form').submit(function(){ 
          $('#scroll-to').val( $('#new-content').scrollTop() ); 
        });
        $('#new-content').scrollTop($('#scroll-to').val());
        enableThemeAjaxBrowser('<?php echo urlencode((WPWINDOWS) ? str_replace("/", "\\", $data["real_file"]) : $data["real_file"]); ?>');
        runCodeMirror('<?php echo $pathinfo["extension"]; ?>');
        $('.ajax-loader').hide();
        $('.download-theme').click(function(e) {
          e.preventDefault();
          $('#download_theme_form').submit();
        });
        $('.download-file').click(function(e) {
          e.preventDefault();
          $('#download_file_form').submit();
        });
        $('#theme_upload_form').submit(function() {
          $('.ajax-loader').show();
          var directory = $('#file_directory').val();
          var current_theme_root = $('#current_theme_root').val();
          var data = new FormData();
          $.each($('input[type=file]')[0].files, function(i, file) {
            data.append('file-'+i, file);
          });
          data.append('action', 'upload_files');
          data.append('current_theme_root', current_theme_root);
          data.append('directory', directory);
          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(result) {
              if(result.error[0] === 0) {
                enableThemeAjaxBrowser('<?php echo urlencode((WPWINDOWS) ? str_replace("/", "\\", $data["real_file"]) : $data["real_file"]); ?>');
                $('#upload_message').html('<p class="WPEditorAjaxSuccess" style="padding:5px;">' + result.success + '</p>');
              }
              if(result.error[0] === -2) {
                $('#upload_message').html('<p class="WPEditorAjaxError" style="padding:5px;">' + result.error[1] + '</p>');
              }
              else if(result.error[0] === -1) {
                $('#upload_message').html('<p class="WPEditorAjaxError" style="padding:5px;">' + result.error[1] + '</p>');
              }
              $('.ajax-loader').hide();
            }
          });
          return false;
        });
      })
    })(jQuery);
    function runCodeMirror(extension) {
      if(extension === 'php') {
        var mode = 'application/x-httpd-php';
      }
      else if(extension === 'css') {
        var mode = 'css';
      }
      else if(extension === 'js') {
        var mode = 'javascript';
      }
      else if(extension === 'html' || extension === 'htm') {
        var mode = 'text/html';
      }
      else if(extension === 'xml') {
        var mode = 'application/xml';
      }
      <?php
      if(WPEditorSetting::getValue('theme_editor_theme')) { ?>
        var theme = '<?php echo WPEditorSetting::getValue("theme_editor_theme"); ?>';
      <?php }
      else { ?>
        var theme = 'default';
      <?php } ?>
      var activeLine = false;
      <?php if(WPEditorSetting::getValue('enable_theme_active_line')) { ?>
        var activeLine = 'activeline-' + theme;
      <?php } ?>
      editor = CodeMirror.fromTextArea(document.getElementById('new-content'), {
        mode: mode,
        theme: theme,
        <?php
        if(WPEditorSetting::getValue('enable_theme_line_numbers')) { ?>
          lineNumbers: true,
        <?php } ?>
          indentUnit: <?php echo WPEditorSetting::getValue('theme_indent_unit') == '' ? 2 : WPEditorSetting::getValue('theme_indent_unit'); ?>,
        <?php
        if(WPEditorSetting::getValue('enable_theme_line_wrapping')) { ?>
          lineWrapping: true,
        <?php }
        if(WPEditorSetting::getValue('enable_theme_tab_characters') && WPEditorSetting::getValue('enable_theme_tab_characters') == 'tabs') { ?>
          indentWithTabs: true,
        <?php }
        if(WPEditorSetting::getValue('enable_theme_tab_size')) { ?>
          tabSize: <?php echo WPEditorSetting::getValue('enable_theme_tab_size'); ?>,
        <?php } else { ?>
          tabSize: 2,
        <?php } ?>
        onCursorActivity: function() {
          if(activeLine) {
            editor.setLineClass(hlLine, null, null);
            hlLine = editor.setLineClass(editor.getCursor().line, null, activeLine);
          }
        },
        onChange: function() {
          changeTrue();
        },
        extraKeys: {
          'F11': toggleFullscreenEditing, 
          'Esc': toggleFullscreenEditing
        } // set fullscreen options here
      });
      $jq('.CodeMirror').css('font-size', '<?php echo WPEditorSetting::getValue("change_theme_editor_font_size") ? WPEditorSetting::getValue("change_theme_editor_font_size") . "px" : "12px"; ?>');
      if(activeLine) {
        var hlLine = editor.setLineClass(0, activeLine);
      }
      <?php if(WPEditorSetting::getValue('enable_theme_editor_height')) { ?>
        $jq = jQuery.noConflict();
        $jq('.CodeMirror-scroll, .CodeMirror').height('<?php echo WPEditorSetting::getValue("enable_theme_editor_height"); ?>px');
        var scrollDivHeight = $jq('.CodeMirror-scroll div:first-child').height();
        var editorDivHeight = $jq('.CodeMirror').height();
        if(scrollDivHeight > editorDivHeight) {
          $jq('.CodeMirror-gutter').height(scrollDivHeight);
        }
      <?php } ?>
      if(!$jq('.CodeMirror .quicktags-toolbar').length) {
        $jq('.CodeMirror').prepend('<div class="quicktags-toolbar">' + 
          '<a href="#" class="button-primary editor-button" id="theme_save">save</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_undo">undo</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_redo">redo</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_search">search</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_find_prev">find prev</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_find_next">find next</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_replace">replace</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_replace_all">replace all</a>&nbsp;' + 
          '<a href="#" class="button-secondary editor-button" id="theme_fullscreen">fullscreen</a>&nbsp;' + 
          '</div>'
        );
        $jq('.CodeMirror-scroll').height($jq('.CodeMirror-scroll').height() - 33);
        editor.focus();
      }
      $jq('#theme_fullscreen').live("click", function() {
        toggleFullscreenEditing();
        editor.focus();
      })
      $jq('#theme_save').live("click", function() {
        $jq('.ajax-editor-update').submit();
        editor.focus();
      })
      $jq('#theme_undo').live("click", function() {
        editor.undo();
        editor.focus();
      })
      $jq('#theme_redo').live("click", function() {
        editor.redo();
        editor.focus();
      })
      $jq('#theme_search').live("click", function() {
        CodeMirror.commands.find(editor);
        return false;
      })
      $jq('#theme_find_next').live("click", function() {
        CodeMirror.commands.findNext(editor);
        return false;
      })
      $jq('#theme_find_prev').live("click", function() {
        CodeMirror.commands.findPrev(editor);
        return false;
      })
      $jq('#theme_replace').live("click", function() {
        CodeMirror.commands.replace(editor);
        return false;
      })
      $jq('#theme_replace_all').live("click", function() {
        CodeMirror.commands.replaceAll(editor);
        return false;
      })
    }
  </script> 
</div>
<div class="alignright">
</div>
<br class="clear" />