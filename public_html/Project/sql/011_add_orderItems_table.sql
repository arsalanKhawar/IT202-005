CREATE TABLE IF NOT EXISTS OrderItems(
    id int AUTO_INCREMENT PRIMARY  KEY,
    order_id int,
    product_id int,
    quntity int,
    unit_price int
)