<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de funcionários</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilo.css" rel="stylesheet">
    <link href="assets/css/corporativo.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-mark bg-primary" aria-hidden="true">GI</span>
            Gestão Interna
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse py-3 py-lg-0" id="menuPrincipal">
            <div class="navbar-nav me-auto gap-1">
                <a class="nav-link px-3 rounded-2" href="index.php">Início</a>
                <a class="nav-link px-3 rounded-2" href="funcionarios/index.php">Funcionários</a>
            </div>
            <form class="navbar-search d-flex w-100 ms-lg-auto gap-2 mt-3 mt-lg-0" action="funcionarios/index.php" method="get" role="search">
                <label class="visually-hidden" for="busca-navbar">Buscar funcionários</label>
                <input class="form-control" id="busca-navbar" type="search" name="q" placeholder="Buscar funcionários..." aria-label="Buscar funcionários">
                <button class="btn btn-search" type="submit">Buscar</button>
            </form>
        </div>
    </div>
</nav>
<main class="container py-5">