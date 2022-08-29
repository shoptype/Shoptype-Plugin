<?php

function renderAwakeCollections($atts = []){
    ob_start();
    global $stPlatformId;
    global $stBackendUrl;
    if(isset($atts['collection_id']) && !empty($atts['collection_id'])){
       $collection_id = $atts['collection_id'];
       try {
          $response = wp_remote_get("{$stBackendUrl}/platforms/{$stPlatformId}/collections/{$collection_id}");
		      $result = wp_remote_retrieve_body( $response );
        
          if( !empty( $result ) ) {
            $st_collection = json_decode($result);
          }else{
          }
        }
        catch(Exception $e) {
          echo "Cart not found";
        }
    } 

    if(!empty($st_collection->collections)) : ?>
        <div class="st-collections">
            <?php foreach($st_collection->collections as $count=>$collection): ?>
                <?php $classCount =  $count > 2 ? 3 : $count +1;?>
                <div class="st-collection-<?php echo $classCount; ?>">
                  <a href="/collections/<?php echo $collection->id; ?>" class="st-collection-link"><img src="<?php echo $collection->preview_image_src; ?>" loading="lazy" alt="" class="st-collection-img">
                    <div class="st-collection-title-block">
                      <h2 class="st-collection-title"><?php echo $collection->name; ?></h2>
                    </div>
                  </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; 
    return ob_get_clean();
}
add_shortcode('awake_collections', 'renderAwakeCollections');
?>
