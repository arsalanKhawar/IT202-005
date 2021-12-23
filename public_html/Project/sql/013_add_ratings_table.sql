CREATE TABLE IF NOT EXISTS Ratings(
    id int AUTO_INCREMENT PRIMARY  KEY,
    product_id int,
    user_id int,
    rating int,
    usercomment TEXT,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)