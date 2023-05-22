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
      var st_face_id = data.find(x=>x.name=="st_face_id");
      var st_shop_url = data.find(x=>x.name=="st_shop_url");

      var myshop_facebook = data.find(x=>x.name=="myshop-facebook");
      var myshop_twitter = data.find(x=>x.name=="myshop-twitter");
      var myshop_instagram = data.find(x=>x.name=="myshop-instagram");
      var myshop_youtube = data.find(x=>x.name=="myshop-youtube");

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

      if(!st_face_id){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,         // Required
            type: 'textbox',                // Required
            name: 'st_face_id', // Required
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("st_face_id exist --- skipped");
      }

      if(!st_shop_url){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,         // Required
            type: 'textbox',                // Required
            name: 'st_shop_url', // Required
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("st_shop_url exist --- skipped");
      }

      if(!myshop_facebook){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,         // Required
            type: 'textbox',                // Required
            name: 'myshop-facebook', // Required
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("myshop-facebook exist --- skipped");
      }

      if(!myshop_twitter){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,         // Required
            type: 'textbox',                // Required
            name: 'myshop-twitter', // Required
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("myshop-twitter exist --- skipped");
      }

      if(!myshop_instagram){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,         // Required
            type: 'textbox',                // Required
            name: 'myshop-instagram', // Required
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("myshop-instagram exist --- skipped");
      }

      if(!myshop_youtube){
        wp.apiRequest( {
          path: 'buddypress/v1/xprofile/fields',
          type: 'POST',
          data: {
            context: 'edit',
            group_id: groupid,         // Required
            type: 'textbox',                // Required
            name: 'myshop-youtube', // Required
            can_delete: false,
            allow_custom_visibility: "disabled"
          }
        } ).done( function( data ) {
          console.info(data);
        } ).fail( function( error ) {
          console.error(error);
        } );
      }else{
        console.info("myshop-youtube exist --- skipped");
      }

    } ).fail( function( error ) {
      console.error(error);
    } );
}
