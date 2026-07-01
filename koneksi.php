<?php
if (!isset($koneksi)) {
    $envLoaded = false;
    if (file_exists(__DIR__ . '/.env')) {
        $envLines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($envLines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2) + [null, null];
            if ($key !== null) {
                $key = trim($key);
                $value = trim($value);
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
        $envLoaded = true;
    }

    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        if ($envLoaded) {
            try {
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
                $dotenv->load();
            } catch (Exception $e) {
                
            }
        }
    }

    $tidb_config = [
        'host' => $_ENV['DB_TIDB_HOST'] ?? getenv('DB_TIDB_HOST') ?? '',
        'db'   => $_ENV['DB_TIDB_DB'] ?? getenv('DB_TIDB_DB') ?? '',
        'user' => $_ENV['DB_TIDB_USER'] ?? getenv('DB_TIDB_USER') ?? '',
        'pass' => $_ENV['DB_TIDB_PASS'] ?? getenv('DB_TIDB_PASS') ?? '',
        'port' => (int) ($_ENV['DB_TIDB_PORT'] ?? getenv('DB_TIDB_PORT') ?? 4000),
    ];

    $local_config = [
        'host' => 'localhost',
        'db'   => 'db_voidtype',
        'user' => 'root',
        'pass' => '',
        'port' => 3306
    ];

    if (!function_exists('connectTiDBWithSSL')) {
        function connectTiDBWithSSL(array $cfg)
        {
            $conn = mysqli_init();
          
            mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
            mysqli_ssl_set($conn, null, null, null, null, null);
            $success = @mysqli_real_connect(
                $conn,
                $cfg['host'],
                $cfg['user'],
                $cfg['pass'],
                $cfg['db'],
                $cfg['port'],
                null,
                MYSQLI_CLIENT_SSL
            );
            return $success ? $conn : false;
        }
    }

    if (!function_exists('connectLocal')) {
        function connectLocal(array $cfg)
        {
            $conn = mysqli_init();
            mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
         
            $success = @mysqli_real_connect(
                $conn,
                $cfg['host'],
                $cfg['user'],
                $cfg['pass'],
                $cfg['db'],
                $cfg['port']
            );
            return $success ? $conn : false;
        }
    }

    $koneksi = null;
    $connectionErrors = [];

    if (!empty($tidb_config['host']) && !empty($tidb_config['db'])) {
        error_log("[DB] Attempting TiDB connection to: " . $tidb_config['host']);
        $koneksi = connectTiDBWithSSL($tidb_config);
        if ($koneksi) {
            error_log("[DB] TiDB connection successful");
        } else {
            $connectionErrors[] = "TiDB: " . mysqli_connect_error();
            error_log("[DB] TiDB connection failed: " . mysqli_connect_error());
        }
    }

    if (!$koneksi) {
        error_log("[DB] Attempting local connection to: " . $local_config['host']);
        $koneksi = connectLocal($local_config);
        if ($koneksi) {
            error_log("[DB] Local connection successful");
        } else {
            $connectionErrors[] = "Local: " . mysqli_connect_error();
            error_log("[DB] Local connection failed: " . mysqli_connect_error());
        }
    }

    if (!$koneksi) {
        $errorMsg = "Koneksi database gagal!\n";
        $errorMsg .= "Gagal terhubung ke:\n" . implode("\n", $connectionErrors) . "\n";
        $errorMsg .= "\nSolusi:\n";
        $errorMsg .= "1. Pastikan MySQL/XAMPP berjalan di localhost\n";
        $errorMsg .= "2. Atau set credentials TiDB di file .env\n";
        $errorMsg .= "3. Pastikan database sudah dibuat\n";
        error_log("[DB] CRITICAL: All database connections failed");
        die(nl2br(htmlspecialchars($errorMsg)));
    }

    mysqli_set_charset($koneksi, 'utf8mb4');
}

/** @var mysqli $koneksi */