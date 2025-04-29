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
