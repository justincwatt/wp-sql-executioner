<?php if ( isset( $results['rows'] ) && count( $results['rows'] ) ): ?>
	<h3>CSV</h3>
	<textarea style='width:100%;overflow:auto;padding:2px;'><?php
		foreach ( $results['rows'] as $row ) {
			print SQL_Executioner_Plugin::str_putcsv($row) . "\n";
		}
	?></textarea>
<?php endif; ?>
