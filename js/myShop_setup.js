wp.apiRequest( {
  path: 'buddypress/v1/xprofile/groups',
  type: 'GET',
  data: {
    context: 'edit',
    name: 'Shop',
    description: 'Shoptype My Shop',
    can_delete: false
  }
} ).done(d=>{
  var shop = d.find(x=> x.name=="Shop");
  if(shop){
    console.info("st_products exist --- skipped");
    setupFields(shop.id);
  }else{
    wp.apiRequest( {
      path: 'buddypress/v1/xprofile/groups',
      type: 'POST',
      data: {
        context: 'edit',
        name: 'Shop',
        description: 'Shoptype My Shop',
        can_delete: false
      }
    } ).done(newGroup=>{
      console("xprifile group Created")
      setupFields(newGroup[0].id);
    });
  }
});


function setupFields(groupid){
    wp.apiRequest( {
      path: 'buddypress/v1/xprofile/fields',
      type: 'get',
    } ).done( function( data ) {
      console.info(data);
      var stProducts = data.find(x=>x.name=="st_products");
      var stTheme = data.find(x=>x.name=="st_shop_theme");

      if(!stProducts){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,
            type: 'textbox',
            name: 'st_products',
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("st_products exist --- skipped");
      }
      if(!stTheme){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,         // Required
            type: 'textbox',                // Required
            name: 'st_shop_theme', // Required
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("st_shop_theme exist --- skipped");
      }
    } ).fail( function( error ) {
      console.error(error);
    } );
}
