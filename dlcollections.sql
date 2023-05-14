-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2023 at 11:22 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dlcollections`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_table`
--

CREATE TABLE `account_table` (
  `ID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contactNumber` varchar(255) DEFAULT NULL,
  `profileImg` varchar(255) DEFAULT NULL,
  `access` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `account_table`
--

INSERT INTO `account_table` (`ID`, `firstName`, `lastName`, `email`, `password`, `contactNumber`, `profileImg`, `access`) VALUES
(1, 'Sigorny', 'Lagdameo', 'dlcollections@gmail.com', '035b96bea410efb6ef73324ea06b680d', '', 'logo.png', 'admin'),
(3, 'User', 'User', 'deliezo.aiiyan01@gmail.com', '25d55ad283aa400af464c76d713c07ad', NULL, NULL, 'user'),
(6, 'Ian', 'Deliezo', 'kali.game@yahoo.com', '101193d7181cc88340ae5b2b17bba8a1', '', '327114279_1287056281860627_6125657925075476494_n-removebg-preview.png', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `address_table`
--

CREATE TABLE `address_table` (
  `ID` int(11) NOT NULL,
  `orderID` varchar(255) NOT NULL,
  `addressID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `postalCode` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phoneNumber` varchar(255) NOT NULL,
  `addressType` varchar(255) DEFAULT NULL,
  `accountID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `address_table`
--

INSERT INTO `address_table` (`ID`, `orderID`, `addressID`, `firstName`, `lastName`, `address1`, `address2`, `city`, `postalCode`, `region`, `country`, `phoneNumber`, `addressType`, `accountID`) VALUES
(50, '6bmkm', 239572378, 'Sigorny', 'Lagdameo', 'Lower Bonbon Butuan City, Agusan Del Norte', '', 'Butuan', '8600', 'Agusan del Norte', 'Philippines', '+639483701309', 'primary address', 6),
(51, '3mf8d', 239572378, 'Sigorny', 'Lagdameo', 'Lower Bonbon Butuan City, Agusan Del Norte', '', 'Butuan', '8600', 'Agusan del Norte', 'Philippines', '+639483701309', 'primary address', 6),
(52, '38063', 239572378, 'Sigorny', 'Lagdameo', 'Lower Bonbon Butuan City, Agusan Del Norte', '', 'Butuan', '8600', 'Agusan del Norte', 'Philippines', '+639483701309', 'primary address', 6);

-- --------------------------------------------------------

--
-- Table structure for table `category_table`
--

CREATE TABLE `category_table` (
  `ID` int(11) NOT NULL,
  `categoryName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category_table`
--

INSERT INTO `category_table` (`ID`, `categoryName`) VALUES
(1, 'Tees'),
(2, 'Jackets'),
(3, 'Accessories'),
(4, 'Bottoms'),
(6, 'Jersey');

-- --------------------------------------------------------

--
-- Table structure for table `costing_table`
--

CREATE TABLE `costing_table` (
  `ID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `materialID` int(11) NOT NULL,
  `materialQty` int(11) NOT NULL,
  `laborFee` int(11) NOT NULL,
  `laborQty` int(11) NOT NULL,
  `layoutFee` int(11) NOT NULL,
  `layoutQty` int(11) NOT NULL,
  `expenseFee` int(11) NOT NULL,
  `expenseQty` int(11) NOT NULL,
  `productCost` int(11) NOT NULL,
  `totalCost` int(11) NOT NULL,
  `salesAmount` int(11) NOT NULL,
  `salesDiscount` int(11) NOT NULL,
  `netSales` int(11) NOT NULL,
  `grossProfit` int(11) NOT NULL,
  `expenses` int(11) NOT NULL,
  `netIncome` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `costing_table`
--

INSERT INTO `costing_table` (`ID`, `productID`, `materialID`, `materialQty`, `laborFee`, `laborQty`, `layoutFee`, `layoutQty`, `expenseFee`, `expenseQty`, `productCost`, `totalCost`, `salesAmount`, `salesDiscount`, `netSales`, `grossProfit`, `expenses`, `netIncome`) VALUES
(34, 14, 12, 1, 1, 1, 1, 1, 1, 1, 3, 3, 999, 0, 999, 996, 0, 996),
(35, 15, 12, 1, 1, 1, 1, 1, 1, 1, 3, 3, 999, 0, 999, 996, 0, 996),
(36, 16, 12, 1, 1, 1, 1, 1, 1, 1, 3, 3, 999, 0, 999, 996, 0, 996);

-- --------------------------------------------------------

--
-- Table structure for table `inventorymaterial_table`
--

CREATE TABLE `inventorymaterial_table` (
  `ID` int(11) NOT NULL,
  `materialID` int(11) NOT NULL,
  `addedQty` int(11) NOT NULL,
  `dateTime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `inventoryproduct_table`
--

CREATE TABLE `inventoryproduct_table` (
  `ID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `stockID` int(11) NOT NULL,
  `addedQty` int(11) NOT NULL,
  `dateTime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `product_table`
--

CREATE TABLE `product_table` (
  `ID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `productDescription` varchar(255) NOT NULL,
  `coverPhoto` varchar(255) NOT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `sizeGuide` varchar(255) DEFAULT NULL,
  `availability` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_table`
--

INSERT INTO `product_table` (`ID`, `categoryID`, `productName`, `productDescription`, `coverPhoto`, `image1`, `image2`, `image3`, `sizeGuide`, `availability`) VALUES
(14, 6, 'Lakers Jersey', 'Lakers', '335066423_204361455614474_6990864828258143613_n-removebg-preview.png', '', '', '', '', 'Available'),
(15, 6, 'Bulls Jersey', 'Bulls', '335332742_164409329755748_7012292850114144958_n-removebg-preview.png', '', '', '', '', 'Available'),
(16, 6, 'Cleveland Jersey', 'CL', '327114279_1287056281860627_6125657925075476494_n-removebg-preview.png', '', '', '', '', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `name`) VALUES
(1, 'Abra'),
(2, 'Agusan del Norte'),
(3, 'Agusan del Sur'),
(4, 'Aklan'),
(5, 'Albay'),
(6, 'Antique'),
(7, 'Apayao'),
(8, 'Aurora'),
(9, 'Basilan'),
(10, 'Bataan'),
(11, 'Batanes'),
(12, 'Batangas'),
(13, 'Benguet'),
(14, 'Biliran'),
(15, 'Bohol'),
(16, 'Bukidnon'),
(17, 'Bulacan'),
(18, 'Cagayan'),
(19, 'Camarines Norte'),
(20, 'Camarines Sur'),
(21, 'Camiguin'),
(22, 'Capiz'),
(23, 'Catanduanes'),
(24, 'Cavite'),
(25, 'Cebu'),
(26, 'Compostela Valley'),
(27, 'Cotabato'),
(28, 'Davao del Norte'),
(29, 'Davao del Sur'),
(30, 'Davao Oriental'),
(31, 'Eastern Samar'),
(32, 'Guimaras'),
(33, 'Ifugao'),
(34, 'Ilocos Norte'),
(35, 'Ilocos Sur'),
(36, 'Iloilo'),
(37, 'Isabela'),
(38, 'Kalinga'),
(39, 'La Union'),
(40, 'Laguna'),
(41, 'Lanao del Norte'),
(42, 'Lanao del Sur'),
(43, 'Leyte'),
(44, 'Maguindanao'),
(45, 'Marinduque'),
(46, 'Masbate'),
(47, 'Metro Manila'),
(48, 'Misamis Occidental'),
(49, 'Misamis Oriental'),
(50, 'Mountain Province'),
(51, 'Negros Occidental'),
(52, 'Negros Oriental'),
(53, 'Northern Samar'),
(54, 'Nueva Ecija'),
(55, 'Nueva Vizcaya'),
(56, 'Occidental Mindoro'),
(57, 'Oriental Mindoro'),
(58, 'Palawan'),
(59, 'Pampanga'),
(60, 'Pangasinan'),
(61, 'Quezon'),
(62, 'Quirino'),
(63, 'Rizal'),
(64, 'Romblon'),
(65, 'Samar'),
(66, 'Sarangani'),
(67, 'Siquijor'),
(68, 'Sorsogon'),
(69, 'South Cotabato'),
(70, 'Southern Leyte'),
(71, 'Sultan Kudarat'),
(72, 'Sulu'),
(73, 'Surigao del Norte'),
(74, 'Surigao del Sur'),
(75, 'Tarlac'),
(76, 'Tawi-Tawi'),
(77, 'Zambales'),
(78, 'Zamboanga del Norte'),
(79, 'Zamboanga del Sur'),
(80, 'Zamboanga Sibugay');

-- --------------------------------------------------------

--
-- Table structure for table `rawmaterials_table`
--

CREATE TABLE `rawmaterials_table` (
  `ID` int(11) NOT NULL,
  `materialName` varchar(255) NOT NULL,
  `unitPrice` int(11) NOT NULL,
  `stockQty` int(11) NOT NULL,
  `supplierID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rawmaterials_table`
--

INSERT INTO `rawmaterials_table` (`ID`, `materialName`, `unitPrice`, `stockQty`, `supplierID`) VALUES
(12, 'NA', 0, 999999, 1);

-- --------------------------------------------------------

--
-- Table structure for table `return_table`
--

CREATE TABLE `return_table` (
  `ID` int(11) NOT NULL,
  `returnOrderID` varchar(255) NOT NULL,
  `returnSku` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `returnImg` varchar(255) NOT NULL,
  `userID` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sales_table`
--

CREATE TABLE `sales_table` (
  `ID` int(11) NOT NULL,
  `orderID` varchar(255) NOT NULL,
  `productID` int(11) NOT NULL,
  `stockID` int(11) NOT NULL,
  `salesQty` int(11) NOT NULL,
  `shipMethod` varchar(255) NOT NULL,
  `shipFee` int(11) NOT NULL,
  `paymentMethod` varchar(255) NOT NULL,
  `totalAmount` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `paymentStatus` varchar(255) NOT NULL,
  `orderStatus` varchar(255) NOT NULL,
  `orderDate` datetime NOT NULL DEFAULT current_timestamp(),
  `addressID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales_table`
--

INSERT INTO `sales_table` (`ID`, `orderID`, `productID`, `stockID`, `salesQty`, `shipMethod`, `shipFee`, `paymentMethod`, `totalAmount`, `accountID`, `paymentStatus`, `orderStatus`, `orderDate`, `addressID`) VALUES
(31, '38063', 15, 97, 1, 'Standard Delivery', 180, 'GCASH or Credit / Debit Card', 1179, 6, 'Paid', 'Processing', '2023-03-16 18:50:54', 239572378);

-- --------------------------------------------------------

--
-- Table structure for table `stocks_table`
--

CREATE TABLE `stocks_table` (
  `ID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `variantID` int(11) NOT NULL,
  `size` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `sku` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stocks_table`
--

INSERT INTO `stocks_table` (`ID`, `productID`, `variantID`, `size`, `stock`, `sku`) VALUES
(96, 14, 24, NULL, 10, 'FRSZ-LakersJersey-qg8iv'),
(97, 15, 25, NULL, 10, 'FRSZ-BullsJersey-qz1u2'),
(98, 16, 26, NULL, 10, 'Rd-ClevelandJersey-b17no');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_table`
--

CREATE TABLE `supplier_table` (
  `ID` int(11) NOT NULL,
  `supplierName` varchar(255) NOT NULL,
  `supplierEmail` varchar(255) NOT NULL,
  `supplierAddress` varchar(255) NOT NULL,
  `supplierContactNumber` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier_table`
--

INSERT INTO `supplier_table` (`ID`, `supplierName`, `supplierEmail`, `supplierAddress`, `supplierContactNumber`) VALUES
(1, 'N/A', 'N/A', 'N/A', 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `variation_table`
--

CREATE TABLE `variation_table` (
  `ID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `variantName` varchar(255) NOT NULL,
  `variantImage` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `variation_table`
--

INSERT INTO `variation_table` (`ID`, `productID`, `variantName`, `variantImage`) VALUES
(24, 14, 'FREESIZE', ''),
(25, 15, 'FREESIZE', ''),
(26, 16, 'Red', '327114279_1287056281860627_6125657925075476494_n-removebg-preview.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_table`
--
ALTER TABLE `account_table`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `address_table`
--
ALTER TABLE `address_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_accountID_idx` (`accountID`);

--
-- Indexes for table `category_table`
--
ALTER TABLE `category_table`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `costing_table`
--
ALTER TABLE `costing_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_productID_idx` (`productID`),
  ADD KEY `fk_materialID_idx` (`materialID`);

--
-- Indexes for table `inventorymaterial_table`
--
ALTER TABLE `inventorymaterial_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_rawID_idx` (`materialID`);

--
-- Indexes for table `inventoryproduct_table`
--
ALTER TABLE `inventoryproduct_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_prodID_idx` (`productID`),
  ADD KEY `fk_stockID_idx` (`stockID`);

--
-- Indexes for table `product_table`
--
ALTER TABLE `product_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_categoryID_idx` (`categoryID`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rawmaterials_table`
--
ALTER TABLE `rawmaterials_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_supplier_idx` (`supplierID`);

--
-- Indexes for table `return_table`
--
ALTER TABLE `return_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_userID_idx` (`userID`);

--
-- Indexes for table `sales_table`
--
ALTER TABLE `sales_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_itemID_idx` (`productID`),
  ADD KEY `fk_acctID_idx` (`accountID`),
  ADD KEY `fk_stockID_idx` (`stockID`);

--
-- Indexes for table `stocks_table`
--
ALTER TABLE `stocks_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_productID_idx` (`productID`),
  ADD KEY `fk_variantID_idx` (`variantID`);

--
-- Indexes for table `supplier_table`
--
ALTER TABLE `supplier_table`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `variation_table`
--
ALTER TABLE `variation_table`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `productID_idx` (`productID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_table`
--
ALTER TABLE `account_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `address_table`
--
ALTER TABLE `address_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `category_table`
--
ALTER TABLE `category_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `costing_table`
--
ALTER TABLE `costing_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `inventorymaterial_table`
--
ALTER TABLE `inventorymaterial_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventoryproduct_table`
--
ALTER TABLE `inventoryproduct_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_table`
--
ALTER TABLE `product_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `rawmaterials_table`
--
ALTER TABLE `rawmaterials_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `return_table`
--
ALTER TABLE `return_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sales_table`
--
ALTER TABLE `sales_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `stocks_table`
--
ALTER TABLE `stocks_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `supplier_table`
--
ALTER TABLE `supplier_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `variation_table`
--
ALTER TABLE `variation_table`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address_table`
--
ALTER TABLE `address_table`
  ADD CONSTRAINT `fk_accountID` FOREIGN KEY (`accountID`) REFERENCES `account_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `costing_table`
--
ALTER TABLE `costing_table`
  ADD CONSTRAINT `fk_materialID` FOREIGN KEY (`materialID`) REFERENCES `rawmaterials_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_productID` FOREIGN KEY (`productID`) REFERENCES `product_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventorymaterial_table`
--
ALTER TABLE `inventorymaterial_table`
  ADD CONSTRAINT `fk_rawID` FOREIGN KEY (`materialID`) REFERENCES `rawmaterials_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventoryproduct_table`
--
ALTER TABLE `inventoryproduct_table`
  ADD CONSTRAINT `fk_prdID` FOREIGN KEY (`productID`) REFERENCES `product_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stckID` FOREIGN KEY (`stockID`) REFERENCES `stocks_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_table`
--
ALTER TABLE `product_table`
  ADD CONSTRAINT `fk_categoryID` FOREIGN KEY (`categoryID`) REFERENCES `category_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rawmaterials_table`
--
ALTER TABLE `rawmaterials_table`
  ADD CONSTRAINT `fk_supplier` FOREIGN KEY (`supplierID`) REFERENCES `supplier_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `return_table`
--
ALTER TABLE `return_table`
  ADD CONSTRAINT `fk_userID` FOREIGN KEY (`userID`) REFERENCES `account_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_table`
--
ALTER TABLE `sales_table`
  ADD CONSTRAINT `fk_acctID` FOREIGN KEY (`accountID`) REFERENCES `account_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prodID` FOREIGN KEY (`productID`) REFERENCES `product_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stockID` FOREIGN KEY (`stockID`) REFERENCES `stocks_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stocks_table`
--
ALTER TABLE `stocks_table`
  ADD CONSTRAINT `fk_itemID` FOREIGN KEY (`productID`) REFERENCES `product_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_variantID` FOREIGN KEY (`variantID`) REFERENCES `variation_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `variation_table`
--
ALTER TABLE `variation_table`
  ADD CONSTRAINT `productID` FOREIGN KEY (`productID`) REFERENCES `product_table` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
