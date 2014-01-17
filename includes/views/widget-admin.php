<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', $this->plugin_slug) ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('topics'); ?>"><?php _e('Topic:', $this->plugin_slug) ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('topics'); ?>" name="<?php echo $this->get_field_name('topics'); ?>">
		<?php foreach (Quotery_Quote::get_instance()->get_topics_options() as $value => $name): ?>
			<option value="<?php echo $value ?>"<?php echo $value == $instance['topics'] ? ' selected="selected"' : ''?>><?php echo $name ?></option>
		<?php endforeach ?>
	</select>
</p>

<?php /*
<p>
	<label for="<?php echo $this->get_field_id('quantity'); ?>"><?php _e('Quantity:', $this->plugin_slug) ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('quantity'); ?>" name="<?php echo $this->get_field_name('quantity'); ?>">
		<?php foreach (Quotery_Quote::get_instance()->get_quantity_options() as $value => $name): ?>
			<option value="<?php echo $value ?>"<?php echo $value == $instance['quantity'] ? ' selected="selected"' : ''?>><?php echo $name ?></option>
		<?php endforeach ?>
	</select>
</p>
*/ ?>

<p>
	<label for="<?php echo $this->get_field_id('color'); ?>"><?php _e('Color:', $this->plugin_slug) ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('color'); ?>" name="<?php echo $this->get_field_name('color'); ?>">
		<?php foreach (Quotery_Quote::get_instance()->get_color_options() as $value => $name): ?>
			<option value="<?php echo $value ?>"<?php echo $value == $instance['color'] ? ' selected="selected"' : ''?>><?php echo $name ?></option>
		<?php endforeach ?>
	</select>
</p>

<p>

	<input type="checkbox" id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" value="1" <?php echo $instance['author'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Display author', $this->plugin_slug) ?></label>
</p>

<p>

	<input type="checkbox" id="<?php echo $this->get_field_id('social'); ?>" name="<?php echo $this->get_field_name('social'); ?>" value="1" <?php echo $instance['social'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id('social'); ?>"><?php _e('Display social links', $this->plugin_slug) ?></label>
</p>