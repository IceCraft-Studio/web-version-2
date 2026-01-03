CREATE TABLE `category` (
  `id` varchar(255) PRIMARY KEY,
  `name` varchar(255) NOT NULL
);

CREATE TABLE `role` (
  `id` varchar(255) PRIMARY KEY,
  `name` varchar(255) NOT NULL
);

CREATE TABLE `user` (
  `username` varchar(255) PRIMARY KEY,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `display_name` varchar(255),
  `email` varchar(255) UNIQUE,
  `social_reddit` varchar(255),
  `social_twitter` varchar(255),
  `social_instagram` varchar(255),
  `social_discord` varchar(255),
  `social_website` varchar(255),
  `datetime_created` datetime NOT NULL,
  FOREIGN KEY (`role`) REFERENCES `role`(`id`)
);

CREATE TABLE `project` (
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_modified` datetime NOT NULL,
  PRIMARY KEY (`category`, `slug`),
  FOREIGN KEY (`category`) REFERENCES `category`(`id`),
  FOREIGN KEY (`username`) REFERENCES `user`(`username`)
);

CREATE TABLE `project_gallery` (
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `caption` varchar(255),
  `in_gallery` BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (`category`, `slug`,`file_name`),
  FOREIGN KEY (`category`, `slug`)
    REFERENCES `project`(`category`, `slug`)
);

CREATE TABLE `project_link` (
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY (`category`, `slug`,`url`),
  FOREIGN KEY (`category`, `slug`)
    REFERENCES `project`(`category`, `slug`)
);

CREATE TABLE `project_upload` (
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY (`category`, `slug`,`file_name`),
  FOREIGN KEY (`category`, `slug`)
    REFERENCES `project`(`category`, `slug`)
);

CREATE TABLE `session` (
  `token` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`token`),
  FOREIGN KEY (`username`) REFERENCES `user`(`username`)
);