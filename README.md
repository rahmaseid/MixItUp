# MixItUp

## Project Structure
```plaintext
mixtape-app/
├── frontend/
│   ├── Mixtape.html                # Main mixtape builder/player UI
│   ├── assets/
│   │   ├── style.css               # All styling, including theme switcher
│   │   ├── script.js               # JS for UI interactions
│   │   └── images/
│   │       └── vintage_radio.png   # Assets (tape images, icons, etc.)
│
├── backend/
│   ├── auth/
│   │   ├── login.php               # Login logic with hashed password
│   │   ├── register.php            # Register logic
│   │   ├── logout.php              # End session
│   │   └── session_check.php       # Protects private pages
│
│   ├── mixtape/
│   │   ├── create_playlist.php     # Save playlist to DB
│   │   ├── get_playlists.php       # Fetch past playlists for user
│   │   └── update_title.php        # Edit playlist title
│
│   └── includes/
│       ├── db.php                  # Database connection
│       └── helpers.php             # Common helper functions
│
├── sql/
│   ├── createTables.sql            # Database structure (users, playlists, songs)
│   └── seed.sql                    # Optional sample data
│
├── report/
│   ├── screenshots/                # UI screenshots for the report
│   └── final_report.pdf            # Final submitted project report
│
├── .gitignore
└── README.md
```



## Requirements
- XAMPP (for Apache server, MySQL, and phpMyAdmin)
- A web browser

---

## How to set up the environment?
  ### 1. Download & Install XAMPP
    - Go to the official XAMPP website: [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
    - Download the version best for your operating system

  ### 2. Launch XAAMP
    - Open the XAMPP Control Panel
    - Start the the following servers:
      - Apache
      - MySQL

  ### 3. Access phpMyAdmin
    - In the XAMPP Control Panel, clock **"Admin"** next to MySQL
    - This open **phpMyAdmin** in your web browser
    - From here, make sure the database 'mixtape_db' exists
      - If it doesn't, download the sql file directly from the github to access it
      - Then go to the **Import** tab and upload the SQL file with all the table definitions.

  Look for the file: **`createTables.sql`**


## Running the Program
### 4. Place your files
  - Place all files from the GitHub repo into your XAMPP 'htdocs' folder: Example path: 'C:\xampp\htdocs\4410-final'

### 5. Edit Database Connection
  - In 'db.php', make sure to edit the connection settings match your local database systems. Example:
    ```php
    $servername = "127.0.0.1:3308";
    $username = "root";
    $password = "";
    $dbname = "supplierpartshipment";
    $conn = new mysqli($servername, $username, $password, $dbname);

### 6. Access Website
- In your browser, visit:
   `http://localhost/<your-folder>/frontend/Mixtape.html`

