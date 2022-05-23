bp.apiRequest( {
  path: 'buddypress/v1/xprofile/groups',
  type: 'POST',
  data: {
    context: 'edit',
    name: 'Shop',
    description: 'Shoptype My Shop',
    can_delete: false
  }
} ).done( function( groupData ) {
    console.info(groupData);
    bp.apiRequest( {
      path: 'buddypress/v1/xprofile/fields',
      type: 'POST',
      data: {
        context: 'edit',
        group_id: groupData[0].id,         // Required
        type: 'textbox',                // Required
        name: 'st_products', // Required
        can_delete: false,
        allow_custom_visibility: "disabled"
      }
    } ).done( function( data ) {
      console.info(data);
    } ).fail( function( error ) {
      console.error(error);
    } );
    bp.apiRequest( {
      path: 'buddypress/v1/xprofile/fields',
      type: 'POST',
      data: {
        context: 'edit',
        group_id: groupData[0].id,         // Required
        type: 'textbox',                // Required
        name: 'st_shop_bio', // Required
        can_delete: false,
        allow_custom_visibility: "disabled"
      }
    } ).done( function( data ) {
      console.info(data);
    } ).fail( function( error ) {
      console.error(error);
    } );
    bp.apiRequest( {
      path: 'buddypress/v1/xprofile/fields',
      type: 'POST',
      data: {
        context: 'edit',
        group_id: groupData[0].id,         // Required
        type: 'textbox',                // Required
        name: 'st_shop_name', // Required
        can_delete: false,
        allow_custom_visibility: "disabled"
      }
    } ).done( function( data ) {
      console.info(data);
    } ).fail( function( error ) {
      console.error(error);
    } );
} ).fail( function( error ) {
  console.error(error);
} );