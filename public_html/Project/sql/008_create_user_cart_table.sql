CREATE TABLE IF NOT EXISTS User_cart(
    id int AUTO_INCREMENT PRIMARY  KEY,
    product_id int,
    user_id int,
    desired_quantity int,
    unit_cost int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
)