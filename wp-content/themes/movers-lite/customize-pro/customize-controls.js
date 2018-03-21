( function( api ) {

	// Extends our custom "movers" section.
	api.sectionConstructor['movers-lite'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );