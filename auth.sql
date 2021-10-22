

CREATE TABLE `auth_table` (
  `auth_pk_id` int(11) NOT NULL,
  `auth_id_user` int(11) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `auth_token_refresh` varchar(255) NOT NULL,
  `auth_timeout` datetime NOT NULL,
  `auth_flag_ativo` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `black_list_login`
--

CREATE TABLE `black_list_login` (
  `pk_id` int(11) NOT NULL,
  `login` varchar(15) NOT NULL,
  `n_tentativas` tinyint(4) NOT NULL,
  `prox_login` datetime(6) NOT NULL,
  `flag_ativo` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `scope`
--

CREATE TABLE `scope` (
  `pk_id` int(11) NOT NULL,
  `fk_login` int(11) NOT NULL,
  `scope` varchar(255) NOT NULL,
  `flag_ativo` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `pk_id` int(11) NOT NULL,
  `login` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `auth_table`
--
ALTER TABLE `auth_table`
  ADD PRIMARY KEY (`auth_pk_id`),
  ADD UNIQUE KEY `auth_token` (`auth_token`),
  ADD KEY `auth_id_user` (`auth_id_user`) USING BTREE;

--
-- Índices para tabela `black_list_login`
--
ALTER TABLE `black_list_login`
  ADD PRIMARY KEY (`pk_id`);

--
-- Índices para tabela `scope`
--
ALTER TABLE `scope`
  ADD PRIMARY KEY (`pk_id`),
  ADD UNIQUE KEY `login` (`fk_login`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`pk_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `auth_table`
--
ALTER TABLE `auth_table`
  MODIFY `auth_pk_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `black_list_login`
--
ALTER TABLE `black_list_login`
  MODIFY `pk_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `scope`
--
ALTER TABLE `scope`
  MODIFY `pk_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `pk_id` int(11) NOT NULL AUTO_INCREMENT;
