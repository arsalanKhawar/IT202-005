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
        <input class="btn btn-primary" type="submit" value="Update" name="submit" />
</div>

<?php
require(__DIR__ . "/../../partials/flash.php");
?>