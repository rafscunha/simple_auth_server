--
-- Estrutura da tabela `auth_table`
--

CREATE TABLE `auth_table` (
  `auth_pk_id` int(11) NOT NULL,
  `auth_id_user` int(11) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `auth_token_refresh` varchar(255) NOT NULL,
  `auth_time_create` datetime(6) NOT NULL,
  `auth_time_expered` int(11) NOT NULL,
  `auth_timeout` datetime(6) NOT NULL,
  `auth_flag_ativo` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rl_auth_user`
--

CREATE TABLE `rl_auth_user` (
  `rl_auth_id` int(11) NOT NULL,
  `name_table_user` varchar(45) NOT NULL,
  `name_colum_user` varchar(45) NOT NULL,
  `name_colum_login` varchar(45) NOT NULL,
  `name_colum_pass` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `rl_auth_user`
--

INSERT INTO `rl_auth_user` (`rl_auth_id`, `name_table_user`, `name_colum_user`, `name_colum_login`, `name_colum_pass`) VALUES
(0, 'usuario', 'usuario_id', 'usuario_login', 'usuario_senha');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL,
  `usuario_login` varchar(45) NOT NULL,
  `usuario_senha` varchar(45) NOT NULL,
  `usuario_nome` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `auth_table`
--
ALTER TABLE `auth_table`
  ADD PRIMARY KEY (`auth_pk_id`),
  ADD UNIQUE KEY `auth_pk_id` (`auth_pk_id`),
  ADD UNIQUE KEY `auth_token` (`auth_token`),
  ADD KEY `auth_id_user` (`auth_id_user`) USING BTREE;

--
-- Índices para tabela `rl_auth_user`
--
ALTER TABLE `rl_auth_user`
  ADD PRIMARY KEY (`rl_auth_id`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `usuario_id_UNIQUE` (`usuario_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `auth_table`
--
ALTER TABLE `auth_table`
  MODIFY `auth_pk_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
