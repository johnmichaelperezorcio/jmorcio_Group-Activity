<?php 
require_once 'cart.php';
$items = cart_items_with_details(); 
$total = cart_total(); 
?> 
<h2>Your Cart</h2> 
<?php if (empty($items)): ?> 
  <p>Cart is empty</p> 
<?php else: ?> 
  <form method="post" action="cart_action.php"> 
  <table> 
    
<tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></
 th></tr> 
    <?php foreach ($items as $it): $p=$it['product']; ?> 
      <tr> 
        <td><?=htmlspecialchars($p['name'])?></td> 
        <td><?=number_format($p['price'],2)?></td> 
        <td> 
          <input type="number" name="qty_<?=$p['id']?>" 
value="<?=$it['qty']?>" min="1" style="width:60px"> 
        </td> 
        <td><?=number_format($it['subtotal'],2)?></td> 
        <td> 
          <form method="post" action="cart_action.php"> 
            <input type="hidden" name="action" value="remove"> 
            <input type="hidden" name="product_id" 
value="<?=$p['id']?>"> 
            <button>Remove</button> 
          </form> 
        </td> 
      </tr> 
    <?php endforeach; ?> 
  </table> 
  <p>Total: <?=number_format($total,2)?></p> 
  <a href="checkout_form.php">Proceed to checkout</a> 
<?php endif; 
?>