<?php
$senha = "12345678";
$hash = password_hash($senha, PASSWORD_DEFAULT);
echo "Hash da senha: " . $hash;