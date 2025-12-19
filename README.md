# 🚗 Travel with Shehana - Driving Experience Manager

A full-stack PHP/MySQL web application for logging, tracking, and analyzing supervised driving experiences. Built as part of the UFAZ 2025 L2 S1 Backend Development course project.

![Project Banner](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

## 🌟 Live Demo

**Website:** [https://shehana.alwaysdata.net](https://shehana.alwaysdata.net)

## 📋 Features

### Core Functionality
- **Log Driving Sessions** - Record date, time, distance, weather conditions, road types, and driving actions
- **View All Records** - Comprehensive table displaying all driving experiences with filtering options
- **Statistical Analysis** - Charts and summaries showing:
  - Total distance traveled
  - Distance breakdown by weather conditions
  - Action success rates
  - Average trip lengths by road category
- **Variable Management** - Dynamically add/delete:
  - Weather conditions (Sunny, Rainy, Foggy, etc.)
  - Fuel levels
  - Road categories
  - Surface types
  - Driving actions

### Technical Highlights
- Mobile-responsive design with Bootstrap 5
- Secure database operations with prepared statements
- Complex SQL queries with JOINs and aggregations
- CRUD operations (Create, Read, Update, Delete)
- Session-based user feedback system
- W3C compliant HTML5 with semantic elements

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL** - Relational database management
- **MySQLi** - Database interface with prepared statements

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Custom styling with Flexbox/Grid
- **Bootstrap 5** - Responsive framework
- **JavaScript** - Client-side validation

### Hosting & Deployment
- **AlwaysData** - Free web hosting platform
- **phpMyAdmin** - Database administration
- **FileZilla** - FTP file transfer

## 📊 Database Structure

The application uses a **normalized relational database** with 7 tables:

┌─────────────────┐ ┌──────────────┐ ┌─────────────┐
│ Weather_ │ │ Fuel_ │ │ Road_ │
│ Condition │ │ Status │ │ Category │
└────────┬────────┘ └──────┬───────┘ └──────┬──────┘
│ │ │
│ │ │
└──────────────┬───────┴─────────────────────┘
│
┌────▼─────┐
│ Drive_ │
│ Log │◄──────┐
└────┬─────┘ │
│ │
┌──────────────┘ │
│ │
┌────▼─────────┐ ┌─────▼────────┐
│ Surface_ │ │ Drive_Log_ │
│ Type │ │ Actions │
└──────────────┘ └─────┬────────┘
│
┌──────▼──────┐
│ Actions_ │
│ Taken │
└─────────────┘

### Key Tables:
- `Drive_Log` - Main table storing each driving session
- `Weather_Condition`, `Fuel_Status`, `Road_Category`, `Surface_Type` - Lookup tables
- `Actions_Taken` - Available driving maneuvers
- `Drive_Log_Actions` - Junction table for many-to-many relationships

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or hosting account

### Setup Instructions

1. **Clone the repository**

git clone https://github.com/sbayramli06/Travel_with_Shehana.git
cd Travel_with_Shehana


2. **Configure database connection**

cp config.example.php config.php


Edit `config.php` with your database credentials:

define('DB_HOST', 'your-database-host');
define('DB_USER', 'your-database-username');
define('DB_PASS', 'your-database-password');
define('DB_NAME', 'your-database-name');


3. **Import database schema**
- Open phpMyAdmin
- Create a new database
- Import `part1.sql` to create tables and sample data

4. **Deploy to web server**
- Upload all PHP files to your web server's public directory (`/www` or `/public_html`)
- Ensure `config.php` has correct permissions (644)

5. **Access the application**
- Navigate to your website URL
- Start logging driving experiences!

## 📁 Project Structure


## 📁 Project Structure

driving-experience/
├── css/
│   └── style.css              # Main stylesheet for the application
│
├── js/
│   └── script.js              # JavaScript for client-side interactions
│
├── .gitignore                 # Files and folders ignored by Git
│
├── add_drive.php              # Form page to add a new driving experience
├── process_drive.php          # Handles form submission and stores data in the database
│
├── view_drives.php            # Displays the list of recorded driving experiences
├── statistics.php             # Shows statistics and analysis of driving data
│
├── manage_variables.php       # Centralized configuration / variable management
│
├── index.php                  # Main entry point of the application
│
├── config.php                 # Database configuration (not tracked by Git)
├── config.example.php         # Example configuration file
│
├── test.php                   # Testing / debugging file
│
└── README.md                  # Project documentation



## 🔒 Security Features

- **Prepared Statements** - Protection against SQL injection
- **Input Sanitization** - Server-side validation of all user inputs
- **XSS Prevention** - Using `htmlspecialchars()` for output escaping
- **Password Protection** - Database credentials stored outside repository
- **Session Management** - Secure session handling for user messages

## 📊 Sample Queries

The application uses complex SQL queries including:


-- Total kilometers by weather condition
SELECT wc.Condition_Desc, ROUND(SUM(dl.Trip_Length), 2) AS Total_KM
FROM Drive_Log dl
INNER JOIN Weather_Condition wc ON dl.Condition_ID = wc.Condition_ID
GROUP BY wc.Condition_Desc
ORDER BY Total_KM DESC;

-- Action success rate analysis
SELECT at.Action_Type,
COUNT() AS Total_Attempts,
ROUND((SUM(at.WasSuccessful) / COUNT()) * 100, 2) AS Success_Rate
FROM Actions_Taken at
INNER JOIN Drive_Log_Actions dla ON at.Action_ID = dla.Action_ID
GROUP BY at.Action_Type;


## 🎨 Design Philosophy

- **User-Friendly Interface** - Clean, intuitive navigation with sidebar menu
- **Mobile-First Approach** - Responsive design adapting to all screen sizes
- **Consistent Branding** - Purple gradient theme ("Travel with Shehana")
- **Accessibility** - Semantic HTML5 elements and proper form labels

## 🏆 Technical Achievements

1. **Normalized Database Design** - 3NF compliance with proper foreign keys
2. **Complex Relationships** - Many-to-many junction table implementation
3. **Dynamic Content** - Database-driven dropdowns and forms
4. **Statistical Analysis** - Aggregate functions, GROUP BY, JOINs
5. **Full CRUD Operations** - Complete data lifecycle management
6. **Session-Based Feedback** - Non-intrusive user notifications
7. **Responsive CSS Grid/Flexbox** - No CSS frameworks dependency for layout
8. **W3C Validation** - Standards-compliant HTML5/CSS3

## 📝 Project Requirements Met

✅ Web form for entering driving experience  
✅ PHP script for recording data to database  
✅ Variable management (add new weather conditions, etc.)  
✅ Multiple summaries with tables and graphs  
✅ Complex MySQL queries on related tables  
✅ Filters by period, variable, and conditions  
✅ Hosted on remote web server (AlwaysData)  
✅ Functional URL with full access (no GitHub requirement)  
✅ Technical documentation (10-20 lines)  

## 👨‍💻 Author

**Shehana Bayramli**  
Computer Science student - UFAZ University 
UFAZ 2025 L2 S1 Backend Development Project  

📍 Baku, Azerbaijan  
📧 [bayramlishehana@gmail.com]  
🔗 [GitHub Profile](https://github.com/sbayramli06)

## 📄 License

This project was created for educational purposes as part of the UFAZ Backend Development course.

## 🙏 Acknowledgments

- UFAZ 2025 L2 S1 Backend Development Course
- AlwaysData for free hosting services
- Bootstrap team for the responsive framework
- MySQL and PHP communities

## 📸 Screenshots

### Dashboard
![Dashboard Screenshot]("C:\Users\Nilufar\Pictures\Screenshots\Screenshot 2025-12-19 201747.png")

### Add Driving Experience
![Add Drive Form]("C:\Users\Nilufar\Pictures\Screenshots\Screenshot 2025-12-19 201802.png")

### Statistics Page
![Statistics View]("C:\Users\Nilufar\Pictures\Screenshots\Screenshot 2025-12-19 201824.png")

---

**⭐ If you found this project helpful, please consider giving it a star!**


