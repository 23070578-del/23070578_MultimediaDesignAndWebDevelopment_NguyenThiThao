-- Homework 7: Bookstore Queries
-- Name: Nguyen Thi Thao
-- Student ID: 23070578
-- Assignment: Homework 7
-- Date: 2026-10-20

USE bookstore_db;

-- Query 1: Books with Authors
-- Show each book with its ISBN and author name.

SELECT
    b.title AS book_title,
    b.isbn AS isbn,
    CONCAT(a.first_name, ' ', a.last_name) AS author_name
FROM books AS b
INNER JOIN book_authors AS ba
    ON b.book_id = ba.book_id
INNER JOIN authors AS a
    ON ba.author_id = a.author_id
ORDER BY b.title, author_name;

/*
Expected Output:
| book_title          | isbn          | author_name     |
|---------------------|---------------|-----------------|
| Example Book        | 9780000000001 | Example Author  |

The actual output depends on the sample data.
*/

-- Query 2: Order Details
-- Show order details with customer, date, book, quantity, and line total.

SELECT
    o.order_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    o.order_date,
    b.title AS book_title,
    oi.quantity,
    oi.quantity * oi.unit_price AS line_total
FROM orders AS o
INNER JOIN customers AS c
    ON o.customer_id = c.customer_id
INNER JOIN order_items AS oi
    ON o.order_id = oi.order_id
INNER JOIN books AS b
    ON oi.book_id = b.book_id
ORDER BY o.order_date DESC;

/*
Expected Output:
| order_id | customer_name | order_date | book_title | quantity | line_total |
|----------|---------------|------------|------------|----------|------------|
| 6        | Example Name  | 2026-...   | Example    | 2        | 27.00      |

Actual results depend on the database sample data.
*/

-- Query 3: Books with Publisher and Categories
-- Show each book with its publisher and category names.

SELECT
    b.title AS book_title,
    p.publisher_name,
    c.category_name
FROM books AS b
INNER JOIN publishers AS p
    ON b.publisher_id = p.publisher_id
INNER JOIN book_categories AS bc
    ON b.book_id = bc.book_id
INNER JOIN categories AS c
    ON bc.category_id = c.category_id
ORDER BY b.title, c.category_name;

/*
Expected Output:
| book_title   | publisher_name | category_name |
|--------------|----------------|---------------|
| Example Book | Example Press  | Fiction       |

Actual results depend on the database sample data.
*/

-- Query 4: Sales by Book
-- Show the total quantity sold and total revenue for each book.

SELECT
    b.book_id,
    b.title AS book_title,
    COALESCE(SUM(oi.quantity), 0) AS total_quantity_sold,
    COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total_revenue
FROM books AS b
LEFT JOIN order_items AS oi
    ON b.book_id = oi.book_id
GROUP BY b.book_id, b.title
ORDER BY total_revenue DESC;

/*
Expected Output:
| book_id | book_title   | total_quantity_sold | total_revenue |
|---------|--------------|---------------------|---------------|
| 1       | Example Book | 5                   | 67.50         |

Actual results depend on the database sample data.
*/

-- Query 5: Customer Order Summary
-- Show each customer's order count and total amount spent,
-- including customers who have not placed any orders.

SELECT
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(DISTINCT o.order_id) AS total_orders,
    COALESCE(SUM(o.total), 0) AS total_amount_spent
FROM customers AS c
LEFT JOIN orders AS o
    ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.first_name, c.last_name
ORDER BY total_amount_spent DESC;

/*
Expected Output:
| customer_id | customer_name | total_orders | total_amount_spent |
|-------------|---------------|--------------|--------------------|
| 1           | Example Name  | 2            | 60.00              |

Actual results depend on the database sample data.
*/

-- Query 6: Books Above Average Price
-- Find books whose prices are above the average book price.

SELECT
    title AS book_title,
    price
FROM books
WHERE price > (
    SELECT AVG(price)
    FROM books
)
ORDER BY price DESC;

/*
Expected Output:
| book_title        | price |
|-------------------|-------|
| Example Book A   | 25.00 |
| Example Book B   | 22.50 |

Actual results depend on the database sample data.
*/

-- Query 7: Best-Selling Author
-- Find the author(s) whose books have the highest total quantity sold.
-- Use a subquery to handle ties without LIMIT.

SELECT
    author_sales.author_name,
    author_sales.total_quantity_sold
FROM (
    SELECT
        a.author_id,
        CONCAT(a.first_name, ' ', a.last_name) AS author_name,
        SUM(oi.quantity) AS total_quantity_sold
    FROM authors AS a
    INNER JOIN book_authors AS ba
        ON a.author_id = ba.author_id
    INNER JOIN order_items AS oi
        ON ba.book_id = oi.book_id
    GROUP BY a.author_id, a.first_name, a.last_name
) AS author_sales
WHERE author_sales.total_quantity_sold = (
    SELECT MAX(sales.total_quantity_sold)
    FROM (
        SELECT
            ba.author_id,
            SUM(oi.quantity) AS total_quantity_sold
        FROM book_authors AS ba
        INNER JOIN order_items AS oi
            ON ba.book_id = oi.book_id
        GROUP BY ba.author_id
    ) AS sales
);

/*
Expected Output:
| author_name  | total_quantity_sold |
|--------------|---------------------|
| Example Name | 8                   |

Actual results depend on the database sample data.
If multiple authors tie for the highest quantity, all are returned.
*/

-- Query 8: Prolific Reviewers
-- Find customers who have written more than 2 reviews
-- with a rating of 3 or higher.

SELECT
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(r.review_id) AS total_reviews
FROM customers AS c
INNER JOIN reviews AS r
    ON c.customer_id = r.customer_id
WHERE r.rating >= 3
GROUP BY c.customer_id, c.first_name, c.last_name
HAVING COUNT(r.review_id) > 2
ORDER BY total_reviews DESC;

/*
Expected Output:
| customer_id | customer_name | total_reviews |
|-------------|---------------|---------------|
| 1           | Example Name  | 3             |

Actual results depend on the database sample data.
If no customer meets the condition, the result will be empty.
*/

-- Query 9: Create a Book Sales Summary View
-- Show sales, publisher, and review information for each book.

CREATE OR REPLACE VIEW v_book_sales_summary AS
SELECT
    b.title AS book_title,
    b.isbn,
    p.publisher_name,
    COALESCE(s.total_quantity_sold, 0) AS total_quantity_sold,
    COALESCE(s.total_revenue, 0) AS total_revenue,
    r.average_review_rating,
    COALESCE(r.number_of_reviews, 0) AS number_of_reviews
FROM books AS b
INNER JOIN publishers AS p
    ON b.publisher_id = p.publisher_id
LEFT JOIN (
    SELECT
        book_id,
        SUM(quantity) AS total_quantity_sold,
        SUM(quantity * unit_price) AS total_revenue
    FROM order_items
    GROUP BY book_id
) AS s
    ON b.book_id = s.book_id
LEFT JOIN (
    SELECT
        book_id,
        AVG(rating) AS average_review_rating,
        COUNT(review_id) AS number_of_reviews
    FROM reviews
    GROUP BY book_id
) AS r
    ON b.book_id = r.book_id;

/*
Expected Output:
The view contains one row per book and these columns:
book_title, isbn, publisher_name, total_quantity_sold,
total_revenue, average_review_rating, number_of_reviews.
Books without sales show 0 for quantity and revenue.
Books without reviews show NULL for average rating and 0 reviews.
*/

-- Query 10: Monthly Revenue Report
-- Show monthly revenue for all 12 months of the current year,
-- including months with no orders.

WITH RECURSIVE months AS (
    SELECT 1 AS month_number
    UNION ALL
    SELECT month_number + 1
    FROM months
    WHERE month_number < 12
),
monthly_orders AS (
    SELECT
        MONTH(order_date) AS month_number,
        COUNT(*) AS number_of_orders,
        SUM(total) AS total_revenue
    FROM orders
    WHERE YEAR(order_date) = YEAR(CURDATE())
    GROUP BY MONTH(order_date)
),
monthly_items AS (
    SELECT
        MONTH(o.order_date) AS month_number,
        SUM(oi.quantity) AS total_items_sold
    FROM orders AS o
    INNER JOIN order_items AS oi
        ON o.order_id = oi.order_id
    WHERE YEAR(o.order_date) = YEAR(CURDATE())
    GROUP BY MONTH(o.order_date)
)
SELECT
    m.month_number,
    MONTHNAME(
        STR_TO_DATE(
            CONCAT(
                YEAR(CURDATE()), '-',
                LPAD(m.month_number, 2, '0'), '-01'
            ),
            '%Y-%m-%d'
        )
    ) AS month_name,
    COALESCE(mo.number_of_orders, 0) AS number_of_orders,
    COALESCE(mi.total_items_sold, 0) AS total_items_sold,
    COALESCE(mo.total_revenue, 0) AS total_revenue
FROM months AS m
LEFT JOIN monthly_orders AS mo
    ON m.month_number = mo.month_number
LEFT JOIN monthly_items AS mi
    ON m.month_number = mi.month_number
ORDER BY m.month_number;

/*
Expected Output:
| month_number | month_name | number_of_orders | total_items_sold | total_revenue |
|--------------|------------|------------------|------------------|---------------|
| 1            | January    | 0                | 0                | 0.00          |
| 2            | February   | 0                | 0                | 0.00          |
| ...          | ...        | ...              | ...              | ...           |
| 12           | December   | 0                | 0                | 0.00          |

Actual results depend on the order dates in the database.
All 12 months are included.
*/
