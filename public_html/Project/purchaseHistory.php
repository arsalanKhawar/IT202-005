<?php
require(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}

$results = [];
$columns = get_columns("Orders");
//echo "<pre>" . var_export($columns, true) . "</pre>";
$ignore = ["id", "modified", "created", "user_id", "total_price", "visibility", "category", "description", "order_id", "product_id", "stock", "image"];
$db = getDB();
//get the item
$stmt = $db->prepare("SELECT * FROM OrderItems INNER JOIN BGD_Items ON OrderItems.product_id= BGD_Items.id Inner Join Orders ON OrderItems.order_id = Orders.id");
try {
    $stmt->execute();
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
            <h1>Purchase history</h1>
            <?php foreach ($results as $index => $record) : ?>
                <?php if ($index == 0) : ?>
                    <thead>
                        <?php foreach ($record as $column => $value) : ?>
                            <?php if (!in_array($column, $ignore)) : ?>
                                <th><?php se($column); ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                       <th> Actions</th>
                    </thead>
                <?php endif; ?>
                <tr>
                    <?php foreach ($record as $column => $value) : ?>
                        <?php if (!in_array($column, $ignore)) : ?>
                            <th><?php se($value, null, "N/A"); ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td>
                        <a href="orderConfirmation.php?order_id=<?php se($results[$index], "order_id"); ?>">Order Confirmation</a> 
                        <a style="color: red;" href="rate.php?product_id=<?php se($results[$index], "product_id"); ?>">   Rate Product</a> 

                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

</div>

<?php
require(__DIR__ . "/../../partials/flash.php");

?>