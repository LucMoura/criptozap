<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CriptoZap - Chat</title>
</head>
<body>
    <img src="./assents/Arte.png" alt="">
    <div id="container">
        <header>
                <span id="logo">Cripto</span>
                <span id="logo2">Zap</span>
        </header>
    <section id="body1">
        <div id="funcionalidades">
        <div id="caixa3">
            <h3>Criptografar</h3>
            <form class="formulario" action="" method="post">
                <input class="email" type="text" name="mensagem" placeholder="Digite a mensagem" required>
                <button class="botao" id="botao1" type="submit" name="action" value="encrypt">Criptografar</button>
            </form>
        </div>
        <div id="caixa4">
            <h3>Descriptografar</h3>
            <form class="formulario" action="" method="post">
                <input class="email" type="text" name="mensagem_criptografada" placeholder="Digite a mensagem" required>
                <button class="botao" id="botao2" type="submit" name="action" value="decrypt">Descriptografar</button>
            </form>
        </div>
</div>

        <div id="mensagens">
            <h3>Mensagens</h3>
            <?php
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

            function encrypt($data, $key) {
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
                $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
                return base64_encode($iv . $encrypted);
            }

            function decrypt($data, $key) {
                $data = base64_decode($data);
                $iv_length = openssl_cipher_iv_length('AES-256-CBC');
                $iv = substr($data, 0, $iv_length);
                $encrypted = substr($data, $iv_length);
                return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
            }

            $key = 'sua-chave-secreta-aqui';

            session_start();
            $userId = $_SESSION['user_id'] ?? null; 

            if ($userId === null) {
                die("Você precisa estar logado para acessar esta página.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($_POST['action'] === 'encrypt' && !empty($_POST['mensagem'])) {
                    $mensagem = $_POST['mensagem'];
                    $mensagemCriptografada = encrypt($mensagem, $key);

                    try {
                        $sql = "INSERT INTO mensagensCriptografadas (mensagem_criptografada, user_id, data) VALUES (:mensagem_criptografada, :user_id, NOW())";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':mensagem_criptografada', $mensagemCriptografada);
                        $stmt->bindParam(':user_id', $userId);
                        $stmt->execute();
                        echo "<p>Mensagem criptografada: " . htmlspecialchars($mensagemCriptografada) . "</p>";
                    } catch (PDOException $e) {
                        die("Erro ao inserir mensagem: " . $e->getMessage());
                    }
                }

                if ($_POST['action'] === 'decrypt' && !empty($_POST['mensagem_criptografada'])) {
                    $mensagemCriptografada = $_POST['mensagem_criptografada'];
                    $mensagemDescriptografada = decrypt($mensagemCriptografada, $key);

                    if ($mensagemDescriptografada !== false) {
                        echo "<p>Mensagem descriptografada: " . htmlspecialchars($mensagemDescriptografada) . "</p>";
                    } else {
                        echo "<p>Falha ao descriptografar a mensagem. Verifique se está correta.</p>";
                    }
                }
            }

            try {
                $sql = "SELECT mensagem_criptografada FROM mensagensCriptografadas WHERE user_id = :user_id ORDER BY data DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();
                $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($mensagens as $msg) {
                    $mensagemCriptografadaFromDb = $msg['mensagem_criptografada'];
                    echo "<div class='mensagem'><strong>Criptografada:</strong> " . htmlspecialchars($mensagemCriptografadaFromDb) . "</div>";
                }
            } catch (PDOException $e) {
                die("Erro ao buscar mensagens: " . $e->getMessage());
            }
            ?>
        </div>
    </section>
    </div>
</body>
</html>
