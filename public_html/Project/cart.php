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

<html>
<style>
input[type=text], select {
  width: 20%;
  padding: 12px 20px;
  margin: 8px 0;
  display: block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=submit] {
  width: 100%;
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type=number], select {
  width: 20%;
  padding: 12px 20px;
  margin: 8px 0;
  display: block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}


input[type=submit]:hover {
  background-color: #45a049;
}


</style>
<body>


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
    
    
?>

<br>
<br>
<br>
<br>
<h2>Your total is: <?php se($total) ?></h2>



        
<?php
$results = [];
$columns = get_columns("User_cart");
$ignore = ["id", "modified", "created"];
$db = getDB();
$userid = get_user_id();
//get the item
$stmt = $db->prepare("SELECT * FROM User_cart INNER JOIN BGD_Items ON User_cart.product_id=BGD_Items.id WHERE user_id =:user_id");
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
    <h1>Checkout</h1>
        <form method="POST">
            <label for="payment_method">Payment Method:</label>
            <input type="text" name="payment_method">
            <label for="cart_total">cart total:</label>
            <input type="number" name="cart_total">
            <label for="Name">Name:</label>
            <input type="text" name="Name">
            <label for="address">Address:</label>
            <input type="text" name="address">
            <label for="city">City:</label>
            <input type="text" name="city">
            <label for="state">State:</label>
            <input type="text" name="state">
            <label for="zipcode">Zipcode:</label>
            <input type="text" name="zipcode">
            <input type="hidden" name="user_id" value="<?php se(get_user_id(), 'user_id'); ?>" />
            <input type="hidden" name="total_price" value="<?php se($total, 'total_price'); ?>" />
            <input type="submit" value="Purchase">
        </form>
</div>

<?php
$isInStock = true;
foreach($results as $index => $record){
    if($results[$index]["stock"] < $results[$index]["desired_quantity"] && isset($_POST["payment_method"]) && isset($_POST["user_id"]) && isset($_POST["cart_total"])){
        flash($results[$index]["name"] . " is out of stock. There are only " . $results[$index]["stock"] . " units left. Please update your cart");
        $isInStock = false;
    }
}


if($isInStock){
    if(se($_POST, "total_price" , "", false) != se($_POST, "cart_total" , "", false)) {
    flash("invalid cart total");
    }
    else if (isset($_POST["payment_method"]) && isset($_POST["user_id"]) && isset($_POST["cart_total"])) {
        //add items into OrderItems
        if (isset($_POST["payment_method"]) && isset($_POST["user_id"]) && isset($_POST["cart_total"])) {

            
        foreach ($results as $index => $record){
                    
        
                if (!in_array($column, $ignore))
                {
                    if($results[$index]["unit_cost"] != $results[$index]["unit_price"]){
                        $total = $total - ($results[$index]["unit_cost"] - $results[$index]["unit_price"]);
                        $results[$index]["unit_cost"] = $results[$index]["unit_price"];
                        flash("the price of some of your items has changed. Price of " . $results[$index]["name"] . " is now". $results[$index]["unit_cost"] . ". New total is $" . $total);
                    }
                    
                        $stmt = $db->prepare("INSERT INTO OrderItems (product_id, order_id, unit_price, quntity) VALUES(:product_id, :order_id, :unit_cost, :quntity)");
                        try {
                            $stmt->execute([":product_id" => $results[$index]["product_id"], ":order_id" => $results[$index]["user_id"], ":unit_cost" => $results[$index]["unit_cost"], ":quntity" => $results[$index]["desired_quantity"]]);
                        } catch (Exception $e) {
                            if(is_logged_in()){
                            flash("There was a problem");
                            }
                            else{
                                flash("You need to be logged in to purchase.");
                            }
                            
                        }
                }
            
        }
        }

        //add order ot Orders

        $address = se($_POST, "address", "", false) . ", " . se($_POST, "city", "", false) . ", " . se($_POST, "state", "", false) . " " . se($_POST, "zipcode", "", false);
        $user_id = se($_POST, "user_id", "", false);
        $total_price = se($_POST, "total_price", "", false);
        $payment_method = se($_POST, "payment_method", "", false);

        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Orders (user_id, total_price, address, payment_method) VALUES( :user_id, :total_price, :address, :payment_method)");
        try {
            $stmt->execute([":address" => $address, ":user_id" => $user_id, ":total_price" => $total_price, ":payment_method" => $payment_method]);
            flash("purchased!");
        } catch (Exception $e) {
            if(is_logged_in()){
            flash("There was a problem");
            }
            else{
                flash("You need to be logged in to purchase.");
            }
            
        }
        


        
    }
}


require(__DIR__ . "/../../partials/flash.php");
?>
