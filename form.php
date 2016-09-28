<div class='wrap'>
	<?php screen_icon(); ?>
	<h2>SQL Executioner</h2>

	<form method="post" name="sql_executioner">
		<?php wp_nonce_field( 'sql-executioner-submit' ); ?>
		<p>
		<label>Tables: </label>
		<?php
		$links = array();
		$link_template ="<a href='#' onclick='sql_executioner_submit_desc( this.innerHTML );return false;' title='Click to describe %s'>%s</a>";
		foreach ( $this->tables as $table_name => $table_stub )
			$links[] = sprintf($link_template, esc_attr( $table_name ), esc_html( $table_stub ) );
		print implode(', ', $links);
		?>
		</p>

		<div class="form-field">
			<label>SQL</label>
			<textarea id='sql' name='sql' rows="3" cols="60" style="width:100%;font-family:Consolas,Monaco,monospace;"><?php print esc_html( $sql ); ?></textarea>
		</div>
		<p><strong>Use with extreme caution!</strong> The author of this plugin assumes no liability whatsoever for the potential destructive effects of its use.</p>
		<input type="submit" class="button button-primary" value="Execute SQL" onclick='return sql_executioner_check_sql();' />
	</form>


	<?php if ( isset( $results['error'] ) ): ?>
		<h3>Error</h3>
		<?php print esc_html( $results['error'] ); ?>

	<?php elseif ( isset( $results['rows'] ) || isset( $results['affected_rows'] ) ): ?>
		
		<h3>Results</h3>
		<strong>Raw query:</strong> <em><?php print esc_html( $results['sql'] ); ?></em><br />
		
		<?php if ( isset( $results['affected_rows'] ) ): ?>
			<?php print $results['affected_rows'] . ' row' . ( $results['affected_rows'] != 1 ? 's' : '' ) . ' affected'; ?>

		<?php elseif ( count( $results['rows'] ) > 0 ): ?>
			<div style='width:100%;overflow:auto;padding:2px;'>
				<table id='sql-executioner-results'>
					<thead>
						<?php						
						$row = array_shift( $results['rows'] );
						print "<tr>";
						foreach ( $row as $value )
							print "<th>" .  esc_html( $value ) . "</th>";
						print "</tr>";					
						?>
					</thead>
					<tbody>
						<?php
						foreach ( $results['rows'] as $row ) {
							print "<tr>";
							foreach ( $row as $value )
								print "<td>" .  esc_html( $value ) . "</td>";
							print "</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		<?php else: ?>
			<p>0 rows returned.</p>
		<?php endif; ?>
	<?php endif; ?>
	
</div>
