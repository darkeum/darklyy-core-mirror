-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 07 2019 г., 12:22
-- Версия сервера: 5.6.41
-- Версия PHP: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `atry_constructor`
--

-- --------------------------------------------------------

--
-- Структура таблицы `constructor_block`
--

CREATE TABLE `constructor_block` (
  `id` int(11) NOT NULL,
  `h1` int(11) NOT NULL DEFAULT '0',
  `h2` int(11) NOT NULL DEFAULT '0',
  `c1` int(11) NOT NULL DEFAULT '0',
  `c2` int(11) NOT NULL DEFAULT '0',
  `t` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `image_block` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `menu` int(11) NOT NULL DEFAULT '0',
  `line` int(11) NOT NULL DEFAULT '0',
  `left` int(11) NOT NULL,
  `podpitka` int(11) NOT NULL,
  `uzel_ucheta` int(11) NOT NULL DEFAULT '0',
  `regul_perepad` int(11) NOT NULL DEFAULT '0',
  `bal_klapan` int(11) NOT NULL DEFAULT '0',
  `nasos` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `constructor_block`
--

INSERT INTO `constructor_block` (`id`, `h1`, `h2`, `c1`, `c2`, `t`, `name`, `image`, `image_block`, `type`, `menu`, `line`, `left`, `podpitka`, `uzel_ucheta`, `regul_perepad`, `bal_klapan`, `nasos`) VALUES
(1, 29, 29, 91, 91, 0, 'Блок ввода', 'block_vvoda.png', '1.png', 'block_vvoda', 1, 0, 22, 0, 0, 0, 0, 0),
(2, 29, 29, 91, 91, 0, 'Блок ввода', 'block_vvoda.png', '2.png', 'block_vvoda', 0, 0, 22, 0, 0, 0, 1, 0),
(3, 29, 29, 91, 91, 0, 'Блок ввода', '', '3.png', 'block_vvoda', 0, 0, 22, 0, 0, 1, 0, 0),
(4, 29, 29, 91, 91, 0, 'Блок ввода', '', '4.png', 'block_vvoda', 0, 0, 22, 0, 0, 1, 1, 0),
(5, 29, 29, 91, 91, 0, 'Блок ввода', '', '5.png', 'block_vvoda', 0, 0, 22, 0, 1, 0, 0, 0),
(6, 29, 29, 91, 91, 0, 'Блок ввода', '', '6.png', 'block_vvoda', 0, 0, 22, 0, 1, 0, 1, 0),
(7, 29, 29, 91, 91, 0, 'Блок ввода', '', '7.png', 'block_vvoda', 0, 0, 22, 0, 1, 1, 0, 0),
(8, 29, 29, 91, 91, 0, 'Блок ввода', '', '8.png', 'block_vvoda', 0, 0, 22, 0, 1, 1, 1, 0),
(9, 29, 29, 91, 91, 177, 'Блок ввода', '', '9.png', 'block_vvoda', 0, 1, 22, 0, 0, 0, 0, 0),
(10, 29, 29, 91, 91, 177, 'Блок ввода', '', '10.png', 'block_vvoda', 0, 1, 22, 0, 0, 0, 1, 0),
(11, 29, 29, 91, 91, 177, 'Блок ввода', '', '11.png', 'block_vvoda', 0, 1, 22, 0, 0, 1, 0, 0),
(12, 29, 29, 91, 91, 177, 'Блок ввода', '', '12.png', 'block_vvoda', 0, 1, 22, 0, 0, 1, 1, 0),
(13, 29, 29, 91, 91, 177, 'Блок ввода', '', '13.png', 'block_vvoda', 0, 1, 22, 0, 1, 0, 0, 0),
(14, 29, 29, 91, 91, 177, 'Блок ввода', '', '14.png', 'block_vvoda', 0, 1, 22, 0, 1, 0, 1, 0),
(15, 29, 29, 91, 91, 177, 'Блок ввода', '', '15.png', 'block_vvoda', 0, 1, 22, 0, 1, 1, 0, 0),
(16, 29, 29, 91, 91, 177, 'Блок ввода', '', '16.png', 'block_vvoda', 0, 1, 22, 0, 1, 1, 1, 0),
(17, 29, 29, 153, 153, 91, 'Блок <span>ГВС</span>', 'block_gvs.png', '1.png', 'block_gvs', 1, 1, 0, 0, 0, 0, 0, 0),
(18, 29, 29, 91, 91, 0, 'Блок <span>ГВС</span>', '', '2.png', 'block_gvs', 0, 0, 0, 0, 0, 0, 0, 0),
(19, 29, 29, 91, 91, 0, 'Блок отоплнения', 'block_otoplenie.png', '1.png', 'block_otopleniya', 1, 1, 0, 395, 0, 0, 0, 0),
(20, 29, 29, 91, 91, 0, 'Блок отоплнения', '', '2.png', 'block_otopleniya', 0, 0, 0, 0, 0, 0, 0, 0),
(21, 29, 29, 91, 91, 0, 'Блок отоплнения', '', '3.png', 'block_otopleniya', 0, 0, 0, 0, 0, 0, 0, 0),
(22, 88, 88, 91, 91, 0, 'Блок подпитки', 'block_podpitky.png', '1.png', 'block_podpitky', 1, 0, 226, 88, 0, 0, 0, 0),
(23, 88, 88, 91, 91, 0, 'Блок подпитки', '', '2.png', 'block_podpitky', 0, 0, 40, 88, 0, 0, 0, 0),
(24, 29, 29, 91, 91, 177, 'Блок ввода', '', '24.png', 'block_vvoda', 0, 1, 22, 0, 0, 0, 0, 1),
(25, 29, 29, 91, 91, 177, 'Блок ввода', '', '25.png', 'block_vvoda', 0, 1, 22, 0, 0, 0, 1, 1),
(26, 29, 29, 91, 91, 177, 'Блок ввода', '', '26.png', 'block_vvoda', 0, 1, 22, 0, 0, 1, 0, 1),
(27, 29, 29, 91, 91, 177, 'Блок ввода', '', '27.png', 'block_vvoda', 0, 1, 22, 0, 0, 1, 1, 1),
(28, 29, 29, 91, 91, 177, 'Блок ввода', '', '28.png', 'block_vvoda', 0, 1, 22, 0, 1, 0, 1, 1),
(29, 29, 29, 91, 91, 177, 'Блок ввода', '', '29.png', 'block_vvoda', 0, 1, 22, 0, 1, 1, 0, 1),
(30, 29, 29, 91, 91, 177, 'Блок ввода', '', '30.png', 'block_vvoda', 0, 1, 22, 0, 1, 1, 1, 1),
(31, 29, 29, 91, 91, 177, 'Блок ввода', '', '31.png', 'block_vvoda', 0, 1, 22, 0, 1, 0, 0, 1),
(32, 88, 88, 91, 91, 0, 'Блок подпитки', '', '3.png', '', 0, 0, 248, 88, 0, 0, 0, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `constructor_block`
--
ALTER TABLE `constructor_block`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `constructor_block`
--
ALTER TABLE `constructor_block`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
