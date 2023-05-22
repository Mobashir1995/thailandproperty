jQuery( document ).ready( function(){
    if( houzez_vars.keyword_autocomplete != 0 ) {
        jQuery('.houzez-keyword-autocomplete').parents( 'form' ).attr( 'onsubmit', 'return false' );
    }
} );