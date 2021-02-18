jQuery( document ).ready( function( $ ){
	/*
	 * toggle visibility of scheduler in widget
	 */
	$( document ).on( 'click', '.hinjipwv-link', function( e ) {
		// remove default behaviour
        e.preventDefault();
		// get grandparent element of clicked link
		var origin_parent = $( this ).parent().parent();
		// if scheduler is closed: open it, else: close it
		// (i.e. change the css class name to let the 'display' property change from 'block' to 'none' and vice versa)
		// and change the text of the clicked link
		if ( origin_parent.hasClass( 'pwv-collapsed' ) ) {
			origin_parent.removeClass( 'pwv-collapsed' ).addClass( 'pwv-expanded' );
			$( this ).text( pwv_i18n.close_scheduler )
		} else {
			origin_parent.removeClass( 'pwv-expanded' ).addClass( 'pwv-collapsed' );
			$( this ).text( pwv_i18n.open_scheduler )
		}
    } );
} );
