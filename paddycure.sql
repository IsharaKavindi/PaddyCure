-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 10, 2026 at 02:07 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paddycure`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `tag` varchar(80) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id`, `title`, `body`, `image`, `tag`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'h', 'kkkkkkkkk', '3.png', 'Weather', '2026-03-09 11:43:51', '2026-03-09 11:44:09', '2026-03-09 11:44:09'),
(4, 'Dead Heart: The Hidden Enemy in Your Paddy Field', '## The Life Cycle of the Yellow Stem Borer\r\n\r\nThe adult is a yellowish-white moth that lays egg masses on rice leaves at night. These egg masses look like white cotton patches on the upper surface of leaves. After hatching, the young larvae crawl down the stem and bore inside. During the vegetative stage, the larva feeds on the central shoot, causing it to die — this is \"Dead Heart.\" During the reproductive stage, the same feeding on the panicle stem causes \"White Ear\" — empty, white panicles.\r\n\r\nThe larva pupates inside the stem, and the adult moth emerges to repeat the cycle. There are typically 3–4 generations per cropping season in Sri Lanka.\r\n\r\n## How to Identify Dead Heart\r\n\r\n- The central tiller turns yellow, then brown, and dries out while surrounding tillers remain green.\r\n- The dead tiller can be pulled out easily with a slight tug — it breaks cleanly at the base.\r\n- A small entry hole may be visible at the base of the tiller.\r\n- Do not confuse with drought stress — drought affects multiple tillers uniformly.\r\n\r\n## Economic Threshold\r\n\r\nSpray only when infestation exceeds 10% dead hearts OR when more than 1 egg mass is found per hill during early tillering. Unnecessary spraying kills natural enemies and leads to resistance.\r\n\r\n## Chemical Treatment\r\n\r\n- **Chlorantraniliprole 18.5% SC (Coragen)** — apply at 0.4 mL per litre. The most effective and safest modern insecticide for stem borer. Also available as Ferterra GR granules at 4 kg per acre.\r\n- **Carbosulfan 200 g/L SC (Marshal)** — apply at 2 mL per litre. Widely used in Sri Lanka; apply at early tillering when egg masses are first seen.\r\n- **Chlorpyrifos 20% EC** — apply at 2.5 mL per litre. Broad-spectrum contact and stomach insecticide; spray in the evening.\r\n- **Imidacloprid 17.8% SL (Confidor)** — apply at 0.5 mL per litre. Systemic; apply at 20–30 days after transplanting.\r\n\r\n## Cultural Management\r\n\r\n- After harvest, immediately collect and burn or incorporate rice stubble — larvae overwinter inside stems.\r\n- Raise the water level periodically to submerge newly hatched larvae.\r\n- Set up light traps (1 per hectare) to monitor and attract adult moths at night.\r\n- Remove egg masses by hand during early infestation in small fields.\r\n- Avoid late or staggered planting in the same area, as this provides a continuous food source for moths.\r\n- Encourage natural enemies — the egg parasitoid Trichogramma japonicum is highly effective against stem borer eggs.\r\n\r\n## Prevention\r\n\r\nUse balanced nitrogen fertilization — excess nitrogen produces lush, soft tissue that attracts egg-laying moths. Plant on time according to the DOA seasonal calendar. Use stem borer-tolerant varieties where available.', '4_1773078752.jpg', 'Disease Guide', '2026-03-09 11:44:58', '2026-03-09 17:52:32', NULL),
(5, 'Water Management in Paddy Farming', '## How Much Water Does Rice Actually Need?\r\n\r\nA paddy crop needs approximately 1,200–2,000 mm of water over the full growing season (about 100–120 days). This includes rainfall, irrigation, and percolation losses. However, the crop does not need to be flooded at all times — this is a common misconception.\r\n\r\n## Traditional vs. Modern Water Management\r\n\r\nTraditional continuous flooding keeps the field underwater from transplanting to harvest. While this suppresses weeds, it uses 30–40% more water than needed and increases methane emissions.\r\n\r\n**Alternate Wetting and Drying (AWD)** is the modern approach promoted by DOA Sri Lanka and IRRI. In AWD, the field is flooded to 5 cm above the soil surface, then allowed to dry until the water level drops to 15 cm below the soil surface (measured using a simple perforated pipe inserted in the field). Then it is re-flooded. This cycle is repeated throughout the season except during the critical periods below.\r\n\r\n## Critical Periods — Never Let the Field Dry\r\n\r\nAlways maintain 3–5 cm of standing water during:\r\n- Tillering stage (15–40 days after transplanting)\r\n- Panicle initiation stage (45–55 days)\r\n- Flowering and grain filling stage (65–85 days)\r\n\r\nDrought stress during these stages causes significant and irreversible yield loss.\r\n\r\n## Benefits of AWD\r\n\r\n- Saves 20–30% of irrigation water with no yield reduction\r\n- Reduces pumping costs for farmers using motor pumps\r\n- Reduces greenhouse gas (methane) emissions\r\n- Reduces the incidence of some root rots and sheath diseases\r\n- Improves soil aeration and root health\r\n\r\n## Drainage is as Important as Irrigation\r\n\r\nPoor drainage causes waterlogged soils, which:\r\n- Reduces oxygen availability for roots\r\n- Increases the severity of bacterial diseases like BLB\r\n- Causes nutrient deficiencies (especially zinc and potassium)\r\n- Makes it impossible to apply top-dress fertilizer efficiently\r\n\r\nEnsure your field has at least one functional drainage outlet. Periodically drain the field completely for 2–3 days before top-dressing fertilizer, then re-flood.\r\n\r\n## Practical Tips for Sri Lankan Farmers\r\n\r\n- Build and maintain proper bunds around your field to control water entry and exit.\r\n- Use a simple bamboo or PVC pipe as a field water tube to monitor the water table when practising AWD.\r\n- Coordinate with neighbouring farmers to manage irrigation turns fairly and efficiently.\r\n- During drought years in the dry zone, prioritise water during flowering — this single stage has the highest sensitivity to water stress.\r\n- After harvest, drain the field completely and allow it to dry — this breaks pest and disease cycles and improves soil structure for the next season.', '5_1773078664.jpeg', 'Irrigation', '2026-03-09 11:45:20', '2026-03-09 17:51:04', NULL),
(6, 'Blast Disease in Paddy', '## Identifying Blast Disease\r\n\r\nThe most recognisable symptom is diamond-shaped or spindle-shaped lesions on leaves. These spots have a grey or white centre with brown to reddish-brown borders. In severe cases, the lesions merge and the entire leaf dries out.\r\n\r\nBlast can also attack the neck of the panicle (neck blast), causing it to break and resulting in empty or partially filled grains. Node blast causes the nodes to turn black and break. These panicle and node infections are the most damaging forms.\r\n\r\n## When Does It Occur?\r\n\r\nBlast thrives in cool, humid, and foggy conditions — typically between 20°C and 28°C. It is most severe during the early morning when dew settles on leaves. Fields with excess nitrogen fertilizer and dense planting are especially vulnerable.\r\n\r\n## Chemical Treatment\r\n\r\nThe following fungicides are effective against blast and are available in Sri Lanka:\r\n\r\n- **Tricyclazole 75% WP (Beam / Blascide)** — apply at 0.6 g per litre of water. This is the most widely used fungicide for blast in Sri Lanka.\r\n- **Isoprothiolane 40% EC (Fuji-One)** — apply at 1.5 mL per litre. Systemic and effective for both leaf and neck blast.\r\n- **Propiconazole 25% EC (Tilt)** — apply at 1 mL per litre as a curative option.\r\n- **Azoxystrobin 23% SC (Amistar)** — apply at 1 mL per litre as a preventive spray.\r\n\r\nRepeat spraying after 10–14 days if disease pressure continues. Always spray in the early morning or late evening.\r\n\r\n## Cultural Management\r\n\r\n- Use blast-resistant varieties recommended by the Department of Agriculture (DOA) Sri Lanka, such as BG 300 and At 362.\r\n- Apply nitrogen fertilizer in split doses using the Leaf Colour Chart (LCC) — avoid a single heavy application.\r\n- Maintain proper plant spacing (20 × 20 cm) to allow air to circulate between plants.\r\n- Avoid overhead irrigation during high-humidity periods.\r\n- Deep-plough and destroy crop residues after harvest to eliminate fungal spores.', '6.jpg', 'Disease Guide', '2026-03-09 17:46:27', '2026-03-09 17:46:27', NULL),
(7, 'Understanding Bacterial Leaf Blight and How to Protect Your Crop', '## Symptoms to Watch For\r\n\r\nThe first sign of BLB is water-soaked yellowish to greyish lesions starting at the leaf margins. These lesions spread inward along the leaf, turning straw-yellow and eventually drying out. In young seedlings, the disease can cause a condition called \"kresek\" — where the entire seedling wilts and dies within days of transplanting.\r\n\r\nA useful diagnostic check: cut a diseased leaf and dip it in a glass of clean water. If bacterial ooze clouds the water, BLB is confirmed.\r\n\r\n## Conditions That Favour BLB\r\n\r\n- Heavy rainfall and strong winds that wound leaves\r\n- Waterlogged fields with poor drainage\r\n- Excess nitrogen fertilizer (especially urea)\r\n- High humidity above 70%\r\n- Dense planting\r\n\r\n## Chemical Treatment\r\n\r\nThere is no complete cure for BLB once it is established, but bactericides can slow its spread:\r\n\r\n- **Copper Oxychloride 50% WP** — apply at 3 g per litre of water as a foliar spray every 10–15 days.\r\n- **Streptomycin Sulfate + Tetracycline (Agrimycin / Streptocycline)** — mix at 200 ppm (1 g in 5 litres of water) and spray during early infection.\r\n- **Copper Hydroxide (Kocide)** — apply at 2 g per litre as an alternative.\r\n\r\nAvoid spraying during rain. Do not apply more than 3–4 times per season to reduce the risk of copper accumulation in the soil.\r\n\r\n## Cultural Management\r\n\r\n- Drain the field immediately when BLB symptoms are first seen. Reducing standing water is the single most effective management step.\r\n- Apply nitrogen in split doses — never apply a heavy top dressing during wet conditions.\r\n- Use BLB-resistant varieties from DOA Sri Lanka, such as BG 250, BG 360, and BG 379.\r\n- Remove and destroy infected plant material. Do not compost it.\r\n- Avoid working in the field when plants are wet — this spreads bacteria from plant to plant.\r\n\r\n## Prevention\r\n\r\nTreat seeds with Streptomycin solution (0.025%) before sowing. Use certified, disease-free seeds. Ensure proper field drainage before land preparation. Follow the recommended planting density and avoid broadcasting seed — transplanted crops are easier to manage.', '7.jpg', 'Disease Guide', '2026-03-09 17:47:52', '2026-03-09 17:47:52', NULL),
(8, 'Smart Fertilizer Management for Higher Paddy Yields in Sri Lanka', '## The Three Major Nutrients\r\n\r\n**Nitrogen (N)** drives leaf and stem growth and is the most important nutrient for rice. Too little causes yellowing and poor tillering. Too much causes lush soft growth, which attracts pests and diseases, increases lodging risk, and wastes money.\r\n\r\n**Phosphorus (P)** is critical for root development and early establishment. Apply most of the phosphorus as a basal (pre-planting) application.\r\n\r\n**Potassium (K)** strengthens cell walls, improves drought tolerance, and helps the plant resist diseases like brown spot and sheath blight. Potassium deficiency is common in Sri Lankan soils — many farmers overlook it.\r\n\r\n## DOA Recommended Fertilizer Programme (per acre)\r\n\r\n**Basal (at transplanting or 7–10 days after broadcasting):**\r\n- Urea: 25 kg\r\n- Triple Super Phosphate (TSP): 25 kg\r\n- Muriate of Potash (MOP): 25 kg\r\n\r\n**Top Dressing 1 (at active tillering, 21–25 days after transplanting):**\r\n- Urea: 25 kg\r\n\r\n**Top Dressing 2 (at panicle initiation, 45–50 days after transplanting):**\r\n- Urea: 25 kg\r\n- MOP: 25 kg\r\n\r\nAdjust rates based on soil test results and variety. High-yielding varieties like BG 300 and BG 352 may require higher rates.\r\n\r\n## Using the Leaf Colour Chart (LCC)\r\n\r\nThe Leaf Colour Chart (LCC) is a simple, free tool issued by DOA that compares the green colour of rice leaves to a set of printed panels. If the leaf is lighter than panel 3, apply urea immediately. If it matches panel 4 or darker, delay application. Using the LCC reduces nitrogen use by 20–30% without reducing yield.\r\n\r\n## Micronutrients to Watch\r\n\r\n**Zinc deficiency** is common in Sri Lankan paddy soils, especially in heavy clay soils and newly levelled fields. Symptoms include yellowing between leaf veins and stunted growth in young seedlings. Apply Zinc Sulphate at 10 kg per acre as a basal application.\r\n\r\n**Silicon** improves plant stiffness and resistance to blast and brown spot. Use rice hull ash or silica slag if available.\r\n\r\n## Common Mistakes to Avoid\r\n\r\n- Do not apply all nitrogen at once — this causes a flush of soft growth and increases disease risk.\r\n- Do not apply urea just before heavy rain — it will be washed away.\r\n- Do not skip potassium — many Sri Lankan farmers only use urea and TSP, leaving crops potassium-deficient.\r\n- Do not fertilize a waterlogged field — nutrients become unavailable in anaerobic conditions.\r\n\r\n## Organic Amendments\r\n\r\nIncorporating rice straw into the soil after ploughing returns nutrients and improves soil structure over time. Compost and well-rotted farmyard manure at 2–3 tonnes per acre as a basal application reduces the need for chemical fertilizers and improves water retention.', '8.jpg', 'Fertilizer', '2026-03-09 17:49:15', '2026-03-09 17:49:15', NULL),
(9, 'Increase Harvest', '1. Use Certified Disease-Resistant Seeds\r\nAlways plant certified seeds from reliable sources. Resistant varieties are scientifically bred to withstand common diseases like Blast and BLB. Before planting, treat seeds with hot water at 52°C for 10 minutes to kill pathogens. Clean seeds reduce the chance of soil and water contamination. This is the most cost-effective first line of defense.\r\n\r\n2. Practice Proper Field Sanitation\r\nAfter every harvest, remove and destroy all infected crop residues and stubble from the field. Plow the soil deeply to bury pathogens and expose them to sunlight. Remove weeds around the field as they can host disease-causing bacteria and fungi. Avoid leaving volunteer seedlings between seasons. A clean field dramatically reduces disease carryover to the next crop.\r\n\r\n3. Manage Water and Drainage Wisely\r\nPoor water management creates humid conditions that encourage fungal and bacterial diseases. Maintain proper drainage to avoid waterlogging, especially in the nursery stage. Drain the field during severe flooding to stop bacterial spread. Use drip or controlled irrigation to keep leaves dry. Dry conditions between irrigation cycles slow disease development significantly.\r\n\r\n4. Apply Fungicides and Bactericides at the Right Time\r\nEarly detection is key — spray approved fungicides like Tricyclazole or Isoprothiolane at the first sign of Blast. For bacterial diseases, copper-based bactericides are effective when applied at heading stage. Always rotate chemicals to prevent resistance from building up. Follow recommended dosages strictly to protect both the crop and the environment. Timely spraying at the right growth stage gives the best protection.\r\n\r\n5. Monitor Crops Regularly with AI Tools\r\nWalk through fields at least twice a week during the growing season to spot early symptoms. Use AI-powered tools like PaddyCare to photograph affected leaves and get instant disease identification. Early diagnosis allows farmers to act before the disease spreads to the whole field. Keep records of disease outbreaks to plan better for future seasons. Combining traditional observation with modern AI detection gives the strongest protection.', '9.jpg', 'Disease Guide', '2026-03-10 02:14:13', '2026-03-10 02:18:41', '2026-03-10 02:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `community_post`
--

DROP TABLE IF EXISTS `community_post`;
CREATE TABLE IF NOT EXISTS `community_post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `tag` varchar(60) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `community_post`
--

INSERT INTO `community_post` (`id`, `user_id`, `title`, `body`, `tag`, `image`, `likes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'hhhhhhhhhhh', 'hhhhhhhhhhhhhhhhhhhhhhhhhh', 'Disease', '', 1, '2026-03-10 02:29:04', '2026-03-10 03:11:20', '2026-03-10 03:11:20'),
(2, 3, 'hhhhhhhhhhh', 'hhhhhhhhhhhhhhhhhhhhhhhhhh', 'Disease', '', 1, '2026-03-10 02:30:10', '2026-03-10 03:11:06', '2026-03-10 03:11:06'),
(3, 3, 'xxxxxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxxx', 'Disease', '', 1, '2026-03-10 03:12:25', '2026-03-10 03:14:21', '2026-03-10 03:14:21'),
(4, 3, 'mmmmmmmmmmmmmm', 'mmmmmmmmmmm', '', '', 1, '2026-03-10 03:14:58', '2026-03-10 07:50:40', '2026-03-10 07:50:40'),
(5, 3, 'jmj', 'jjjjjjjjjjjjjjjj', '', '', 0, '2026-03-10 03:18:15', '2026-03-10 03:22:59', '2026-03-10 03:22:59'),
(6, 3, 'gggg', 'nnn', 'Pest Control', '', 0, '2026-03-10 05:27:21', '2026-03-10 07:50:37', '2026-03-10 07:50:37'),
(7, 3, 'My Field', 'share a tip', 'Harvest', 'post_69af803bb1e55.jpg', 0, '2026-03-10 07:51:47', '2026-03-10 07:51:47', NULL),
(8, 3, 'How to Do Irrigation properly', 'Save water with proper irrigation', 'Irrigation', 'post_69af80a20516c.jpeg', 0, '2026-03-10 07:53:30', '2026-03-10 08:09:04', '2026-03-10 08:09:04'),
(9, 3, 'Blast Disease', 'My crop affected blast, how long takes to recover it?', 'Disease', '', 1, '2026-03-10 07:56:58', '2026-03-10 08:03:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `community_post_comment`
--

DROP TABLE IF EXISTS `community_post_comment`;
CREATE TABLE IF NOT EXISTS `community_post_comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `community_post_comment`
--

INSERT INTO `community_post_comment` (`id`, `post_id`, `user_id`, `body`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 'mmm', '2026-03-10 02:57:12', '2026-03-10 02:57:12', NULL),
(2, 3, 3, 'mmmm', '2026-03-10 03:14:13', '2026-03-10 03:14:13', NULL),
(3, 4, 3, ',k,,,', '2026-03-10 03:18:00', '2026-03-10 03:18:00', NULL),
(4, 4, 3, 'kkk', '2026-03-10 03:18:05', '2026-03-10 03:18:05', NULL),
(5, 9, 3, 'about 1 month', '2026-03-10 08:04:18', '2026-03-10 08:04:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `community_post_like`
--

DROP TABLE IF EXISTS `community_post_like`;
CREATE TABLE IF NOT EXISTS `community_post_like` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `community_post_like`
--

INSERT INTO `community_post_like` (`id`, `post_id`, `user_id`) VALUES
(1, 2, 1),
(2, 1, 1),
(3, 3, 3),
(5, 4, 3),
(6, 9, 3);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Isharaa Kavindi', 'amal@gmail.com', 'Harvest Guidance', 'mmmmmmmmmmmmmmm', '2026-03-09 15:47:02', '2026-03-09 16:45:55', '2026-03-09 16:45:55'),
(2, 1, 'Ishara Kavindi', 'amal@gmail.com', 'Crop Disease Question', 'How long takes to recover from brown spot', '2026-03-10 06:38:31', '2026-03-10 06:38:31', NULL),
(3, 2, 'Thalawe Thakshmila', 'ishar444dara668@gmail.com', 'Account Help', 'Issue with my account', '2026-03-10 07:31:27', '2026-03-10 07:35:31', '2026-03-10 07:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(199) NOT NULL,
  `password` varchar(199) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `user_role` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `contact_number`, `user_role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ishara', 'Kavindi', 'amal@gmail.com', '$2y$10$672m2k/zzql32zPO8/QL6OrhuR8SuqNYufPZIgzak9Fxe9A.SaxxW', '+94771234563', 'ADMIN', '2026-03-09 15:06:48', '2026-03-10 07:20:53', NULL),
(2, 'Thalawe', 'Thakshmila', 'ishar444dara668@gmail.com', '$2y$10$FfKn0yot8Few79eKDF2et.Dlj3ezDM2CO04gV2uuAmZIGNtk6sP9q', '+94771234888', 'USER', '2026-03-09 15:30:38', '2026-03-09 16:30:57', '2026-03-09 16:30:57'),
(3, 'Kamal', 'Silava', 'admin@gmail.com', '$2y$10$wG.YCDRLyKQETxmLFIa6OekeS7l3TsfarLinzC0etDLx06kV1R9xq', '+9471423338', 'ADMIN', '2026-03-03 10:48:46', '2026-03-10 04:09:28', NULL),
(4, 'Shehani', 'Fernando', 'shey@gmail.com', '$2y$10$oGlDGPn5TmIWgMXIuWTB7OUwFY7GrieSK4SeLQSXDZEAiieVAE.CG', '0112367320', 'USER', '2026-03-09 23:32:35', '2026-03-09 23:32:35', NULL),
(5, 'Shani', 'Thakshmila', 'shanijayasundara668@gmail.com', '$2y$10$nd6AImSyEHMU3MvYhLKs..mPALU850723ztYnRG24ao7NivyrfaTG', '0112367300', 'USER', '2026-03-09 23:33:21', '2026-03-10 07:24:26', '2026-03-10 07:24:26'),
(6, 'Kapil', 'Silva', 'kapil@gmail.com', '$2y$10$yZoielrplAPgGZV2pd.dcOqhgGUmP21Z9PvHe0g2TCycMDa40E9ii', '+94771234999', 'USER', '2026-03-09 23:34:29', '2026-03-09 23:34:29', NULL),
(7, 'Sasara', 'Kavishan', 'sasara@gmail.com', '$2y$10$40MSHWpsz65/n81FYNa1WO4q30ztrq26xo6ggRe78wvpY8GWjZ/uu', '+94711134563', 'USER', '2026-03-09 23:35:23', '2026-03-09 23:35:23', NULL),
(8, 'Ishara', 'Kavindi', 'amallllll@gmail.com', '$2y$10$bTV7WUWSc7V3AAtpVF.dge4565IQ7pYBYyFXvasSo4Jlzb8IkmOlq', '+94771234563', 'USER', '2026-03-10 04:51:06', '2026-03-10 04:51:06', NULL),
(9, 'Kavindi', 'Perera', 'kavindi@gmail.com', '$2y$10$ohovmrF0tbhx8aIg.vFUzu41NseI6qrduUwA3mpWMWH1p5EWemR2u', '+94701234563', 'USER', '2026-03-10 05:44:45', '2026-03-10 05:44:45', NULL),
(10, 'Jon', 'Dias', 'jon@gmail.com', '$2y$10$4/UpaIvPIcpgnrOGjZlBJOmuaCrCfbkPcllbceZdFzEu8alCeF7CC', '+94771234561', 'USER', '2026-03-10 07:26:27', '2026-03-10 07:26:27', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
