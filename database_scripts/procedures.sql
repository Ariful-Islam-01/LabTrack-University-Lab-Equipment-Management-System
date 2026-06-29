-- ============================================
-- PROCEDURES
-- ============================================


-- PROCEDURE 1: APPROVE REQUEST
-- Approve a booking request.

CREATE OR REPLACE PROCEDURE approve_request(
    p_booking_id NUMBER,
    p_approved_by NUMBER
)
AS
BEGIN
    UPDATE booking_requests SET status = 'APPROVED',
    approved_by = p_approved_by,
    approval_date = SYSDATE
    WHERE booking_id = p_booking_id;
COMMIT;
END;
/
-- Execute
EXEC approve_request(5006,3007001);


-- PROCEDURE 2: BORROW EQUIPMENT
/*-- Create borrow record and reduce stock.

CREATE OR REPLACE PROCEDURE borrow_equipment(
    p_borrow_id NUMBER,
    p_booking_id NUMBER,
    p_user_id NUMBER,
    p_equipment_id VARCHAR2,
    p_quantity NUMBER
)
AS
BEGIN
    INSERT INTO borrow_records(
        borrow_id,
        booking_id,
        user_id,
        equipment_id,
        quantity,
        borrow_date,
        expected_return_date,
        borrow_status
    )
    VALUES(
        p_borrow_id,
        p_booking_id,
        p_user_id,
        p_equipment_id,
        p_quantity,
        SYSDATE,
        SYSDATE + 7,
        'BORROWED'
    );

    UPDATE equipment
    SET available_quantity = available_quantity - p_quantity
    WHERE equipment_id = p_equipment_id;
COMMIT;
END;
/
*/

-- Create borrow record

CREATE OR REPLACE PROCEDURE borrow_equipment(
    p_borrow_id NUMBER,
    p_booking_id NUMBER,
    p_user_id NUMBER,
    p_equipment_id VARCHAR2,
    p_quantity NUMBER
)
AS
    v_stock NUMBER;

BEGIN
    v_stock := get_available_stock(p_equipment_id);

    IF v_stock < p_quantity THEN
        RAISE_APPLICATION_ERROR(
            -20001,
            'Insufficient stock available.'
        );
    END IF;

    INSERT INTO borrow_records
    (
        borrow_id,
        booking_id,
        user_id,
        equipment_id,
        quantity,
        borrow_date,
        expected_return_date,
        borrow_status
    )

    VALUES
    (
        p_borrow_id,
        p_booking_id,
        p_user_id,
        p_equipment_id,
        p_quantity,
        SYSDATE,
        SYSDATE+7,
        'BORROWED'
    );

    COMMIT;

END;
/

-- PROCEDURE 3: RETURN EQUIPMENT
/*-- Mark equipment as returned and update stock.

CREATE OR REPLACE PROCEDURE return_equipment(
    p_borrow_id NUMBER
)
AS
v_equipment_id VARCHAR2(10);
v_quantity NUMBER;
BEGIN
    SELECT equipment_id, quantity
    INTO v_equipment_id, v_quantity
    FROM borrow_records
    WHERE borrow_id = p_borrow_id;

    UPDATE borrow_records
    SET borrow_status = 'RETURNED',
    actual_return_date = SYSDATE
    WHERE borrow_id = p_borrow_id;

    UPDATE equipment
    SET available_quantity =
    available_quantity + v_quantity
    WHERE equipment_id = v_equipment_id;

COMMIT;
END;
/
-- Execute
EXEC return_equipment(7003);
*/
-- Mark equipment as returned

CREATE OR REPLACE PROCEDURE return_equipment(
    p_borrow_id NUMBER
)
AS

BEGIN
    UPDATE borrow_records
    SET borrow_status='RETURNED', actual_return_date=SYSDATE
    WHERE borrow_id=p_borrow_id;

    COMMIT;
END;
/


-- PROCEDURE 4: ADD EQUIPMENT
-- Insert a new equipment.

CREATE OR REPLACE PROCEDURE add_equipment(
    p_equipment_id VARCHAR2,
    p_equipment_name VARCHAR2,
    p_category_id VARCHAR2,
    p_lab_id VARCHAR2,
    p_quantity NUMBER
)
AS
BEGIN
    INSERT INTO equipment
    VALUES(
        p_equipment_id,
        p_equipment_name,
        p_category_id,
        p_lab_id,
        p_quantity,
        p_quantity,
        'AVAILABLE',
        SYSDATE
    );

COMMIT;
END;
/
-- Execute
EXEC add_equipment('EQ021','NodeMCU','CAT05','LAB05',10);


-- PROCEDURE 5: GENERATE FINE
/*-- Create a fine record.

CREATE OR REPLACE PROCEDURE generate_fine(
    p_fine_id NUMBER,
    p_borrow_id NUMBER,
    p_amount NUMBER,
    p_reason VARCHAR2
)
AS
BEGIN
    INSERT INTO fines(
        fine_id,
        borrow_id,
        amount,
        reason,
        payment_status
    )
    VALUES(
        p_fine_id,
        p_borrow_id,
        p_amount,
        p_reason,
        'UNPAID'
    );

COMMIT;
END;
/
-- Execute
EXEC generate_fine(8005,7003,200,'Late Return');
/
*/
-- Create a fine record automatically based on overdue days.

CREATE OR REPLACE PROCEDURE generate_fine(
    p_fine_id NUMBER,
    p_borrow_id NUMBER
)

AS
    v_expected DATE;
    v_actual DATE;
    v_amount NUMBER;

BEGIN
    SELECT
        expected_return_date,
        actual_return_date
    INTO
        v_expected,
        v_actual
    FROM borrow_records
    WHERE borrow_id=p_borrow_id;

    v_amount := calculate_fine(
        v_expected,
        v_actual
    );

    INSERT INTO fines
    VALUES
    (
        p_fine_id,
        p_borrow_id,
        v_amount,
        'Late Return',
        'UNPAID'
    );

    COMMIT;

END;
/
