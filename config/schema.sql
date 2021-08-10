CREATE DATABASE minimalCMS;
USE minimalCMS;

CREATE TABLE page
(
    id      INT AUTO_INCREMENT,
    uri     VARCHAR(1024) NOT NULL,
    title   VARCHAR(128)  NOT NULL,
    content TEXT,
    PRIMARY KEY (id)
);
CREATE UNIQUE INDEX uri ON page (uri);
CREATE UNIQUE INDEX id ON page (id);

CREATE TABLE user
(
    id                 INT AUTO_INCREMENT,
    username           VARCHAR(64)   NOT NULL,
    password           VARCHAR(1024) NOT NULL,
    session_id         VARCHAR(256),
    session_expires_at DATETIME,
    PRIMARY KEY (id)
);
CREATE UNIQUE INDEX username ON user (username);
CREATE UNIQUE INDEX id ON user (id);