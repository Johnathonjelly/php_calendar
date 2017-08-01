-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 30, 2017 at 11:57 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `calendar`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_addEvent` (IN `title` VARCHAR(250), IN `description` TEXT, IN `url` VARCHAR(250), IN `active` BIT, IN `location` VARCHAR(250))  BEGIN
 INSERT INTO events(title, description, url, active, location) VALUES (
   title,
   description,
   url,
   active,
   location
 );
 SELECT LAST_INSERT_ID() AS eventID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_addTime` (IN `eventID` INT(11), IN `eventTime` DATETIME)  BEGIN
  INSERT INTO times(eventID, eventTime) VALUES (eventID, eventTime);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_deleteEvent` (IN `eventIDs` INT(11))  BEGIN
DELETE FROM events
WHERE eventID = eventIDs;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getActive` (IN `year` CHAR(4), IN `month` VARCHAR(2), IN `day` VARCHAR(2))  BEGIN
SELECT eventTime, title, description, url, location
FROM times JOIN events ON times.eventID = events.eventID
WHERE YEAR(times.eventTime) = year AND
MONTH(times.eventTime) = month AND
DAY(times.eventTime) = day AND events.active = 1
ORDER BY times.eventTime ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getAllEvents` ()  BEGIN
  SELECT eventTime, title, description, url, location, active, events.eventID
  FROM times JOIN events ON times.eventID = events.eventID
  ORDER BY times.eventTime DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getEventForUpdate` (IN `eventIDs` INT(11))  BEGIN
SELECT title, description, url, location, active, eventTime, events.eventID
FROM times JOIN events ON times.eventID = events.eventID
WHERE events.eventID = eventIDs;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_getEvents` (IN `month` VARCHAR(2), IN `year` CHAR(4))  BEGIN
  SELECT eventTime, title, description, url, location, active
  FROM times JOIN events ON times.eventID = events.eventID
    WHERE MONTH(times.eventTime) = month AND YEAR(times.eventTime) = year;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login` (IN `un` VARCHAR(50), IN `pwd` VARCHAR(50))  BEGIN
SELECT * FROM admin WHERE un = userName AND pwd = password;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_updateEvent` (IN `title` VARCHAR(250), IN `description` TEXT, IN `url` VARCHAR(250), IN `location` VARCHAR(250), IN `active` BIT, IN `eventIDs` INT(11))  BEGIN
  UPDATE events
  SET title = title,
  description = description,
  url = url,
  location = location,
  active = active
  WHERE eventID = eventIDs;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_updateTime` (IN `eventTimes` DATETIME, IN `eventIDs` INT(11))  BEGIN
  UPDATE times
  SET eventTime = eventTimes
  WHERE eventID = eventIDs;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL,
  `userName` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `userName`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(250) DEFAULT NULL,
  `location` varchar(250) DEFAULT NULL,
  `active` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `title`, `description`, `url`, `location`, `active`) VALUES
(14, 'update from sp', 'update from sp', 'update from sp', 'update from sp', b'0'),
(18, 'event 1', '<p>event event</p>', 'url event', 'location event 1', b'1'),
(19, 'event 2', '<p>event 2 event 2</p>', 'event url 2', 'event location2', b'1'),
(20, 'ASDFA', '<p>UPDATED FROM WEBPAGE</p>', 'ASDF', '', b'1'),
(21, 'event 4', '<p>eventevner;klasdf</p>', '', '', b'1'),
(22, 'event5', '<p>event5lkfds</p>', '', '', b'1'),
(23, 'event 19', '<p>event 19ls;dfkj</p>', '', '', b'1');

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

CREATE TABLE `times` (
  `timeID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL,
  `eventTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `times`
--

INSERT INTO `times` (`timeID`, `eventID`, `eventTime`) VALUES
(19, 14, '2017-05-03 09:22:00'),
(25, 18, '2017-05-23 20:15:00'),
(26, 18, '2017-05-24 23:15:00'),
(27, 19, '2017-05-09 21:25:00'),
(28, 20, '2017-05-10 23:45:00'),
(29, 21, '2017-05-04 15:15:00'),
(30, 22, '2017-05-05 13:10:00'),
(31, 23, '2017-05-19 12:55:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `times`
--
ALTER TABLE `times`
  ADD PRIMARY KEY (`timeID`),
  ADD KEY `eventID` (`eventID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `times`
--
ALTER TABLE `times`
  MODIFY `timeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `times`
--
ALTER TABLE `times`
  ADD CONSTRAINT `times_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
