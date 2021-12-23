<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");


$result = [];
$columns = get_columns("BGD_Items");
$ignore = ["id", "modified", "created", "visibility", "image"];
$db = getDB();
//get the item
$id = se($_GET, "id", -1, false);
$stmt = $db->prepare("SELECT * FROM BGD_Items where id =:id");
try {
    $stmt->execute([":id" => $id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) {
        $result = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}

?>
<div class="container-fluid">
    <h1>Product Details</h1>
        <?php foreach ($result as $column => $value) : ?>
            <?php /* Lazily ignoring fields via hardcoded array*/ ?>
            <?php if (!in_array($column, $ignore)) : ?>
                <div class="mb-4">
                    <?php se($column); ?>:
                    <?php se($value); ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <form method="POST">
            <label for="desired_quantity">Quantity:</label>
            <input type="number" name="desired_quantity">
            <input type="hidden" name="product_id" value="<?php se($result['id'], 'id'); ?>" />
            <input type="hidden" name="user_id" value="<?php se(get_user_id(), 'user_id'); ?>" />
            <input type="hidden" name="unit_cost" value="<?php se($result['unit_price'], 'user_id'); ?>" />
            <input type="submit" value="Add to cart">
        </form>

</div>

<?php
if (isset($_POST["product_id"]) && isset($_POST["user_id"]) && isset($_POST["unit_cost"]) && isset($_POST["desired_quantity"])) {
    $product_id = se($_POST, "product_id", "", false);
    $user_id = se($_POST, "user_id", "", false);
    $unit_cost = se($_POST, "unit_cost", "", false);
    $desired_quantity = se($_POST, "desired_quantity", "", false);

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO User_cart (product_id, user_id, unit_cost, desired_quantity) VALUES(:product_id, :user_id, :unit_cost, :desired_quantity)");
    try {
        $stmt->execute([":product_id" => $product_id, ":user_id" => $user_id, ":unit_cost" => $unit_cost, ":desired_quantity" => $desired_quantity]);
        flash("Added to cart!");
    } catch (Exception $e) {
        if(is_logged_in()){
        flash("There was a problem");
        }
        else{
            flash("You need to be logged in to add items to the cart.");
        }
        
    }
}



$results = [];
$columns = get_columns("Ratings");
//echo "<pre>" . var_export($columns, true) . "</pre>";
$ignore = ["id", "created", "user_id", "product_id", "password", "modified", "privacy", "email"];

$db = getDB();
//get the item
$stmt = $db->prepare("SELECT * FROM Ratings INNER JOIN Users ON Ratings.user_id= Users.id WHERE product_id = :product_id");
try {
    $stmt->execute([":product_id" => $id]);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}


?>





<div class="container-fluid">
        <table class="table text-light">
            <h1>Ratings</h1>
            <?php foreach ($results as $index => $record) : ?>
                <?php if ($index == 0) : ?>
                    <thead>
                        <?php foreach ($record as $column => $value) : ?>
                            <?php if (!in_array($column, $ignore)) : ?>
                                <th><?php se($column); ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <th>Email</th>
                    </thead>
                <?php endif; ?>
                <tr>
                    <?php foreach ($record as $column => $value) : ?>
                        <?php if (!in_array($column, $ignore)) : ?>
                            <th><?php se($value, null, "N/A"); ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if ($results[$index]["privacy"] == "public") : ?>
                        <th><?php se(get_user_email()) ?></th>
                    <?php endif; ?>

                </tr>
            <?php endforeach; ?>
        </table>

</div>

<?php

require(__DIR__ . "/../../partials/flash.php");
?>