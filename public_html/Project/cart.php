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

    <form method="POST" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="input-group mb-3">
            <input class="form-control" type="search" name="itemName" placeholder="Item Filter" />
            <input class="btn btn-primary" type="submit" value="Search" />
        </div>
    </form>
    <?php if (count($results) == 0) : ?>
        <p>No results to show</p>
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
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<?php 
    
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