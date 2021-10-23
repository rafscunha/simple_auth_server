 --- SCRIPTS PARA CRIAÇÃO DA BASE DE DADOS UTILIZADO PARA O SISTEMA DE AUTENTICAÇÃO

--Caso não tenho criado a base de dados, executar esse script, se não pular para linha 7
CREATE DATABASE `auth`;

--Caso ja tenho uma base de dados, adicionar as tabelas abaixo
CREATE TABLE `auth_table` (
  `auth_pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id_user` int(11) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `auth_token_refresh` varchar(255) NOT NULL,
  `auth_timeout` datetime NOT NULL,
  `auth_flag_ativo` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`auth_pk_id`),
  UNIQUE KEY `auth_token` (`auth_token`),
  KEY `auth_id_user` (`auth_id_user`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `black_list_login` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(15) NOT NULL,
  `n_tentativas` tinyint(4) NOT NULL,
  `prox_login` datetime(6) NOT NULL,
  `flag_ativo` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`pk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scope` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_login` int(11) NOT NULL,
  `scope` varchar(255) NOT NULL,
  `flag_ativo` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`pk_id`),
  UNIQUE KEY `login` (`fk_login`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `usuario` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`pk_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `scope_list` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `scope` varchar(50) NOT NULL,
  PRIMARY KEY (`pk_id`),
  UNIQUE KEY `scope` (`scope`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1