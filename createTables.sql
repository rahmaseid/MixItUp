Create TABLE users (
	user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE songs (
	song_id INT PRIMARY KEY AUTO_INCREMENT,
    tittle VARCHAR(255) NOT NULL,
    artist VARCHAR(255),
    file_path VARCHAR(255) NOT NULL,
    uploaded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE playlists (
	playlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL DEFAULT 'Untitled Mixtape',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE playlist_songs (
	id INT PRIMARY KEY AUTO_INCREMENT,
    playlist_id INT NOT NULL,
    song_id INT NOT NULL,
    position INT DEFAULT 0,
    FOREIGN KEY (playlist_id) REFERENCES playlists(playlist_id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(song_id) ON DELETE CASCADE

);