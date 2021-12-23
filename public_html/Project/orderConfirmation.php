<?php
require(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}

$results = [];
$columns = get_columns("Orders");
//echo "<pre>" . var_export($columns, true) . "</pre>";
$ignore = ["id", "modified", "created", "user_id", "total_price", "visibility", "category", "description", "order_id", "product_id", "stock"];
$db = getDB();
//get the item
$order_id = se($_GET, "order_id", -1, false);
$stmt = $db->prepare("SELECT * FROM OrderItems INNER JOIN BGD_Items ON OrderItems.product_id= BGD_Items.id Inner Join Orders ON OrderItems.order_id = Orders.id WHERE order_id = :order_id");
try {
    $stmt->execute([":order_id" => $order_id]);
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
            <h1>Thanks for shopping!</h1>
            <?php foreach ($results as $index => $record) : ?>
                <?php if ($index == 0) : ?>
                    <thead>
                        <?php foreach ($record as $column => $value) : ?>
                            <?php if (!in_array($column, $ignore)) : ?>
                                <th><?php se($column); ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <th>subtotal</th>
                    </thead>
                <?php endif; ?>
                <tr>
                    <?php foreach ($record as $column => $value) : ?>
                        <?php if (!in_array($column, $ignore)) : ?>
                            <th><?php se($value, null, "N/A"); ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td> $ <?php se($results[$index]["unit_price"] * $results[$index]["quntity"])  ?></td>

                </tr>
            <?php endforeach; ?>
        </table>
    <h2>Your total is: <?php se($results[0]["total_price"]) ?></h2>

</div>

<?php
require(__DIR__ . "/../../partials/flash.php");

?>