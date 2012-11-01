function sql_executioner_submit_desc( table_stub ) {
	document.getElementById( 'sql' ).value = 'describe ' + table_stub;
	document.forms['sql_executioner'].submit();
}

function sql_executioner_check_sql() {
	sql = document.getElementById( 'sql' ).value;
	if ( sql.match( /\s*(alter|create|drop|rename|insert|delete|update|replace|truncate) /i ) ) {
		return confirm( "This query may modify data in your database. There is no undo. Are you sure?" );
	} else {
		return true;
	}
}
