CREATE TABLE `user` (
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` int(2) NOT NULL,
  `email` varchar(255) UNIQUE,
  `social_reddit` varchar(255),
  `social_instagram` varchar(255),
  `social_discord` varchar(255),
  PRIMARY KEY (`username`)
);

CREATE TABLE `project` (
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`category`, `slug`)
);

CREATE TABLE `project_download` (
  `id` int AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
)

CREATE TABLE `category` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `user` (`username`, `password_hash` `role`) VALUES
('admin', 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec',2),
('pavel', '4d0b24ccade22df6d154778cd66baf04288aae26df97a961f3ea3dd616fbe06dcebecc9bbe4ce93c8e12dca21e5935c08b0954534892c568b8c12b92f26a2448',2);