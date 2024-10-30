<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CriptoZap - Login e Registro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <img src="./assents/Arte.png" alt="">    
        <div id="container">
        <header>
                <span id="logo">Cripto</span>
                <span id="logo2">Zap</span>
        </header>
        <section id="body">
            <div id="caixa1">
                <div id="login">
                    <h3>Login</h3>
                    <p>Coloque a sua conta</p>
                </div>
                <form action="" method="post">
                    <input class="email" type="email" name="email" placeholder="Digite seu email" required>
                    <input class="senha" type="password" name="senha" placeholder="Digite sua senha" required>
                    <button class="botao" type="submit" name="login">Login</button>
                </form>
            </div>
            <div id="caixa2">
                <h3>Crie sua Conta</h3>
                <form id="cadastro" action="" method="post">
                    <input id="nome" type="text" placeholder="Digite seu nome" required>
                    <input class="email" type="email" name="email_registro" placeholder="Digite seu email" required>
                    <input class="senha" type="password" name="senha_registro" placeholder="Digite sua senha" required>
                    <input class="senha" type="password" placeholder="Confirme sua senha" required>
                    <button class="botao" type="submit" name="registro">Registrar</button>
                </form>
            </div>
        </section>
    

    <?php
    session_start(); // Inicie a sessão aqui

    $host = 'localhost'; 
    $dbname = 'banco_php'; 
    $username = 'root'; 
    $password_db = '04100411LuBo!'; 

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }

    // Login
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email"; 
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha_criptografada'])) {
                // Armazena o ID do usuário na sessão
                $_SESSION['user_id'] = $usuario['id'];
                header("Location: cripto.php"); // Redireciona para a página de mensagens
                exit(); 
            } else {
                echo "<p>Email ou senha incorretos. Tente novamente.</p>";
            }
        } catch (PDOException $e) {
            die("Erro ao buscar usuário: " . $e->getMessage());
        }
    }

    // Registro
    if (isset($_POST['registro'])) {
        $email_registro = $_POST['email_registro'];
        $senha_registro = $_POST['senha_registro'];

        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email_registro"; 
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email_registro', $email_registro);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "<p>Email já está em uso. Escolha outro.</p>";
            } else {
                $senhaHash = password_hash($senha_registro, PASSWORD_DEFAULT);

                $sql = "INSERT INTO usuarios (email, senha_criptografada) VALUES (:email_registro, :senhaHash)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':email_registro', $email_registro);
                $stmt->bindParam(':senhaHash', $senhaHash);

                if ($stmt->execute()) {
                    echo "<p>Registro bem-sucedido! Você já pode fazer login.</p>";
                } else {
                    echo "<p>Erro ao registrar usuário.</p>";
                }
            }
        } catch (PDOException $e) {
            die("Erro ao verificar ou registrar usuário: " . $e->getMessage());
        }
    }
    ?>
    </div>
</body>
</html>
