<?php if ( $title ) {
	// echo $before_title . $title . $after_title;
} ?>
<div class="quotery-quote-widget quotery-quote-widget-color-<?php echo $instance['color'] ?>">
	<div class="quotery-quote-widget-title"><?php echo $title ?></div>
	<div class="quotery-quote-widget-body">
		<?php if ($instance['author']): ?>
			<div class="quotery-quote-widget-author">
				<div class="quotery-quote-widget-author-image">
						<img src="<?php echo $author->image ?>" alt="<?php echo $author->name ?> <?php __('Image', $this->plugin_slug) ?>">
				</div>
				<div class="quotery-quote-widget-author-content">
					<div class="quotery-quote-widget-author-name">
						<?php echo $author->name ?>
					</div>
					<div class="quotery-quote-widget-author-lived"><?php echo $author->lived ?></div>
				</div>
			</div>
		<?php endif ?>
		<div class="quotery-quote-widget-quote">
			<?php echo $quote ?>
		</div>
		<?php if ($instance['social']): ?>
			<div class="quotery-quote-widget-social">
				<span>Share</span>
				<a href="//pinterest.com/pin/create/%20button?url=<?php echo $share_url; ?>&amp;media=<?php echo $author->image?>&amp;description=<?php echo $quote?>" rel="nofollow" class="entypo-social pinterest"></a>
				<a href="//plus.google.com/share?url=<?php echo $share_url; ?>" class="entypo-social googlep"></a>
				<a href="//twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo $quote?>" class="entypo-social twitter" rel="nofollow" ></a>
				<a href="//www.facebook.com/sharer.php?u=<?php echo $share_url; ?>&amp;title=<?php echo $quote?>" class="entypo-social facebook" rel="nofollow" ></a>
				<a href="//www.tumblr.com/share?s=&amp;v=3&amp;u=<?php echo urlencode($share_url); ?>&amp;t=<?php echo $quote?>" class="entypo-social tumblr" rel="nofollow" ></a>
			</div>
		<?php endif ?>
	</div>
	<div class="quotery-quote-widget-copy"><?php _e('Quotes via', $this->plugin_slug) ?> <a href="http://www.quotery.com/" target="_blank" <?php if(!empty($follow)): ?>rel="nofollow"<?php endif;?> >Quotery</a></div>
</div>
