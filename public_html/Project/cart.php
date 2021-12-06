<?php
require(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
$total = 0;
$results = [];
$columns = get_columns("User_cart");
$ignore = ["id", "modified", "created", "user_id", "description" , "image", "category", "product_id", "visibility", "stock", "unit_price"];
$db = getDB();
$userid = get_user_id();
//get the item
$stmt = $db->prepare("SELECT * FROM BGD_Items Inner Join User_cart ON BGD_Items.id=User_cart.product_id  where  user_id =:user_id");
try {
    $stmt->execute([":user_id" => $userid]);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}
?>
<div class="container-fluid">
    <h1>Your Cart</h1>

    
    <?php if (count($results) == 0) : ?>
        <p>Your Cart is Empty</p>
    <?php else : ?>
        <table class="table text-light">
            <?php foreach ($results as $index => $record) : ?>
                <?php if ($index == 0) : ?>
                    <thead>
                        <?php foreach ($record as $column => $value) : ?>
                            <?php if (!in_array($column, $ignore)) : ?>
                                <th><?php se($column); ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <th>subtotal</th>
                        <th>Actions</th>
                    </thead>
                <?php endif; ?>
                <tr>
                    <?php foreach ($record as $column => $value) : ?>
                        <?php if (!in_array($column, $ignore)) : ?>
                            <td><?php se($value, null, "N/A"); ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                            <td> $ <?php se($results[$index]["unit_price"] * $results[$index]["desired_quantity"])  ?></td>
                            
                    <td>
                        <a href="productDetails.php?id=<?php se($results[$index], "product_id"); ?>">Product Details</a> 
                        <form method="POST">
                            <input type="hidden" name="product_id2" value="<?php se($results[$index]["product_id"], 'product_id'); ?>" />
                            <input type="hidden" name="user_id2" value="<?php se(get_user_id(), 'user_id'); ?>" />
                            <input type="submit" value="Remove from cart">
                        </form>                   
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<?php 
    if (isset($_POST["product_id2"]) && isset($_POST["user_id2"])) {
        $product_id = se($_POST, "product_id2", "", false);
        $user_id = se($_POST, "user_id2", "", false);
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM User_cart WHERE product_id = :product_id2 AND user_id = :user_id2");
        try {
            $stmt->execute([":product_id2" => $product_id, ":user_id2" => $user_id]);
            flash("removed from cart!");
            
        } catch (Exception $e) {
            if(is_logged_in()){
            flash("There was a problem");
            }
            else{
                flash("You need to be logged in to remove items from the cart.");
            }
            
        }
    }
    
    foreach ($results as $index => $record){
        $total = $total + $results[$index]["unit_price"] * $results[$index]["desired_quantity"];
    }
?>
<br>
<br>
<br>
<br>
<?php if(!count($results)==0) : ?>
    <form method="POST">
        <input type="hidden" name="user_id3" value="<?php se(get_user_id(), 'user_id'); ?>" />
        <input type="submit" value="Remove all from cart">
    </form>           
<?php endif; ?>
<?php 

    if (isset($_POST["user_id3"])) {
        $user_id = se($_POST, "user_id3", "", false);
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM User_cart WHERE user_id = :user_id3");
        try {
            $stmt->execute([":user_id3" => $user_id]);
            flash("removed all from cart!");
            
        } catch (Exception $e) {
            if(is_logged_in()){
            flash("There was a problem");
            }
            else{
                flash("You need to be logged in to remove items from the cart.");
            }
            
        }
    }
    
    foreach ($results as $index => $record){
        $total = $total + $results[$index]["unit_price"] * $results[$index]["desired_quantity"];
    }
?>

<br>
<br>
<br>
<br>
<h2>Your total is: <?php se($total) ?></h2>

        
<?php
require(__DIR__ . "/../../partials/flash.php");
?>