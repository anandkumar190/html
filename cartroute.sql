-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2018 at 11:45 AM
-- Server version: 10.1.24-MariaDB
-- PHP Version: 7.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cartroute`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `id` int(11) NOT NULL,
  `acitivityname` varchar(100) DEFAULT '',
  `activitytype` varchar(100) DEFAULT NULL,
  `relatedto` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `activitydate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `id` int(11) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `latitude` decimal(18,10) DEFAULT NULL,
  `longitude` decimal(18,10) DEFAULT NULL,
  `km` decimal(10,2) DEFAULT NULL,
  `registrationdate` datetime DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attendence`
--

CREATE TABLE `attendence` (
  `id` int(11) NOT NULL,
  `empid` int(11) DEFAULT NULL,
  `dateofactivity` date DEFAULT NULL,
  `firstactivity` datetime DEFAULT NULL,
  `lastactivity` datetime DEFAULT NULL,
  `calculatedhours` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT '',
  `creationdate` datetime NOT NULL,
  `createdby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `designation`
--

INSERT INTO `designation` (`id`, `name`, `creationdate`, `createdby`) VALUES
(1, 'Manager', '2018-12-08 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT '',
  `modelno` varchar(100) DEFAULT '',
  `appversion` varchar(100) DEFAULT '',
  `osversion` varchar(100) DEFAULT '',
  `logindate` datetime DEFAULT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT '',
  `empid` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT '',
  `contact` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT '',
  `designationid` int(11) DEFAULT NULL,
  `roleid` int(11) DEFAULT NULL,
  `managerid` int(11) DEFAULT NULL,
  `usertype` int(11) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT '0.00',
  `commission` decimal(10,2) DEFAULT '0.00',
  `city` varchar(100) DEFAULT '',
  `state` varchar(100) DEFAULT '',
  `latitude` decimal(18,10) DEFAULT NULL,
  `longitude` decimal(18,10) DEFAULT NULL,
  `region` varchar(100) DEFAULT '',
  `doj` date DEFAULT NULL,
  `dol` date DEFAULT NULL,
  `reportsto` int(11) DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `stockistid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `image`, `name`, `empid`, `email`, `contact`, `address`, `designationid`, `roleid`, `managerid`, `usertype`, `password`, `salary`, `commission`, `city`, `state`, `latitude`, `longitude`, `region`, `doj`, `dol`, `reportsto`, `creationdate`, `createdby`, `lastlogin`, `stockistid`) VALUES
(1, '  ', 'Saurabh', 'EMP0001', 'tyagi1511@gmail.com', '8445523455', 'Meerut', 1, 1, NULL, 1, '*6BB4837EB74329105EE4568DDA7DC67ED2CA2AD9', '0.00', '0.00', '', '', NULL, NULL, '', '2018-12-08', NULL, NULL, '2018-12-08 00:00:00', NULL, NULL, NULL),
(3, 'EMP0002.jpg', 'Shashikant', 'EMP0002', 'tyagi151@gmail.com', '8273989630', 'Meerut', 1, 0, 0, 1, '*6BB4837EB74329105EE4568DDA7DC67ED2CA2AD9', '0.00', '0.00', '', '', NULL, NULL, '', '2018-12-08', NULL, 0, '0000-00-00 00:00:00', 2147483647, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `feedback` varchar(255) DEFAULT NULL,
  `outletid` int(11) DEFAULT NULL,
  `rating` decimal(1,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `productid` int(11) DEFAULT NULL,
  `distributorid` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `units` varchar(100) DEFAULT NULL,
  `orderdate` datetime DEFAULT NULL,
  `orderby` int(11) DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outlets`
--

CREATE TABLE `outlets` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT '',
  `address` varchar(255) DEFAULT NULL,
  `lastvisitpic` varchar(255) DEFAULT NULL,
  `contactperson` varchar(100) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `pincode` varchar(30) DEFAULT NULL,
  `gstnumber` varchar(100) DEFAULT NULL,
  `outlettype` varchar(100) DEFAULT NULL,
  `outletsubtype` varchar(100) DEFAULT NULL,
  `copetitor_presence` varchar(200) DEFAULT NULL,
  `distributorid` int(11) DEFAULT NULL,
  `routeid` int(11) DEFAULT NULL,
  `salesmanagerid` int(11) DEFAULT NULL,
  `rsmid` int(11) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `locality` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `latitude` decimal(18,10) DEFAULT NULL,
  `longitude` decimal(18,10) DEFAULT NULL,
  `areaid` int(11) DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT '',
  `creationdate` datetime NOT NULL,
  `createdby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `creationdate`, `createdby`) VALUES
(1, 'Area Manager', '2018-12-08 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `skus`
--

CREATE TABLE `skus` (
  `id` int(11) NOT NULL,
  `productname` varchar(100) DEFAULT '',
  `productid` varchar(100) NOT NULL,
  `unit` varchar(100) DEFAULT 'EACH',
  `mrp` decimal(10,2) DEFAULT '0.00',
  `rate` decimal(10,2) DEFAULT '0.00',
  `brandname` varchar(100) DEFAULT '',
  `quantity` decimal(10,2) DEFAULT '0.00',
  `image` varchar(200) NOT NULL DEFAULT '''''',
  `creationdate` datetime NOT NULL,
  `createdby` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `skus`
--

INSERT INTO `skus` (`id`, `productname`, `productid`, `unit`, `mrp`, `rate`, `brandname`, `quantity`, `image`, `creationdate`, `createdby`) VALUES
(1, 'kjfgkj', 'kdvgkj', '2', '65.00', '25.25', 'FRIC BERGEN', '0.00', 'kjfgkjkdvgkj.jpg', '2018-12-06 03:07:59', 0),
(2, 'djghjnmnbjn', 'jdhcsjx', '2', '0.00', '0.00', 'FRIC BERGEN', '0.00', 'djghjjdhcj.jpg', '2018-12-07 11:56:43', 0),
(4, 'djghj', 'jdhcjjghj', '1', '0.00', '0.00', 'FRIC BERGEN', '0.00', 'djghjjdhcjjghj.jpg', '2018-12-07 12:20:02', 0),
(6, 'kjvhk', 'jdhc5454', '1', '0.00', '0.00', 'FRIC BERGEN', '0.00', 'kjvhkjdhc5454.jpg', '2018-12-07 12:24:33', 0),
(7, 'kjvhk', 'hjghg852', '1', '0.00', '0.00', 'FRIC BERGEN', '0.00', 'kjvhkhjghg852.jpg', '2018-12-07 12:26:18', 0),
(8, 'kjvhk', 'jdhc528', '1', '0.00', '0.00', 'FRIC BERGEN', '0.00', 'kjvhkjdhc528.jpg', '2018-12-07 12:27:11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `usertypes`
--

CREATE TABLE `usertypes` (
  `id` int(11) NOT NULL,
  `type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `createdby_idxfk` (`createdby`);

--
-- Indexes for table `attendence`
--
ALTER TABLE `attendence`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `empid_idxfk` (`empid`);

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `userid_idxfk` (`userid`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `empid` (`empid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `contact` (`contact`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `distributorid_idxfk` (`distributorid`),
  ADD KEY `orderby_idxfk` (`orderby`),
  ADD KEY `orderby_idxfk1` (`productid`);

--
-- Indexes for table `outlets`
--
ALTER TABLE `outlets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `distributorid_idxfk_1` (`distributorid`),
  ADD KEY `areaid_idxfk` (`areaid`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `skus`
--
ALTER TABLE `skus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `productid` (`productid`);

--
-- Indexes for table `usertypes`
--
ALTER TABLE `usertypes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `area`
--
ALTER TABLE `area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `attendence`
--
ALTER TABLE `attendence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `outlets`
--
ALTER TABLE `outlets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `skus`
--
ALTER TABLE `skus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `usertypes`
--
ALTER TABLE `usertypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `area`
--
ALTER TABLE `area`
  ADD CONSTRAINT `area_ibfk_1` FOREIGN KEY (`createdby`) REFERENCES `employees` (`id`);

--
-- Constraints for table `attendence`
--
ALTER TABLE `attendence`
  ADD CONSTRAINT `attendence_ibfk_1` FOREIGN KEY (`empid`) REFERENCES `employees` (`id`);

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `employees` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`distributorid`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`orderby`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`productid`) REFERENCES `skus` (`id`);

--
-- Constraints for table `outlets`
--
ALTER TABLE `outlets`
  ADD CONSTRAINT `outlets_ibfk_1` FOREIGN KEY (`distributorid`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `outlets_ibfk_2` FOREIGN KEY (`areaid`) REFERENCES `area` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
