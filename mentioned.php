<?php $alt = ( $count % 2 ) ? 'irdx-box-even' : 'irdx-box-odd'; ?>

<div class="irdx-box <?php echo $alt; ?> vcard">

	<h3 class="irdx-title"><a href="<?php echo esc_url( $irdx->get_permalink() ); ?>" class="url fn"><?php echo $irdx->get_title(); ?></a></h3>

	<div class="irdx-box-inner">

		<?php
		if ( $thumbnail = $irdx->get_thumbnail( array( 'class' => 'photo' ) ) )
			echo '<div class="irdx-icon"><div>' . $thumbnail . '</div></div>';
		?>

		<div class="irdx-description">
			<div class="irdx-content note">
				<?php echo $this->format_content( $irdx->get_description() ); ?>
			</div>
			<p class="irdx-link">
				<a href="<?php echo esc_url( $irdx->get_permalink() ); ?>"><?php _e( 'Read full IRDX entry', 'irdx_embed' ); ?></a>
			</p>
		</div>

	</div>

</div>