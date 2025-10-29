<?php
/*  ----------------------------------------------------------------------------
    Travium Install UI
    - PHP 7.4
    ------------------------------------------------------------------------- */
error_reporting(E_ALL);

$errors = [];
$result = null;

function json_out($data, int $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// Load main/global config
$globalConfigPath = dirname(__DIR__,2) . '/config.php';
if (!file_exists($globalConfigPath)) {
    http_response_code(500);
    echo "<h1>config.php missing</h1><p>Expected at: {$globalConfigPath}</p>";
    exit;
}
require $globalConfigPath;

if(!isset($_GET['key']) || $_GET['key'] != $globalConfig['installer_key'])
{
  http_response_code(401);
  echo 'Unauthorized';
  exit;
}

$indexUrl = $globalConfig['staticParameters']['indexUrl'] ?? '';
$parsedHost = parse_url((strpos($indexUrl, '://')===false ? 'http://' . $indexUrl : $indexUrl), PHP_URL_HOST);
$host = $parsedHost ?: ($_SERVER['HTTP_HOST'] ?? 'localhost');
$host = preg_replace('/:\d+$/', '', $host);  // strip port if any
$host = preg_replace('/^www\./i', '', $host); // strip www.
$bgImage = "https://{$host}/dist/8c198efd2ffc51138cf1425c6156bcb4.jpg";

// Main DB creds from config
$mainDb = [
    'host'     => $globalConfig['dataSources']['globalDB']['hostname'] ?? 'localhost',
    'user'     => $globalConfig['dataSources']['globalDB']['username'] ?? '',
    'pass'     => $globalConfig['dataSources']['globalDB']['password'] ?? '',
    'name'     => $globalConfig['dataSources']['globalDB']['database'] ?? '',
    'charset'  => $globalConfig['dataSources']['globalDB']['charset'] ?? 'utf8mb4',
];

// Defaults
$defaults = [
    'db_host'                 => 'localhost',
    'db_user'                 => '',
    'db_name'                 => '',
    'db_password'             => '',
    'worldId'                 => 's1',
    'serverName'              => 'x50000',
    'speed'                   => 50000,
    'roundLength'             => 7,
    'mapSize'                 => 100,
    'isPromoted'              => 0,
    'startGold'               => 3600,
    'buyTroops'               => 0,
    'buyTroopsInterval'       => 0,
    'buyResources'            => 0,
    'buyResourcesInterval'    => 0,
    'buyAnimals'              => 0,
    'buyAnimalsInterval'      => 0,
    'protectionHours'         => 24,
    'needPreregistrationCode' => 0,
    'serverHidden'            => 0,
    'instantFinishTraining'   => 1,
    'buyAdventure'            => 1,
    'activation'              => 0,
    'auto_reinstall'          => 0,
    'auto_reinstall_start_after' => 86400,
    'startTimeDT'             => (new DateTime('+1 hour'))->format('Y-m-d\TH:i'),
    'admin_password'          => '',
];

// AJAX: check if world path exists
if (isset($_GET['action']) && $_GET['action'] === 'checkWorld') {
    $worldIdRaw = (string)($_GET['worldId'] ?? '');
    $worldId = strtolower(preg_replace('~[^a-z0-9-]~', '', $worldIdRaw));
    if ($worldId === '' || strlen($worldId) > 32) {
        json_out(['ok' => true, 'exists' => false, 'worldId' => $worldId, 'reason' => 'invalid_worldId']);
    }

    $serversRoot = dirname(__DIR__,2) . '/servers/';
    $worldPath   = $serversRoot . $worldId . '/';

    json_out([
        'ok' => true,
        'exists' => is_dir($worldPath),
        'worldPath' => $worldPath,
        'archiveName' => rtrim($worldPath, '/').'-'.time(),
    ]);
}

// Form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize & validate
    $g = static function(string $k, $fallback = null) {
        return $_POST[$k] ?? $fallback;
    };

    $input = [
        'db_host'     => trim((string)$g('db_host', $defaults['db_host'])),
        'db_user'     => trim((string)$g('db_user', $defaults['db_user'])),
        'db_name'     => trim((string)$g('db_name', $defaults['db_name'])),
        'db_password' => (string)$g('db_password', $defaults['db_password']),
        'worldId'     => strtolower(trim((string)$g('worldId', $defaults['worldId']))),
        'serverName'  => trim((string)$g('serverName', $defaults['serverName'])),
        'speed'       => (int)$g('speed', $defaults['speed']),
        'roundLength' => (int)$g('roundLength', $defaults['roundLength']),
        'mapSize'     => (int)$g('mapSize', $defaults['mapSize']),
        'isPromoted'  => (int)!!$g('isPromoted', $defaults['isPromoted']),
        'startGold'   => (int)$g('startGold', $defaults['startGold']),
        'buyTroops'   => (int)!!$g('buyTroops', $defaults['buyTroops']),
        'buyTroopsInterval'    => (int)$g('buyTroopsInterval', $defaults['buyTroopsInterval']),
        'buyResources'=> (int)!!$g('buyResources', $defaults['buyResources']),
        'buyResourcesInterval' => (int)$g('buyResourcesInterval', $defaults['buyResourcesInterval']),
        'buyAnimals'  => (int)!!$g('buyAnimals', $defaults['buyAnimals']),
        'buyAnimalsInterval'   => (int)$g('buyAnimalsInterval', $defaults['buyAnimalsInterval']),
        'protectionHours'      => max(0, (int)$g('protectionHours', $defaults['protectionHours'])),
        'needPreregistrationCode' => (int)!!$g('needPreregistrationCode', $defaults['needPreregistrationCode']),
        'serverHidden'            => (int)!!$g('serverHidden', $defaults['serverHidden']),
        'instantFinishTraining'   => (int)!!$g('instantFinishTraining', $defaults['instantFinishTraining']),
        'buyAdventure'            => (int)!!$g('buyAdventure', $defaults['buyAdventure']),
        'activation'              => (int)!!$g('activation', $defaults['activation']),
        'auto_reinstall'          => (int)!!$g('auto_reinstall', $defaults['auto_reinstall']),
        'auto_reinstall_start_after' => (int)$g('auto_reinstall_start_after', $defaults['auto_reinstall_start_after']),
        'admin_password'          => (string)$g('admin_password', ''),
        'startTimeDT'             => (string)$g('startTimeDT', $defaults['startTimeDT']),
    ];

    if (!preg_match('~^[a-z0-9-]{1,32}$~', $input['worldId'])) {
        $errors[] = "World ID must be 1-32 chars [a-z0-9-].";
    }
    if ($input['serverName'] === '') {
        $errors[] = "Server name is required.";
    }
    foreach (['db_host','db_user','db_name'] as $k) {
        if ($input[$k] === '') $errors[] = "Game DB field '$k' is required.";
    }
    if ($input['admin_password'] === '' || strlen($input['admin_password']) < 6) {
        $errors[] = "Admin password is required and must be at least 6 characters.";
    }
    if ($input['speed'] <= 0 || $input['roundLength'] <= 0 || $input['mapSize'] <= 0) {
        $errors[] = "Speed, round length and map size must be positive.";
    }

    // Compute start timestamp
    $startTs = null;
    try {
        $dt = new DateTime($input['startTimeDT']);
        $startTs = $dt->getTimestamp();
    } catch (Throwable $e) {
        $errors[] = "Invalid start time.";
    }

    // Build gameWorldUrl based on worldId + domain
    $gameWorldUrl = "http://{$input['worldId']}.{$host}/";

    if (!$errors) {
        // Paths and prep (installer elsewhere)
        $basePath     = dirname(__DIR__,2) . '/src/';
        $htdocsRoot   = dirname(__DIR__,2) . '/';
        $serversRoot  = $htdocsRoot . 'servers/';
        $templateRoot = $htdocsRoot . 'server.tpl';               // source template (directory)
        $worldIdSafe  = $input['worldId'];
        $worldRoot    = $serversRoot . $worldIdSafe . '/';        // /servers/<worldId>/
        $publicRoot   = $worldRoot . 'public/';
        $script_path  = $worldRoot;                               // same as legacy
        $includePath  = $script_path . 'include/';

        $connectionFile = $includePath . 'connection.php';
        $installerFile  = $includePath . 'install.php';
        $updateFile     = $includePath . 'update.php';
        $envFile        = $includePath . 'env.php';

        // 1) If world exists, archive it by renaming with -{timestamp}
        if (is_dir($worldRoot)) {
            $archivePath = rtrim($worldRoot, '/').'-'.time();
            if (!@rename($worldRoot, $archivePath)) {
                throw new RuntimeException("Failed to archive existing world: $worldRoot -> $archivePath");
            }
        }

        // 2) Create fresh world dir and clone template
        // We want contents of server.tpl into /servers/<worldId>/ (not nested)
        if (!is_dir($worldRoot) && !mkdir($worldRoot, 0775, true)) {
            throw new RuntimeException("Failed to create world directory: $worldRoot");
        }
        if (!is_dir($templateRoot)) {
            throw new RuntimeException("Template not found at: $templateRoot");
        }

        // Fast and safe copy using cp -a (copies contents with attrs)
        // Note the trailing '/.' on source to copy contents, not the folder itself
        $copyCmd = sprintf(
            '/bin/cp -a %s/. %s 2>&1',
            escapeshellarg($templateRoot),
            escapeshellarg($worldRoot)
        );
        [$copyOut, $copyCode] = run_cmd($copyCmd);
        if ($copyCode !== 0) {
            throw new RuntimeException("Failed to copy template:\n$copyCmd\n$copyOut");
        }

        // Ensure include/ exists after copy (template should provide it, but belt and suspenders)
        if (!is_dir($includePath) && !mkdir($includePath, 0775, true)) {
            throw new RuntimeException("Failed to ensure include/ directory: $includePath");
        }
      	
        try {
            // Connect PDOs
            $db = new PDO(
                "mysql:host={$input['db_host']};dbname={$input['db_name']};charset=utf8mb4",
                $input['db_user'],
                $input['db_password'],
                [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]
            );

            $globalDB = new PDO(
                "mysql:host={$mainDb['host']};dbname={$mainDb['name']};charset={$mainDb['charset']}",
                $mainDb['user'],
                $mainDb['pass'],
                [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]
            );

            // Ensure env flag
            if (file_exists($envFile)) {
                $envContent = file_get_contents($envFile);
                if ($envContent !== false) {
                    $envContent = str_replace("'%[IS_DEV]%'", 'true', $envContent);
                    file_put_contents($envFile, $envContent);
                }
            }

            // Prepare connection.php template replacement
            if (!file_exists($connectionFile)) {
                throw new RuntimeException("Missing $connectionFile");
            }
            $connection_content = file_get_contents($connectionFile);

            // Replace blocks exactly like your legacy script
            $order = [
                '[PAYMENT_FEATURES_TOTALLY_DISABLED]',
                '[TITLE]',
                '[GAME_WORLD_URL]',
                '[GAME_SERVER_NAME]',
                '[DATABASE_HOST]',
                '[DATABASE_DATABASE]',
                '[DATABASE_USERNAME]',
                '[DATABASE_PASSWORD]'
            ];
            $order_values = [
                'false',
                $input['worldId'],
                $gameWorldUrl,
                $input['serverName'],
                $input['db_host'],
                $input['db_name'],
                $input['db_user'],
                $input['db_password'],
            ];
            $connection_content = str_replace($order, $order_values, $connection_content);

            $processName = 'travian_500x.service';
            $order = [
                '[SETTINGS_WORLD_ID]',
                '[SETTINGS_WORLD_UNIQUE_ID]',
                '[GAME_SPEED]',
                '[GAME_START_TIME]',
                '[GAME_ROUND_LENGTH]',
                '[SECURE_HASH_CODE]',
                '[AUTO_REINSTALL]',
                '[AUTO_REINSTALL_START_AFTER]',
                '[ENGINE_FILENAME]',
            ];

            // Insert row in gameServers using PREPARED statement to fix silent failures
            $stmt = $globalDB->prepare("
                INSERT INTO `gameServers`
                (`worldId`,`speed`,`name`,`version`,`gameWorldUrl`,`startTime`,`roundLength`,
                 `preregistration_key_only`,`promoted`,`hidden`,`configFileLocation`,`activation`)
                VALUES (:worldId,:speed,:name,0,:url,:start,:length,:preKey,:promoted,:hidden,:cfg,:activation)
            ");
            $stmt->execute([
                ':worldId'   => $input['worldId'],
                ':speed'     => $input['speed'],
                ':name'      => $input['serverName'],
                ':url'       => $gameWorldUrl,
                ':start'     => $startTs,
                ':length'    => $input['roundLength'],
                ':preKey'    => $input['needPreregistrationCode'],
                ':promoted'  => $input['isPromoted'],
                ':hidden'    => $input['serverHidden'],
                ':cfg'       => $connectionFile,
                ':activation'=> $input['activation'],
            ]);
            $worldUniqueId = (int)$globalDB->lastInsertId();

            // Complete connection.php replacements
            $order_values = [
                $input['worldId'],
                $worldUniqueId,
                $input['speed'],
                $startTs,
                $input['roundLength'],
                md5(sha1(microtime())),
                $input['auto_reinstall'],
                $input['auto_reinstall_start_after'], // mind the correct key name
                $processName
            ];
            $connection_content = str_replace($order, $order_values, $connection_content);
            file_put_contents($connectionFile, $connection_content);

            // Import schema
            $schemaPath = $basePath . 'schema/T4.4.sql';
            if (!file_exists($schemaPath)) {
                throw new RuntimeException("Missing schema at $schemaPath");
            }
            $sql = file_get_contents($schemaPath);
            $queries = array_filter(array_map('trim', explode(";", $sql)));
            foreach ($queries as $query) {
                if ($query !== '') {
                    $db->exec($query . ';');
                }
            }

            // Add config row
            $cfgStmt = $db->prepare("INSERT INTO `config`
                (`startTime`,`map_size`,`worldUniqueId`,`installed`,`loginInfoTitle`,`loginInfoHTML`,`message`)
                VALUES (:start,:map,:uid,0,'','','')");
            $cfgStmt->execute([
                ':start' => $startTs,
                ':map'   => $input['mapSize'],
                ':uid'   => $worldUniqueId,
            ]);

            // Write config.custom.php
            $configCustom = [
                '<?php',
                'global $globalConfig, $config;',
                '$config->gold->startGold = ' . (int)$input['startGold'] . ';',
                '$config->extraSettings->buyTroops[\'enabled\'] = ' . ($input['buyTroops'] ? 'true' : 'false') . ';',
                '$config->extraSettings->buyAnimal[\'enabled\'] = ' . ($input['buyAnimals'] ? 'true' : 'false') . ';',
                '$config->extraSettings->buyResources[\'enabled\'] = ' . ($input['buyResources'] ? 'true' : 'false') . ';',
                '$config->extraSettings->buyTroops[\'buyInterval\'] = ' . (int)$input['buyTroopsInterval'] . ';',
                '$config->extraSettings->buyResources[\'buyInterval\'] = ' . (int)$input['buyResourcesInterval'] . ';',
                '$config->extraSettings->buyAnimal[\'buyInterval\'] = ' . (int)$input['buyAnimalsInterval'] . ';',
                '$config->game->protection_time = ' . ((int)$input['protectionHours'] * 3600) . ';',
                '$config->extraSettings->generalOptions->finishTraining->enabled = ' . ($input['instantFinishTraining'] ? 'true' : 'false') . ';',
                '$config->extraSettings->generalOptions->buyAdventure->enabled = ' . ($input['buyAdventure'] ? 'true' : 'false') . ';',
            ];
            file_put_contents($includePath . 'config.custom.php', implode("\n", $configCustom) . "\n");

            // Run installer + updater via CLI
            $adminPass = $input['admin_password'];
            $cmd1 = "/usr/bin/php7.4 $installerFile install " . escapeshellarg($adminPass);
            $cmd2 = "/usr/bin/php7.4 $updateFile";

            [$out1,$code1] = run_cmd($cmd1);
            [$out2,$code2] = run_cmd($cmd2);

            $result = [
                'success'       => ($code1 === 0 && $code2 === 0),
                'worldId'       => $input['worldId'],
                'worldUniqueId' => $worldUniqueId,
                'gameWorldUrl'  => $gameWorldUrl,
                'cmd1'          => $cmd1,
                'cmd1_code'     => $code1,
                'cmd1_out'      => $out1,
                'cmd2'          => $cmd2,
                'cmd2_code'     => $code2,
                'cmd2_out'      => $out2,
            ];

        } catch (Throwable $e) {
            $errors[] = "Installer failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}

// Helpers
function checked($v) { return $v ? 'checked' : ''; }
function sel($a,$b){ return ((string)$a === (string)$b) ? 'selected' : ''; }
function run_cmd(string $cmd): array {
    $out = [];
    $code = 0;
    exec($cmd . ' 2>&1', $out, $code);
    return [implode("\n", $out), $code];
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Travium Installer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    :root {
        --glass-bg: rgba(17, 17, 17, 0.55);
        --glass-brd: rgba(255,255,255,0.18);
        --text: #eaeaea;
        --muted: #b9c0c8;
        --accent: #a58cff;
        --ok: #3ecf8e;
        --bad: #ff6b6b;
    }
    html, body {
        height: 100%;
    }
    body {
        margin:0;
        background: url('<?=htmlspecialchars($bgImage, ENT_QUOTES)?>') center/cover fixed no-repeat;
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
        color: var(--text);
    }
    .wrap {
        min-height:100%;
        backdrop-filter: blur(2px);
        display:flex; align-items:center; justify-content:center;
        padding: 40px 16px;
        background: linear-gradient(0deg, rgba(0,0,0,0.35), rgba(0,0,0,0.35));
    }
    .card {
        width: min(1100px, 95vw);
        background: var(--glass-bg);
        border: 1px solid var(--glass-brd);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.45);
        overflow: hidden;
    }
    .header {
        padding: 22px 28px;
        background: linear-gradient(90deg, rgba(165,140,255,0.18), rgba(165,140,255,0.04));
        border-bottom: 1px solid var(--glass-brd);
        display:flex; align-items:center; justify-content:space-between;
    }
    .title { font-size: 20px; letter-spacing:.3px; }
    .grid {
        display:grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 22px;
        padding: 24px;
    }
    .panel {
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--glass-brd);
        border-radius: 16px;
        padding: 18px;
    }
    .panel h3 {
        margin: 0 0 12px 0;
        font-size: 16px;
        color: #fff;
    }
    label { display:block; font-size:12px; color: var(--muted); margin:10px 0 6px; }
    input[type=text],
    input[type=number],
    input[type=password],
    input[type=datetime-local],
    select {
        width:100%; padding:12px 4px;
        border-radius: 10px;
        border:1px solid rgba(255,255,255,0.18);
        background: rgba(0,0,0,0.35);
        color: var(--text);
        outline: none;
    }
    .row2 { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    .row3 { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; }
    .switch {
        display:flex; align-items:center; gap:10px; margin:8px 0;
    }
    .hint { font-size:11px; color: var(--muted); margin-top:4px; }
    .preview {
        margin-top:8px; font-size:12px; color:#d5d9e0;
        background: rgba(255,255,255,0.05);
        border: 1px dashed rgba(255,255,255,0.15);
        padding:8px 10px; border-radius:8px;
    }
    .footer {
        padding: 18px 24px;
        border-top: 1px solid var(--glass-brd);
        display:flex; align-items:center; justify-content:space-between;
        gap:12px; flex-wrap: wrap;
    }
    .btn {
        padding: 12px 18px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.18);
        background: linear-gradient(180deg, rgba(165,140,255,0.35), rgba(165,140,255,0.18));
        color: #fff; cursor:pointer; font-weight:600; letter-spacing:.3px;
    }
    .btn:active { transform: translateY(1px); }
    .errors {
        background: rgba(255,0,0,0.13);
        border: 1px solid rgba(255,0,0,0.25);
        color: #ffdede;
        padding: 10px 12px; border-radius: 12px; margin: 12px 24px 0;
    }
    .result-ok { color: var(--ok); }
    .result-bad { color: var(--bad); }
    pre.out {
        background: rgba(0,0,0,0.5);
        border:1px solid var(--glass-brd);
        padding:10px; border-radius:10px; color:#dfe4ea; overflow:auto; max-height:220px;
    }
</style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="header">
            <div class="title">Travium Server Installer</div>
        </div>

        <?php if ($errors): ?>
            <div class="errors">
                <strong>Fix your inputs:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?=htmlspecialchars($e)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="grid">
                <div class="panel">
                    <h3>Result</h3>
                    <div>World ID: <strong><?=htmlspecialchars($result['worldId'])?></strong></div>
                    <div>World Unique ID: <strong><?=htmlspecialchars((string)$result['worldUniqueId'])?></strong></div>
                    <div>URL: <a target="_blank" style="color:var(--accent)" href="<?=htmlspecialchars($result['gameWorldUrl'])?>"><?=htmlspecialchars($result['gameWorldUrl'])?></a></div>
                    <div>Status:
                        <?php if ($result['success']): ?>
                            <strong class="result-ok">Installer and Updater ran successfully.</strong>
                        <?php else: ?>
                            <strong class="result-bad">Something failed. See output below.</strong>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="panel">
                    <h3>Manual commands (if needed)</h3>
                    <div class="hint">Copy/paste if the automated run failed:</div>
                    <pre class="out"><?=htmlspecialchars($result['cmd1'])?>

<?=htmlspecialchars($result['cmd2'])?></pre>
                </div>
            </div>
            <div class="grid">
                <div class="panel">
                    <h3>Installer output (<?= (int)$result['cmd1_code'] ?>)</h3>
                    <pre class="out"><?=htmlspecialchars($result['cmd1_out'])?></pre>
                </div>
                <div class="panel">
                    <h3>Updater output (<?= (int)$result['cmd2_code'] ?>)</h3>
                    <pre class="out"><?=htmlspecialchars($result['cmd2_out'])?></pre>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off" novalidate>
            <div class="grid">
                <div class="panel">
                    <h3>Game DB (per-world)</h3>
                    <label>DB Host</label>
                    <input name="db_host" type="text" value="<?=htmlspecialchars($_POST['db_host'] ?? $defaults['db_host'])?>">

                    <div class="row2">
                        <div>
                            <label>DB Username</label>
                            <input name="db_user" type="text" value="<?=htmlspecialchars($_POST['db_user'] ?? $defaults['db_user'])?>">
                        </div>
                        <div>
                            <label>DB Name</label>
                            <input name="db_name" type="text" value="<?=htmlspecialchars($_POST['db_name'] ?? $defaults['db_name'])?>">
                        </div>
                    </div>
                    <label>DB Password</label>
                    <input name="db_password" type="password" value="<?=htmlspecialchars($_POST['db_password'] ?? $defaults['db_password'])?>">

                    <div class="hint">Main DB is read from config.php and not editable here.</div>
                </div>

                <div class="panel">
                    <h3>Server Identity</h3>
                    <div class="row2">
                        <div>
                            <label>World ID</label>
                            <input id="worldId" name="worldId" type="text" value="<?=htmlspecialchars($_POST['worldId'] ?? $defaults['worldId'])?>" pattern="[a-z0-9-]{1,32}" required>
                        </div>
                        <div>
                            <label>Server Name</label>
                            <input name="serverName" type="text" value="<?=htmlspecialchars($_POST['serverName'] ?? $defaults['serverName'])?>">
                        </div>
                        <div id="worldExistsWarn" class="preview" style="display:none; border-color: rgba(255,0,0,0.35); color:#ffdede;">
                          This world folder already exists on the server and will be archived as <code id="archiveName"></code> when you submit.
                        </div>
                    </div>

                    <label>Game World URL (auto)</label>
                    <?php
                        $w = $_POST['worldId'] ?? $defaults['worldId'];
                        $previewUrl = "http://".strtolower(preg_replace('~[^a-z0-9-]~','',$w)).".{$host}/";
                    ?>
                    <div class="preview" id="urlPreview"><?=htmlspecialchars($previewUrl)?></div>

                    <div class="row3" style="margin-top:10px">
                        <div>
                            <label>Speed</label>
                            <input name="speed" type="number" min="1" step="1" value="<?=htmlspecialchars((string)($_POST['speed'] ?? $defaults['speed']))?>">
                        </div>
                        <div>
                            <label>Round Length (days)</label>
                            <input name="roundLength" type="number" min="1" step="1" value="<?=htmlspecialchars((string)($_POST['roundLength'] ?? $defaults['roundLength']))?>">
                        </div>
                        <div>
                            <label>Map Size</label>
                            <input name="mapSize" type="number" min="1" step="1" value="<?=htmlspecialchars((string)($_POST['mapSize'] ?? $defaults['mapSize']))?>">
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <h3>Options</h3>
                    <div class="row3">
                        <div>
                            <label>Start Gold</label>
                            <input name="startGold" type="number" min="0" step="1" value="<?=htmlspecialchars((string)($_POST['startGold'] ?? $defaults['startGold']))?>">
                        </div>
                        <div>
                            <label>Protection (hours)</label>
                            <input name="protectionHours" type="number" min="0" step="1" value="<?=htmlspecialchars((string)($_POST['protectionHours'] ?? $defaults['protectionHours']))?>">
                        </div>
                        <div>
                            <label>Start time</label>
                            <input name="startTimeDT" type="datetime-local" value="<?=htmlspecialchars($_POST['startTimeDT'] ?? $defaults['startTimeDT'])?>">
                        </div>
                    </div>

                    <div class="row3">
                        <div class="switch"><input type="checkbox" name="instantFinishTraining" value="1" <?=checked((int)($_POST['instantFinishTraining'] ?? $defaults['instantFinishTraining']))?>> <span>Instant Finish Training</span></div>
                        <div class="switch"><input type="checkbox" name="buyAdventure" value="1" <?=checked((int)($_POST['buyAdventure'] ?? $defaults['buyAdventure']))?>> <span>Buy Adventure</span></div>
                        <div class="switch"><input type="checkbox" name="activation" value="1" <?=checked((int)($_POST['activation'] ?? $defaults['activation']))?>> <span>Activation Required</span></div>
                    </div>

                    <div class="row3">
                        <div class="switch"><input type="checkbox" name="isPromoted" value="1" <?=checked((int)($_POST['isPromoted'] ?? $defaults['isPromoted']))?>> <span>Promoted</span></div>
                        <div class="switch"><input type="checkbox" name="serverHidden" value="1" <?=checked((int)($_POST['serverHidden'] ?? $defaults['serverHidden']))?>> <span>Hidden</span></div>
                        <div class="switch"><input type="checkbox" name="needPreregistrationCode" value="1" <?=checked((int)($_POST['needPreregistrationCode'] ?? $defaults['needPreregistrationCode']))?>> <span>Preregistration Key Only</span></div>
                    </div>

                    <div class="row3">
                        <div class="switch"><input type="checkbox" name="buyTroops" value="1" <?=checked((int)($_POST['buyTroops'] ?? $defaults['buyTroops']))?>> <span>Buy Troops</span></div>
                        <div class="switch"><input type="checkbox" name="buyResources" value="1" <?=checked((int)($_POST['buyResources'] ?? $defaults['buyResources']))?>> <span>Buy Resources</span></div>
                        <div class="switch"><input type="checkbox" name="buyAnimals" value="1" <?=checked((int)($_POST['buyAnimals'] ?? $defaults['buyAnimals']))?>> <span>Buy Animals</span></div>
                    </div>

                    <div class="row3">
                        <div>
                            <label>Buy Troops Interval (sec)</label>
                            <input name="buyTroopsInterval" type="number" min="0" step="1" value="<?=htmlspecialchars((string)($_POST['buyTroopsInterval'] ?? $defaults['buyTroopsInterval']))?>">
                        </div>
                        <div>
                            <label>Buy Resources Interval (sec)</label>
                            <input name="buyResourcesInterval" type="number" min="0" step="1" value="<?=htmlspecialchars((string)($_POST['buyResourcesInterval'] ?? $defaults['buyResourcesInterval']))?>">
                        </div>
                        <div>
                            <label>Buy Animals Interval (sec)</label>
                            <input name="buyAnimalsInterval" type="number" min="0" step="1" value="<?=htmlspecialchars((string)($_POST['buyAnimalsInterval'] ?? $defaults['buyAnimalsInterval']))?>">
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <h3>Automation</h3>
                    <div class="row2">
                        <div class="switch"><input type="checkbox" name="auto_reinstall" value="1" <?=checked((int)($_POST['auto_reinstall'] ?? $defaults['auto_reinstall']))?>> <span>Auto reinstall</span></div>
                        <div>
                            <label>Auto reinstall start after (sec)</label>
                            <input name="auto_reinstall_start_after" type="number" min="0" step="1" value="<?=htmlspecialchars((string)($_POST['auto_reinstall_start_after'] ?? $defaults['auto_reinstall_start_after']))?>">
                        </div>
                    </div>

                    <label>Admin password</label>
                    <input name="admin_password" type="password" value="<?=htmlspecialchars($_POST['admin_password'] ?? $defaults['admin_password'])?>">
                    <div class="hint">Password for user "Support"</div>
                </div>
            </div>

            <div class="footer">
                <button class="btn" type="submit">Run Installer</button>
            </div>
        </form>
    </div>
</div>
<script>
  const worldIdEl = document.getElementById('worldId');
  const urlPrev = document.getElementById('urlPreview');
  const host = <?= json_encode($host) ?>;
  const sanitize = s => (s||'').toLowerCase().replace(/[^a-z0-9-]/g,'');
  const warnBox = document.getElementById('worldExistsWarn');
  const archNameEl = document.getElementById('archiveName');
  let worldExists = false;
  let pendingCheck = null;

  function updateUrl() {
    const w = sanitize(worldIdEl.value);
    urlPrev.textContent = `https://${w}.${host}/`;
    scheduleExistCheck(w);
  }

  function scheduleExistCheck(w) {
    if (pendingCheck) clearTimeout(pendingCheck);
    pendingCheck = setTimeout(() => checkWorld(w), 200);
  }

  async function checkWorld(w) {
    worldExists = false;
    warnBox.style.display = 'none';
    archNameEl.textContent = '';
    if (!w) return;
    try {
      const u = new URL(window.location.href);
      u.searchParams.set('action','checkWorld');
      u.searchParams.set('worldId', w);
      const res = await fetch(u.toString(), { credentials: 'same-origin' });
      if (!res.ok) return;
      const data = await res.json();
      if (data && data.ok && data.exists) {
        worldExists = true;
        warnBox.style.display = 'block';
        archNameEl.textContent = data.archiveName || (w + '-' + Math.floor(Date.now()/1000));
      }
    } catch(e) { /* eat it */ }
  }

  worldIdEl && worldIdEl.addEventListener('input', updateUrl);
  updateUrl();

  // Intercept submit if existing world detected to avoid "surprise"
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    if (worldExists) {
      const ok = confirm('World folder already exists. It will be archived with a timestamp and replaced from template. Continue?');
      if (!ok) e.preventDefault();
    }
  });
</script>
</body>
</html>
