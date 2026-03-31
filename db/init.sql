CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre_navigation VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,      
    meta_description VARCHAR(1000) NOT NULL,
    category_id INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
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

CREATE TABLE utilisateurs(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO utilisateurs (username, password) VALUES ('admin', '$2y$10$iuUHScgpvSBkveav0cIivei3cU/mM.b.ItmAUzKc1iwjddGyuPtAK');

INSERT INTO categories (libelle, slug, description) VALUES 
('Actualites', 'actualites', 'Articles d\'actualite et nouvelles'),
('Politique', 'politique', 'Articles relatifs à la politique'),
('Societe', 'societe', 'Articles concernant la societe');