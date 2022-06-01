<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template is overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 *
 * @see     https://backend.shoptype.com/api/#/cart
 * @package Shoptype
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); 

global $stApiKey;
global $stPlatformId;
global $stCurrency;
global $productUrl;

$path = dirname(plugin_dir_url( __FILE__ ));
$cartId = get_query_var( 'cart' );

if(empty($cartId)){
  $cartstr = stripslashes($_COOKIE["carts"]);
  $cartsParsed = json_decode($cartstr);
  $cartId = $cartsParsed->shoptypeCart;
}
try {
  $headers = array(
    "X-Shoptype-Api-Key: ".$stApiKey,
    "X-Shoptype-PlatformId: ".$stPlatformId
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://backend.shoptype.com/cart/$cartId");
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  if( !empty( $result ) ) {
    $st_cart = json_decode($result);
    $prodCurrency = $stCurrency[$st_cart->sub_total->currency];
  }
}
catch(Exception $e) {
  echo "Cart not found";
}


?>



<?php if ( ! empty($st_cart->cart_lines) ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( $st_cart->cart_lines as $cart_item_key => $cart_item ) {
			$_product   = $cart_item;
			$product_id = $cart_item->product_id;
			$cart_item->id = $cart_item->product_id;

 			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
				$product_name      = $_product->name;
				$thumbnail         = "<img src='$_product->image_src' loading='lazy' />"
				$product_price     = $_product->price->amount;
				$product_permalink = str_replace("{{productId}}",$product_id,$productUrl);
				?>
				<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
							esc_url( wc_get_cart_remove_url( $product_id ) ),
							esc_attr__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $product_id ),
							esc_attr( $_product->product_variant_id )
						),
						$product_id
					);
					?>
					<?php if ( empty( $product_permalink ) ) : ?>
						<?php echo $thumbnail . wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<a href="<?php echo esc_url( $product_permalink ); ?>">
							<?php echo $thumbnail . wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<p class="woocommerce-mini-cart__total total">
		<?php
		/**
		 * Hook: woocommerce_widget_shopping_cart_total.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
