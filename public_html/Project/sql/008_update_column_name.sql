ALTER TABLE BGD_Items DROP COLUMN cost;
ALTER TABLE BGD_Items ADD unit_price int DEFAULT 0;
ALTER TABLE BGD_Items ADD category VARCHAR(20);