<?php

CREATE TABLE Users
(
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    hash_pass VARCHAR(60) NOT NULL,
    PRIMARY KEY(id)
);

