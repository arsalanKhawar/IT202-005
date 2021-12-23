<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}

$result = [];
$columns = get_columns("BGD_Items");
$ignore = ["id", "modified", "created", "visibility", "image"];
$db = getDB();
//get the item
$id = se($_GET, "product_id", -1, false);
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
            <label for="rating">rating (1-5):</label>
            <input type="number" name="rating" min = 1 max = 5>
            <label for="usercomment">Comment:</label>
            <input type="text" name="usercomment">
            <input type="hidden" name="product_id" value="<?php se($result['id'], 'id'); ?>" />
            <input type="hidden" name="user_id" value="<?php se(get_user_id(), 'user_id'); ?>" />
            <input type="submit" value="Submit rating">
        </form>

</div>

<?php
if (isset($_POST["product_id"]) && isset($_POST["user_id"]) && isset($_POST["rating"]) && isset($_POST["usercomment"])) {
    $product_id = se($_POST, "product_id", "", false);
    $user_id = se($_POST, "user_id", "", false);
    $rating = se($_POST, "rating", "", false);
    $usercomment = se($_POST, "usercomment", "", false);

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Ratings (product_id, user_id, rating, usercomment) VALUES(:product_id, :user_id, :rating, :usercomment)");
    try {
        $stmt->execute([":product_id" => $product_id, ":user_id" => $user_id, ":rating" => $rating, ":usercomment" => $usercomment]);
        flash("Rating submitted");
    } catch (Exception $e) {
        if(is_logged_in()){
        flash("There was a problem");
        }
        else{
            flash("You need to be logged in to rate items");
        }
        
    }
}

require(__DIR__ . "/../../partials/flash.php");
?>