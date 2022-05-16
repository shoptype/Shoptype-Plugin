<?php

function renderAwakeCollections($atts = []){
    ob_start();
    global $stPlatformId;
    if(isset($atts['collection_id']) && !empty($atts['collection_id'])){
       $collection_id = $atts['collection_id'];
       try {
          $ch = curl_init();
          $urlparts = parse_url(home_url());
          $domain = $urlparts['host'];
          curl_setopt($ch, CURLOPT_URL, "https://backend.shoptype.com/platforms/{$stPlatformId}/collections/{$collection_id}");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($ch);
          curl_close($ch);
          
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
