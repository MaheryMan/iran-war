CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre_navigation VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,      
    meta_description VARCHAR(160),          
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contenus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    type_balise VARCHAR(10) NOT NULL, 
    valeur TEXT NOT NULL,
    alt_text VARCHAR(255),
    ordre INT NOT NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

