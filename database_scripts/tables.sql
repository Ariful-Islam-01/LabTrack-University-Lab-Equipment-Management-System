CREATE TABLE users (
    user_id NUMBER(10) PRIMARY KEY,
    full_name VARCHAR2(100) NOT NULL,
    email VARCHAR2(100) UNIQUE NOT NULL,
    password VARCHAR2(255) NOT NULL,
    role VARCHAR2(20) NOT NULL,
    department VARCHAR2(50),
    
    CONSTRAINT check_user_role CHECK(role IN ('STUDENT','TEACHER','LAB_ASSISTANT'))
);

CREATE TABLE labs (
    lab_id VARCHAR2(10) PRIMARY KEY,
    lab_name VARCHAR2(100) NOT NULL,
    room_no VARCHAR2(20) NOT NULL
);

CREATE TABLE categories (
    category_id VARCHAR2(10) PRIMARY KEY,
    category_name VARCHAR2(100) UNIQUE NOT NULL
);

CREATE TABLE equipment (
    equipment_id VARCHAR2(10) PRIMARY KEY,
    equipment_name VARCHAR2(150) NOT NULL,
    category_id VARCHAR2(10) NOT NULL,
    lab_id VARCHAR2(10) NOT NULL,
    total_quantity NUMBER(10) NOT NULL,
    available_quantity NUMBER(10) NOT NULL,
    status VARCHAR2(30) DEFAULT 'AVAILABLE',
    purchase_date DATE,

    CONSTRAINT fk_equipment_category FOREIGN KEY(category_id) REFERENCES categories(category_id),
    CONSTRAINT fk_equipment_lab FOREIGN KEY(lab_id) REFERENCES labs(lab_id),
    CONSTRAINT check_quantity CHECK(total_quantity >= 0),
    CONSTRAINT check_available CHECK(available_quantity >= 0 AND available_quantity <= total_quantity),
    CONSTRAINT check_equipment_status CHECK(status IN ('AVAILABLE', 'OUT_OF_STOCK', 'UNDER_MAINTENANCE'))
);

CREATE TABLE booking_requests (
    booking_id NUMBER(10) PRIMARY KEY,
    user_id NUMBER(10) NOT NULL,
    equipment_id VARCHAR2(10) NOT NULL,
    quantity NUMBER(10) DEFAULT 1 NOT NULL,
    request_date DATE DEFAULT SYSDATE,
    status VARCHAR2(20) DEFAULT 'PENDING',
    approved_by NUMBER(10),
    approval_date DATE,
    remarks VARCHAR2(255),

    CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_booking_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id),
    CONSTRAINT fk_booking_approver FOREIGN KEY (approved_by) REFERENCES users(user_id),
    CONSTRAINT check_booking_status CHECK (status IN ('PENDING', 'APPROVED', 'REJECTED')),
    CONSTRAINT check_booking_quantity CHECK (quantity > 0)
);

CREATE TABLE borrow_records (
    borrow_id NUMBER(10) PRIMARY KEY,
    booking_id NUMBER(10) UNIQUE NOT NULL,
    user_id NUMBER(10) NOT NULL,
    equipment_id VARCHAR2(10) NOT NULL,
    quantity NUMBER(10) NOT NULL,
    borrow_date DATE DEFAULT SYSDATE,
    expected_return_date DATE NOT NULL,
    actual_return_date DATE,
    borrow_status VARCHAR2(20) DEFAULT 'BORROWED',

    CONSTRAINT fk_borrow_booking FOREIGN KEY (booking_id) REFERENCES booking_requests(booking_id),
    CONSTRAINT fk_borrow_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_borrow_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id),
    CONSTRAINT check_borrow_status CHECK (borrow_status IN ('BORROWED', 'RETURNED', 'OVERDUE')),
    CONSTRAINT check_borrow_quantity CHECK (quantity > 0)
);

CREATE TABLE damage_reports (
    damage_id NUMBER(10) PRIMARY KEY,
    borrow_id NUMBER(10) NOT NULL,
    description VARCHAR2(500),
    fine_amount NUMBER(10,2),
    report_date DATE DEFAULT SYSDATE,

    CONSTRAINT fk_damage_borrow FOREIGN KEY(borrow_id) REFERENCES borrow_records(borrow_id),
    CONSTRAINT check_damage_amount CHECK (fine_amount >= 0)
);

CREATE TABLE fines (
    fine_id NUMBER(10) PRIMARY KEY,
    borrow_id NUMBER(10) NOT NULL,
    amount NUMBER(10,2),
    reason VARCHAR2(255),
    payment_status VARCHAR2(20) DEFAULT 'UNPAID',

    CONSTRAINT fk_fine_borrow FOREIGN KEY(borrow_id) REFERENCES borrow_records(borrow_id),
    CONSTRAINT check_payment_status CHECK (payment_status IN ('PAID', 'UNPAID')),
    CONSTRAINT check_fine_amount CHECK (amount >= 0)
);

CREATE TABLE equipment_logs (
    log_id NUMBER(10) PRIMARY KEY,
    equipment_id VARCHAR2(10) NOT NULL,
    user_id NUMBER(10),
    action_type VARCHAR2(30) NOT NULL,
    quantity NUMBER(10),
    action_date DATE DEFAULT SYSDATE,
    remarks VARCHAR2(255),

    CONSTRAINT fk_log_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id),
    CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT check_action_type CHECK (action_type IN ('BORROW', 'RETURN', 'ADD_STOCK', 'REMOVE_STOCK', 'DAMAGE_REPORTED')),
    CONSTRAINT check_log_quantity CHECK (quantity >= 0)
);