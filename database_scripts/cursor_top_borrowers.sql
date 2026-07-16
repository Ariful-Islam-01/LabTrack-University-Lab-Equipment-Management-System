-- ======================================================
-- PHASE: ADVANCED DATABASE REPORTS
-- VIEW & CURSOR PL/SQL DEMONSTRATION
-- ======================================================

-- PART 1: CREATE OR REPLACE VIEW
-- Joins borrow_records, users, equipment, and categories.
CREATE OR REPLACE VIEW vw_borrow_details AS
SELECT 
    b.borrow_id,
    u.full_name AS student_name,
    e.equipment_name,
    c.category_name AS category,
    b.quantity,
    b.borrow_date,
    b.expected_return_date,
    b.actual_return_date,
    b.borrow_status
FROM borrow_records b
JOIN users u ON b.user_id = u.user_id
JOIN equipment e ON b.equipment_id = e.equipment_id
JOIN categories c ON e.category_id = c.category_id;

-- Verification query
SELECT * FROM vw_borrow_details;


-- PART 2: PL/SQL CURSOR DEMONSTRATION (TOP BORROWERS)
-- Note: This is a standalone PL/SQL script for execution in SQL*Plus / SQL Developer.
SET SERVEROUTPUT ON;

DECLARE
    -- Define the CURSOR to fetch top borrowers based on borrow record count
    CURSOR c_top_borrowers IS
        SELECT 
            u.user_id, 
            u.full_name, 
            COUNT(b.borrow_id) AS borrow_count
        FROM users u
        JOIN borrow_records b ON u.user_id = b.user_id
        GROUP BY u.user_id, u.full_name
        ORDER BY borrow_count DESC;
        
    -- Variable declarations
    v_user_id      users.user_id%TYPE;
    v_full_name    users.full_name%TYPE;
    v_borrow_count NUMBER;
    v_rank         NUMBER := 1;
BEGIN
    DBMS_OUTPUT.PUT_LINE('------------------------------------------------------------');
    DBMS_OUTPUT.PUT_LINE('            UNIVERSITY LAB SYSTEM - TOP BORROWERS           ');
    DBMS_OUTPUT.PUT_LINE('------------------------------------------------------------');
    
    OPEN c_top_borrowers;
    LOOP
        FETCH c_top_borrowers INTO v_user_id, v_full_name, v_borrow_count;
        EXIT WHEN c_top_borrowers%NOTFOUND;
        
        DBMS_OUTPUT.PUT_LINE('Rank #' || v_rank || ' | Student ID: ' || v_user_id || ' | Name: ' || v_full_name || ' | Borrows Count: ' || v_borrow_count);
        v_rank := v_rank + 1;
    END LOOP;
    CLOSE c_top_borrowers;
    
    DBMS_OUTPUT.PUT_LINE('------------------------------------------------------------');
END;
/
