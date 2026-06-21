-- ============================================
-- SQL QUERIES
-- ============================================

-- QUERY 1: INSERT
-- Add a new equipment to the equipment table.
INSERT INTO equipment VALUES ('EQ021','NodeMCU ESP8266','CAT05','LAB05',15,15,'AVAILABLE',DATE '2025-01-15');


-- QUERY 2: UPDATE
-- Update the available quantity of an equipment.
UPDATE equipment SET available_quantity = 12 WHERE equipment_id = 'EQ021';


-- QUERY 3: DELETE
-- Remove an equipment from the equipment table.
DELETE FROM equipment WHERE equipment_id = 'EQ021';


-- QUERY 4: SELECT
-- Display all equipment information.
SELECT * FROM equipment;


-- QUERY 5: SEARCH QUERY
-- Search equipment by equipment name.
-- Example: Search for Arduino devices.
SELECT * FROM equipment WHERE UPPER(equipment_name) LIKE UPPER('%Arduino%');


-- QUERY 6: FILTER QUERY
-- Display only available equipment.
SELECT * FROM equipment WHERE status = 'AVAILABLE';


-- QUERY 7: FILTER QUERY
-- Display all equipment under IoT category.
SELECT * FROM equipment WHERE category_id = 'CAT05';


-- QUERY 8: SORT QUERY
-- Display newest equipment first based on purchase date.
SELECT * FROM equipment ORDER BY purchase_date DESC;


-- QUERY 9: SORT QUERY
-- Display oldest equipment first based on purchase date.
SELECT * FROM equipment ORDER BY purchase_date ASC;


-- QUERY 10: SEARCH + FILTER QUERY
-- Display all available IoT devices.
SELECT * FROM equipment WHERE category_id = 'CAT05' AND status = 'AVAILABLE';


-- QUERY 11: SEARCH + SORT QUERY
-- Search router-related equipment and show newest records first.
SELECT * FROM equipment WHERE UPPER(equipment_name) LIKE UPPER('%Router%') ORDER BY purchase_date DESC;


-- SAVE CHANGES
COMMIT;


-- ============================================
-- ADVANCED SQL QUERIES
-- ============================================

-- QUERY 1: JOIN
-- Show booking information with user details.
SELECT b.booking_id, u.full_name, b.equipment_id, b.quantity, b.status FROM booking_requests b JOIN users u ON b.user_id = u.user_id;


-- QUERY 2: INNER JOIN
-- Show equipment with category information.
SELECT e.equipment_id, e.equipment_name, c.category_name FROM equipment e INNER JOIN categories c ON e.category_id = c.category_id;


-- QUERY 3: LEFT JOIN
-- Show all users and their booking requests.
SELECT u.user_id, u.full_name, b.booking_id, b.status FROM users u LEFT JOIN booking_requests b ON u.user_id = b.user_id;


-- QUERY 4: LEFT JOIN
-- Show all equipment and borrow records.
SELECT e.equipment_id, e.equipment_name, br.borrow_id, br.borrow_status FROM equipment e LEFT JOIN borrow_records br ON e.equipment_id = br.equipment_id;


-- QUERY 5: GROUP BY
-- Count equipment in each category.
SELECT category_id, COUNT(*) AS total_equipment FROM equipment GROUP BY category_id;


-- QUERY 6: GROUP BY
-- Count bookings made by each user.
SELECT user_id, COUNT(*) AS total_bookings FROM booking_requests GROUP BY user_id;


-- QUERY 7: HAVING
-- Show users who made more than one booking.
SELECT user_id, COUNT(*) AS total_bookings FROM booking_requests GROUP BY user_id HAVING COUNT(*) > 1;


-- QUERY 8: HAVING
-- Show categories having more than 3 equipment.
SELECT category_id, COUNT(*) AS total_equipment FROM equipment GROUP BY category_id HAVING COUNT(*) > 3;


-- QUERY 9: SUBQUERY
-- Find equipment with quantity greater than average quantity.
SELECT equipment_id, equipment_name, total_quantity FROM equipment WHERE total_quantity > (SELECT AVG(total_quantity) FROM equipment);


-- QUERY 10: SUBQUERY
-- Find users who have approved bookings.
SELECT full_name FROM users WHERE user_id IN (SELECT user_id FROM booking_requests WHERE status = 'APPROVED');


-- REPORT 1: MOST BORROWED EQUIPMENT
-- Display most borrowed equipment.
SELECT equipment_id, SUM(quantity) AS total_borrowed FROM borrow_records GROUP BY equipment_id ORDER BY total_borrowed DESC;


-- REPORT 2: LATE RETURNS
-- Display overdue borrowed equipment.
SELECT borrow_id, user_id, equipment_id, expected_return_date FROM borrow_records WHERE borrow_status = 'OVERDUE';


-- REPORT 3: EQUIPMENT BY CATEGORY
-- Display equipment grouped by category.
SELECT c.category_name, COUNT(e.equipment_id) AS total_equipment FROM categories c LEFT JOIN equipment e ON c.category_id = e.category_id GROUP BY c.category_name ORDER BY total_equipment DESC;


-- REPORT 4: TOTAL FINES BY USER
-- Display total fines for each user.
SELECT u.full_name, SUM(f.amount) AS total_fine FROM users u JOIN borrow_records br ON u.user_id = br.user_id JOIN fines f ON br.borrow_id = f.borrow_id GROUP BY u.full_name ORDER BY total_fine DESC;


-- REPORT 5: BORROWED EQUIPMENT DETAILS
-- Show borrower name with equipment.
SELECT u.full_name, e.equipment_name, br.quantity, br.borrow_status FROM borrow_records br JOIN users u ON br.user_id = u.user_id JOIN equipment e ON br.equipment_id = e.equipment_id;


-- SAVE CHANGES
COMMIT;


-- ============================================
-- TRANSACTIONS + PL/SQL
-- ============================================


-- QUERY 1: COMMIT
-- Update equipment quantity permanently.
UPDATE equipment SET available_quantity = available_quantity + 5 WHERE equipment_id = 'EQ017';

COMMIT;


-- QUERY 2: ROLLBACK
-- Demonstrate rollback operation.
UPDATE equipment SET available_quantity = available_quantity - 5 WHERE equipment_id = 'EQ017';

ROLLBACK;


-- QUERY 3: ANONYMOUS BLOCK
-- Display total equipment count.
SET SERVEROUTPUT ON;
DECLARE
    total_equipment NUMBER;
BEGIN
    SELECT COUNT(*) INTO total_equipment FROM equipment;
    DBMS_OUTPUT.PUT_LINE('Total Equipment = ' || total_equipment);
END;
/


-- QUERY 4: IF ELSE
-- Check equipment availability.
DECLARE
    qty NUMBER;
BEGIN
    SELECT available_quantity INTO qty FROM equipment WHERE equipment_id = 'EQ017';
    IF qty > 0 THEN
        DBMS_OUTPUT.PUT_LINE('Equipment Available');
    ELSE
        DBMS_OUTPUT.PUT_LINE('Out of Stock');
    END IF;
END;
/


-- QUERY 5: IF ELSIF ELSE
-- Check booking status.
DECLARE
    booking_status booking_requests.status%TYPE;
BEGIN
    SELECT status INTO booking_status FROM booking_requests WHERE booking_id = 5001;
    IF booking_status = 'APPROVED' THEN
        DBMS_OUTPUT.PUT_LINE('Booking Approved');
    ELSIF booking_status = 'PENDING' THEN
        DBMS_OUTPUT.PUT_LINE('Booking Pending');
    ELSE
        DBMS_OUTPUT.PUT_LINE('Booking Rejected');
    END IF;
END;
/


-- QUERY 6: CASE
-- Display message based on borrow status.
DECLARE
    v_status borrow_records.borrow_status%TYPE;
BEGIN
    SELECT borrow_status INTO v_status FROM borrow_records WHERE borrow_id = 7004;
    CASE v_status
        WHEN 'BORROWED' THEN
            DBMS_OUTPUT.PUT_LINE('Item Currently Borrowed');
        WHEN 'RETURNED' THEN
            DBMS_OUTPUT.PUT_LINE('Item Returned');
        WHEN 'OVERDUE' THEN
            DBMS_OUTPUT.PUT_LINE('Late Return Detected');
        ELSE
            DBMS_OUTPUT.PUT_LINE('Unknown Status');
    END CASE;
END;
/


-- QUERY 7: FOR LOOP
-- Display first five equipment IDs.
BEGIN
    FOR rec IN (SELECT equipment_id, equipment_name FROM equipment WHERE ROWNUM <= 5)
    LOOP
        DBMS_OUTPUT.PUT_LINE(rec.equipment_id || ' - ' || rec.equipment_name);
    END LOOP;
END;
/


-- QUERY 8: WHILE LOOP
-- Print numbers from 1 to 5.
DECLARE
    counter NUMBER := 1;
BEGIN
    WHILE counter <= 5
    LOOP
        DBMS_OUTPUT.PUT_LINE('Counter = ' || counter);
        counter := counter + 1;
    END LOOP;
END;
/


-- QUERY 9: SIMPLE LOOP
-- Print numbers from 1 to 5.
DECLARE
    num NUMBER := 1;
BEGIN
    LOOP
        DBMS_OUTPUT.PUT_LINE('Number = ' || num);
        num := num + 1;
        EXIT WHEN num > 5;
    END LOOP;
END;
/


-- QUERY 10: PL/SQL CALCULATION
-- Calculate total unpaid fine amount.
DECLARE
    total_fine NUMBER;
BEGIN
    SELECT SUM(amount) INTO total_fine FROM fines WHERE payment_status = 'UNPAID';
    DBMS_OUTPUT.PUT_LINE('Total Unpaid Fine = ' || total_fine);
END;
/