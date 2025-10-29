SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `activation` (
  `id` int(11) NOT NULL,
  `worldId` varchar(5) NOT NULL,
  `name` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(45) NOT NULL,
  `activationCode` varchar(15) NOT NULL,
  `newsletter` tinyint(1) UNSIGNED NOT NULL,
  `used` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `refUid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `reminded` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `banIP` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` bigint(12) UNSIGNED NOT NULL,
  `reason` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED NOT NULL,
  `blockTill` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `bannerShop` (
  `id` int(11) NOT NULL,
  `content` mediumtext NOT NULL,
  `expire` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `clubMedals` (
  `id` int(11) NOT NULL,
  `worldId` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tribe` tinyint(1) UNSIGNED NOT NULL,
  `type` int(10) NOT NULL,
  `params` varchar(500) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL,
  `hidden` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `paymentAmount` double NOT NULL,
  `expiretime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `configurations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `configurations` (`id`, `name`, `data`) VALUES
(2, '3x', '{\"speed\":\"3\",\"mapSize\":\"400\",\"startGold\":\"10000\",\"protectionHours\":\"72\",\"roundLength\":\"auto\",\"isPromoted\":\"0\",\"needPreregistrationCode\":\"0\",\"buyAnimals\":\"0\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"0\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"0\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"0\",\"instantFinishTraining\":\"0\",\"buyAdventure\":\"0\",\"activation\":\"1\"}'),
(3, 'Tx5', '{\"speed\":\"5\",\"mapSize\":\"400\",\"startGold\":\"100\",\"protectionHours\":\"48\",\"roundLength\":\"auto\",\"isPromoted\":\"1\",\"needPreregistrationCode\":\"0\",\"buyAnimals\":\"0\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"0\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"0\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"0\",\"instantFinishTraining\":\"0\",\"buyAdventure\":\"0\",\"activation\":\"1\"}'),
(4, '100x', '{\"speed\":\"100\",\"mapSize\":\"200\",\"startGold\":\"0\",\"protectionHours\":\"12\",\"roundLength\":\"7\",\"isPromoted\":\"1\",\"needPreregistrationCode\":\"0\",\"buyAnimals\":\"0\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"0\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"0\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"1\",\"instantFinishTraining\":\"0\",\"buyAdventure\":\"0\",\"activation\":\"1\"}'),
(5, '100k', '{\"speed\":\"100000\",\"mapSize\":\"200\",\"startGold\":\"500\",\"protectionHours\":\"12\",\"roundLength\":\"5\",\"isPromoted\":\"0\",\"needPreregistrationCode\":\"1\",\"buyAnimals\":\"1\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"1\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"1\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"1\",\"instantFinishTraining\":\"1\",\"buyAdventure\":\"1\",\"activation\":\"1\"}'),
(6, 'SP1', '{\"speed\":\"200000\",\"mapSize\":\"200\",\"startGold\":\"1000\",\"protectionHours\":\"6\",\"roundLength\":\"7\",\"isPromoted\":\"1\",\"needPreregistrationCode\":\"0\",\"buyAnimals\":\"1\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"1\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"1\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"1\",\"instantFinishTraining\":\"1\",\"buyAdventure\":\"1\",\"activation\":\"1\"}'),
(7, '10x', '{\"speed\":\"10\",\"mapSize\":\"400\",\"startGold\":\"50\",\"protectionHours\":\"12\",\"roundLength\":\"30\",\"isPromoted\":\"1\",\"needPreregistrationCode\":\"1\",\"buyAnimals\":\"0\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"0\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"0\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"1\",\"instantFinishTraining\":\"0\",\"buyAdventure\":\"1\",\"activation\":\"1\"}'),
(8, '5X', '{\"speed\":\"5\",\"mapSize\":\"400\",\"startGold\":\"50\",\"protectionHours\":\"48\",\"roundLength\":\"30\",\"isPromoted\":\"1\",\"needPreregistrationCode\":\"0\",\"buyAnimals\":\"0\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"0\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"0\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"1\",\"instantFinishTraining\":\"0\",\"buyAdventure\":\"0\",\"activation\":\"1\"}'),
(9, '100000x', '{\"speed\":\"100000\",\"mapSize\":\"200\",\"startGold\":\"500\",\"protectionHours\":\"12\",\"roundLength\":\"3\",\"isPromoted\":\"0\",\"needPreregistrationCode\":\"0\",\"buyAnimals\":\"1\",\"buyAnimalsInterval\":\"0\",\"buyResources\":\"1\",\"buyResourcesInterval\":\"0\",\"buyTroops\":\"1\",\"buyTroopsInterval\":\"0\",\"startTimezone\":\"1\",\"instantFinishTraining\":\"1\",\"buyAdventure\":\"1\",\"activation\":\"0\"}');

CREATE TABLE `email_blacklist` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `gameServers` (
  `id` int(11) NOT NULL,
  `worldId` varchar(100) NOT NULL,
  `speed` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` int(1) NOT NULL,
  `gameWorldUrl` varchar(500) NOT NULL,
  `startTime` int(10) UNSIGNED NOT NULL,
  `roundLength` int(10) UNSIGNED NOT NULL,
  `finished` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `registerClosed` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `activation` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `preregistration_key_only` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `hidden` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `configFileLocation` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `goldProducts` (
  `goldProductId` int(11) NOT NULL,
  `goldProductName` varchar(100) NOT NULL,
  `goldProductLocation` int(10) UNSIGNED NOT NULL,
  `goldProductGold` int(10) UNSIGNED NOT NULL,
  `goldProductPrice` double UNSIGNED NOT NULL,
  `goldProductMoneyUnit` varchar(100) NOT NULL,
  `goldProductImageName` varchar(100) NOT NULL,
  `goldProductHasOffer` tinyint(3) UNSIGNED NOT NULL,
  `isBestSeller` tinyint(4) NOT NULL DEFAULT 0,
  `isBestValue` tinyint(4) NOT NULL DEFAULT 0,
  `isSMS` tinyint(4) NOT NULL DEFAULT 0,
  `isActive` tinyint(4) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `goldProducts` (`goldProductId`, `goldProductName`, `goldProductLocation`, `goldProductGold`, `goldProductPrice`, `goldProductMoneyUnit`, `goldProductImageName`, `goldProductHasOffer`, `isBestSeller`, `isBestValue`, `isSMS`, `isActive`) VALUES
(14, 'Package A', 2, 300, 2.99, 'EUR', '4_6_1.png', 0, 0, 0, 0, 1),
(15, 'Package B', 2, 750, 5.99, 'EUR', '4_6_2.png', 0, 0, 0, 0, 1),
(16, 'Package C', 2, 1600, 11.99, 'EUR', '4_6_3.png', 0, 0, 0, 0, 1),
(17, 'Package D', 2, 3600, 23.99, 'EUR', '4_6_4.png', 0, 1, 0, 0, 1),
(18, 'Package E', 2, 8000, 47.99, 'EUR', '4_6_5.png', 0, 0, 0, 0, 1),
(19, 'Package F', 2, 18000, 95.99, 'EUR', '4_6_6.png', 0, 0, 1, 0, 1);

CREATE TABLE `handshakes` (
  `id` int(11) NOT NULL,
  `handshakes` varchar(100) NOT NULL,
  `isSitter` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `expireTime` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `infobox` (
  `id` int(11) UNSIGNED NOT NULL,
  `autoType` tinyint(1) NOT NULL DEFAULT 0,
  `params` text NOT NULL,
  `showFrom` int(10) UNSIGNED NOT NULL,
  `showTo` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `location` varchar(100) NOT NULL,
  `content_language` varchar(100) NOT NULL COMMENT 'Like: USD, Rials'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext DEFAULT NULL,
  `expire` int(10) UNSIGNED NOT NULL,
  `shortDesc` mediumtext DEFAULT NULL,
  `moreLink` varchar(255) NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(60) NOT NULL,
  `private_key` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `pin` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `package_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(100) NOT NULL,
  `isGift` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `used` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `passwordRecovery` (
  `id` int(10) UNSIGNED NOT NULL,
  `wid` int(10) UNSIGNED NOT NULL,
  `recoveryCode` varchar(255) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `paymentConfig` (
  `id` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `offerFrom` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `offer` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `mailerLock` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `notificationGroupId` varchar(50) NOT NULL DEFAULT '-1001069565293',
  `notificationLock` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `lastIncomeCheck` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `lastIncomeHash` varchar(32) DEFAULT NULL,
  `loginToken` varchar(100) DEFAULT NULL,
  `votingGold` int(10) UNSIGNED NOT NULL DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `paymentLog` (
  `id` int(10) UNSIGNED NOT NULL,
  `worldUniqueId` int(11) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `secureId` varchar(100) NOT NULL,
  `paymentProvider` int(10) UNSIGNED NOT NULL,
  `productId` int(10) UNSIGNED NOT NULL,
  `payPrice` double UNSIGNED DEFAULT 0,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `data` text DEFAULT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `paymentProviders` (
  `providerId` int(10) UNSIGNED NOT NULL,
  `providerType` int(10) UNSIGNED NOT NULL,
  `location` int(10) UNSIGNED NOT NULL,
  `posId` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` mediumtext NOT NULL,
  `img` varchar(100) NOT NULL,
  `delivery` varchar(100) NOT NULL,
  `connectInfo` mediumtext NOT NULL,
  `isProviderLoadedByHTML` tinyint(1) UNSIGNED NOT NULL,
  `hidden` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `isActive` tinyint(4) NOT NULL DEFAULT 1,
  `isSMS` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `paymentVoucher` (
  `id` int(11) NOT NULL,
  `gold` int(10) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `worldId` varchar(100) DEFAULT NULL,
  `player` varchar(100) DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `voucherCode` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED NOT NULL,
  `used` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usedTime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usedWorldId` varchar(350) DEFAULT NULL,
  `usedPlayer` varchar(100) DEFAULT NULL,
  `usedEmail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `preregistration_keys` (
  `id` int(10) UNSIGNED NOT NULL,
  `worldId` varchar(20) NOT NULL,
  `pre_key` varchar(32) NOT NULL,
  `used` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `taskQueue` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('install','uninstall','flushTokens','start-engine','stop-engine','restart-engine','') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `status` enum('pending','done','failed') NOT NULL DEFAULT 'pending',
  `time` int(10) UNSIGNED NOT NULL,
  `failReason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `worldUniqueId` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` mediumtext NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  `answered` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `txn_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `voting_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `wid` int(10) UNSIGNED NOT NULL,
  `uid` int(11) UNSIGNED NOT NULL,
  `ip` bigint(20) UNSIGNED DEFAULT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `activation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reminded` (`reminded`);

ALTER TABLE `banIP`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip` (`ip`,`blockTill`);

ALTER TABLE `bannerShop`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expire` (`expire`);

ALTER TABLE `clubMedals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hidden` (`hidden`);

ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `configurations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `email_blacklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `time` (`time`);

ALTER TABLE `gameServers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `search` (`finished`,`registerClosed`,`hidden`);

ALTER TABLE `goldProducts`
  ADD PRIMARY KEY (`goldProductId`);

ALTER TABLE `handshakes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `handshakes` (`handshakes`);

ALTER TABLE `infobox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `search` (`showTo`);

ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expire` (`expire`);

ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `private_key` (`private_key`);

ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `search` (`time`);

ALTER TABLE `package_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_2` (`code`),
  ADD KEY `code` (`code`),
  ADD KEY `used` (`used`),
  ADD KEY `package_id` (`package_id`);

ALTER TABLE `passwordRecovery`
  ADD PRIMARY KEY (`id`,`wid`),
  ADD KEY `recoveryCode` (`recoveryCode`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `paymentConfig`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `paymentLog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `secureId` (`secureId`),
  ADD KEY `uid` (`uid`),
  ADD KEY `paymentProvider` (`paymentProvider`),
  ADD KEY `email` (`email`),
  ADD KEY `worldUniqueId` (`worldUniqueId`);

ALTER TABLE `paymentProviders`
  ADD PRIMARY KEY (`providerId`),
  ADD KEY `location` (`location`),
  ADD KEY `posId` (`posId`),
  ADD KEY `hidden` (`hidden`),
  ADD KEY `providerType` (`providerType`);

ALTER TABLE `paymentVoucher`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucherCode` (`voucherCode`),
  ADD KEY `gold` (`gold`),
  ADD KEY `email` (`email`),
  ADD KEY `time` (`time`),
  ADD KEY `used` (`used`);

ALTER TABLE `preregistration_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `worldId` (`worldId`,`pre_key`,`used`);

ALTER TABLE `taskQueue`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answered` (`answered`,`time`);

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`);

ALTER TABLE `voting_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`,`type`),
  ADD KEY `wid` (`wid`),
  ADD KEY `ip` (`ip`);


ALTER TABLE `activation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `banIP`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `bannerShop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `clubMedals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `configurations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `email_blacklist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `gameServers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `goldProducts`
  MODIFY `goldProductId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

ALTER TABLE `handshakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `infobox`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `package_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `passwordRecovery`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `paymentConfig`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `paymentLog`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `paymentProviders`
  MODIFY `providerId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `paymentVoucher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `preregistration_keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `taskQueue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `voting_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
