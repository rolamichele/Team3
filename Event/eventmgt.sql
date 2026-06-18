-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 02:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventmgt`
--

-- --------------------------------------------------------

--
-- Table structure for table `availabletimeslots`
--

CREATE TABLE `availabletimeslots` (
  `TimeSlotID` int(11) NOT NULL,
  `VendorID` int(11) NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Status` enum('Available','Unavailable') DEFAULT 'Available',
  `Day` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `availabletimeslots`
--

INSERT INTO `availabletimeslots` (`TimeSlotID`, `VendorID`, `StartTime`, `EndTime`, `Status`, `Day`) VALUES
(1, 1, '13:15:16', '23:15:16', 'Available', 'Friday'),
(2, 1, '00:00:00', '00:00:00', 'Available', 'Saturday'),
(4, 2, '13:28:55', '16:28:55', 'Available', 'Saturday'),
(5, 3, '09:00:00', '12:00:00', 'Available', 'Sunday'),
(6, 3, '13:00:00', '17:00:00', 'Available', 'Monday');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `Name`, `Description`) VALUES
(1, 'Decoration', 'Event decoration services including flowers, lighting, and setup'),
(2, 'Catering', 'Food and beverage services for events and parties'),
(3, 'Sound & Lighting', 'Audio systems, speakers, microphones, and lighting setups'),
(4, 'Photography', 'Professional photography and videography for events'),
(5, 'Venues', 'Halls, gardens, and locations for hosting events'),
(6, 'Entertainment', 'DJ, bands, and live entertainment services'),
(7, 'Security', 'Event security and crowd management services');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `ItemID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `VendorName` varchar(255) DEFAULT NULL,
  `Price` decimal(10,0) DEFAULT NULL,
  `OrderID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `VendorID` int(11) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `City` varchar(255) DEFAULT NULL,
  `Governorate` varchar(255) DEFAULT NULL,
  `Country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`VendorID`, `Address`, `City`, `Governorate`, `Country`) VALUES
(1, '12 Tahrir Street', 'Cairo', 'Cairo', 'Egypt'),
(1, '5 Nile Corniche', 'Giza', 'Giza', 'Egypt'),
(2, '18 Ramses Street', 'Cairo', 'Cairo', 'Egypt'),
(3, 'Street 10', 'Nasr City', 'Cairo', 'Egypt'),
(3, 'Street 20', 'Dokki', 'Giza', 'Egypt');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `VendorID` int(11) NOT NULL,
  `Status` enum('Pending','Cancelled','Completed') DEFAULT 'Pending',
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `PackageID` int(11) NOT NULL,
  `VendorID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,0) NOT NULL,
  `ActivityStatus` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `VendorID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `Rate` enum('0','1','2','3','4','5') DEFAULT '0',
  `Comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(255) DEFAULT NULL,
  `Role` enum('Client','Admin') DEFAULT 'Client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `VendorID` int(11) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `ActivityStatus` enum('Active','Inactive') DEFAULT 'Active',
  `Role` enum('Vendor') DEFAULT 'Vendor',
  `AvgRate` decimal(3,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`VendorID`, `CategoryID`, `Name`, `Email`, `Password`, `PhoneNumber`, `Description`, `ActivityStatus`, `Role`, `AvgRate`) VALUES
(1, 2, 'Alpha Catering', 'alpha@vendor.com', 'pass123', '01011111111', 'High quality catering services for events', 'Active', 'Vendor', 0.00),
(2, 1, 'Bright Decor', 'bright@vendor.com', 'pass123', '01022222222', 'Event decoration and setup services', 'Active', 'Vendor', 0.00),
(3, 1, 'Vendor Test', 'sound@vendor.com', 'pass123', '01033333333', 'Test Description', 'Inactive', 'Vendor', 0.00),
(4, 2, 'Golden Venue', 'golden@vendor.com', 'pass123', '01044444444', 'Luxury venues for weddings and parties', 'Active', 'Vendor', 0.00),
(5, 4, 'Quick Photography', 'photo@vendor.com', 'pass123', '01055555555', 'Event photography and videography services', 'Active', 'Vendor', 0.00),
(6, 1, 'Mariam', 'mariam@gmail.com', '$2y$10$B04.qSsYoD/pm1CO.BwZ4eQoIdwPJRG9MBtWFvA/gUcXywThsQMT2', '01012345678', 'Event organizer and planner', 'Inactive', 'Vendor', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `availabletimeslots`
--
ALTER TABLE `availabletimeslots`
  ADD PRIMARY KEY (`TimeSlotID`),
  ADD KEY `fk_availabletimeslots_vendor` (`VendorID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `FK_Items_Orders` (`OrderID`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`VendorID`,`Address`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `VendorID` (`VendorID`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`PackageID`),
  ADD KEY `VendorID` (`VendorID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD UNIQUE KEY `unique_review` (`UserID`,`VendorID`),
  ADD KEY `VendorID` (`VendorID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`VendorID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `availabletimeslots`
--
ALTER TABLE `availabletimeslots`
  MODIFY `TimeSlotID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `PackageID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `VendorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `availabletimeslots`
--
ALTER TABLE `availabletimeslots`
  ADD CONSTRAINT `fk_availabletimeslots_vendor` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`VendorID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `FK_Items_Orders` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE;

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`VendorID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`VendorID`) ON DELETE CASCADE;

--
-- Constraints for table `packages`
--
ALTER TABLE `packages`
  ADD CONSTRAINT `packages_ibfk_1` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`VendorID`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`VendorID`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`);

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
