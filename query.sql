CREATE TABLE songs (
    spotify_id VARCHAR(255) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    song_path VARCHAR(255),
    lyric_path VARCHAR(255)
);