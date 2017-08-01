USE calendar;
DELIMITER //

DROP PROCEDURE IF EXISTS sp_login;

CREATE PROCEDURE sp_login(
  IN un VARCHAR(50),
  IN pwd VARCHAR(50)
)BEGIN
SELECT * FROM admin WHERE un = userName AND pwd = password;
END;


DROP PROCEDURE IF EXISTS sp_addEvent;

CREATE PROCEDURE sp_addEvent(
IN title VARCHAR(250),
IN description TEXT,
IN url VARCHAR(250),
IN active BIT,
IN location VARCHAR(250)
)BEGIN
 INSERT INTO events(title, description, url, active, location) VALUES (
   title,
   description,
   url,
   active,
   location
 );
 SELECT LAST_INSERT_ID() AS eventID;
END;

DROP PROCEDURE IF EXISTS sp_addTime;
CREATE PROCEDURE sp_addTime(
  IN eventID INT(11),
  IN eventTime DATETIME
)BEGIN
  INSERT INTO times(eventID, eventTime) VALUES (eventID, eventTime);
END;

DROP PROCEDURE IF EXISTS sp_getEvents;
CREATE PROCEDURE sp_getEvents(
  IN month VARCHAR(2),
  IN year CHAR(4)
)BEGIN
  SELECT eventTime, title, description, url, location, active
  FROM times JOIN events ON times.eventID = events.eventID
    WHERE MONTH(times.eventTime) = month AND YEAR(times.eventTime) = year;
END;

DROP PROCEDURE IF EXISTS sp_getAllEvents;
CREATE PROCEDURE sp_getAllEvents(
)BEGIN
  SELECT eventTime, title, description, url, location, active, events.eventID
  FROM times JOIN events ON times.eventID = events.eventID
  ORDER BY times.eventTime DESC;
END;

DROP PROCEDURE IF EXISTS sp_updateEvent;
CREATE PROCEDURE sp_updateEvent(
  IN title VARCHAR(250),
  IN description TEXT,
  IN url VARCHAR(250),
  IN location VARCHAR(250),
  IN active BIT,
  IN eventIDs INT(11)
)BEGIN
  UPDATE events
  SET title = title,
  description = description,
  url = url,
  location = location,
  active = active
  WHERE eventID = eventIDs;
END;

DROP PROCEDURE IF EXISTS sp_updateTime;
CREATE PROCEDURE sp_updateTime(
  IN eventTimes DATETIME,
  IN eventIDs INT(11)
)BEGIN
  UPDATE times
  SET eventTime = eventTimes
  WHERE eventID = eventIDs;
END;

DROP PROCEDURE IF EXISTS sp_deleteEvent;
CREATE PROCEDURE sp_deleteEvent(
  IN eventIDs INT(11)
)BEGIN
DELETE FROM events
WHERE eventID = eventIDs;
END;

DROP PROCEDURE IF EXISTS sp_getActive;
CREATE PROCEDURE sp_getActive(
  IN year CHAR(4),
  IN month VARCHAR(2),
  IN day VARCHAR(2)
)BEGIN
SELECT eventTime, title, description, url, location
FROM times JOIN events ON times.eventID = events.eventID
WHERE YEAR(times.eventTime) = year AND
MONTH(times.eventTime) = month AND
DAY(times.eventTime) = day AND events.active = 1
ORDER BY times.eventTime ASC;
END;

DROP PROCEDURE IF EXISTS sp_getEventForUpdate;
CREATE PROCEDURE sp_getEventForUpdate(
  IN eventIDs INT(11)
)BEGIN
SELECT title, description, url, location, active, eventTime, events.eventID
FROM times JOIN events ON times.eventID = events.eventID
WHERE events.eventID = eventIDs;
END;
//
DELIMITER ;
