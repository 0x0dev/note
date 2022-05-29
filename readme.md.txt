CREATE TABLE account (
	id int NOT NULL AUTO_INCREMENT,
	firstname varchar(30) NOT NULL,
	lastname varchar(30),
	email varchar(64),
	pwd_hash varchar(128),
	username varchar(64)
	PRIMARY KEY (id)
);

CREATE TABLE note (
	nid int NOT NULL AUTO_INCREMENT,
	aid int NOT NULL,
	title varchar(255),
	content varchar(24000),
	created_at date default current_timestamp,
	PRIMARY KEY (nid)
);