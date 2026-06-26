-- ============================================
-- FUNCTIONS
-- ============================================


-- FUNCTION 1: GET AVAILABLE STOCK
-- Returns the available stock of an equipment.

CREATE OR REPLACE FUNCTION get_available_stock(p_equipment_id VARCHAR2) RETURN NUMBER AS v_stock NUMBER;
BEGIN
    SELECT available_quantity INTO v_stock FROM equipment WHERE equipment_id = p_equipment_id;
    RETURN v_stock;
END;
/

-- Execute
SELECT get_available_stock('EQ017') AS available_stock FROM dual;


-- FUNCTION 2: CALCULATE FINE
-- Returns fine amount based on overdue days.
-- Rule: 50 Taka per overdue day.

CREATE OR REPLACE FUNCTION calculate_fine(p_expected_date DATE, p_actual_date DATE) RETURN NUMBER AS v_days NUMBER;
BEGIN
    v_days := p_actual_date - p_expected_date;
    IF v_days > 0 THEN
        RETURN v_days * 50;
    ELSE
        RETURN 0;
    END IF;
END;
/

-- Execute
SELECT calculate_fine(DATE '2025-06-01', DATE '2025-06-06') AS fine_amount FROM dual;


-- FUNCTION 3: GET BORROW COUNT
-- Returns total borrowed records of a user.

CREATE OR REPLACE FUNCTION get_borrow_count(p_user_id NUMBER) RETURN NUMBER AS v_count NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM borrow_records WHERE user_id = p_user_id;
    RETURN v_count;
END;
/

-- Execute

SELECT get_borrow_count(2207046) AS total_borrow FROM dual;



-- ============================================

-- EXAMPLE: USE FUNCTION IN A QUERY

SELECT equipment_id, equipment_name, get_available_stock(equipment_id) AS available_stock FROM equipment;


-- EXAMPLE: CHECK BORROW COUNT OF ALL USERS

SELECT user_id, full_name, get_borrow_count(user_id) AS borrow_count FROM users WHERE role = 'STUDENT';
