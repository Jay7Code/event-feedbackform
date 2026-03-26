UPDATE ef_events SET event_name = REPLACE(event_name, '&#039;', '''');
UPDATE ef_attendees SET attendee_name = REPLACE(attendee_name, '&#039;', '''');
UPDATE ef_attendees SET email = REPLACE(email, '&#039;', '''');
UPDATE ef_attendees SET contact_no = REPLACE(contact_no, '&#039;', '''');
UPDATE ef_event_feedbacks SET effective_aspects = REPLACE(effective_aspects, '&#039;', '''');
UPDATE ef_event_feedbacks SET improvement_suggestions = REPLACE(improvement_suggestions, '&#039;', '''');
UPDATE ef_event_feedbacks SET participate_future = REPLACE(participate_future, '&#039;', '''');
UPDATE ef_event_feedbacks SET additional_feedback = REPLACE(additional_feedback, '&#039;', '''');

-- Merge Duplicate locations for St. Patrick's
UPDATE ef_events e
JOIN ef_locations bad ON e.location_id = bad.id
JOIN ef_locations good ON good.location_name = 'St. Patrick''s'
SET e.location_id = good.id
WHERE bad.location_name = 'St. Patrick&#039;s';

DELETE FROM ef_locations WHERE location_name = 'St. Patrick&#039;s';

-- Update any other locations that have quotes encoded
UPDATE IGNORE ef_locations SET location_name = REPLACE(location_name, '&#039;', '''') WHERE location_name LIKE '%&#039;%';
