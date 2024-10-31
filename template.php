<form action="" method="post">
    <?php wp_nonce_field( $namespace . "_options", $namespace . '_update_wpnonce' ); 
    $args_posts = array(
	    'posts_per_page'  => 100,
	    'offset'          => 0,
	    'orderby'         => 'title',
	    'order'           => 'ASC',
	    'post_type'       => 'post',
	    'post_status'     => 'publish',
	    'suppress_filters' => true );
    ?>
    <input type="hidden" name="form_action" value="update_options" />
    <div class="wrap" id="notifysnack_container">
		<img class="not_title" src="<?php echo $this->url_path; ?>/images/basic.png" />
        <h2><?php echo $page_title; ?></h2>

		<div class="clear"></div>
        <div>
            <h3 class="title">Embed code:</h3>
			<textarea rows="10" cols="70" name="notifyscript"><?php echo $notifyscript; ?></textarea>
            <p style="font-style: italic; margin: 0px;">Grab your notification code from <a href="http://www.notifysnack.com">www.notifysnack.com</a> and paste it above.</p>

			<h3>Insert embed code in:</h3>
			<input id="notify_body" type="radio" name="notifyscript_location" value="body" <?php echo $notifyscript_location == 'body' ? ' checked="checked"' : ''; ?> />
			<label for="notify_body">Body (after &lt;body&gt;)</label>
			<br />			
			<input id="notify_header" type="radio" name="notifyscript_location" value="header" <?php echo $notifyscript_location == 'header' ? ' checked="checked"' : ''; ?> />
			<label for="notify_header">Header (before &lt;/head&gt;)</label>
			<br />
			<input id="notify_footer" type="radio" name="notifyscript_location" value="footer" <?php echo $notifyscript_location == 'footer' ? ' checked="checked"' : ''; ?> />
			<label for="notify_footer">Footer (before &lt;/body&gt;)</label>
			<br />
			
			<div id="notify_save">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</div>
        </div>
    </div>
</form>