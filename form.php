<div class='wrap'>
	<h2>SQL Executioner</h2>
	<script type="text/javascript">
	function submit_desc( table_stub ) {
		document.getElementById( 'sql' ).value = 'describe ' + table_stub;
		document.forms['sql_executioner'].submit();
	}

	function check_sql() {
		sql = document.getElementById( 'sql' ).value;
		if ( sql.match( /\s*(alter|create|drop|rename|insert|delete|update|replace|truncate) /i ) ) {
			return confirm( "This query may modify data in your database. There is no undo. Are you sure?" );
		} else {
			return true;
		}
	}
	</script>

	<form method="post" name="sql_executioner">
		<?php wp_nonce_field( 'sql-executioner-submit' ); ?>
		<strong>Tables: </strong>
		<?php 
		$links = array();
		$link_template ="<a href='#' onclick='submit_desc( this.innerHTML );return false;' title='Click to describe %s'>%s</a>";
		foreach ( $this->tables as $table_name => $table_stub )
			$links[] = sprintf($link_template, esc_attr( $table_name ), esc_html( $table_stub ) );
		print implode(', ', $links);
		?>
		
		<h3>SQL</h3>
		<textarea id='sql' name='sql' rows="3" cols="60" style="width:100%"><?php print esc_html( $sql ); ?></textarea><br />
		<p><strong>Use with extreme caution!</strong> The author of this plugin assumes no liability whatsoever for the potential destructive effects of its use.</p>
		<input type="submit" class="button" value="Execute SQL" onclick='return check_sql();' />
	</form>

	<?php if ( count( $results['rows'] ) > 0 ): ?>
		<h3>Results</h3>
		<strong>Raw query:</strong> <?php print esc_html( $results['sql'] ); ?>
		
		<?php 
		if ( isset( $results['affected_rows'] ) )
			print $results['affected_rows'] + ' row' + ($results['affected_rows'] != 1 ? 's': '') + 'affected';
		?>

		<div style='width:100%;overflow:auto;padding:2px;'>
			<table border='1' style='border-collapse:collapse;background:#F4F4F4;'>
				<thead>
					<?php
					$row = array_shift( $results['rows'] );
					print "<tr>";
					foreach ( $row as $value ) 
						print "<th>" .  esc_html($value) . "</th>";
					print "</tr>";					
					?>
				</thead>
				<tbody>
					<?php
					foreach ( $results['rows'] as $row ) {
						print "<tr>";
						foreach ( $row as $value ) 
							print "<td>" .  esc_html($value) . "</td>";
						print "</tr>";
					}
					?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<?php if ( isset( $results['error'] ) ): ?>
		<h3>Error</h3>
		<?php print esc_html( $results['error'] ); ?>
	<?php endif; ?>
	
</div>