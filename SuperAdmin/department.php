<?php
// Check if logged in
if (!isset($_SESSION['is_superadmin'])) {
    header("Location: login.php");
    exit();
}

$departments = [
"Animal and Range Science",
    "Natural Resources and Environmental Science",
    "Plant Sciences",
    "Agricultural Economics and Agribusiness",
    "Rural Development and Agricultural Extension",

    //College of Business and Economics (CBE)
    "Accounting",
    "Cooperatives",
    "Management",
    "Economics",
    "Public Administration and Development Management",

    //College of Computing and Informatics
    "Computer Science",
    "Information Science",
    "Information Technology",
    "Software Engineering",
    "Statistics",

    // College of Education and Behavioral Sciences
    "Pedagogy",
    "Special Needs",
    "Educational Planning and Management",
    "English Language Improvement Centre",

    // College of Health and Medical Sciences
    "Medicine",
    "Pharmacy",
    "Nursing and Midwifery",
    "Public Health",
    "Environmental Health Sciences",
    "Medical Laboratory Science",

    //College of Law
    "Law",

    // College of Natural and Computational Sciences
    "Biology",
    "Chemistry",
    "Mathematics",
    "Physics",

    //College of Social Sciences and Humanities
    "Afan Oromo, Literature and Communication",
    "Gender and Development Studies",
    "Foreign Languages and Journalism",
    "History and Heritage Management",
    "Geography and Environmental Studies",
    "Sociology",

    // College of Veterinary Medicine
    "Veterinary Medicine",
    "Veterinary Laboratory Technology",

    // Haramaya Institute of Technology
    "Agricultural Engineering",
    "Water Resources and Irrigation Engineering",
    "Civil Engineering",
    "Electrical and Computer Engineering",
    "Mechanical Engineering",
    "Chemical Engineering",
    "Food Science and Post-Harvest Technology",
    "Food Technology and Process Engineering",

    // Sport Sciences Academy
    "Sport Sciences",

    "Land Administration",
    "Dairy and Meat Technology",
    "Forest Resource Management",
    "Soil Resources and Watershed Management",
];