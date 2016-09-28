<?php if ( isset( $results['error'] ) ): ?>
	<h3>Error</h3>
	<?php print esc_html( $results['error'] ); ?>
<?php elseif ( isset( $results['rows'] ) || isset( $results['affected_rows'] ) ): ?>
	<h3>CSV</h3>
	<textarea style='width:100%;overflow:auto;padding:2px;'><?php
			foreach ( $results['rows'] as $row ) {
			$col = 0;
			foreach ( $row as $value ) {
				$col++;
				print '"' . esc_html( $value ) . '"';
				if ( $col === count( $row ) ) {
					print "\n";
				} else {
					print ',';
				}
			}
		}
	?></textarea>
<?php endif; ?>
