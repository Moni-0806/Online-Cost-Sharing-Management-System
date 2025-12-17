

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assigned_costs`
--

CREATE TABLE `assigned_costs` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cost_type_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` enum('unpaid','partially_paid','paid') DEFAULT 'unpaid',
  `assigned_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cost_categories`
--

CREATE TABLE `cost_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `years_to_complete` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(55) NOT NULL,
  `id_number` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL,
  `batch` year(4) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `woreda` varchar(50) DEFAULT NULL,
  `tin_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `years_to_complete`, `description`) VALUES
(1, 'Computer Science', 4, 'Department of Computer Science and Engineering'),
(2, 'Business Administration', 4, 'Department of Business and Management'),
(3, 'Civil Engineering', 5, 'Department of Civil Engineering');

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `phone`, `id_number`, `username`, `department_id`, `batch`, `region`, `woreda`, `tin_number`) VALUES
(1, 'Ahmed Ali', '0911234567', 'STD001', 'ahmed.ali', 1, 2023, 'Addis Ababa', 'Bole', 'TIN001'),
(2, 'Fatima Hassan', '0922345678', 'STD002', 'fatima.hassan', 1, 2023, 'Oromia', 'Adama', 'TIN002'),
(3, 'Mohammed Yusuf', '0933456789', 'STD003', 'mohammed.yusuf', 2, 2024, 'Amhara', 'Bahir Dar', 'TIN003');

--
-- Dumping data for table `cost_categories`
--

INSERT INTO `cost_categories` (`id`, `name`, `description`) VALUES
(1, 'Tuition Fee', 'Annual tuition fee for students'),
(2, 'Registration Fee', 'One-time registration fee'),
(3, 'Library Fee', 'Annual library access fee'),
(4, 'Laboratory Fee', 'Laboratory usage and materials fee');

-- --------------------------------------------------------

--
-- Table structure for table `superadmin`
--

CREATE TABLE `superadmin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `assigned_costs`
--
ALTER TABLE `assigned_costs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`student_id`,`cost_type_id`,`academic_year`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `cost_categories`
--
ALTER TABLE `cost_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_student_number` (`id_number`),
  ADD KEY `idx_batch_year` (`batch`);

--
-- Indexes for table `superadmin`
--
ALTER TABLE `superadmin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assigned_costs`
--
ALTER TABLE `assigned_costs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_categories`
--
ALTER TABLE `cost_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `superadmin`
--
ALTER TABLE `superadmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `assigned_costs`
--
ALTER TABLE `assigned_costs`
  ADD CONSTRAINT `assigned_costs_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigned_costs_ibfk_2` FOREIGN KEY (`assigned_by`) REFERENCES `admins` (`id`);

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`id`, `username`, `password`, `name`, `phone`) VALUES
(1, 'superadmin', '$2y$10$yD14x9tq1CkZJxxNZe9yX.22MAPP2fw0WOVRAVRbyFja45TDgA4x.', 'Super Administrator', '0900000000');

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `phone`, `username`, `password`, `department_id`) VALUES
(1, 'John Doe', '0911111111', 'admin', '$2y$10$2uYEyumBGcwDq8OKgD1RFeeBa/XShzmA4GDtiZvR7cn0e5nqHpT7O', 1),
(2, 'Jane Smith', '0922222222', 'jane.smith', '$2y$10$2uYEyumBGcwDq8OKgD1RFeeBa/XShzmA4GDtiZvR7cn0e5nqHpT7O', 2);

COMMIT;


