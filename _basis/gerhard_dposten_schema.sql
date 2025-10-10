-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 08, 2025 at 06:50 PM
-- Server version: 8.0.43-cll-lve
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gerhard_dposten`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblAnnonse`
--

CREATE TABLE `tblAnnonse` (
  `PRID` int NOT NULL,
  `AnnonseInnh` varchar(255) DEFAULT NULL,
  `AnnID` int DEFAULT NULL,
  `AnnonseBilde` varchar(255) DEFAULT NULL,
  `AnnonseTekst` varchar(255) DEFAULT NULL,
  `AnnonseSize` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `AvtaltPris` double DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblAnnonsor`
--

CREATE TABLE `tblAnnonsor` (
  `AnnID` int NOT NULL,
  `AnnNavn` varchar(255) DEFAULT NULL,
  `AnnAvd` varchar(255) DEFAULT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Postnr` varchar(255) DEFAULT NULL,
  `Poststed` varchar(255) DEFAULT NULL,
  `Land` varchar(255) DEFAULT NULL,
  `Utland` tinyint DEFAULT '0',
  `EpostAdresse` varchar(255) DEFAULT NULL,
  `WebSide` varchar(255) DEFAULT NULL,
  `KontaktPers` varchar(255) DEFAULT NULL,
  `KontaktEpost` varchar(255) DEFAULT NULL,
  `KontaktTelf` varchar(255) DEFAULT NULL,
  `Notater` longtext,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblArtikkel`
--

CREATE TABLE `tblArtikkel` (
  `ArtID` int NOT NULL,
  `BladID` int DEFAULT NULL,
  `Side` smallint DEFAULT NULL,
  `ArtTittel` varchar(255) DEFAULT NULL,
  `ArtUnderTittel` varchar(255) DEFAULT NULL,
  `ArtType` varchar(1) DEFAULT NULL,
  `Sprog` varchar(255) DEFAULT NULL,
  `Abstrakt` longtext,
  `Kapitler` longtext,
  `EksternRef` varchar(255) DEFAULT NULL,
  `ArtAntBilde` smallint DEFAULT NULL,
  `SpID` int DEFAULT NULL,
  `Merknad` longtext,
  `Flag` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblBilde`
--

CREATE TABLE `tblBilde` (
  `PicID` int NOT NULL,
  `PicMotiv` varchar(255) DEFAULT NULL,
  `PicType` varchar(15) DEFAULT NULL,
  `PicMerk` longtext,
  `ForfID` int DEFAULT NULL,
  `UsikkerForf` tinyint DEFAULT '0',
  `OpphRett` varchar(255) DEFAULT NULL,
  `PicLenke` varchar(255) DEFAULT NULL,
  `TattYear` smallint DEFAULT NULL,
  `TattYearSenest` smallint DEFAULT NULL,
  `CaYear` tinyint DEFAULT '0',
  `MStedID` int DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `DM` tinyint DEFAULT '0',
  `DigMusID` varchar(20) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblBlad`
--

CREATE TABLE `tblBlad` (
  `BladID` int NOT NULL,
  `BladNr` smallint DEFAULT NULL,
  `Year` smallint DEFAULT NULL,
  `YearNr` smallint DEFAULT NULL,
  `AntArt` smallint DEFAULT NULL,
  `AntNot` smallint DEFAULT NULL,
  `AntSpalte` smallint DEFAULT NULL,
  `AntProg` smallint DEFAULT NULL,
  `AntBilde` smallint DEFAULT NULL,
  `Lenke` varchar(50) DEFAULT NULL,
  `BladFormat` varchar(10) DEFAULT NULL,
  `AntSider` smallint DEFAULT NULL,
  `Farger` tinyint DEFAULT '0',
  `Redaktor` varchar(50) DEFAULT NULL,
  `Trykkeri` varchar(100) DEFAULT NULL,
  `Katalog` tinyint DEFAULT '0',
  `Opplag` smallint DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblFartBulk`
--

CREATE TABLE `tblFartBulk` (
  `TempID` int NOT NULL,
  `FartNavn` varchar(50) DEFAULT NULL,
  `Tilnavn` varchar(50) DEFAULT NULL,
  `FTIDBulk` int DEFAULT NULL,
  `NaID` int DEFAULT NULL,
  `ByggYear` int DEFAULT NULL,
  `ByggMnd` tinyint UNSIGNED DEFAULT NULL,
  `RegHavn` varchar(50) DEFAULT NULL,
  `VerftID` int DEFAULT NULL,
  `Byggenr` smallint DEFAULT NULL,
  `RedID` int DEFAULT NULL,
  `MMSI` varchar(15) DEFAULT NULL,
  `Kallesignal` varchar(10) DEFAULT NULL,
  `FartFunkID` int DEFAULT NULL,
  `FartSkrogID` int DEFAULT NULL,
  `FartDriftID` int DEFAULT NULL,
  `FunkDetalj` varchar(50) DEFAULT NULL,
  `LinkType1` varchar(50) DEFAULT NULL,
  `LinkTekst1` varchar(50) DEFAULT NULL,
  `Link1` longtext,
  `LinkType2` varchar(50) DEFAULT NULL,
  `LinkTekst2` varchar(50) DEFAULT NULL,
  `Link2` longtext,
  `LinkType3` varchar(50) DEFAULT NULL,
  `LinkTekst3` varchar(50) DEFAULT NULL,
  `Link3` longtext,
  `ObjID` int DEFAULT NULL,
  `Objekt` tinyint DEFAULT '0',
  `FartVern` tinyint DEFAULT '0',
  `IMO` int DEFAULT NULL,
  `Rigg` varchar(50) DEFAULT NULL,
  `ObjNotater` longtext,
  `UsikreData` tinyint DEFAULT '0',
  `IngenData` tinyint DEFAULT '0',
  `NySpes` tinyint DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `FlagChkDM` tinyint DEFAULT '0',
  `SekvNr` smallint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblFartNavn`
--

CREATE TABLE `tblFartNavn` (
  `FartID` int NOT NULL,
  `ObjID` int DEFAULT NULL,
  `YearNavn` smallint DEFAULT '0',
  `MndNavn` smallint DEFAULT '0',
  `FartNavn` varchar(100) NOT NULL,
  `FTIDNavn` int DEFAULT NULL,
  `Tilnavn` varchar(50) DEFAULT NULL,
  `OrgID` int DEFAULT NULL,
  `FartVern` tinyint DEFAULT '0',
  `PicFile` varchar(50) DEFAULT NULL,
  `FartNotater` longtext,
  `UsikreData` tinyint DEFAULT '0',
  `AntArt` smallint DEFAULT NULL,
  `AntNot` smallint DEFAULT NULL,
  `AntSpalte` smallint DEFAULT NULL,
  `AntProg` smallint DEFAULT NULL,
  `AntBilde` smallint DEFAULT NULL,
  `AntHoved` smallint DEFAULT NULL,
  `AntOmtalt` smallint DEFAULT NULL,
  `AntDP` smallint DEFAULT NULL,
  `SortPri` tinyint DEFAULT '0',
  `FlagNo` smallint DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `FlagChkDM` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblFartObj`
--

CREATE TABLE `tblFartObj` (
  `ObjID` int NOT NULL,
  `FartObjNavn` varchar(100) NOT NULL,
  `FTIDObj` int DEFAULT NULL,
  `IMO` int DEFAULT NULL,
  `StroketYear` smallint DEFAULT NULL,
  `StroketGrunn` varchar(50) DEFAULT NULL,
  `Typebetegn` tinyint DEFAULT '0',
  `FartKlasse` varchar(100) DEFAULT NULL,
  `ObjNotater` longtext,
  `UsikreData` tinyint DEFAULT '0',
  `IngenData` tinyint DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblFartSpes`
--

CREATE TABLE `tblFartSpes` (
  `SpesID` int NOT NULL,
  `ObjID` int DEFAULT NULL,
  `YearSpes` smallint DEFAULT '0',
  `MndSpes` smallint DEFAULT '0',
  `VerftID` int DEFAULT NULL,
  `Byggenr` smallint DEFAULT NULL,
  `FTIDSpes` int DEFAULT NULL,
  `FartFunkID` int DEFAULT NULL,
  `FartSkrogID` int DEFAULT NULL,
  `FartDriftID` int DEFAULT NULL,
  `FunkDetalj` varchar(50) DEFAULT NULL,
  `MotorEff` varchar(20) DEFAULT NULL,
  `MotorType` varchar(50) DEFAULT NULL,
  `MaxFart` smallint DEFAULT NULL,
  `Lengde` smallint DEFAULT NULL,
  `Bredde` smallint DEFAULT NULL,
  `Dypg` smallint DEFAULT NULL,
  `Tonnasje` int DEFAULT NULL,
  `Nybygg` tinyint DEFAULT '0',
  `Rigg` varchar(50) DEFAULT NULL,
  `Flag` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblFartTid`
--

CREATE TABLE `tblFartTid` (
  `TidID` int NOT NULL,
  `YearTid` smallint DEFAULT '0',
  `MndTid` smallint DEFAULT '0',
  `ObjID` int DEFAULT NULL,
  `FartID` int DEFAULT NULL,
  `SpesID` int DEFAULT NULL,
  `RedID` int DEFAULT NULL,
  `NaID` int DEFAULT NULL,
  `RegHavn` varchar(50) DEFAULT NULL,
  `MMSI` varchar(15) DEFAULT NULL,
  `Kallesignal` varchar(15) DEFAULT NULL,
  `Bygging` tinyint DEFAULT '0',
  `Objekt` tinyint DEFAULT '0',
  `Navning` tinyint DEFAULT '0',
  `Eierskifte` tinyint DEFAULT '0',
  `Annet` tinyint DEFAULT '0',
  `Hendelse` varchar(255) DEFAULT NULL,
  `Flag` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblForfatter`
--

CREATE TABLE `tblForfatter` (
  `ForfID` int NOT NULL,
  `ForfNavn` varchar(255) DEFAULT NULL,
  `FNavn` varchar(255) DEFAULT NULL,
  `ENavn` varchar(255) NOT NULL,
  `PostAdresse` varchar(255) DEFAULT NULL,
  `Postnr` varchar(255) DEFAULT NULL,
  `Poststed` varchar(255) DEFAULT NULL,
  `Land` varchar(255) DEFAULT NULL,
  `Utland` tinyint DEFAULT '0',
  `EpostAdresse` varchar(255) DEFAULT NULL,
  `AntArt` smallint DEFAULT NULL,
  `AntNot` smallint DEFAULT NULL,
  `AntSpalte` smallint DEFAULT NULL,
  `AntBilde` smallint DEFAULT NULL,
  `Fotograf` tinyint DEFAULT '0',
  `Forfatter` tinyint DEFAULT '0',
  `ForfBio` longtext,
  `SortPri` tinyint DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblForfBulk`
--

CREATE TABLE `tblForfBulk` (
  `TempFFID` int NOT NULL,
  `ENavn` varchar(255) DEFAULT NULL,
  `FNavn` varchar(255) DEFAULT NULL,
  `PostAdresse` varchar(255) DEFAULT NULL,
  `Postnr` varchar(255) DEFAULT NULL,
  `Poststed` varchar(255) DEFAULT NULL,
  `Land` varchar(255) DEFAULT NULL,
  `Utland` tinyint DEFAULT '0',
  `EpostAdresse` varchar(255) DEFAULT NULL,
  `Fotograf` tinyint DEFAULT '0',
  `Forfatter` tinyint DEFAULT '0',
  `ForfNavn` varchar(255) DEFAULT NULL,
  `Flag` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblForlag`
--

CREATE TABLE `tblForlag` (
  `ForlID` int NOT NULL,
  `ForlagNavn` varchar(255) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblMotivSted`
--

CREATE TABLE `tblMotivSted` (
  `MStedID` int NOT NULL,
  `StedTatt` varchar(255) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblOrganisasjon`
--

CREATE TABLE `tblOrganisasjon` (
  `OrgID` int NOT NULL,
  `OrgNavn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Postnr` varchar(255) DEFAULT NULL,
  `Poststed` varchar(255) DEFAULT NULL,
  `EpostAdresse` varchar(255) DEFAULT NULL,
  `WebSide` varchar(255) DEFAULT NULL,
  `FB` varchar(255) DEFAULT NULL,
  `OrgNavnKort` varchar(20) DEFAULT NULL,
  `Notater` longtext,
  `AntArt` smallint DEFAULT '0',
  `AntNot` smallint DEFAULT '0',
  `AntSpalte` smallint DEFAULT '0',
  `AntBilde` smallint DEFAULT '0',
  `AntHoved` smallint DEFAULT '0',
  `AntOmtalt` smallint DEFAULT '0',
  `AntDP` smallint DEFAULT '0',
  `VernOrg` tinyint DEFAULT '0',
  `MuseumOrg` tinyint DEFAULT '0',
  `StatOrg` tinyint DEFAULT '0',
  `IkkeOrg` tinyint DEFAULT '0',
  `Nedlagt` tinyint DEFAULT '0',
  `SortPri` tinyint DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `FlagNo` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblOrgBulk`
--

CREATE TABLE `tblOrgBulk` (
  `TempID` int NOT NULL,
  `OrgNavn` varchar(255) DEFAULT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Postnr` varchar(255) DEFAULT NULL,
  `Poststed` varchar(255) DEFAULT NULL,
  `WebSide` varchar(255) DEFAULT NULL,
  `OrgNavnKort` varchar(10) DEFAULT NULL,
  `VernOrg` tinyint DEFAULT '0',
  `MuseumOrg` tinyint DEFAULT '0',
  `StatOrg` tinyint DEFAULT '0',
  `IkkeOrg` tinyint DEFAULT '0',
  `Nedlagt` tinyint DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `SekvNr` smallint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblOrgPerson`
--

CREATE TABLE `tblOrgPerson` (
  `OPID` int NOT NULL,
  `OrgPersNavn` varchar(255) DEFAULT NULL,
  `OrgPersAdr` varchar(255) DEFAULT NULL,
  `OrgPersPNr` varchar(50) DEFAULT NULL,
  `OrgPersPSted` varchar(100) DEFAULT NULL,
  `OrgPersEpost` varchar(255) DEFAULT NULL,
  `OrgPersTelf` varchar(255) DEFAULT NULL,
  `Kontakt` tinyint DEFAULT '0',
  `DagLed` tinyint DEFAULT '0',
  `StyreLed` tinyint DEFAULT '0',
  `AktivMedl` tinyint DEFAULT '0',
  `YearFra` smallint DEFAULT NULL,
  `YearTil` smallint DEFAULT NULL,
  `Merknad` longtext,
  `OrgID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblPersBulk`
--

CREATE TABLE `tblPersBulk` (
  `TempID` int NOT NULL,
  `ENavn` varchar(50) DEFAULT NULL,
  `FNavn` varchar(50) DEFAULT NULL,
  `PersTittel` varchar(50) DEFAULT NULL,
  `PersYrke` varchar(50) DEFAULT NULL,
  `PersBorn` varchar(50) DEFAULT NULL,
  `RelFartBedr` longtext,
  `RelSted` longtext,
  `PersNasjon` varchar(50) DEFAULT NULL,
  `Link` longtext,
  `TilNavn` varchar(50) DEFAULT NULL,
  `Flag` tinyint DEFAULT '0',
  `SekvNr` smallint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblPerson`
--

CREATE TABLE `tblPerson` (
  `PersID` int NOT NULL,
  `ENavn` varchar(50) DEFAULT NULL,
  `FNavn` varchar(50) DEFAULT NULL,
  `PersTittel` varchar(50) DEFAULT NULL,
  `PersBorn` varchar(10) DEFAULT NULL,
  `PersYrke` varchar(50) DEFAULT NULL,
  `PersNasjon` varchar(50) DEFAULT NULL,
  `RelFartBedr` longtext,
  `RelSted` longtext,
  `Link` longtext,
  `TilNavn` varchar(50) DEFAULT NULL,
  `AntArt` smallint DEFAULT NULL,
  `AntNot` smallint DEFAULT NULL,
  `AntProg` smallint DEFAULT NULL,
  `AntSpalte` smallint DEFAULT NULL,
  `AntBilde` smallint DEFAULT NULL,
  `AntHoved` smallint DEFAULT NULL,
  `AntOmtalt` smallint DEFAULT NULL,
  `AntDP` smallint DEFAULT NULL,
  `SortPri` tinyint DEFAULT '0',
  `Flag` tinyint DEFAULT '0',
  `FlagNo` smallint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblPubForfatter`
--

CREATE TABLE `tblPubForfatter` (
  `PubForfID` int NOT NULL,
  `PubForfNavn` varchar(255) DEFAULT NULL,
  `PubENavn` varchar(255) NOT NULL,
  `PubFNavn` varchar(255) DEFAULT NULL,
  `NaID` int DEFAULT NULL,
  `AntPub` smallint DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblPublikasjon`
--

CREATE TABLE `tblPublikasjon` (
  `PubID` int NOT NULL,
  `PubTittel` varchar(255) DEFAULT NULL,
  `PubTittelSub` varchar(255) DEFAULT NULL,
  `ISBN` varchar(17) DEFAULT NULL,
  `Forlag` varchar(255) DEFAULT NULL,
  `ForlID` int DEFAULT '0',
  `UtgittYear` smallint DEFAULT NULL,
  `Sider` smallint DEFAULT NULL,
  `SalgNavn` varchar(255) DEFAULT NULL,
  `SalgEpost` varchar(255) DEFAULT NULL,
  `SalgWeb` varchar(255) DEFAULT NULL,
  `Bok` tinyint DEFAULT '0',
  `PubInnh` longtext,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblRederi`
--

CREATE TABLE `tblRederi` (
  `RedID` int NOT NULL,
  `RedNavn` varchar(255) NOT NULL,
  `Sted` varchar(255) DEFAULT NULL,
  `NaID` int DEFAULT NULL,
  `RegHavn` varchar(50) DEFAULT NULL,
  `RedSenere` varchar(255) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblTema`
--

CREATE TABLE `tblTema` (
  `TID` int NOT NULL,
  `Tema` varchar(255) DEFAULT NULL,
  `TemaInnhold` longtext,
  `AntArt` smallint DEFAULT NULL,
  `AntNot` smallint DEFAULT NULL,
  `AntSpalte` smallint DEFAULT NULL,
  `AntProg` smallint DEFAULT NULL,
  `AntBilde` smallint DEFAULT NULL,
  `AntDP` smallint DEFAULT NULL,
  `Flag` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblTrykkeri`
--

CREATE TABLE `tblTrykkeri` (
  `TrykkID` int NOT NULL,
  `Trykkeri` varchar(100) DEFAULT NULL,
  `TrykkAdr` varchar(100) DEFAULT NULL,
  `TrykkKontakt` varchar(50) DEFAULT NULL,
  `TrykkTlf` varchar(20) DEFAULT NULL,
  `TrykkEpost` varchar(50) DEFAULT NULL,
  `TrykkWeb` varchar(100) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblVerft`
--

CREATE TABLE `tblVerft` (
  `VerftID` int NOT NULL,
  `VerftNavn` varchar(255) NOT NULL,
  `Sted` varchar(255) DEFAULT NULL,
  `NaID` int DEFAULT NULL,
  `AndreNavn` longtext,
  `Etablert` smallint DEFAULT NULL,
  `Nedlagt` smallint DEFAULT NULL,
  `Merknad` varchar(255) DEFAULT NULL,
  `Flag` tinyint DEFAULT '0',
  `FlagNo` smallint DEFAULT '0',
  `AntArt` smallint DEFAULT NULL,
  `AntNot` smallint DEFAULT NULL,
  `AntSpalte` smallint DEFAULT NULL,
  `AntProg` smallint DEFAULT NULL,
  `AntBilde` smallint DEFAULT NULL,
  `AntHoved` smallint DEFAULT NULL,
  `AntOmtalt` smallint DEFAULT NULL,
  `AntDP` smallint DEFAULT NULL,
  `SortPri` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxAnnBlad`
--

CREATE TABLE `tblxAnnBlad` (
  `AnnBlID` int NOT NULL,
  `AnnID` int DEFAULT NULL,
  `BladID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxAnnonseBlad`
--

CREATE TABLE `tblxAnnonseBlad` (
  `BlPRID` int NOT NULL,
  `PRID` int DEFAULT NULL,
  `BladID` int DEFAULT NULL,
  `Side` smallint DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxArtFart`
--

CREATE TABLE `tblxArtFart` (
  `ArtFartID` int NOT NULL,
  `ArtID` int DEFAULT NULL,
  `FartID` int DEFAULT NULL,
  `FokID` int DEFAULT NULL,
  `SpesTypeID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxArtForf`
--

CREATE TABLE `tblxArtForf` (
  `ArtForfID` int NOT NULL,
  `ArtID` int DEFAULT NULL,
  `ForfID` int DEFAULT NULL,
  `UsikkerForf` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxArtOrg`
--

CREATE TABLE `tblxArtOrg` (
  `ArtOrgID` int NOT NULL,
  `ArtID` int DEFAULT NULL,
  `OrgID` int DEFAULT NULL,
  `FokID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxArtPub`
--

CREATE TABLE `tblxArtPub` (
  `ArtPubID` int NOT NULL,
  `ArtID` int DEFAULT NULL,
  `PubID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxArtTema`
--

CREATE TABLE `tblxArtTema` (
  `ArtTEmID` int NOT NULL,
  `ArtID` int DEFAULT NULL,
  `TID` int DEFAULT NULL,
  `Innhold` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxArtVerft`
--

CREATE TABLE `tblxArtVerft` (
  `ArtVerftID` int NOT NULL,
  `ArtID` int DEFAULT NULL,
  `VerftID` int DEFAULT NULL,
  `FokID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxBildeBlad`
--

CREATE TABLE `tblxBildeBlad` (
  `BlPicID` int NOT NULL,
  `BladID` int DEFAULT NULL,
  `ArtID` int DEFAULT NULL,
  `PicID` int DEFAULT NULL,
  `Dimen` varchar(255) DEFAULT NULL,
  `Side` smallint DEFAULT NULL,
  `PicTitBlad` longtext,
  `PicTxtBlad` longtext,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxBildeFart`
--

CREATE TABLE `tblxBildeFart` (
  `BildFartID` int NOT NULL,
  `PicID` int DEFAULT NULL,
  `FartID` int DEFAULT NULL,
  `Modell` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxBildeOrg`
--

CREATE TABLE `tblxBildeOrg` (
  `BildOrgID` int NOT NULL,
  `PicID` int DEFAULT NULL,
  `OrgID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxBildeTema`
--

CREATE TABLE `tblxBildeTema` (
  `BildTID` int NOT NULL,
  `PicID` int DEFAULT NULL,
  `TID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxBildeVerft`
--

CREATE TABLE `tblxBildeVerft` (
  `BildVerftID` int NOT NULL,
  `PicID` int DEFAULT NULL,
  `VerftID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxDPRef`
--

CREATE TABLE `tblxDPRef` (
  `ID` int NOT NULL,
  `BladID` int DEFAULT NULL,
  `ArtID` int DEFAULT NULL,
  `Kommentar` varchar(255) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxFartLink`
--

CREATE TABLE `tblxFartLink` (
  `FartLkID` int NOT NULL,
  `ObjID` int DEFAULT NULL,
  `FartID` int DEFAULT NULL,
  `LTID` int DEFAULT NULL,
  `LinkType` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `LinkInnh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Link` longtext,
  `SerNo` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxPersBlad`
--

CREATE TABLE `tblxPersBlad` (
  `BlPersID` int NOT NULL,
  `PersID` int DEFAULT NULL,
  `ArtID` int DEFAULT NULL,
  `PicID` int DEFAULT NULL,
  `BladID` int DEFAULT NULL,
  `Side` smallint DEFAULT NULL,
  `FokID` int DEFAULT NULL,
  `OmtaleStilling` varchar(50) DEFAULT NULL,
  `OmtaleFirma` varchar(100) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxPubForf`
--

CREATE TABLE `tblxPubForf` (
  `PuFoID` int NOT NULL,
  `PubID` int DEFAULT NULL,
  `PubForfID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblxVerftLink`
--

CREATE TABLE `tblxVerftLink` (
  `VerftLkID` int NOT NULL,
  `VerftID` int DEFAULT NULL,
  `LTID` int DEFAULT NULL,
  `LinkType` varchar(50) DEFAULT NULL,
  `LinkInnh` varchar(50) DEFAULT NULL,
  `Link` longtext,
  `SerNo` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzBildeDim`
--

CREATE TABLE `tblzBildeDim` (
  `ID` int NOT NULL,
  `Dimen` varchar(255) DEFAULT NULL,
  `A4` tinyint DEFAULT '0',
  `Brd` varchar(10) DEFAULT NULL,
  `Hyd` varchar(10) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzBruker`
--

CREATE TABLE `tblzBruker` (
  `BrukerID` int NOT NULL,
  `Passord` varchar(255) DEFAULT NULL,
  `Bruker` varchar(255) DEFAULT NULL,
  `BrukerNavn` varchar(255) DEFAULT NULL,
  `Admin` tinyint DEFAULT '0',
  `Redaksjon` tinyint DEFAULT '0',
  `Gjest` tinyint DEFAULT '0',
  `Filbane` varchar(255) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzFartDrift`
--

CREATE TABLE `tblzFartDrift` (
  `FartDriftID` int NOT NULL,
  `DriftMiddel` varchar(255) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzFartFunk`
--

CREATE TABLE `tblzFartFunk` (
  `FartFunkID` int NOT NULL,
  `TypeFunksjon` varchar(255) DEFAULT NULL,
  `FunkDet` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzFartSkrog`
--

CREATE TABLE `tblzFartSkrog` (
  `FartSkrogID` int NOT NULL,
  `TypeSkrog` varchar(255) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzFartType`
--

CREATE TABLE `tblzFartType` (
  `FTID` int NOT NULL,
  `TypeFork` varchar(3) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzFokusert`
--

CREATE TABLE `tblzFokusert` (
  `FokID` int NOT NULL,
  `FokusType` varchar(20) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzLinkType`
--

CREATE TABLE `tblzLinkType` (
  `LTID` int NOT NULL,
  `LinkType` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzNasjon`
--

CREATE TABLE `tblzNasjon` (
  `NaID` int NOT NULL,
  `Nasjon` varchar(25) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzRapporter`
--

CREATE TABLE `tblzRapporter` (
  `ID` int NOT NULL,
  `TabNr` int DEFAULT NULL,
  `Rapport` varchar(255) DEFAULT NULL,
  `RepNr` int DEFAULT NULL,
  `Redak` tinyint DEFAULT '0',
  `Gjest` tinyint DEFAULT '0',
  `FormatPdf` tinyint DEFAULT '0',
  `Tab0` tinyint DEFAULT '0',
  `Tab1` tinyint DEFAULT '0',
  `Tab2` tinyint DEFAULT '0',
  `Tab3` tinyint DEFAULT '0',
  `Tab4` tinyint DEFAULT '0',
  `Tab5` tinyint DEFAULT '0',
  `Tab6` tinyint DEFAULT '0',
  `Tab7` tinyint DEFAULT '0',
  `Tab8` tinyint DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzSpalte`
--

CREATE TABLE `tblzSpalte` (
  `SpID` int NOT NULL,
  `SpTittel` varchar(100) DEFAULT NULL,
  `TID` int DEFAULT NULL,
  `ForfID` int DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblzSpesType`
--

CREATE TABLE `tblzSpesType` (
  `SpesTypeID` int NOT NULL,
  `SpesType` varchar(50) DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwArtikkelPers`
-- (See below for the actual view)
--
CREATE TABLE `vwArtikkelPers` (
`PersID` int
,`ArtID` int
,`FokusType` varchar(20)
,`OmtaleStilling` varchar(50)
,`OmtaleFirma` varchar(100)
,`ENavn` varchar(50)
,`FNavn` varchar(50)
,`Navn` varchar(101)
,`PersTittel` varchar(50)
,`PersBorn` varchar(10)
,`PersYrke` varchar(50)
,`PersNasjon` varchar(50)
,`RelFartBedr` longtext
,`RelSted` longtext
,`TilNavn` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwArtikkelsBilder`
-- (See below for the actual view)
--
CREATE TABLE `vwArtikkelsBilder` (
`PicMotiv` varchar(255)
,`PicType` varchar(15)
,`PicMerk` longtext
,`ForfID` int
,`UsikkerForf` tinyint
,`OpphRett` varchar(255)
,`PicLenke` varchar(255)
,`TattYear` smallint
,`TattYearSenest` smallint
,`CaYear` tinyint
,`MStedID` int
,`DM` tinyint
,`DigMusID` varchar(20)
,`ArtID` int
,`PicID` int
,`Side` smallint
,`PicTitBlad` longtext
,`PicTxtBlad` longtext
,`StedTatt` varchar(255)
,`ForfNavn` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwArtikkelsFart`
-- (See below for the actual view)
--
CREATE TABLE `vwArtikkelsFart` (
`ArtID` int
,`FartID` int
,`FokusType` varchar(20)
,`FTIDNavn` int
,`TypeFork` varchar(3)
,`FartNavn` varchar(100)
,`Tilnavn` varchar(50)
,`FartVern` tinyint
,`FartNotater` longtext
,`UsikreData` tinyint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwArtikler`
-- (See below for the actual view)
--
CREATE TABLE `vwArtikler` (
`ArtID` int
,`BladID` int
,`Side` smallint
,`ArtTittel` varchar(255)
,`ArtUnderTittel` varchar(255)
,`ArtType` varchar(1)
,`Sprog` varchar(255)
,`Abstrakt` longtext
,`Kapitler` longtext
,`EksternRef` varchar(255)
,`ArtAntBilde` smallint
,`SpID` int
,`Merknad` longtext
,`BladNr` smallint
,`Lenke` varchar(50)
,`Year` smallint
,`YearNr` smallint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwBildeBruk`
-- (See below for the actual view)
--
CREATE TABLE `vwBildeBruk` (
`BladID` int
,`PicID` int
,`Dimen` varchar(255)
,`Side` smallint
,`PicTitBlad` longtext
,`PicTxtBlad` longtext
,`BladNr` smallint
,`Year` smallint
,`YearNr` smallint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwBilder`
-- (See below for the actual view)
--
CREATE TABLE `vwBilder` (
`PicID` int
,`PicMotiv` varchar(255)
,`PicType` varchar(15)
,`PicMerk` longtext
,`ForfID` int
,`UsikkerForf` tinyint
,`ForfNavn` varchar(255)
,`OpphRett` varchar(255)
,`PicLenke` varchar(255)
,`TattYear` smallint
,`TattYearSenest` smallint
,`CaYear` tinyint
,`StedTatt` varchar(255)
,`DigMusID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwBildesFart`
-- (See below for the actual view)
--
CREATE TABLE `vwBildesFart` (
`PicID` int
,`FartID` int
,`Modell` tinyint
,`BladNr` smallint
,`Side` smallint
,`Year` smallint
,`YearNr` smallint
,`Lenke` varchar(50)
,`PicTitBlad` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwBildesPers`
-- (See below for the actual view)
--
CREATE TABLE `vwBildesPers` (
`PicID` int
,`PersID` int
,`BladID` int
,`BladNr` smallint
,`Year` smallint
,`YearNr` smallint
,`Side` smallint
,`Lenke` varchar(50)
,`ENavn` varchar(50)
,`FNavn` varchar(50)
,`PersTittel` varchar(50)
,`PersBorn` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwBladAnnonsor`
-- (See below for the actual view)
--
CREATE TABLE `vwBladAnnonsor` (
`BladID` int
,`Side` smallint
,`AnnNavn` varchar(255)
,`AnnAvd` varchar(255)
,`WebSide` varchar(255)
,`AnnonseInnh` varchar(255)
,`AnnonseBilde` varchar(255)
,`AnnonseTekst` varchar(255)
,`AnnonseSize` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwBladBilde`
-- (See below for the actual view)
--
CREATE TABLE `vwBladBilde` (
`BladID` int
,`PicID` int
,`Dimen` varchar(255)
,`Side` smallint
,`PicTitBlad` longtext
,`PicTxtBlad` longtext
,`PicMotiv` varchar(255)
,`PicType` varchar(15)
,`UsikkerForf` tinyint
,`OpphRett` varchar(255)
,`PicLenke` varchar(255)
,`TattYear` smallint
,`TattYearSenest` smallint
,`CaYear` tinyint
,`DM` tinyint
,`DigMusID` varchar(20)
,`ForfNavn` varchar(255)
,`StedTatt` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwFartArtikler`
-- (See below for the actual view)
--
CREATE TABLE `vwFartArtikler` (
`FartID` int
,`FokusType` varchar(20)
,`ArtTittel` varchar(255)
,`ArtUnderTittel` varchar(255)
,`tblArtikkel_BladID` int
,`Side` smallint
,`Lenke` varchar(50)
,`Sprog` varchar(255)
,`Kapitler` longtext
,`EksternRef` varchar(255)
,`ArtAntBilde` smallint
,`Merknad` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwFartBilder`
-- (See below for the actual view)
--
CREATE TABLE `vwFartBilder` (
`FartID` int
,`PicID` int
,`Modell` tinyint
,`PicMotiv` varchar(255)
,`StedTatt` varchar(255)
,`PicType` varchar(15)
,`Dimen` varchar(255)
,`BladNr` smallint
,`Lenke` varchar(50)
,`ForfID` int
,`OpphRett` varchar(255)
,`TattYear` smallint
,`TattYearSenest` smallint
,`ForfNavn` varchar(255)
,`PicTitBlad` longtext
,`PicTxtBlad` longtext
,`ArtTittel` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwFartNavnDposten`
-- (See below for the actual view)
--
CREATE TABLE `vwFartNavnDposten` (
`tblFartNavn_FartID` int
,`TypeFork` varchar(3)
,`FartNavn` varchar(100)
,`YearNavn` smallint
,`MndNavn` smallint
,`ObjID` int
,`RegHavn` varchar(50)
,`RedID` int
,`Nasjon` varchar(25)
,`RedNavn` varchar(255)
,`Kallesignal` varchar(15)
,`FartVern` tinyint
,`PicFile` varchar(50)
,`FartNotater` longtext
,`Link` longtext
,`SerNo` int
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwFartObjSpes`
-- (See below for the actual view)
--
CREATE TABLE `vwFartObjSpes` (
`ObjID` int
,`FartObjNavn` varchar(100)
,`FTIDObj` int
,`IMO` int
,`StroketYear` smallint
,`StroketGrunn` varchar(50)
,`Typebetegn` tinyint
,`ObjNotater` longtext
,`UsikreData` tinyint
,`IngenData` tinyint
,`YearSpes` smallint
,`MndSpes` smallint
,`VerftID` int
,`Byggenr` smallint
,`FTIDSpes` int
,`FartFunkID` int
,`FartSkrogID` int
,`FartDriftID` int
,`FunkDetalj` varchar(50)
,`MotorEff` varchar(20)
,`MotorType` varchar(50)
,`MaxFart` smallint
,`Lengde` smallint
,`Bredde` smallint
,`Dypg` smallint
,`Tonnasje` int
,`Nybygg` tinyint
,`VerftNavn` varchar(255)
,`Sted` varchar(255)
,`NaID` int
,`AndreNavn` longtext
,`Nasjon` varchar(25)
,`TypeFork` varchar(3)
,`DriftMiddel` varchar(255)
,`TypeFunksjon` varchar(255)
,`TypeSkrog` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwForfatter`
-- (See below for the actual view)
--
CREATE TABLE `vwForfatter` (
`ForfNavn` varchar(255)
,`ForfID` int
,`ENavn` varchar(255)
,`FNavn` varchar(255)
,`PostAdresse` varchar(255)
,`Postnr` varchar(255)
,`Poststed` varchar(255)
,`Land` varchar(255)
,`Utland` tinyint
,`EpostAdresse` varchar(255)
,`AntArt` smallint
,`AntNot` smallint
,`AntSpalte` smallint
,`Fotograf` tinyint
,`Forfatter` tinyint
,`ForfBio` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwForfattersArtikler`
-- (See below for the actual view)
--
CREATE TABLE `vwForfattersArtikler` (
`ForfID` int
,`BladNr` smallint
,`Year` smallint
,`YearNr` smallint
,`BladID` int
,`Side` smallint
,`ArtTittel` varchar(255)
,`ArtUnderTittel` varchar(255)
,`Lenke` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwForfsArtikler`
-- (See below for the actual view)
--
CREATE TABLE `vwForfsArtikler` (
`ForfID` int
,`ArtID` int
,`BladID` int
,`Side` smallint
,`ArtTittel` varchar(255)
,`ArtUnderTittel` varchar(255)
,`ArtType` varchar(1)
,`BladNr` smallint
,`Year` smallint
,`YearNr` smallint
,`Lenke` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwFotograf`
-- (See below for the actual view)
--
CREATE TABLE `vwFotograf` (
`ForfNavn` varchar(255)
,`ForfID` int
,`ENavn` varchar(255)
,`FNavn` varchar(255)
,`PostAdresse` varchar(255)
,`Postnr` varchar(255)
,`Poststed` varchar(255)
,`Land` varchar(255)
,`Utland` tinyint
,`EpostAdresse` varchar(255)
,`AntBilde` smallint
,`Fotograf` tinyint
,`Forfatter` tinyint
,`ForfBio` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwFotografsBilder`
-- (See below for the actual view)
--
CREATE TABLE `vwFotografsBilder` (
`PicID` int
,`PicMotiv` varchar(255)
,`PicType` varchar(15)
,`PicMerk` longtext
,`ForfID` int
,`UsikkerForf` tinyint
,`OpphRett` varchar(255)
,`PicLenke` varchar(255)
,`TattYear` smallint
,`TattYearSenest` smallint
,`CaYear` tinyint
,`MStedID` int
,`DM` tinyint
,`DigMusID` varchar(20)
,`BladID` int
,`Dimen` varchar(255)
,`Side` smallint
,`PicTitBlad` longtext
,`PicTxtBlad` longtext
,`BladNr` smallint
,`Year` smallint
,`YearNr` smallint
,`Lenke` varchar(50)
,`StedTatt` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwFotogrBilder`
-- (See below for the actual view)
--
CREATE TABLE `vwFotogrBilder` (
`ForfID` int
,`PicMotiv` varchar(255)
,`PicType` varchar(15)
,`PicMerk` longtext
,`UsikkerForf` tinyint
,`OpphRett` varchar(255)
,`TattYear` smallint
,`TattYearSenest` smallint
,`CaYear` tinyint
,`StedTatt` varchar(255)
,`DigMusID` varchar(20)
,`BladNr` smallint
,`Year` smallint
,`YearNr` smallint
,`Lenke` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwOrgDposten`
-- (See below for the actual view)
--
CREATE TABLE `vwOrgDposten` (
`OrgID` int
,`VernOrg` tinyint
,`OrgNavn` varchar(255)
,`Adresse` varchar(255)
,`Postnr` varchar(255)
,`Poststed` varchar(255)
,`EpostAdresse` varchar(255)
,`WebSide` varchar(255)
,`FB` varchar(255)
,`Notater` longtext
,`AntArt` smallint
,`AntNot` smallint
,`AntSpalte` smallint
,`AntBilde` smallint
,`AntHoved` smallint
,`AntOmtalt` smallint
,`AntDP` smallint
,`StatOrg` tinyint
,`IkkeOrg` tinyint
,`Nedlagt` tinyint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwOrgFart`
-- (See below for the actual view)
--
CREATE TABLE `vwOrgFart` (
`OrgID` int
,`FartID` int
,`FartNavn` varchar(100)
,`TypeFork` varchar(3)
,`YearNavn` smallint
,`MndNavn` smallint
,`ObjID` int
,`RegHavn` varchar(50)
,`Nasjon` varchar(25)
,`Kallesignal` varchar(15)
,`FartVern` tinyint
,`FartNotater` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwPersArtikkel`
-- (See below for the actual view)
--
CREATE TABLE `vwPersArtikkel` (
`PersID` int
,`FokID` int
,`FokusType` varchar(20)
,`BladNr` smallint
,`Year` smallint
,`YearNr` smallint
,`ArtTittel` varchar(255)
,`ArtUnderTittel` varchar(255)
,`Side` smallint
,`Lenke` varchar(50)
,`OmtaleStilling` varchar(50)
,`OmtaleFirma` varchar(100)
,`Kapitler` longtext
,`ArtAntBilde` smallint
,`Merknad` longtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwPersBilde`
-- (See below for the actual view)
--
CREATE TABLE `vwPersBilde` (
`PersID` int
,`PB_PicID` int
,`BB_PicID` int
,`Dimen` varchar(255)
,`Side` smallint
,`PicTitBlad` longtext
,`PicTxtBlad` longtext
,`BladID` int
,`Lenke` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwPersDp`
-- (See below for the actual view)
--
CREATE TABLE `vwPersDp` (
`PersID` int
,`ENavn` varchar(50)
,`FNavn` varchar(50)
,`PersTittel` varchar(50)
,`PersBorn` varchar(10)
,`PersYrke` varchar(50)
,`PersNasjon` varchar(50)
,`RelFartBedr` longtext
,`RelSted` longtext
,`TilNavn` varchar(50)
,`Link` longtext
,`AntArt` smallint
,`AntBilde` smallint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwRederi`
-- (See below for the actual view)
--
CREATE TABLE `vwRederi` (
`RedID` int
,`RedNavn` varchar(255)
,`Sted` varchar(255)
,`Nasjon` varchar(25)
,`RegHavn` varchar(50)
,`RedSenere` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwTemaArtikkel`
-- (See below for the actual view)
--
CREATE TABLE `vwTemaArtikkel` (
`TID` int
,`BladID` int
,`Side` smallint
,`ArtTittel` varchar(255)
,`ArtUnderTittel` varchar(255)
,`ArtType` varchar(1)
,`Sprog` varchar(255)
,`Abstrakt` longtext
,`Kapitler` longtext
,`EksternRef` varchar(255)
,`Lenke` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwTemaBilde`
-- (See below for the actual view)
--
CREATE TABLE `vwTemaBilde` (
`PicID` int
,`TID` int
,`PicMotiv` varchar(255)
,`PicType` varchar(15)
,`PicMerk` longtext
,`ForfNavn` varchar(255)
,`UsikkerForf` tinyint
,`OpphRett` varchar(255)
,`TattYear` smallint
,`TattYearSenest` smallint
,`CaYear` tinyint
,`StedTatt` varchar(255)
,`DigMusID` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwVerftDposten`
-- (See below for the actual view)
--
CREATE TABLE `vwVerftDposten` (
`VerftID` int
,`VerftNavn` varchar(255)
,`Sted` varchar(255)
,`NaID` int
,`Nasjon` varchar(25)
,`AndreNavn` longtext
,`Etablert` smallint
,`Nedlagt` smallint
,`Merknad` varchar(255)
,`AntArt` smallint
,`AntNot` smallint
,`AntSpalte` smallint
,`AntProg` smallint
,`AntBilde` smallint
,`AntHoved` smallint
,`AntOmtalt` smallint
,`AntDP` smallint
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblAnnonse`
--
ALTER TABLE `tblAnnonse`
  ADD PRIMARY KEY (`PRID`),
  ADD KEY `tblAnnonse$IX_tblAnnonse_AnnID` (`AnnID`);

--
-- Indexes for table `tblAnnonsor`
--
ALTER TABLE `tblAnnonsor`
  ADD PRIMARY KEY (`AnnID`);

--
-- Indexes for table `tblArtikkel`
--
ALTER TABLE `tblArtikkel`
  ADD PRIMARY KEY (`ArtID`),
  ADD KEY `tblArtikkel$IX_tblArtikkel_BladID` (`BladID`);

--
-- Indexes for table `tblBilde`
--
ALTER TABLE `tblBilde`
  ADD PRIMARY KEY (`PicID`),
  ADD KEY `tblBilde$IX_tblBilde_MStedID` (`MStedID`);

--
-- Indexes for table `tblBlad`
--
ALTER TABLE `tblBlad`
  ADD PRIMARY KEY (`BladID`);

--
-- Indexes for table `tblFartBulk`
--
ALTER TABLE `tblFartBulk`
  ADD PRIMARY KEY (`TempID`),
  ADD KEY `tblFartBulk$IX_tblFartBulk_NaID` (`NaID`);

--
-- Indexes for table `tblFartNavn`
--
ALTER TABLE `tblFartNavn`
  ADD PRIMARY KEY (`FartID`),
  ADD KEY `tblFartNavn$IX_tblxFartNavn_FartID` (`FartID`),
  ADD KEY `tblFartNavn$IX_tblxFartNavn_FartNavn` (`FartNavn`),
  ADD KEY `tblFartNavn$IX_tblxFartNavn_FartObjID` (`ObjID`),
  ADD KEY `tblFartNavn$IX_tblxFartNavn_Yearnavn` (`YearNavn`);

--
-- Indexes for table `tblFartObj`
--
ALTER TABLE `tblFartObj`
  ADD PRIMARY KEY (`ObjID`);

--
-- Indexes for table `tblFartSpes`
--
ALTER TABLE `tblFartSpes`
  ADD PRIMARY KEY (`SpesID`),
  ADD KEY `tblFartSpes$IX_tblFartSpes_ObjID` (`ObjID`);

--
-- Indexes for table `tblFartTid`
--
ALTER TABLE `tblFartTid`
  ADD PRIMARY KEY (`TidID`),
  ADD KEY `tblFartTid$IX_tblFartTid_RedID` (`RedID`),
  ADD KEY `tblFartTid$IX_tblxFartTid_FartID` (`FartID`),
  ADD KEY `tblFartTid$IX_tblxFartTid_ObjID` (`ObjID`),
  ADD KEY `tblFartTid$IX_tblxFartTid_SpesID` (`SpesID`);

--
-- Indexes for table `tblForfatter`
--
ALTER TABLE `tblForfatter`
  ADD PRIMARY KEY (`ForfID`);

--
-- Indexes for table `tblForfBulk`
--
ALTER TABLE `tblForfBulk`
  ADD PRIMARY KEY (`TempFFID`);

--
-- Indexes for table `tblForlag`
--
ALTER TABLE `tblForlag`
  ADD PRIMARY KEY (`ForlID`);

--
-- Indexes for table `tblMotivSted`
--
ALTER TABLE `tblMotivSted`
  ADD PRIMARY KEY (`MStedID`);

--
-- Indexes for table `tblOrganisasjon`
--
ALTER TABLE `tblOrganisasjon`
  ADD PRIMARY KEY (`OrgID`);

--
-- Indexes for table `tblOrgBulk`
--
ALTER TABLE `tblOrgBulk`
  ADD PRIMARY KEY (`TempID`);

--
-- Indexes for table `tblOrgPerson`
--
ALTER TABLE `tblOrgPerson`
  ADD PRIMARY KEY (`OPID`),
  ADD KEY `tblOrgPerson$IX_tblOrgPerson` (`OrgID`);

--
-- Indexes for table `tblPersBulk`
--
ALTER TABLE `tblPersBulk`
  ADD PRIMARY KEY (`TempID`);

--
-- Indexes for table `tblPerson`
--
ALTER TABLE `tblPerson`
  ADD PRIMARY KEY (`PersID`);

--
-- Indexes for table `tblPubForfatter`
--
ALTER TABLE `tblPubForfatter`
  ADD PRIMARY KEY (`PubForfID`);

--
-- Indexes for table `tblPublikasjon`
--
ALTER TABLE `tblPublikasjon`
  ADD PRIMARY KEY (`PubID`),
  ADD KEY `tblPublikasjon$IX_tblPublikasjon_ForlID` (`ForlID`);

--
-- Indexes for table `tblRederi`
--
ALTER TABLE `tblRederi`
  ADD PRIMARY KEY (`RedID`);

--
-- Indexes for table `tblTema`
--
ALTER TABLE `tblTema`
  ADD PRIMARY KEY (`TID`);

--
-- Indexes for table `tblTrykkeri`
--
ALTER TABLE `tblTrykkeri`
  ADD PRIMARY KEY (`TrykkID`);

--
-- Indexes for table `tblVerft`
--
ALTER TABLE `tblVerft`
  ADD PRIMARY KEY (`VerftID`),
  ADD KEY `tblVerft$IX_tblVerft_NaID` (`NaID`);

--
-- Indexes for table `tblxAnnBlad`
--
ALTER TABLE `tblxAnnBlad`
  ADD PRIMARY KEY (`AnnBlID`),
  ADD KEY `tblxAnnBlad$IX_tblxAnnBlad_AnnID` (`AnnID`),
  ADD KEY `tblxAnnBlad$IX_tblxAnnBlad_BladID` (`BladID`);

--
-- Indexes for table `tblxAnnonseBlad`
--
ALTER TABLE `tblxAnnonseBlad`
  ADD PRIMARY KEY (`BlPRID`),
  ADD KEY `tblxAnnonseBlad$IX_tblxAnnonseBlad_BladID` (`BladID`),
  ADD KEY `tblxAnnonseBlad$IX_tblxAnnonseBlad_PRID` (`PRID`);

--
-- Indexes for table `tblxArtFart`
--
ALTER TABLE `tblxArtFart`
  ADD PRIMARY KEY (`ArtFartID`),
  ADD UNIQUE KEY `tblxArtFart$IX_tblxArtFart_ID` (`FartID`,`ArtID`,`SpesTypeID`),
  ADD KEY `tblxArtFart$IX_tblxArtFart_ArtID` (`ArtID`),
  ADD KEY `tblxArtFart$IX_tblxArtFart_FartID` (`FartID`),
  ADD KEY `tblxArtFart$IX_tblxArtFart_FokID` (`FokID`);

--
-- Indexes for table `tblxArtForf`
--
ALTER TABLE `tblxArtForf`
  ADD PRIMARY KEY (`ArtForfID`),
  ADD KEY `tblxArtForf$IX_tblxArtForf_ArtID` (`ArtID`),
  ADD KEY `tblxArtForf$IX_tblxArtForf_ForfID` (`ForfID`);

--
-- Indexes for table `tblxArtOrg`
--
ALTER TABLE `tblxArtOrg`
  ADD PRIMARY KEY (`ArtOrgID`),
  ADD KEY `tblxArtOrg$IX_tblxArtOrg_ArtID` (`OrgID`),
  ADD KEY `tblxArtOrg$IX_tblxArtOrg_FokID` (`FokID`),
  ADD KEY `tblxArtOrg$IX_tblxArtOrg_OrgID` (`OrgID`);

--
-- Indexes for table `tblxArtPub`
--
ALTER TABLE `tblxArtPub`
  ADD PRIMARY KEY (`ArtPubID`),
  ADD KEY `tblxArtPub$IX_tblxArtPub_ArtID` (`ArtID`),
  ADD KEY `tblxArtPub$IX_tblxArtPub_PubID` (`PubID`);

--
-- Indexes for table `tblxArtTema`
--
ALTER TABLE `tblxArtTema`
  ADD PRIMARY KEY (`ArtTEmID`);

--
-- Indexes for table `tblxArtVerft`
--
ALTER TABLE `tblxArtVerft`
  ADD PRIMARY KEY (`ArtVerftID`),
  ADD KEY `tblxArtVerft$IX_tblxArtVerft_ArtID` (`ArtID`),
  ADD KEY `tblxArtVerft$IX_tblxArtVerft_VerftID` (`VerftID`);

--
-- Indexes for table `tblxBildeBlad`
--
ALTER TABLE `tblxBildeBlad`
  ADD PRIMARY KEY (`BlPicID`),
  ADD KEY `tblxBildeBlad$IX_tblxBildeBlad_BlPicID` (`BlPicID`),
  ADD KEY `tblxBildeBlad$IX_tblxBildeBlad_BladID` (`BladID`),
  ADD KEY `tblxBildeBlad$IX_tblxBildeBlad_PicID` (`PicID`);

--
-- Indexes for table `tblxBildeFart`
--
ALTER TABLE `tblxBildeFart`
  ADD PRIMARY KEY (`BildFartID`),
  ADD KEY `tblxBildeFart$IX_tblxBildeFart_FartID` (`FartID`),
  ADD KEY `tblxBildeFart$IX_tblxBildeFart_PicID` (`PicID`);

--
-- Indexes for table `tblxBildeOrg`
--
ALTER TABLE `tblxBildeOrg`
  ADD PRIMARY KEY (`BildOrgID`),
  ADD KEY `tblxBildeOrg$IX_tblxBildeOrg_PicID` (`PicID`),
  ADD KEY `tblxBildeOrg$IX_tblxBildeOrg_OrgID` (`OrgID`);

--
-- Indexes for table `tblxBildeTema`
--
ALTER TABLE `tblxBildeTema`
  ADD PRIMARY KEY (`BildTID`),
  ADD KEY `tblxBildeTema$IX_tblxBildeTema_PicID` (`PicID`),
  ADD KEY `tblxBildeTema$IX_tblxBildeTema_TID` (`TID`);

--
-- Indexes for table `tblxBildeVerft`
--
ALTER TABLE `tblxBildeVerft`
  ADD PRIMARY KEY (`BildVerftID`);

--
-- Indexes for table `tblxDPRef`
--
ALTER TABLE `tblxDPRef`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblxFartLink`
--
ALTER TABLE `tblxFartLink`
  ADD PRIMARY KEY (`FartLkID`);

--
-- Indexes for table `tblxPersBlad`
--
ALTER TABLE `tblxPersBlad`
  ADD PRIMARY KEY (`BlPersID`),
  ADD KEY `tblxPersBlad$IX_tblxPersBlad_ArtID` (`ArtID`),
  ADD KEY `tblxPersBlad$IX_tblxPersBlad_BladID` (`BladID`),
  ADD KEY `tblxPersBlad$IX_tblxPersBlad_PersID` (`PersID`),
  ADD KEY `tblxPersBlad$IX_tblxPersBlad_PicID` (`PicID`);

--
-- Indexes for table `tblxPubForf`
--
ALTER TABLE `tblxPubForf`
  ADD PRIMARY KEY (`PuFoID`),
  ADD KEY `tblxPubForf$IX_tblxPubForf_PubForfID` (`PubForfID`),
  ADD KEY `tblxPubForf$IX_tblxPubForf_PubID` (`PubID`);

--
-- Indexes for table `tblxVerftLink`
--
ALTER TABLE `tblxVerftLink`
  ADD PRIMARY KEY (`VerftLkID`);

--
-- Indexes for table `tblzBildeDim`
--
ALTER TABLE `tblzBildeDim`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblzBruker`
--
ALTER TABLE `tblzBruker`
  ADD PRIMARY KEY (`BrukerID`),
  ADD KEY `tblzBruker$IX_tblpBruker_Bruker` (`Bruker`);

--
-- Indexes for table `tblzFartDrift`
--
ALTER TABLE `tblzFartDrift`
  ADD PRIMARY KEY (`FartDriftID`);

--
-- Indexes for table `tblzFartFunk`
--
ALTER TABLE `tblzFartFunk`
  ADD PRIMARY KEY (`FartFunkID`);

--
-- Indexes for table `tblzFartSkrog`
--
ALTER TABLE `tblzFartSkrog`
  ADD PRIMARY KEY (`FartSkrogID`);

--
-- Indexes for table `tblzFartType`
--
ALTER TABLE `tblzFartType`
  ADD PRIMARY KEY (`FTID`);

--
-- Indexes for table `tblzFokusert`
--
ALTER TABLE `tblzFokusert`
  ADD PRIMARY KEY (`FokID`);

--
-- Indexes for table `tblzLinkType`
--
ALTER TABLE `tblzLinkType`
  ADD PRIMARY KEY (`LTID`);

--
-- Indexes for table `tblzNasjon`
--
ALTER TABLE `tblzNasjon`
  ADD PRIMARY KEY (`NaID`);

--
-- Indexes for table `tblzRapporter`
--
ALTER TABLE `tblzRapporter`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblzSpalte`
--
ALTER TABLE `tblzSpalte`
  ADD PRIMARY KEY (`SpID`);

--
-- Indexes for table `tblzSpesType`
--
ALTER TABLE `tblzSpesType`
  ADD PRIMARY KEY (`SpesTypeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblAnnonse`
--
ALTER TABLE `tblAnnonse`
  MODIFY `PRID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblAnnonsor`
--
ALTER TABLE `tblAnnonsor`
  MODIFY `AnnID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblArtikkel`
--
ALTER TABLE `tblArtikkel`
  MODIFY `ArtID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblBilde`
--
ALTER TABLE `tblBilde`
  MODIFY `PicID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblBlad`
--
ALTER TABLE `tblBlad`
  MODIFY `BladID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblFartBulk`
--
ALTER TABLE `tblFartBulk`
  MODIFY `TempID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblFartNavn`
--
ALTER TABLE `tblFartNavn`
  MODIFY `FartID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblFartObj`
--
ALTER TABLE `tblFartObj`
  MODIFY `ObjID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblFartSpes`
--
ALTER TABLE `tblFartSpes`
  MODIFY `SpesID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblFartTid`
--
ALTER TABLE `tblFartTid`
  MODIFY `TidID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblForfatter`
--
ALTER TABLE `tblForfatter`
  MODIFY `ForfID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblForfBulk`
--
ALTER TABLE `tblForfBulk`
  MODIFY `TempFFID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblForlag`
--
ALTER TABLE `tblForlag`
  MODIFY `ForlID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblMotivSted`
--
ALTER TABLE `tblMotivSted`
  MODIFY `MStedID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblOrganisasjon`
--
ALTER TABLE `tblOrganisasjon`
  MODIFY `OrgID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblOrgBulk`
--
ALTER TABLE `tblOrgBulk`
  MODIFY `TempID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblOrgPerson`
--
ALTER TABLE `tblOrgPerson`
  MODIFY `OPID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblPersBulk`
--
ALTER TABLE `tblPersBulk`
  MODIFY `TempID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblPerson`
--
ALTER TABLE `tblPerson`
  MODIFY `PersID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblPubForfatter`
--
ALTER TABLE `tblPubForfatter`
  MODIFY `PubForfID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblPublikasjon`
--
ALTER TABLE `tblPublikasjon`
  MODIFY `PubID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblRederi`
--
ALTER TABLE `tblRederi`
  MODIFY `RedID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblTema`
--
ALTER TABLE `tblTema`
  MODIFY `TID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblTrykkeri`
--
ALTER TABLE `tblTrykkeri`
  MODIFY `TrykkID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblVerft`
--
ALTER TABLE `tblVerft`
  MODIFY `VerftID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxAnnBlad`
--
ALTER TABLE `tblxAnnBlad`
  MODIFY `AnnBlID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxAnnonseBlad`
--
ALTER TABLE `tblxAnnonseBlad`
  MODIFY `BlPRID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxArtFart`
--
ALTER TABLE `tblxArtFart`
  MODIFY `ArtFartID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxArtForf`
--
ALTER TABLE `tblxArtForf`
  MODIFY `ArtForfID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxArtOrg`
--
ALTER TABLE `tblxArtOrg`
  MODIFY `ArtOrgID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxArtPub`
--
ALTER TABLE `tblxArtPub`
  MODIFY `ArtPubID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxArtTema`
--
ALTER TABLE `tblxArtTema`
  MODIFY `ArtTEmID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxArtVerft`
--
ALTER TABLE `tblxArtVerft`
  MODIFY `ArtVerftID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxBildeBlad`
--
ALTER TABLE `tblxBildeBlad`
  MODIFY `BlPicID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxBildeFart`
--
ALTER TABLE `tblxBildeFart`
  MODIFY `BildFartID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxBildeOrg`
--
ALTER TABLE `tblxBildeOrg`
  MODIFY `BildOrgID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxBildeTema`
--
ALTER TABLE `tblxBildeTema`
  MODIFY `BildTID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxBildeVerft`
--
ALTER TABLE `tblxBildeVerft`
  MODIFY `BildVerftID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxDPRef`
--
ALTER TABLE `tblxDPRef`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxFartLink`
--
ALTER TABLE `tblxFartLink`
  MODIFY `FartLkID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxPersBlad`
--
ALTER TABLE `tblxPersBlad`
  MODIFY `BlPersID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxPubForf`
--
ALTER TABLE `tblxPubForf`
  MODIFY `PuFoID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblxVerftLink`
--
ALTER TABLE `tblxVerftLink`
  MODIFY `VerftLkID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzBildeDim`
--
ALTER TABLE `tblzBildeDim`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzBruker`
--
ALTER TABLE `tblzBruker`
  MODIFY `BrukerID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzFartDrift`
--
ALTER TABLE `tblzFartDrift`
  MODIFY `FartDriftID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzFartFunk`
--
ALTER TABLE `tblzFartFunk`
  MODIFY `FartFunkID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzFartSkrog`
--
ALTER TABLE `tblzFartSkrog`
  MODIFY `FartSkrogID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzFartType`
--
ALTER TABLE `tblzFartType`
  MODIFY `FTID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzFokusert`
--
ALTER TABLE `tblzFokusert`
  MODIFY `FokID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzLinkType`
--
ALTER TABLE `tblzLinkType`
  MODIFY `LTID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzNasjon`
--
ALTER TABLE `tblzNasjon`
  MODIFY `NaID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzRapporter`
--
ALTER TABLE `tblzRapporter`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzSpalte`
--
ALTER TABLE `tblzSpalte`
  MODIFY `SpID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblzSpesType`
--
ALTER TABLE `tblzSpesType`
  MODIFY `SpesTypeID` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `vwArtikkelPers`
--
DROP TABLE IF EXISTS `vwArtikkelPers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwArtikkelPers`  AS SELECT `tblxPersBlad`.`PersID` AS `PersID`, `tblxPersBlad`.`ArtID` AS `ArtID`, `tblzFokusert`.`FokusType` AS `FokusType`, `tblxPersBlad`.`OmtaleStilling` AS `OmtaleStilling`, `tblxPersBlad`.`OmtaleFirma` AS `OmtaleFirma`, `tblPerson`.`ENavn` AS `ENavn`, `tblPerson`.`FNavn` AS `FNavn`, concat(`tblPerson`.`FNavn`,' ',`tblPerson`.`ENavn`) AS `Navn`, `tblPerson`.`PersTittel` AS `PersTittel`, `tblPerson`.`PersBorn` AS `PersBorn`, `tblPerson`.`PersYrke` AS `PersYrke`, `tblPerson`.`PersNasjon` AS `PersNasjon`, `tblPerson`.`RelFartBedr` AS `RelFartBedr`, `tblPerson`.`RelSted` AS `RelSted`, `tblPerson`.`TilNavn` AS `TilNavn` FROM ((`tblxPersBlad` left join `tblzFokusert` on((`tblxPersBlad`.`FokID` = `tblzFokusert`.`FokID`))) left join `tblPerson` on((`tblxPersBlad`.`PersID` = `tblPerson`.`PersID`))) ORDER BY `tblxPersBlad`.`FokID` DESC, `tblPerson`.`ENavn` ASC, `tblPerson`.`FNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwArtikkelsBilder`
--
DROP TABLE IF EXISTS `vwArtikkelsBilder`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwArtikkelsBilder`  AS SELECT `tblBilde`.`PicMotiv` AS `PicMotiv`, `tblBilde`.`PicType` AS `PicType`, `tblBilde`.`PicMerk` AS `PicMerk`, `tblBilde`.`ForfID` AS `ForfID`, `tblBilde`.`UsikkerForf` AS `UsikkerForf`, `tblBilde`.`OpphRett` AS `OpphRett`, `tblBilde`.`PicLenke` AS `PicLenke`, `tblBilde`.`TattYear` AS `TattYear`, `tblBilde`.`TattYearSenest` AS `TattYearSenest`, `tblBilde`.`CaYear` AS `CaYear`, `tblBilde`.`MStedID` AS `MStedID`, `tblBilde`.`DM` AS `DM`, `tblBilde`.`DigMusID` AS `DigMusID`, `tblxBildeBlad`.`ArtID` AS `ArtID`, `tblxBildeBlad`.`PicID` AS `PicID`, `tblxBildeBlad`.`Side` AS `Side`, `tblxBildeBlad`.`PicTitBlad` AS `PicTitBlad`, `tblxBildeBlad`.`PicTxtBlad` AS `PicTxtBlad`, `tblMotivSted`.`StedTatt` AS `StedTatt`, `tblForfatter`.`ForfNavn` AS `ForfNavn` FROM (((`tblxBildeBlad` left join `tblBilde` on((`tblBilde`.`PicID` = `tblxBildeBlad`.`PicID`))) left join `tblMotivSted` on((`tblBilde`.`MStedID` = `tblMotivSted`.`MStedID`))) left join `tblForfatter` on((`tblBilde`.`ForfID` = `tblForfatter`.`ForfID`))) ORDER BY `tblxBildeBlad`.`Side` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwArtikkelsFart`
--
DROP TABLE IF EXISTS `vwArtikkelsFart`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwArtikkelsFart`  AS SELECT `tblxArtFart`.`ArtID` AS `ArtID`, `tblxArtFart`.`FartID` AS `FartID`, `tblzFokusert`.`FokusType` AS `FokusType`, `tblFartNavn`.`FTIDNavn` AS `FTIDNavn`, `tblzFartType`.`TypeFork` AS `TypeFork`, `tblFartNavn`.`FartNavn` AS `FartNavn`, `tblFartNavn`.`Tilnavn` AS `Tilnavn`, `tblFartNavn`.`FartVern` AS `FartVern`, `tblFartNavn`.`FartNotater` AS `FartNotater`, `tblFartNavn`.`UsikreData` AS `UsikreData` FROM (((`tblxArtFart` left join `tblzFokusert` on((`tblxArtFart`.`FokID` = `tblzFokusert`.`FokID`))) left join `tblFartNavn` on((`tblxArtFart`.`FartID` = `tblFartNavn`.`FartID`))) left join `tblzFartType` on((`tblFartNavn`.`FTIDNavn` = `tblzFartType`.`FTID`))) WHERE (`tblxArtFart`.`ArtID` > 1) ORDER BY `tblxArtFart`.`FokID` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `vwArtikler`
--
DROP TABLE IF EXISTS `vwArtikler`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwArtikler`  AS SELECT `tblArtikkel`.`ArtID` AS `ArtID`, `tblArtikkel`.`BladID` AS `BladID`, `tblArtikkel`.`Side` AS `Side`, `tblArtikkel`.`ArtTittel` AS `ArtTittel`, `tblArtikkel`.`ArtUnderTittel` AS `ArtUnderTittel`, `tblArtikkel`.`ArtType` AS `ArtType`, `tblArtikkel`.`Sprog` AS `Sprog`, `tblArtikkel`.`Abstrakt` AS `Abstrakt`, `tblArtikkel`.`Kapitler` AS `Kapitler`, `tblArtikkel`.`EksternRef` AS `EksternRef`, `tblArtikkel`.`ArtAntBilde` AS `ArtAntBilde`, `tblArtikkel`.`SpID` AS `SpID`, `tblArtikkel`.`Merknad` AS `Merknad`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Lenke` AS `Lenke`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr` FROM (`tblArtikkel` left join `tblBlad` on((`tblArtikkel`.`BladID` = `tblBlad`.`BladID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vwBildeBruk`
--
DROP TABLE IF EXISTS `vwBildeBruk`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwBildeBruk`  AS SELECT `tblxBildeBlad`.`BladID` AS `BladID`, `tblxBildeBlad`.`PicID` AS `PicID`, `tblxBildeBlad`.`Dimen` AS `Dimen`, `tblxBildeBlad`.`Side` AS `Side`, `tblxBildeBlad`.`PicTitBlad` AS `PicTitBlad`, `tblxBildeBlad`.`PicTxtBlad` AS `PicTxtBlad`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr` FROM (`tblxBildeBlad` left join `tblBlad` on((`tblxBildeBlad`.`BladID` = `tblBlad`.`BladID`))) ORDER BY `tblBlad`.`Year` ASC, `tblBlad`.`YearNr` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwBilder`
--
DROP TABLE IF EXISTS `vwBilder`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwBilder`  AS SELECT `tblBilde`.`PicID` AS `PicID`, `tblBilde`.`PicMotiv` AS `PicMotiv`, `tblBilde`.`PicType` AS `PicType`, `tblBilde`.`PicMerk` AS `PicMerk`, `tblBilde`.`ForfID` AS `ForfID`, `tblBilde`.`UsikkerForf` AS `UsikkerForf`, `tblForfatter`.`ForfNavn` AS `ForfNavn`, `tblBilde`.`OpphRett` AS `OpphRett`, `tblBilde`.`PicLenke` AS `PicLenke`, `tblBilde`.`TattYear` AS `TattYear`, `tblBilde`.`TattYearSenest` AS `TattYearSenest`, `tblBilde`.`CaYear` AS `CaYear`, `tblMotivSted`.`StedTatt` AS `StedTatt`, `tblBilde`.`DigMusID` AS `DigMusID` FROM ((`tblBilde` left join `tblMotivSted` on((`tblBilde`.`MStedID` = `tblMotivSted`.`MStedID`))) left join `tblForfatter` on((`tblBilde`.`ForfID` = `tblForfatter`.`ForfID`))) ORDER BY `tblBilde`.`PicMotiv` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwBildesFart`
--
DROP TABLE IF EXISTS `vwBildesFart`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwBildesFart`  AS SELECT `tblxBildeFart`.`PicID` AS `PicID`, `tblxBildeFart`.`FartID` AS `FartID`, `tblxBildeFart`.`Modell` AS `Modell`, `tblBlad`.`BladNr` AS `BladNr`, `tblxBildeBlad`.`Side` AS `Side`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr`, `tblBlad`.`Lenke` AS `Lenke`, `tblxBildeBlad`.`PicTitBlad` AS `PicTitBlad` FROM (`tblxBildeFart` left join (`tblxBildeBlad` left join `tblBlad` on((`tblBlad`.`BladID` = `tblxBildeBlad`.`BladID`))) on((`tblxBildeBlad`.`PicID` = `tblxBildeFart`.`PicID`))) WHERE (`tblxBildeFart`.`FartID` > 1) ORDER BY `tblBlad`.`Year` ASC, `tblBlad`.`YearNr` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwBildesPers`
--
DROP TABLE IF EXISTS `vwBildesPers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwBildesPers`  AS SELECT `tblxPersBlad`.`PicID` AS `PicID`, `tblxPersBlad`.`PersID` AS `PersID`, `tblxPersBlad`.`BladID` AS `BladID`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr`, `tblxPersBlad`.`Side` AS `Side`, `tblBlad`.`Lenke` AS `Lenke`, `tblPerson`.`ENavn` AS `ENavn`, `tblPerson`.`FNavn` AS `FNavn`, `tblPerson`.`PersTittel` AS `PersTittel`, `tblPerson`.`PersBorn` AS `PersBorn` FROM ((`tblxPersBlad` left join `tblBlad` on((`tblxPersBlad`.`BladID` = `tblBlad`.`BladID`))) left join `tblPerson` on((`tblxPersBlad`.`PersID` = `tblPerson`.`PersID`))) ORDER BY `tblBlad`.`Year` ASC, `tblBlad`.`YearNr` ASC, `tblxPersBlad`.`Side` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwBladAnnonsor`
--
DROP TABLE IF EXISTS `vwBladAnnonsor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwBladAnnonsor`  AS SELECT `tblxAnnonseBlad`.`BladID` AS `BladID`, `tblxAnnonseBlad`.`Side` AS `Side`, `tblAnnonsor`.`AnnNavn` AS `AnnNavn`, `tblAnnonsor`.`AnnAvd` AS `AnnAvd`, `tblAnnonsor`.`WebSide` AS `WebSide`, `tblAnnonse`.`AnnonseInnh` AS `AnnonseInnh`, `tblAnnonse`.`AnnonseBilde` AS `AnnonseBilde`, `tblAnnonse`.`AnnonseTekst` AS `AnnonseTekst`, `tblAnnonse`.`AnnonseSize` AS `AnnonseSize` FROM (`tblxAnnonseBlad` left join (`tblAnnonse` left join `tblAnnonsor` on((`tblAnnonsor`.`AnnID` = `tblAnnonse`.`AnnID`))) on((`tblAnnonse`.`PRID` = `tblxAnnonseBlad`.`PRID`))) ORDER BY `tblAnnonsor`.`AnnNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwBladBilde`
--
DROP TABLE IF EXISTS `vwBladBilde`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwBladBilde`  AS SELECT `tblxBildeBlad`.`BladID` AS `BladID`, `tblxBildeBlad`.`PicID` AS `PicID`, `tblxBildeBlad`.`Dimen` AS `Dimen`, `tblxBildeBlad`.`Side` AS `Side`, `tblxBildeBlad`.`PicTitBlad` AS `PicTitBlad`, `tblxBildeBlad`.`PicTxtBlad` AS `PicTxtBlad`, `tblBilde`.`PicMotiv` AS `PicMotiv`, `tblBilde`.`PicType` AS `PicType`, `tblBilde`.`UsikkerForf` AS `UsikkerForf`, `tblBilde`.`OpphRett` AS `OpphRett`, `tblBilde`.`PicLenke` AS `PicLenke`, `tblBilde`.`TattYear` AS `TattYear`, `tblBilde`.`TattYearSenest` AS `TattYearSenest`, `tblBilde`.`CaYear` AS `CaYear`, `tblBilde`.`DM` AS `DM`, `tblBilde`.`DigMusID` AS `DigMusID`, `tblForfatter`.`ForfNavn` AS `ForfNavn`, `tblMotivSted`.`StedTatt` AS `StedTatt` FROM (((`tblxBildeBlad` left join `tblBilde` on((`tblxBildeBlad`.`PicID` = `tblBilde`.`PicID`))) left join `tblForfatter` on((`tblBilde`.`ForfID` = `tblForfatter`.`ForfID`))) left join `tblMotivSted` on((`tblBilde`.`MStedID` = `tblMotivSted`.`MStedID`))) ORDER BY `tblxBildeBlad`.`Side` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwFartArtikler`
--
DROP TABLE IF EXISTS `vwFartArtikler`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwFartArtikler`  AS SELECT `tblxArtFart`.`FartID` AS `FartID`, `tblzFokusert`.`FokusType` AS `FokusType`, `tblArtikkel`.`ArtTittel` AS `ArtTittel`, `tblArtikkel`.`ArtUnderTittel` AS `ArtUnderTittel`, `tblArtikkel`.`BladID` AS `tblArtikkel_BladID`, `tblArtikkel`.`Side` AS `Side`, `tblBlad`.`Lenke` AS `Lenke`, `tblArtikkel`.`Sprog` AS `Sprog`, `tblArtikkel`.`Kapitler` AS `Kapitler`, `tblArtikkel`.`EksternRef` AS `EksternRef`, `tblArtikkel`.`ArtAntBilde` AS `ArtAntBilde`, `tblArtikkel`.`Merknad` AS `Merknad` FROM (((`tblxArtFart` left join `tblArtikkel` on((`tblxArtFart`.`ArtID` = `tblArtikkel`.`ArtID`))) left join `tblzFokusert` on((`tblxArtFart`.`FokID` = `tblzFokusert`.`FokID`))) left join `tblBlad` on((`tblArtikkel`.`BladID` = `tblBlad`.`BladID`))) WHERE (`tblxArtFart`.`ArtID` > 0) ORDER BY `tblxArtFart`.`FokID` DESC, `tblArtikkel`.`BladID` ASC, `tblArtikkel`.`Side` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwFartBilder`
--
DROP TABLE IF EXISTS `vwFartBilder`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwFartBilder`  AS SELECT `tblxBildeFart`.`FartID` AS `FartID`, `tblxBildeFart`.`PicID` AS `PicID`, `tblxBildeFart`.`Modell` AS `Modell`, `tblBilde`.`PicMotiv` AS `PicMotiv`, `tblMotivSted`.`StedTatt` AS `StedTatt`, `tblBilde`.`PicType` AS `PicType`, `tblxBildeBlad`.`Dimen` AS `Dimen`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Lenke` AS `Lenke`, `tblBilde`.`ForfID` AS `ForfID`, `tblBilde`.`OpphRett` AS `OpphRett`, `tblBilde`.`TattYear` AS `TattYear`, `tblBilde`.`TattYearSenest` AS `TattYearSenest`, `tblForfatter`.`ForfNavn` AS `ForfNavn`, `tblxBildeBlad`.`PicTitBlad` AS `PicTitBlad`, `tblxBildeBlad`.`PicTxtBlad` AS `PicTxtBlad`, `tblArtikkel`.`ArtTittel` AS `ArtTittel` FROM (((((`tblxBildeFart` left join `tblBilde` on((`tblBilde`.`PicID` = `tblxBildeFart`.`PicID`))) left join (`tblxBildeBlad` left join `tblBlad` on((`tblBlad`.`BladID` = `tblxBildeBlad`.`BladID`))) on((`tblBilde`.`PicID` = `tblxBildeBlad`.`PicID`))) left join `tblMotivSted` on((`tblBilde`.`MStedID` = `tblMotivSted`.`MStedID`))) left join `tblForfatter` on((`tblBilde`.`ForfID` = `tblForfatter`.`ForfID`))) left join `tblArtikkel` on((`tblxBildeBlad`.`ArtID` = `tblArtikkel`.`ArtID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vwFartNavnDposten`
--
DROP TABLE IF EXISTS `vwFartNavnDposten`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwFartNavnDposten`  AS SELECT `tblFartNavn`.`FartID` AS `tblFartNavn_FartID`, `tblzFartType`.`TypeFork` AS `TypeFork`, `tblFartNavn`.`FartNavn` AS `FartNavn`, `tblFartNavn`.`YearNavn` AS `YearNavn`, `tblFartNavn`.`MndNavn` AS `MndNavn`, `tblFartNavn`.`ObjID` AS `ObjID`, `tblFartTid`.`RegHavn` AS `RegHavn`, `tblFartTid`.`RedID` AS `RedID`, `tblzNasjon`.`Nasjon` AS `Nasjon`, `tblRederi`.`RedNavn` AS `RedNavn`, `tblFartTid`.`Kallesignal` AS `Kallesignal`, `tblFartNavn`.`FartVern` AS `FartVern`, `tblFartNavn`.`PicFile` AS `PicFile`, `tblFartNavn`.`FartNotater` AS `FartNotater`, `tblxFartLink`.`Link` AS `Link`, `tblxFartLink`.`SerNo` AS `SerNo` FROM (((((`tblFartNavn` left join `tblFartTid` on((`tblFartTid`.`FartID` = `tblFartNavn`.`FartID`))) left join `tblzNasjon` on((`tblFartTid`.`NaID` = `tblzNasjon`.`NaID`))) left join `tblRederi` on((`tblFartTid`.`RedID` = `tblRederi`.`RedID`))) left join `tblzFartType` on((`tblFartNavn`.`FTIDNavn` = `tblzFartType`.`FTID`))) left join `tblxFartLink` on((`tblFartNavn`.`FartID` = `tblxFartLink`.`FartID`))) WHERE ((`tblFartNavn`.`AntDP` > 0) AND (`tblFartTid`.`Navning` <> 0)) ORDER BY `tblFartNavn`.`FartNavn` ASC, `tblFartNavn`.`YearNavn` ASC, `tblFartNavn`.`MndNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwFartObjSpes`
--
DROP TABLE IF EXISTS `vwFartObjSpes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwFartObjSpes`  AS SELECT `tblFartObj`.`ObjID` AS `ObjID`, `tblFartObj`.`FartObjNavn` AS `FartObjNavn`, `tblFartObj`.`FTIDObj` AS `FTIDObj`, `tblFartObj`.`IMO` AS `IMO`, `tblFartObj`.`StroketYear` AS `StroketYear`, `tblFartObj`.`StroketGrunn` AS `StroketGrunn`, `tblFartObj`.`Typebetegn` AS `Typebetegn`, `tblFartObj`.`ObjNotater` AS `ObjNotater`, `tblFartObj`.`UsikreData` AS `UsikreData`, `tblFartObj`.`IngenData` AS `IngenData`, `tblFartSpes`.`YearSpes` AS `YearSpes`, `tblFartSpes`.`MndSpes` AS `MndSpes`, `tblFartSpes`.`VerftID` AS `VerftID`, `tblFartSpes`.`Byggenr` AS `Byggenr`, `tblFartSpes`.`FTIDSpes` AS `FTIDSpes`, `tblFartSpes`.`FartFunkID` AS `FartFunkID`, `tblFartSpes`.`FartSkrogID` AS `FartSkrogID`, `tblFartSpes`.`FartDriftID` AS `FartDriftID`, `tblFartSpes`.`FunkDetalj` AS `FunkDetalj`, `tblFartSpes`.`MotorEff` AS `MotorEff`, `tblFartSpes`.`MotorType` AS `MotorType`, `tblFartSpes`.`MaxFart` AS `MaxFart`, `tblFartSpes`.`Lengde` AS `Lengde`, `tblFartSpes`.`Bredde` AS `Bredde`, `tblFartSpes`.`Dypg` AS `Dypg`, `tblFartSpes`.`Tonnasje` AS `Tonnasje`, `tblFartSpes`.`Nybygg` AS `Nybygg`, `tblVerft`.`VerftNavn` AS `VerftNavn`, `tblVerft`.`Sted` AS `Sted`, `tblVerft`.`NaID` AS `NaID`, `tblVerft`.`AndreNavn` AS `AndreNavn`, `tblzNasjon`.`Nasjon` AS `Nasjon`, `tblzFartType`.`TypeFork` AS `TypeFork`, `tblzFartDrift`.`DriftMiddel` AS `DriftMiddel`, `tblzFartFunk`.`TypeFunksjon` AS `TypeFunksjon`, `tblzFartSkrog`.`TypeSkrog` AS `TypeSkrog` FROM (((((((`tblFartObj` left join `tblFartSpes` on((`tblFartObj`.`ObjID` = `tblFartSpes`.`ObjID`))) left join `tblVerft` on((`tblFartSpes`.`VerftID` = `tblVerft`.`VerftID`))) left join `tblzNasjon` on((`tblVerft`.`NaID` = `tblzNasjon`.`NaID`))) left join `tblzFartDrift` on((`tblFartSpes`.`FartDriftID` = `tblzFartDrift`.`FartDriftID`))) left join `tblzFartFunk` on((`tblFartSpes`.`FartFunkID` = `tblzFartFunk`.`FartFunkID`))) left join `tblzFartSkrog` on((`tblFartSpes`.`FartSkrogID` = `tblzFartSkrog`.`FartSkrogID`))) left join `tblzFartType` on((`tblFartObj`.`FTIDObj` = `tblzFartType`.`FTID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vwForfatter`
--
DROP TABLE IF EXISTS `vwForfatter`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwForfatter`  AS SELECT `tblForfatter`.`ForfNavn` AS `ForfNavn`, `tblForfatter`.`ForfID` AS `ForfID`, `tblForfatter`.`ENavn` AS `ENavn`, `tblForfatter`.`FNavn` AS `FNavn`, `tblForfatter`.`PostAdresse` AS `PostAdresse`, `tblForfatter`.`Postnr` AS `Postnr`, `tblForfatter`.`Poststed` AS `Poststed`, `tblForfatter`.`Land` AS `Land`, `tblForfatter`.`Utland` AS `Utland`, `tblForfatter`.`EpostAdresse` AS `EpostAdresse`, `tblForfatter`.`AntArt` AS `AntArt`, `tblForfatter`.`AntNot` AS `AntNot`, `tblForfatter`.`AntSpalte` AS `AntSpalte`, `tblForfatter`.`Fotograf` AS `Fotograf`, `tblForfatter`.`Forfatter` AS `Forfatter`, `tblForfatter`.`ForfBio` AS `ForfBio` FROM `tblForfatter` WHERE ((`tblForfatter`.`ForfID` > 1) AND (`tblForfatter`.`Forfatter` <> 0)) ORDER BY `tblForfatter`.`ENavn` ASC, `tblForfatter`.`FNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwForfattersArtikler`
--
DROP TABLE IF EXISTS `vwForfattersArtikler`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwForfattersArtikler`  AS SELECT `tblxArtForf`.`ForfID` AS `ForfID`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr`, `tblArtikkel`.`BladID` AS `BladID`, `tblArtikkel`.`Side` AS `Side`, `tblArtikkel`.`ArtTittel` AS `ArtTittel`, `tblArtikkel`.`ArtUnderTittel` AS `ArtUnderTittel`, `tblBlad`.`Lenke` AS `Lenke` FROM ((`tblxArtForf` left join `tblArtikkel` on((`tblxArtForf`.`ArtID` = `tblArtikkel`.`ArtID`))) left join `tblBlad` on((`tblArtikkel`.`BladID` = `tblBlad`.`BladID`))) ORDER BY `tblBlad`.`BladNr` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwForfsArtikler`
--
DROP TABLE IF EXISTS `vwForfsArtikler`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwForfsArtikler`  AS SELECT `tblxArtForf`.`ForfID` AS `ForfID`, `tblxArtForf`.`ArtID` AS `ArtID`, `tblArtikkel`.`BladID` AS `BladID`, `tblArtikkel`.`Side` AS `Side`, `tblArtikkel`.`ArtTittel` AS `ArtTittel`, `tblArtikkel`.`ArtUnderTittel` AS `ArtUnderTittel`, `tblArtikkel`.`ArtType` AS `ArtType`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr`, `tblBlad`.`Lenke` AS `Lenke` FROM ((`tblxArtForf` left join `tblArtikkel` on((`tblxArtForf`.`ArtID` = `tblArtikkel`.`ArtID`))) left join `tblBlad` on((`tblArtikkel`.`BladID` = `tblBlad`.`BladID`))) ORDER BY `tblArtikkel`.`BladID` ASC, `tblArtikkel`.`Side` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwFotograf`
--
DROP TABLE IF EXISTS `vwFotograf`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwFotograf`  AS SELECT `tblForfatter`.`ForfNavn` AS `ForfNavn`, `tblForfatter`.`ForfID` AS `ForfID`, `tblForfatter`.`ENavn` AS `ENavn`, `tblForfatter`.`FNavn` AS `FNavn`, `tblForfatter`.`PostAdresse` AS `PostAdresse`, `tblForfatter`.`Postnr` AS `Postnr`, `tblForfatter`.`Poststed` AS `Poststed`, `tblForfatter`.`Land` AS `Land`, `tblForfatter`.`Utland` AS `Utland`, `tblForfatter`.`EpostAdresse` AS `EpostAdresse`, `tblForfatter`.`AntBilde` AS `AntBilde`, `tblForfatter`.`Fotograf` AS `Fotograf`, `tblForfatter`.`Forfatter` AS `Forfatter`, `tblForfatter`.`ForfBio` AS `ForfBio` FROM `tblForfatter` WHERE ((`tblForfatter`.`ForfID` > 1) AND (`tblForfatter`.`Fotograf` <> 0)) ORDER BY `tblForfatter`.`ENavn` ASC, `tblForfatter`.`FNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwFotografsBilder`
--
DROP TABLE IF EXISTS `vwFotografsBilder`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwFotografsBilder`  AS SELECT `tblBilde`.`PicID` AS `PicID`, `tblBilde`.`PicMotiv` AS `PicMotiv`, `tblBilde`.`PicType` AS `PicType`, `tblBilde`.`PicMerk` AS `PicMerk`, `tblBilde`.`ForfID` AS `ForfID`, `tblBilde`.`UsikkerForf` AS `UsikkerForf`, `tblBilde`.`OpphRett` AS `OpphRett`, `tblBilde`.`PicLenke` AS `PicLenke`, `tblBilde`.`TattYear` AS `TattYear`, `tblBilde`.`TattYearSenest` AS `TattYearSenest`, `tblBilde`.`CaYear` AS `CaYear`, `tblBilde`.`MStedID` AS `MStedID`, `tblBilde`.`DM` AS `DM`, `tblBilde`.`DigMusID` AS `DigMusID`, `tblxBildeBlad`.`BladID` AS `BladID`, `tblxBildeBlad`.`Dimen` AS `Dimen`, `tblxBildeBlad`.`Side` AS `Side`, `tblxBildeBlad`.`PicTitBlad` AS `PicTitBlad`, `tblxBildeBlad`.`PicTxtBlad` AS `PicTxtBlad`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr`, `tblBlad`.`Lenke` AS `Lenke`, `tblMotivSted`.`StedTatt` AS `StedTatt` FROM (((`tblBilde` left join `tblxBildeBlad` on((`tblBilde`.`PicID` = `tblxBildeBlad`.`PicID`))) left join `tblBlad` on((`tblxBildeBlad`.`BladID` = `tblBlad`.`BladID`))) left join `tblMotivSted` on((`tblBilde`.`MStedID` = `tblMotivSted`.`MStedID`))) ORDER BY `tblBilde`.`TattYear` ASC, `tblBlad`.`BladNr` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwFotogrBilder`
--
DROP TABLE IF EXISTS `vwFotogrBilder`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwFotogrBilder`  AS SELECT `tblBilde`.`ForfID` AS `ForfID`, `tblBilde`.`PicMotiv` AS `PicMotiv`, `tblBilde`.`PicType` AS `PicType`, `tblBilde`.`PicMerk` AS `PicMerk`, `tblBilde`.`UsikkerForf` AS `UsikkerForf`, `tblBilde`.`OpphRett` AS `OpphRett`, `tblBilde`.`TattYear` AS `TattYear`, `tblBilde`.`TattYearSenest` AS `TattYearSenest`, `tblBilde`.`CaYear` AS `CaYear`, `tblMotivSted`.`StedTatt` AS `StedTatt`, `tblBilde`.`DigMusID` AS `DigMusID`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr`, `tblBlad`.`Lenke` AS `Lenke` FROM ((`tblBilde` left join (`tblxBildeBlad` left join `tblBlad` on((`tblBlad`.`BladID` = `tblxBildeBlad`.`BladID`))) on((`tblxBildeBlad`.`PicID` = `tblBilde`.`PicID`))) left join `tblMotivSted` on((`tblBilde`.`MStedID` = `tblMotivSted`.`MStedID`))) WHERE (`tblBilde`.`ForfID` > 1) ORDER BY `tblBilde`.`TattYear` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwOrgDposten`
--
DROP TABLE IF EXISTS `vwOrgDposten`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwOrgDposten`  AS SELECT `tblOrganisasjon`.`OrgID` AS `OrgID`, `tblOrganisasjon`.`VernOrg` AS `VernOrg`, `tblOrganisasjon`.`OrgNavn` AS `OrgNavn`, `tblOrganisasjon`.`Adresse` AS `Adresse`, `tblOrganisasjon`.`Postnr` AS `Postnr`, `tblOrganisasjon`.`Poststed` AS `Poststed`, `tblOrganisasjon`.`EpostAdresse` AS `EpostAdresse`, `tblOrganisasjon`.`WebSide` AS `WebSide`, `tblOrganisasjon`.`FB` AS `FB`, `tblOrganisasjon`.`Notater` AS `Notater`, `tblOrganisasjon`.`AntArt` AS `AntArt`, `tblOrganisasjon`.`AntNot` AS `AntNot`, `tblOrganisasjon`.`AntSpalte` AS `AntSpalte`, `tblOrganisasjon`.`AntBilde` AS `AntBilde`, `tblOrganisasjon`.`AntHoved` AS `AntHoved`, `tblOrganisasjon`.`AntOmtalt` AS `AntOmtalt`, `tblOrganisasjon`.`AntDP` AS `AntDP`, `tblOrganisasjon`.`StatOrg` AS `StatOrg`, `tblOrganisasjon`.`IkkeOrg` AS `IkkeOrg`, `tblOrganisasjon`.`Nedlagt` AS `Nedlagt` FROM `tblOrganisasjon` WHERE ((`tblOrganisasjon`.`VernOrg` <> 0) OR (`tblOrganisasjon`.`MuseumOrg` <> 0)) ORDER BY `tblOrganisasjon`.`VernOrg` ASC, `tblOrganisasjon`.`OrgNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwOrgFart`
--
DROP TABLE IF EXISTS `vwOrgFart`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwOrgFart`  AS SELECT `tblFartNavn`.`OrgID` AS `OrgID`, `tblFartNavn`.`FartID` AS `FartID`, `tblFartNavn`.`FartNavn` AS `FartNavn`, `tblzFartType`.`TypeFork` AS `TypeFork`, `tblFartNavn`.`YearNavn` AS `YearNavn`, `tblFartNavn`.`MndNavn` AS `MndNavn`, `tblFartNavn`.`ObjID` AS `ObjID`, `tblFartTid`.`RegHavn` AS `RegHavn`, `tblzNasjon`.`Nasjon` AS `Nasjon`, `tblFartTid`.`Kallesignal` AS `Kallesignal`, `tblFartNavn`.`FartVern` AS `FartVern`, `tblFartNavn`.`FartNotater` AS `FartNotater` FROM (((`tblFartNavn` left join `tblFartTid` on((`tblFartTid`.`FartID` = `tblFartNavn`.`FartID`))) left join `tblzNasjon` on((`tblFartTid`.`NaID` = `tblzNasjon`.`NaID`))) left join `tblzFartType` on((`tblFartNavn`.`FTIDNavn` = `tblzFartType`.`FTID`))) WHERE ((`tblFartNavn`.`AntDP` > 0) AND (`tblFartTid`.`Navning` <> 0)) ORDER BY `tblFartNavn`.`FartNavn` ASC, `tblFartNavn`.`YearNavn` ASC, `tblFartNavn`.`MndNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwPersArtikkel`
--
DROP TABLE IF EXISTS `vwPersArtikkel`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwPersArtikkel`  AS SELECT `tblxPersBlad`.`PersID` AS `PersID`, `tblxPersBlad`.`FokID` AS `FokID`, `tblzFokusert`.`FokusType` AS `FokusType`, `tblBlad`.`BladNr` AS `BladNr`, `tblBlad`.`Year` AS `Year`, `tblBlad`.`YearNr` AS `YearNr`, `tblArtikkel`.`ArtTittel` AS `ArtTittel`, `tblArtikkel`.`ArtUnderTittel` AS `ArtUnderTittel`, `tblArtikkel`.`Side` AS `Side`, `tblBlad`.`Lenke` AS `Lenke`, `tblxPersBlad`.`OmtaleStilling` AS `OmtaleStilling`, `tblxPersBlad`.`OmtaleFirma` AS `OmtaleFirma`, `tblArtikkel`.`Kapitler` AS `Kapitler`, `tblArtikkel`.`ArtAntBilde` AS `ArtAntBilde`, `tblArtikkel`.`Merknad` AS `Merknad` FROM (((`tblxPersBlad` left join `tblArtikkel` on((`tblxPersBlad`.`ArtID` = `tblArtikkel`.`ArtID`))) left join `tblzFokusert` on((`tblxPersBlad`.`FokID` = `tblzFokusert`.`FokID`))) left join `tblBlad` on((`tblxPersBlad`.`BladID` = `tblBlad`.`BladID`))) WHERE (`tblxPersBlad`.`ArtID` > 0) ORDER BY `tblxPersBlad`.`FokID` DESC, `tblxPersBlad`.`PersID` ASC, `tblBlad`.`BladNr` ASC, `tblArtikkel`.`Side` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwPersBilde`
--
DROP TABLE IF EXISTS `vwPersBilde`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwPersBilde`  AS SELECT `tblxPersBlad`.`PersID` AS `PersID`, `tblxPersBlad`.`PicID` AS `PB_PicID`, `tblxBildeBlad`.`PicID` AS `BB_PicID`, `tblxBildeBlad`.`Dimen` AS `Dimen`, `tblxBildeBlad`.`Side` AS `Side`, `tblxBildeBlad`.`PicTitBlad` AS `PicTitBlad`, `tblxBildeBlad`.`PicTxtBlad` AS `PicTxtBlad`, `tblxBildeBlad`.`BladID` AS `BladID`, `tblBlad`.`Lenke` AS `Lenke` FROM ((`tblxPersBlad` left join `tblxBildeBlad` on((`tblxPersBlad`.`PicID` = `tblxBildeBlad`.`PicID`))) left join `tblBlad` on((`tblxBildeBlad`.`BladID` = `tblBlad`.`BladID`))) WHERE ((`tblxPersBlad`.`PersID` > 1) AND (`tblxPersBlad`.`PicID` > 1)) ;

-- --------------------------------------------------------

--
-- Structure for view `vwPersDp`
--
DROP TABLE IF EXISTS `vwPersDp`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwPersDp`  AS SELECT `tblPerson`.`PersID` AS `PersID`, `tblPerson`.`ENavn` AS `ENavn`, `tblPerson`.`FNavn` AS `FNavn`, `tblPerson`.`PersTittel` AS `PersTittel`, `tblPerson`.`PersBorn` AS `PersBorn`, `tblPerson`.`PersYrke` AS `PersYrke`, `tblPerson`.`PersNasjon` AS `PersNasjon`, `tblPerson`.`RelFartBedr` AS `RelFartBedr`, `tblPerson`.`RelSted` AS `RelSted`, `tblPerson`.`TilNavn` AS `TilNavn`, `tblPerson`.`Link` AS `Link`, `tblPerson`.`AntArt` AS `AntArt`, `tblPerson`.`AntBilde` AS `AntBilde` FROM `tblPerson` WHERE (`tblPerson`.`AntDP` > 0) ORDER BY `tblPerson`.`ENavn` ASC, `tblPerson`.`FNavn` ASC, `tblPerson`.`PersBorn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwRederi`
--
DROP TABLE IF EXISTS `vwRederi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwRederi`  AS SELECT `tblRederi`.`RedID` AS `RedID`, `tblRederi`.`RedNavn` AS `RedNavn`, `tblRederi`.`Sted` AS `Sted`, `tblzNasjon`.`Nasjon` AS `Nasjon`, `tblRederi`.`RegHavn` AS `RegHavn`, `tblRederi`.`RedSenere` AS `RedSenere` FROM (`tblRederi` left join `tblzNasjon` on((`tblRederi`.`NaID` = `tblzNasjon`.`NaID`))) WHERE (`tblRederi`.`RedID` > 1) ORDER BY `tblRederi`.`RedNavn` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwTemaArtikkel`
--
DROP TABLE IF EXISTS `vwTemaArtikkel`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwTemaArtikkel`  AS SELECT `tblxArtTema`.`TID` AS `TID`, `tblArtikkel`.`BladID` AS `BladID`, `tblArtikkel`.`Side` AS `Side`, `tblArtikkel`.`ArtTittel` AS `ArtTittel`, `tblArtikkel`.`ArtUnderTittel` AS `ArtUnderTittel`, `tblArtikkel`.`ArtType` AS `ArtType`, `tblArtikkel`.`Sprog` AS `Sprog`, `tblArtikkel`.`Abstrakt` AS `Abstrakt`, `tblArtikkel`.`Kapitler` AS `Kapitler`, `tblArtikkel`.`EksternRef` AS `EksternRef`, `tblBlad`.`Lenke` AS `Lenke` FROM ((`tblxArtTema` left join `tblArtikkel` on((`tblxArtTema`.`ArtID` = `tblArtikkel`.`ArtID`))) left join `tblBlad` on((`tblArtikkel`.`BladID` = `tblBlad`.`BladID`))) WHERE ((`tblxArtTema`.`TID` > 1) AND (`tblArtikkel`.`BladID` > 0)) ORDER BY `tblArtikkel`.`ArtTittel` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwTemaBilde`
--
DROP TABLE IF EXISTS `vwTemaBilde`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwTemaBilde`  AS SELECT `tblxBildeTema`.`PicID` AS `PicID`, `tblxBildeTema`.`TID` AS `TID`, `tblBilde`.`PicMotiv` AS `PicMotiv`, `tblBilde`.`PicType` AS `PicType`, `tblBilde`.`PicMerk` AS `PicMerk`, `tblForfatter`.`ForfNavn` AS `ForfNavn`, `tblBilde`.`UsikkerForf` AS `UsikkerForf`, `tblBilde`.`OpphRett` AS `OpphRett`, `tblBilde`.`TattYear` AS `TattYear`, `tblBilde`.`TattYearSenest` AS `TattYearSenest`, `tblBilde`.`CaYear` AS `CaYear`, `tblMotivSted`.`StedTatt` AS `StedTatt`, `tblBilde`.`DigMusID` AS `DigMusID` FROM (((`tblxBildeTema` left join `tblBilde` on((`tblxBildeTema`.`PicID` = `tblBilde`.`PicID`))) left join `tblMotivSted` on((`tblBilde`.`MStedID` = `tblMotivSted`.`MStedID`))) left join `tblForfatter` on((`tblBilde`.`ForfID` = `tblForfatter`.`ForfID`))) ORDER BY `tblBilde`.`PicMotiv` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vwVerftDposten`
--
DROP TABLE IF EXISTS `vwVerftDposten`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gerhard`@`localhost` SQL SECURITY DEFINER VIEW `vwVerftDposten`  AS SELECT `tblVerft`.`VerftID` AS `VerftID`, `tblVerft`.`VerftNavn` AS `VerftNavn`, `tblVerft`.`Sted` AS `Sted`, `tblVerft`.`NaID` AS `NaID`, `tblzNasjon`.`Nasjon` AS `Nasjon`, `tblVerft`.`AndreNavn` AS `AndreNavn`, `tblVerft`.`Etablert` AS `Etablert`, `tblVerft`.`Nedlagt` AS `Nedlagt`, `tblVerft`.`Merknad` AS `Merknad`, `tblVerft`.`AntArt` AS `AntArt`, `tblVerft`.`AntNot` AS `AntNot`, `tblVerft`.`AntSpalte` AS `AntSpalte`, `tblVerft`.`AntProg` AS `AntProg`, `tblVerft`.`AntBilde` AS `AntBilde`, `tblVerft`.`AntHoved` AS `AntHoved`, `tblVerft`.`AntOmtalt` AS `AntOmtalt`, `tblVerft`.`AntDP` AS `AntDP` FROM (`tblVerft` left join `tblzNasjon` on((`tblVerft`.`NaID` = `tblzNasjon`.`NaID`))) WHERE ((`tblVerft`.`VerftID` > 1) AND (`tblVerft`.`AntDP` > 0)) ORDER BY `tblVerft`.`VerftNavn` ASC ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
