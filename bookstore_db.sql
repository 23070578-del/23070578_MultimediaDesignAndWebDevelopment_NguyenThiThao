
-- =========================================================
-- HOMEWORK 6: ONLINE BOOKSTORE DATABASE
-- File: bookstore_db.sql
-- DBMS: MySQL / MariaDB
-- Charset: utf8mb4
-- =========================================================

CREATE DATABASE IF NOT EXISTS bookstore_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bookstore_db;

-- =========================================================
-- NORMALIZATION NOTES: 1NF, 2NF, 3NF
--
-- 1NF:
-- All attributes contain single values. Repeating groups of
-- authors, categories, and order books are stored in separate
-- junction/detail tables.
--
-- 2NF:
-- Non-key attributes depend on the complete primary key.
-- Junction tables use composite primary keys. Their non-key
-- attributes do not depend on only part of those keys.
--
-- 3NF:
-- Non-key attributes depend on the key, the whole key, and
-- nothing but the key. Author, publisher, category, customer,
-- and book information are stored in their own tables.
--
-- Design note:
-- orders.total is retained because the assignment requires it.
-- It must be kept consistent with the sum of order item subtotals.
-- order_items.subtotal is generated from quantity * unit_price.
-- =========================================================

-- Drop tables in dependency order so this file can be imported
-- again into a local coursework database.
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS book_categories;
DROP TABLE IF EXISTS book_authors;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS publishers;

-- =========================================================
-- 1. PUBLISHERS
-- =========================================================

CREATE TABLE publishers (
    publisher_id INT UNSIGNED AUTO_INCREMENT,
    publisher_name VARCHAR(150) NOT NULL,
    address VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (publisher_id),
    UNIQUE KEY uq_publishers_name (publisher_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. AUTHORS
-- =========================================================

CREATE TABLE authors (
    author_id INT UNSIGNED AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    biography TEXT NULL,
    nationality VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (author_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3. CATEGORIES
-- Supports parent categories and subcategories.
-- =========================================================

CREATE TABLE categories (
    category_id INT UNSIGNED AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    parent_category_id INT UNSIGNED NULL,

    PRIMARY KEY (category_id),
    UNIQUE KEY uq_categories_name (category_name),

    CONSTRAINT fk_categories_parent
        FOREIGN KEY (parent_category_id)
        REFERENCES categories(category_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 4. BOOKS
-- =========================================================

CREATE TABLE books (
    book_id INT UNSIGNED AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL,
    publisher_id INT UNSIGNED NOT NULL,
    publication_year YEAR NULL,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (book_id),
    UNIQUE KEY uq_books_isbn (isbn),
    KEY idx_books_publisher (publisher_id),

    CONSTRAINT fk_books_publisher
        FOREIGN KEY (publisher_id)
        REFERENCES publishers(publisher_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_books_price
        CHECK (price > 0),

    CONSTRAINT chk_books_stock
        CHECK (stock_quantity >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 5. BOOK_AUTHORS
-- Resolves the M:N relationship between books and authors.
-- =========================================================

CREATE TABLE book_authors (
    book_id INT UNSIGNED NOT NULL,
    author_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (book_id, author_id),
    KEY idx_book_authors_author (author_id),

    CONSTRAINT fk_book_authors_book
        FOREIGN KEY (book_id)
        REFERENCES books(book_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_book_authors_author
        FOREIGN KEY (author_id)
        REFERENCES authors(author_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 6. BOOK_CATEGORIES
-- Resolves the M:N relationship between books and categories.
-- =========================================================

CREATE TABLE book_categories (
    book_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (book_id, category_id),
    KEY idx_book_categories_category (category_id),

    CONSTRAINT fk_book_categories_book
        FOREIGN KEY (book_id)
        REFERENCES books(book_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_book_categories_category
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 7. CUSTOMERS
-- password_hash contains sample placeholder values for this
-- coursework dataset, not real user credentials.
-- =========================================================

CREATE TABLE customers (
    customer_id INT UNSIGNED AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (customer_id),
    UNIQUE KEY uq_customers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 8. ORDERS
-- =========================================================

CREATE TABLE orders (
    order_id INT UNSIGNED AUTO_INCREMENT,
    customer_id INT UNSIGNED NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM(
        'Pending',
        'Processing',
        'Shipped',
        'Delivered',
        'Cancelled'
    ) NOT NULL DEFAULT 'Pending',
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    shipping_address VARCHAR(255) NOT NULL,

    PRIMARY KEY (order_id),
    KEY idx_orders_customer (customer_id),

    CONSTRAINT fk_orders_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(customer_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_orders_total
        CHECK (total >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 9. ORDER_ITEMS
-- Resolves the M:N relationship between orders and books.
-- subtotal is a generated column.
-- =========================================================

CREATE TABLE order_items (
    order_item_id INT UNSIGNED AUTO_INCREMENT,
    order_id INT UNSIGNED NOT NULL,
    book_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2)
        GENERATED ALWAYS AS (quantity * unit_price) STORED,

    PRIMARY KEY (order_item_id),
    KEY idx_order_items_order (order_id),
    KEY idx_order_items_book (book_id),

    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_order_items_book
        FOREIGN KEY (book_id)
        REFERENCES books(book_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_order_items_quantity
        CHECK (quantity > 0),

    CONSTRAINT chk_order_items_unit_price
        CHECK (unit_price > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 10. REVIEWS
-- =========================================================

CREATE TABLE reviews (
    review_id INT UNSIGNED AUTO_INCREMENT,
    customer_id INT UNSIGNED NOT NULL,
    book_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT NULL,
    review_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (review_id),
    UNIQUE KEY uq_reviews_customer_book (customer_id, book_id),
    KEY idx_reviews_book (book_id),

    CONSTRAINT fk_reviews_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(customer_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_reviews_book
        FOREIGN KEY (book_id)
        REFERENCES books(book_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT chk_reviews_rating
        CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- SAMPLE DATA
-- Minimums: 6 authors, 4 publishers, 5 categories,
-- 8 books, 5 customers, 6 orders, 12 order items, 10 reviews.
-- =========================================================

-- 4 publishers
INSERT INTO publishers
    (publisher_id, publisher_name, address, website)
VALUES
    (1, 'Penguin Random House', 'New York, USA',
        'https://www.penguinrandomhouse.com'),
    (2, 'HarperCollins', 'New York, USA',
        'https://www.harpercollins.com'),
    (3, 'Macmillan Publishers', 'London, UK',
        'https://www.macmillan.com'),
    (4, 'Simon & Schuster', 'New York, USA',
        'https://www.simonandschuster.com');

-- 6 authors
INSERT INTO authors
    (author_id, first_name, last_name, biography, nationality)
VALUES
    (1, 'George', 'Orwell', 'English novelist and essayist.', 'British'),
    (2, 'Jane', 'Austen', 'English novelist known for social commentary.', 'British'),
    (3, 'J. K.', 'Rowling', 'Author of the Harry Potter series.', 'British'),
    (4, 'Yuval Noah', 'Harari', 'Historian and nonfiction author.', 'Israeli'),
    (5, 'Paulo', 'Coelho', 'Brazilian novelist.', 'Brazilian'),
    (6, 'Michelle', 'Obama', 'Author and former First Lady of the United States.', 'American');

-- 5 categories, including subcategories
INSERT INTO categories
    (category_id, category_name, parent_category_id)
VALUES
    (1, 'Fiction', NULL),
    (2, 'Non-Fiction', NULL),
    (3, 'Fantasy', 1),
    (4, 'Classic Literature', 1),
    (5, 'Biography', 2);

-- 8 books
INSERT INTO books
    (book_id, title, isbn, publisher_id, publication_year,
     price, stock_quantity, description)
VALUES
    (1, '1984', '9780451524935', 1, 1949,
        12.99, 50, 'A dystopian novel.'),
    (2, 'Pride and Prejudice', '9780141439518', 2, 1813,
        10.50, 40, 'A classic novel of manners.'),
    (3, 'Animal Farm', '9780451526342', 1, 1945,
        11.25, 35, 'A political allegorical novella.'),
    (4, 'Harry Potter and the Philosophers Stone', '9780747532699', 3, 1997,
        18.99, 60, 'A fantasy novel about a young wizard.'),
    (5, 'Sapiens', '9780062316097', 2, 2011,
        29.99, 25, 'A history of humankind.'),
    (6, 'The Alchemist', '9780061122415', 4, 1988,
        15.99, 30, 'A philosophical novel.'),
    (7, 'Becoming', '9781524763138', 4, 2018,
        15.99, 20, 'A memoir.'),
    (8, 'Emma', '9780141439587', 2, 1815,
        13.50, 28, 'A classic novel by Jane Austen.');

-- M:N: books and authors
INSERT INTO book_authors (book_id, author_id)
VALUES
    (1, 1),
    (2, 2),
    (3, 1),
    (4, 3),
    (5, 4),
    (6, 5),
    (7, 6),
    (8, 2);

-- M:N: books and categories
INSERT INTO book_categories (book_id, category_id)
VALUES
    (1, 1),
    (1, 4),
    (2, 1),
    (2, 4),
    (3, 1),
    (3, 4),
    (4, 1),
    (4, 3),
    (5, 2),
    (6, 1),
    (7, 2),
    (7, 5),
    (8, 1),
    (8, 4);

-- 5 customers
-- These password_hash values are placeholders for coursework only.
INSERT INTO customers
    (customer_id, first_name, last_name, email,
     password_hash, phone, address)
VALUES
    (1, 'An', 'Nguyen', 'an.nguyen@example.com',
        'sample_hash_customer_1', '0901000001', 'Hanoi, Vietnam'),
    (2, 'Binh', 'Tran', 'binh.tran@example.com',
        'sample_hash_customer_2', '0901000002', 'Da Nang, Vietnam'),
    (3, 'Chi', 'Le', 'chi.le@example.com',
        'sample_hash_customer_3', '0901000003', 'Ho Chi Minh City, Vietnam'),
    (4, 'Dung', 'Pham', 'dung.pham@example.com',
        'sample_hash_customer_4', '0901000004', 'Hai Phong, Vietnam'),
    (5, 'Hoa', 'Vo', 'hoa.vo@example.com',
        'sample_hash_customer_5', '0901000005', 'Can Tho, Vietnam');

-- 6 orders
-- Totals below match the sum of their order item subtotals.
INSERT INTO orders
    (order_id, customer_id, order_date, status, total, shipping_address)
VALUES
    (1, 1, '2026-09-01 09:00:00', 'Delivered',
        23.49, 'Hanoi, Vietnam'),
    (2, 2, '2026-09-03 10:30:00', 'Shipped',
        40.74, 'Da Nang, Vietnam'),
    (3, 3, '2026-09-05 14:15:00', 'Processing',
        35.25, 'Ho Chi Minh City, Vietnam'),
    (4, 1, '2026-09-07 16:00:00', 'Delivered',
        40.74, 'Hanoi, Vietnam'),
    (5, 4, '2026-09-09 11:20:00', 'Pending',
        24.00, 'Hai Phong, Vietnam'),
    (6, 5, '2026-09-10 08:45:00', 'Shipped',
        48.98, 'Can Tho, Vietnam');

-- 13 order items
-- order 3: 11.25 + 13.50 + 10.50 = 35.25
-- order 4: 15.99 + 11.25 + 13.50 = 40.74
INSERT INTO order_items
    (order_item_id, order_id, book_id, quantity, unit_price)
VALUES
    (1, 1, 1, 1, 12.99),
    (2, 1, 2, 1, 10.50),

    (3, 2, 4, 1, 18.99),
    (4, 2, 3, 1, 11.25),
    (5, 2, 2, 1, 10.50),

    (6, 3, 3, 1, 11.25),
    (7, 3, 8, 1, 13.50),
    (8, 3, 2, 1, 10.50),

    (9, 4, 7, 1, 15.99),
    (10, 4, 3, 1, 11.25),
    (11, 4, 8, 1, 13.50),

    (12, 5, 8, 1, 13.50),
    (13, 5, 2, 1, 10.50),

    (14, 6, 4, 1, 18.99),
    (15, 6, 5, 1, 29.99);

-- 10 reviews
INSERT INTO reviews
    (review_id, customer_id, book_id, rating, comment, review_date)
VALUES
    (1, 1, 1, 5, 'An excellent and thought-provoking book.', '2026-09-02 10:00:00'),
    (2, 2, 2, 5, 'A wonderful classic.', '2026-09-04 11:00:00'),
    (3, 3, 3, 4, 'Short and meaningful.', '2026-09-06 12:00:00'),
    (4, 4, 4, 5, 'A very enjoyable fantasy novel.', '2026-09-08 13:00:00'),
    (5, 5, 5, 4, 'Interesting historical perspective.', '2026-09-11 14:00:00'),
    (6, 1, 6, 4, 'An inspiring story.', '2026-09-12 15:00:00'),
    (7, 2, 7, 5, 'A compelling memoir.', '2026-09-13 16:00:00'),
    (8, 3, 8, 4, 'A charming classic.', '2026-09-14 17:00:00'),
    (9, 4, 1, 5, 'Worth reading more than once.', '2026-09-15 18:00:00'),
    (10, 5, 4, 5, 'Great for fantasy readers.', '2026-09-16 19:00:00');

-- =========================================================
-- VERIFICATION QUERIES
-- Run these after importing to verify the sample data.
-- =========================================================

SELECT 'authors' AS table_name, COUNT(*) AS total FROM authors
UNION ALL
SELECT 'publishers', COUNT(*) FROM publishers
UNION ALL
SELECT 'categories', COUNT(*) FROM categories
UNION ALL
SELECT 'books', COUNT(*) FROM books
UNION ALL
SELECT 'book_authors', COUNT(*) FROM book_authors
UNION ALL
SELECT 'book_categories', COUNT(*) FROM book_categories
UNION ALL
SELECT 'customers', COUNT(*) FROM customers
UNION ALL
SELECT 'orders', COUNT(*) FROM orders
UNION ALL
SELECT 'order_items', COUNT(*) FROM order_items
UNION ALL
SELECT 'reviews', COUNT(*) FROM reviews;

-- Verify order totals against the sum of line subtotals.
SELECT
    o.order_id,
    o.total AS order_total,
    COALESCE(SUM(oi.subtotal), 0) AS calculated_total
FROM orders AS o
LEFT JOIN order_items AS oi
    ON o.order_id = oi.order_id
GROUP BY o.order_id, o.total
ORDER BY o.order_id;

-- =========================================================
-- END OF HOMEWORK 6 SQL FILE
-- =========================================================
