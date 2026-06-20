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