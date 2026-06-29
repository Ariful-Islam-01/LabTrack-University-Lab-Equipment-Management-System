-- ============================================
-- TRIGGERS
-- ============================================


-- TRIGGER 1: DECREASE STOCK ON BORROW
-- Automatically decrease available stock when a borrow record is inserted.

CREATE OR REPLACE TRIGGER trg_decrease_stock
AFTER INSERT ON borrow_records
FOR EACH ROW
BEGIN
    UPDATE equipment SET available_quantity = available_quantity - :NEW.quantity
    WHERE equipment_id = :NEW.equipment_id;
END;
/


-- TRIGGER 2: INCREASE STOCK ON RETURN
-- Automatically increase available stock when equipment is returned.

CREATE OR REPLACE TRIGGER trg_increase_stock
AFTER UPDATE OF borrow_status ON borrow_records
FOR EACH ROW
WHEN (NEW.borrow_status = 'RETURNED')
BEGIN
    UPDATE equipment SET available_quantity = available_quantity + :NEW.quantity
    WHERE equipment_id = :NEW.equipment_id;
END;
/


-- TRIGGER 3: LOG BORROW ACTION
-- Automatically insert borrow log.

CREATE OR REPLACE TRIGGER trg_log_borrow
AFTER INSERT ON borrow_records
FOR EACH ROW

DECLARE
    v_log_id NUMBER;
BEGIN
    SELECT NVL(MAX(log_id),0)+1 INTO v_log_id FROM equipment_logs;

    INSERT INTO equipment_logs
    VALUES
    (
        v_log_id,
        :NEW.equipment_id,
        :NEW.user_id,
        'BORROW',
        :NEW.quantity,
        SYSDATE,
        'Equipment Borrowed'
    );

END;
/


-- TRIGGER 4: LOG RETURN ACTION
-- Automatically insert return log.

CREATE OR REPLACE TRIGGER trg_log_return
AFTER UPDATE OF borrow_status ON borrow_records
FOR EACH ROW
WHEN (NEW.borrow_status='RETURNED')

DECLARE
    v_log_id NUMBER;

BEGIN

    SELECT NVL(MAX(log_id),0)+1 INTO v_log_id FROM equipment_logs;

    INSERT INTO equipment_logs
    VALUES
    (
        v_log_id,
        :NEW.equipment_id,
        :NEW.user_id,
        'RETURN',
        :NEW.quantity,
        SYSDATE,
        'Equipment Returned'
    );

END;
/


-- TEST 1
-- Borrow Equipment

INSERT INTO borrow_records VALUES(7010, 5006, 2207045, 'EQ019', 1, SYSDATE, SYSDATE+7, NULL, 'BORROWED');


-- TEST 2
-- Return Equipment

UPDATE borrow_records SET borrow_status='RETURNED', actual_return_date=SYSDATE WHERE borrow_id=7010;

COMMIT;
