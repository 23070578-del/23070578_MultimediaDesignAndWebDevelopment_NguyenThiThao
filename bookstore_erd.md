# Homework 6 — Online Bookstore ERD

## 1. Entities and Attributes

### 1. Authors
- `author_id` INT — PK, AUTO_INCREMENT
- `first_name` VARCHAR(100) — NOT NULL
- `last_name` VARCHAR(100) — NOT NULL
- `bio` TEXT — NULL
- `nationality` VARCHAR(100) — NULL
- `created_at` TIMESTAMP — DEFAULT CURRENT_TIMESTAMP

### 2. Publishers
- `publisher_id` INT — PK, AUTO_INCREMENT
- `publisher_name` VARCHAR(150) — NOT NULL, UNIQUE
- `address` VARCHAR(255) — NULL
- `website` VARCHAR(255) — NULL
- `created_at` TIMESTAMP — DEFAULT CURRENT_TIMESTAMP

### 3. Categories
- `category_id` INT — PK, AUTO_INCREMENT
- `category_name` VARCHAR(100) — NOT NULL, UNIQUE
- `parent_category_id` INT — FK → `categories.category_id`, NULL

### 4. Books
- `book_id` INT — PK, AUTO_INCREMENT
- `title` VARCHAR(255) — NOT NULL
- `isbn` VARCHAR(20) — NOT NULL, UNIQUE
- `publisher_id` INT — FK → `publishers.publisher_id`
- `publication_year` YEAR — NULL
- `price` DECIMAL(10,2) — NOT NULL, CHECK (`price` > 0)
- `stock_quantity` INT — NOT NULL, CHECK (`stock_quantity` >= 0)
- `description` TEXT — NULL
- `created_at` TIMESTAMP — DEFAULT CURRENT_TIMESTAMP

### 5. Book_Authors
- `book_id` INT — PK, FK → `books.book_id`
- `author_id` INT — PK, FK → `authors.author_id`
- Composite primary key: (`book_id`, `author_id`)

### 6. Book_Categories
- `book_id` INT — PK, FK → `books.book_id`
- `category_id` INT — PK, FK → `categories.category_id`
- Composite primary key: (`book_id`, `category_id`)

### 7. Customers
- `customer_id` INT — PK, AUTO_INCREMENT
- `first_name` VARCHAR(100) — NOT NULL
- `last_name` VARCHAR(100) — NOT NULL
- `email` VARCHAR(255) — NOT NULL, UNIQUE
- `password_hash` VARCHAR(255) — NOT NULL
- `phone` VARCHAR(30) — NULL
- `address` VARCHAR(255) — NULL
- `created_at` TIMESTAMP — DEFAULT CURRENT_TIMESTAMP

### 8. Orders
- `order_id` INT — PK, AUTO_INCREMENT
- `customer_id` INT — FK → `customers.customer_id`
- `order_date` DATETIME — NOT NULL
- `status` ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') — NOT NULL
- `total` DECIMAL(10,2) — NOT NULL
- `shipping_address` VARCHAR(255) — NOT NULL

### 9. Order_Items
- `order_item_id` INT — PK, AUTO_INCREMENT
- `order_id` INT — FK → `orders.order_id`
- `book_id` INT — FK → `books.book_id`
- `quantity` INT — NOT NULL, CHECK (`quantity` > 0)
- `unit_price` DECIMAL(10,2) — NOT NULL, CHECK (`unit_price` > 0)
- `subtotal` DECIMAL(10,2) — generated from `quantity * unit_price`

### 10. Reviews
- `review_id` INT — PK, AUTO_INCREMENT
- `customer_id` INT — FK → `customers.customer_id`
- `book_id` INT — FK → `books.book_id`
- `rating` TINYINT — NOT NULL, CHECK (`rating` BETWEEN 1 AND 5)
- `comment` TEXT — NULL
- `review_date` DATETIME — NOT NULL

## 2. Relationships and Cardinalities

1. Publishers (1) — (N) Books
2. Authors (M) — (N) Books through `book_authors`
3. Categories (M) — (N) Books through `book_categories`
4. Customers (1) — (N) Orders
5. Orders (1) — (N) Order_Items
6. Books (1) — (N) Order_Items
7. Customers (1) — (N) Reviews
8. Books (1) — (N) Reviews
9. Parent Category (1) — (N) Child Categories through `parent_category_id`

## 3. Normalization and Design Notes

- Each table has a primary key.
- Foreign keys maintain referential integrity between related tables.
- `book_authors` and `book_categories` resolve many-to-many relationships.
- `order_items` resolves the many-to-many relationship between orders and books.
- UNIQUE constraints prevent duplicate ISBNs, publisher names, category names, and customer emails.
- CHECK constraints validate positive prices, positive order quantities, non-negative stock quantities, and ratings between 1 and 5.
- The schema is designed to satisfy First Normal Form (1NF), Second Normal Form (2NF), and Third Normal Form (3NF).
- `subtotal` is derived from quantity multiplied by unit price.
- `orders.total` must be kept consistent with the sum of its order items.
