<?php
/* Template Name:Cosell Widget */
  global $wp;

  $cosell_link = get_query_var('cosell_link');

  if(!isset($_COOKIE["stToken"])) {
    $user_token = "";
  }else{
    $user_token = $_COOKIE["stToken"];
  }

  if(!empty($user_token)){
    try {
      $args = array(
        'headers' => array(
          "Authorization" =>$user_token
        ));
      $current_url = home_url( $wp->request );
      $response = wp_remote_get("{$stBackendUrl}/track/network?referrer={$current_url}",$args);
      $result = wp_remote_retrieve_body( $response );

      if( !empty( $result ) ) {
        $st_tracker = json_decode($result);
        $tracker  = $st_tracker->trackerId;
      }else{
        $tracker = "";
      }
    }
    catch(Exception $e) {
      $tracker ="";
    }
  }

if(!empty($tracker)){
  
  if (!function_exists('http_build_url'))
{
    define('HTTP_URL_REPLACE', 1);              // Replace every part of the first URL when there's one of the second URL
    define('HTTP_URL_JOIN_PATH', 2);            // Join relative paths
    define('HTTP_URL_JOIN_QUERY', 4);           // Join query strings
    define('HTTP_URL_STRIP_USER', 8);           // Strip any user authentication information
    define('HTTP_URL_STRIP_PASS', 16);          // Strip any password authentication information
    define('HTTP_URL_STRIP_AUTH', 32);          // Strip any authentication information
    define('HTTP_URL_STRIP_PORT', 64);          // Strip explicit port numbers
    define('HTTP_URL_STRIP_PATH', 128);         // Strip complete path
    define('HTTP_URL_STRIP_QUERY', 256);        // Strip query string
    define('HTTP_URL_STRIP_FRAGMENT', 512);     // Strip any fragments (#identifier)
    define('HTTP_URL_STRIP_ALL', 1024);         // Strip anything but scheme and host

    // Build an URL
    // The parts of the second URL will be merged into the first according to the flags argument. 
    // 
    // @param   mixed           (Part(s) of) an URL in form of a string or associative array like parse_url() returns
    // @param   mixed           Same as the first argument
    // @param   int             A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
    // @param   array           If set, it will be filled with the parts of the composed url like parse_url() would return 
    function http_build_url($url, $parts=array(), $flags=HTTP_URL_REPLACE, &$new_url=false)
    {
        $keys = array('user','pass','port','path','query','fragment');

        // HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
        if ($flags & HTTP_URL_STRIP_ALL)
        {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
            $flags |= HTTP_URL_STRIP_PORT;
            $flags |= HTTP_URL_STRIP_PATH;
            $flags |= HTTP_URL_STRIP_QUERY;
            $flags |= HTTP_URL_STRIP_FRAGMENT;
        }
        // HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
        else if ($flags & HTTP_URL_STRIP_AUTH)
        {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
        }

        // Parse the original URL
        // - Suggestion by Sayed Ahad Abbas
        //   In case you send a parse_url array as input
        $parse_url = !is_array($url) ? parse_url($url) : $url;

        // Scheme and Host are always replaced
        if (isset($parts['scheme']))
            $parse_url['scheme'] = $parts['scheme'];
        if (isset($parts['host']))
            $parse_url['host'] = $parts['host'];

        // (If applicable) Replace the original URL with it's new parts
        if ($flags & HTTP_URL_REPLACE)
        {
            foreach ($keys as $key)
            {
                if (isset($parts[$key]))
                    $parse_url[$key] = $parts[$key];
            }
        }
        else
        {
            // Join the original URL path with the new path
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH))
            {
                if (isset($parse_url['path']))
                    $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
                else
                    $parse_url['path'] = $parts['path'];
            }

            // Join the original query string with the new query string
            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY))
            {
                if (isset($parse_url['query']))
                    $parse_url['query'] .= '&' . $parts['query'];
                else
                    $parse_url['query'] = $parts['query'];
            }
        }

        // Strips all the applicable sections of the URL
        // Note: Scheme and Host are never stripped
        foreach ($keys as $key)
        {
            if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key)))
                unset($parse_url[$key]);
        }


        $new_url = $parse_url;

        return 
             ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
            .((isset($parse_url['host'])) ? $parse_url['host'] : '')
            .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            .((isset($parse_url['path'])) ? $parse_url['path'] : '')
            .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
        ;
    }
}
  
  
  
  $url_parts = parse_url("https://".$_SERVER['HTTP_HOST']."/".$cosell_link);
  if (isset($url_parts['query'])) { 
      parse_str($url_parts['query'], $params);
  } else {
      $params = array();
  }

  $params['tid'] = $tracker;     // Overwrite if exists

  $url_parts['query'] = http_build_query($params);

  $current_url = http_build_url($url_parts);

  $sharetxt = "Hey, found this really interesting site that you may really like, checkit out";
  
?>

<div id="st-cosell-mask" style="display:none" class="st-cosell-link-mask" onclick="ShoptypeUI.hide(this)">
  <div class="st-cosell-links" onclick="event.stopPropagation()">
    <div class="st-cosell-links-header">Here’s your unique Cosell link!</div>
    <div class="st-cosell-body">
      <div class="st-cosell-links-image">
        <img src="https://user-images.githubusercontent.com/4776769/164173060-33787091-37fc-45a9-b16e-2c3eb1fb82e7.png" loading="lazy" alt="">
      </div>
      <div class="st-cosell-social-links">
        <div class="st-cosell-social-title">Share it on Social Media</div>
        <div class="st-cosell-socialshare">
          <a id="st-fb-link" href="<?php echo "https://www.facebook.com/sharer/sharer.php?u=$current_url" ?>" class="st-cosell-socialshare-link w-inline-block">
            <img src="https://user-images.githubusercontent.com/4776769/164173335-e156685a-9be9-468f-9aef-145e4d6b8ee7.png" loading="lazy" alt="">
          </a>
          <a id="st-twitter-link" href="<?php echo "http://twitter.com/share?text=$sharetxt&url=$current_url" ?>" class="st-cosell-socialshare-link w-inline-block">
            <img src="https://user-images.githubusercontent.com/4776769/164174320-1234c471-5b69-473e-8b63-46b4d8f61189.png" loading="lazy" alt="">
          </a>
          <a id="st-whatsapp-link" href="<?php echo "whatsapp://send?text=$sharetxt $current_url" ?>" class="st-cosell-socialshare-link w-inline-block">
            <img src="https://user-images.githubusercontent.com/4776769/164174179-5103826f-d131-4677-b581-031727195c0e.png" loading="lazy" alt="">
          </a>
          <a id="st-pinterest-link" href="<?php echo "https://pinterest.com/pin/create/link/?url=$current_url" ?>" class="st-cosell-socialshare-link w-inline-block">
            <img src="https://user-images.githubusercontent.com/4776769/164173344-e0f1fbe1-1ac0-4846-837b-97f47a556bf5.png" loading="lazy" alt="">
          </a>
          <a id="st-linkedin-link" href="<?php echo "https://www.linkedin.com/shareArticle?mini=true&source=LinkedIn&url=$current_url" ?>" class="st-cosell-socialshare-link w-inline-block">
            <img src="https://user-images.githubusercontent.com/4776769/164173350-af72f6b5-7926-42c6-abb4-c77b6db9da58.png" loading="lazy" alt="">
          </a>
        </div>
      </div>
      <div class="st-cosell-links-txt">or</div>
      <div class="st-cosell-sharelink">
        <div class="st-cosell-sharelink-div">
          <div class="st-cosell-sharelink-url">
            <div class="st-cosell-link-copy-btn" onclick="shoptype_UI.stCopyCosellUrl('st-cosell-url-input')">🔗 Copy to Clipboard</div>
            <input type="text" id="st-cosell-url-input" class="st-cosell-sharelink-url-txt" value="<?php echo "$current_url"?>"></input>
          </div>
        </div>
        <div id="st-cosell-sharewidget" class="st-cosell-sharelink-div">
          <div class="st-cosell-share-widget-txt">Share on Blogs</div>
          <div id="st-widget-btn" class="st-cosell-share-widget-btn">Get an Embed</div>
        </div>
      </div>
    </div>
    <div class="st-cosell-links-footer">
      <div class="st-cosell-footer-shoptype">Powered by <a href="https://www.shoptype.com" target="_blank" class="st-cosell-footer-shoptype-link">Shoptype</a>
      </div>
      <a href="#" target="_blank" class="w-inline-block" style="display:none;">
        <div class="st-cosell-page-txt">Learn more about Coselling</div>
      </a>
    </div>
  </div>
</div>

<?php
}
else{
?>

<div class="st-cosell-link-mask" id="st-cosell-intro-mask" style="display:none" onclick="ShoptypeUI.hide(this)">
  <div class="st-cosell-links" onclick="event.stopPropagation()">
    <div class="st-cosell-links-header" id="st-cosell-links-header"><?php echo $_SERVER['HTTP_HOST'] ?> is proud to introduce &quot;Cosell&quot; , A unique way to boost the influencer in you. <br>
      <span class="st-cosell-links-header-span">Share and make Money Instantly.</span>
    </div>
    <div class="st-cosell-body">
      <div class="st-cosell-steps-div">
        <div class="st-cosell-exp">
          <div class="st-cosell-exp-header-div">
            <h3 class="st-cosell-exp-header">How to be a Coseller</h3>
          </div>
          <div class="st-cosell-exp-steps">
            <div class="st-cosell-step">
              <div class="st-cosell-step-no st-cosell-step-overlay">1</div>
              <div class="st-cosell-step-img-div">
                <img src="https://user-images.githubusercontent.com/4776769/164172794-7618254d-eac2-4bd3-a7c2-5d5a12195b71.png" loading="lazy" alt="" class="st-cosell-step-img">
              </div>
              <div class="st-cosell-step-title">Signup</div>
            </div>
            <div class="st-cosell-step">
              <div class="st-cosell-step-no st-cosell-step-overlay">2</div>
              <div class="st-cosell-step-img-div">
                <img src="https://user-images.githubusercontent.com/4776769/164173181-bff98789-3c04-4448-a0d9-7f70ff24b800.png" loading="lazy" alt="" class="st-cosell-step-img">
              </div>
              <div class="st-cosell-step-title">Click Cosell on cool products</div>
            </div>
            <div class="st-cosell-step">
              <div class="st-cosell-step-no st-cosell-step-overlay">3</div>
              <div class="st-cosell-step-img-div">
                <img src="https://user-images.githubusercontent.com/4776769/164172794-7618254d-eac2-4bd3-a7c2-5d5a12195b71.png" loading="lazy" alt="" class="st-cosell-step-img">
              </div>
              <div class="st-cosell-step-title">Share with your Network</div>
            </div>
          </div>
        </div>
        <div class="st-cosell-signup">
          <div class="st-cosell-sugnup-btn" onclick="shoptype_UI.showLogin()">Become a Coseller</div>
        </div>
      </div>
      <div class="st-cosell-adv">
        <div class="st-cosell-step-pts">
          <div class="st-cosell-step-no">1</div>
          <div class="st-cosell-step-txt">Coselling is Free, No membership fee.</div>
        </div>
        <div class="st-cosell-step-pts">
          <div class="st-cosell-step-no">2</div>
          <div class="st-cosell-step-txt">Cosell across all participating Market Networks, across the Internet.</div>
        </div>
        <div class="st-cosell-step-pts">
          <div class="st-cosell-step-no">3</div>
          <div class="st-cosell-step-txt">Cosell links are unique. Share, get paid when inviting others to grow your referral Network.</div>
        </div>
      </div>
    </div>
    <div class="st-cosell-links-footer">
      <div class="st-cosell-footer-shoptype">Powered by <a href="https://www.shoptype.com" target="_blank" class="st-cosell-footer-shoptype-link">Shoptype</a>
      </div>
      <a href="#" target="_blank" class="st-link-block">
        <div class="st-cosell-page-txt">Learn more about Coselling</div>
      </a>
    </div>
  </div>
</div>
<?php 
}
?>