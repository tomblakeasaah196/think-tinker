-- ============================================================
-- seed-bookstore-products.sql
--
-- Seeds 11 real bookstore products (9 workbooks + 2 stationery items)
-- into the `products` table, with cover images already placed at
-- public_html/uploads/products/2026/08/ (upload this folder alongside
-- the code changes, before or together with running this script).
--
-- SAFE TO RE-RUN: uses INSERT IGNORE keyed on the unique `products.slug`
-- column, so running this twice (or after a product with the same slug
-- already exists) just skips the duplicates — it will never overwrite
-- prices, stock counts, or anything else you've since edited in Hub >
-- Bookstore. This is a one-time seed, not a sync: after this runs once,
-- Hub > Bookstore is the source of truth — edit products there.
--
-- Prices are Tom's "estimated price" figures from the product table.
-- Stock quantities are placeholders (30 for books, 60 for stationery) —
-- update them to real counts via Hub > Bookstore > Edit once you know
-- actual inventory on hand.
-- ============================================================

INSERT IGNORE INTO `products`
    (`name`, `slug`, `description`, `category`, `grade_level`, `subject`, `series`, `price`, `stock_quantity`, `cover_image`, `is_featured`, `is_active`, `location_id`)
VALUES
    ('Easy Painting Book 2',
     'easy-painting-book-2',
     'A fun and colourful painting activity book designed for young children. It introduces children to simple pictures, colours and creative painting while encouraging imagination, hand-eye coordination and artistic confidence.',
     'workbooks', 'Nursery', 'Art & Creativity', 'Alka Easy Painting Book',
     3500.00, 30, 'uploads/products/2026/08/87f9b286bf24_1787142580.jpg', 0, 1, 1),

    ('My First Bumper Copy Colouring Book',
     'my-first-bumper-copy-colouring-book',
     'A vibrant early-learning colouring and copying book featuring fun animals and engaging illustrations. Perfect for helping young children develop pencil control, creativity, colour recognition and early writing skills.',
     'workbooks', 'Nursery', 'Art & Creativity', 'My First Bumper Copy Colouring',
     4000.00, 30, 'uploads/products/2026/08/0722529e48f2_1787142581.jpg', 0, 1, 1),

    ('Mental Maths: Ages 7-8',
     'mental-maths-ages-7-8',
     'A practical mathematics workbook for children aged 7-8, designed to strengthen mental calculation, accuracy and confidence. Includes regular practice and tests covering a range of essential maths skills.',
     'workbooks', 'Ages 7-8', 'Mathematics', 'Collins Mental Maths',
     5000.00, 30, 'uploads/products/2026/08/47f7e6a16450_1787142582.jpg', 0, 1, 1),

    ('Mental Maths: Ages 6-7',
     'mental-maths-ages-6-7',
     'An engaging mental mathematics workbook for children aged 6-7. It provides structured practice to improve calculation skills, accuracy, number confidence and problem-solving through regular exercises and tests.',
     'workbooks', 'Ages 6-7', 'Mathematics', 'Collins Mental Maths',
     5000.00, 30, 'uploads/products/2026/08/9de0e89b0e90_1787142583.jpg', 0, 1, 1),

    ('Mental Arithmetic 3',
     'mental-arithmetic-3',
     'A structured arithmetic workbook designed to develop children''s confidence and speed with numbers. It combines varied mathematical exercises with visual challenges to reinforce essential arithmetic skills and build accuracy.',
     'workbooks', 'Book 3', 'Mathematics', 'Schofield & Sims Mental Arithmetic',
     4500.00, 30, 'uploads/products/2026/08/881fd6fada8b_1787142584.jpg', 0, 1, 1),

    ('Lantern Verbal Reasoning for Primary Schools 2',
     'lantern-verbal-reasoning-for-primary-schools-2',
     'A structured verbal reasoning workbook for primary school learners, designed to develop vocabulary, word relationships, anagrams, analogies and logical thinking through engaging exercises and age-appropriate activities.',
     'workbooks', 'Primary 2 (Lower Basic)', 'Verbal Reasoning', 'Lantern Steps to Verbal Reasoning',
     4500.00, 30, 'uploads/products/2026/08/ab51d748191b_1787142585.jpg', 0, 1, 1),

    ('Lantern Quantitative Reasoning for Primary Schools 2',
     'lantern-quantitative-reasoning-for-primary-schools-2',
     'A practical quantitative reasoning workbook that helps primary school learners build confidence with numbers, number patterns, basic operations, even numbers and essential mathematical problem-solving skills.',
     'workbooks', 'Primary 2 (Lower Basic)', 'Quantitative Reasoning', 'Lantern Steps to Quantitative Reasoning',
     4500.00, 30, 'uploads/products/2026/08/8b7e1e0f6ad4_1787142586.jpg', 0, 1, 1),

    ('Scholastic Success With Grammar: Grade 4',
     'scholastic-success-with-grammar-grade-4',
     'A comprehensive grammar workbook for Grade 4 learners covering sentence types, parts of speech, punctuation, nouns, complete sentences and other essential grammar skills through guided exercises and practice activities.',
     'workbooks', 'Grade 4', 'Grammar', 'Scholastic Success With',
     6000.00, 30, 'uploads/products/2026/08/8d23bd494259_1787142587.jpg', 0, 1, 1),

    ('Scholastic Success With Grammar: Grade 2',
     'scholastic-success-with-grammar-grade-2',
     'An engaging grammar workbook for Grade 2 learners covering sentence structure, nouns, adjectives, capitalization, punctuation and other foundational language skills through simple, child-friendly exercises.',
     'workbooks', 'Grade 2', 'Grammar', 'Scholastic Success With',
     5500.00, 30, 'uploads/products/2026/08/656072d7cf3e_1787142588.jpg', 0, 1, 1),

    ('Deli Enovation Wood-Free HB Pencils - 12 Pack',
     'deli-enovation-wood-free-hb-pencils-12-pack',
     'A set of durable wood-free HB pencils made from recycled materials. Ideal for school, office and everyday writing, with a smooth writing experience and strong, break-resistant lead.',
     'stationery', NULL, NULL, 'Deli Enovation',
     4500.00, 60, 'uploads/products/2026/08/f504d9ce3ea9_1787142589.jpg', 0, 1, 1),

    ('Ergonomic Pencil Grips - Assorted Colours',
     'ergonomic-pencil-grips-assorted-colours',
     'Comfortable ergonomic pencil grips designed to encourage proper finger positioning and improve writing comfort. Suitable for children learning to write and for anyone looking for a more comfortable pencil grip.',
     'stationery', NULL, NULL, NULL,
     2000.00, 60, 'uploads/products/2026/08/a08684ccce41_1787142590.jpg', 0, 1, 1);

-- Verification: run this after the INSERT IGNORE above to confirm all 11
-- landed (re-running the script later will keep showing 11 here, since
-- IGNORE skips rows that already exist rather than duplicating them).
SELECT id, name, slug, category, price, stock_quantity, cover_image
FROM products
WHERE slug IN (
    'easy-painting-book-2',
    'my-first-bumper-copy-colouring-book',
    'mental-maths-ages-7-8',
    'mental-maths-ages-6-7',
    'mental-arithmetic-3',
    'lantern-verbal-reasoning-for-primary-schools-2',
    'lantern-quantitative-reasoning-for-primary-schools-2',
    'scholastic-success-with-grammar-grade-4',
    'scholastic-success-with-grammar-grade-2',
    'deli-enovation-wood-free-hb-pencils-12-pack',
    'ergonomic-pencil-grips-assorted-colours'
)
ORDER BY id;
