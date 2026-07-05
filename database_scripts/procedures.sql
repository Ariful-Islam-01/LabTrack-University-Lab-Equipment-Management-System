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


-- PROCEDURE 6: Update Equipment
-- Update equipment details and ensure total quantity is not less than borrowed quantity.

CREATE OR REPLACE PROCEDURE update_equipment(
    p_equipment_id VARCHAR2,
    p_equipment_name VARCHAR2,
    p_category_id VARCHAR2,
    p_lab_id VARCHAR2,
    p_total_quantity NUMBER
)
AS
    v_total_quantity NUMBER;
    v_available_quantity NUMBER;
    v_borrowed_quantity NUMBER;
BEGIN
    -- 1. Read current total_quantity and available_quantity
    SELECT total_quantity, available_quantity
    INTO v_total_quantity, v_available_quantity
    FROM equipment
    WHERE equipment_id = p_equipment_id;

    -- 2. Calculate borrowed_quantity
    v_borrowed_quantity := v_total_quantity - v_available_quantity;

    -- 3. Check if total quantity is less than borrowed quantity
    IF p_total_quantity < v_borrowed_quantity THEN
        raise_application_error(
            -20001,
            'Total quantity cannot be less than borrowed quantity.'
        );
    END IF;

    -- 4. Update the specified columns
    UPDATE equipment
    SET equipment_name = p_equipment_name,
        category_id = p_category_id,
        lab_id = p_lab_id,
        total_quantity = p_total_quantity
    WHERE equipment_id = p_equipment_id;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        raise_application_error(
            -20002,
            'Equipment not found.'
        );
END;
/


-- PROCEDURE 7: Delete Equipment
-- Delete an equipment if no related records exist.

CREATE OR REPLACE PROCEDURE delete_equipment(
    p_equipment_id VARCHAR2
)
AS
    v_exists NUMBER;
BEGIN
    -- 1. Check whether the equipment exists.
    SELECT COUNT(*)
    INTO v_exists
    FROM equipment
    WHERE equipment_id = p_equipment_id;

    IF v_exists = 0 THEN
        raise_application_error(
            -20003,
            'Equipment not found.'
        );
    END IF;

    -- 2. Check if any record exists in booking_requests using equipment_id
    SELECT COUNT(*)
    INTO v_exists
    FROM booking_requests
    WHERE equipment_id = p_equipment_id;

    IF v_exists > 0 THEN
        raise_application_error(
            -20004,
            'Cannot delete equipment because booking records exist.'
        );
    END IF;

    -- 3. Check if any record exists in borrow_records
    SELECT COUNT(*)
    INTO v_exists
    FROM borrow_records
    WHERE equipment_id = p_equipment_id;

    IF v_exists > 0 THEN
        raise_application_error(
            -20005,
            'Cannot delete equipment because borrow records exist.'
        );
    END IF;

    -- 4. Check if any record exists in equipment_logs
    SELECT COUNT(*)
    INTO v_exists
    FROM equipment_logs
    WHERE equipment_id = p_equipment_id;

    IF v_exists > 0 THEN
        raise_application_error(
            -20006,
            'Cannot delete equipment because equipment logs exist.'
        );
    END IF;

    -- 5. Otherwise
    DELETE FROM equipment
    WHERE equipment_id = p_equipment_id;
END;
/


-- PROCEDURE 8: Add Booking Request
-- Insert a new booking request after verifying equipment availability and preventing duplicates.

CREATE OR REPLACE PROCEDURE add_booking_request(
    p_user_id NUMBER,
    p_equipment_id VARCHAR2,
    p_quantity NUMBER
)
AS
    v_booking_id NUMBER;
    v_available_qty NUMBER;
    v_status VARCHAR2(50);
    v_pending_count NUMBER;
BEGIN
    -- 1. Verify that the equipment exists and retrieve its details.
    BEGIN
        SELECT available_quantity, status
        INTO v_available_qty, v_status
        FROM equipment
        WHERE equipment_id = p_equipment_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            raise_application_error(
                -20010,
                'Equipment not found.'
            );
    END;

    -- 2. Allow booking only when equipment status is 'AVAILABLE'.
    IF v_status <> 'AVAILABLE' THEN
        raise_application_error(
            -20011,
            'Equipment is not available for booking.'
        );
    END IF;

    -- 3. Verify requested quantity does not exceed available quantity.
    IF p_quantity > v_available_qty THEN
        raise_application_error(
            -20012,
            'Requested quantity exceeds available quantity.'
        );
    END IF;

    -- 4. Prevent duplicate pending request for the same user and equipment.
    SELECT COUNT(*)
    INTO v_pending_count
    FROM booking_requests
    WHERE user_id = p_user_id
      AND equipment_id = p_equipment_id
      AND status = 'PENDING';

    IF v_pending_count > 0 THEN
        raise_application_error(
            -20013,
            'You already have a pending request for this equipment.'
        );
    END IF;

    -- 5. Generate booking_id using MAX + 1.
    SELECT NVL(MAX(booking_id), 0) + 1
    INTO v_booking_id
    FROM booking_requests;

    -- 6. Insert the new booking request record.
    INSERT INTO booking_requests (
        booking_id,
        user_id,
        equipment_id,
        quantity,
        request_date,
        status,
        approved_by,
        approval_date,
        remarks
    ) VALUES (
        v_booking_id,
        p_user_id,
        p_equipment_id,
        p_quantity,
        SYSDATE,
        'PENDING',
        NULL,
        NULL,
        NULL
    );
END;
/

