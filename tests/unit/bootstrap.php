<?php declare(strict_types=1);

// 環境変数
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../', '.env.default');
$dotenv->load();
$dotenv->required('SAMPLE_API_ENDPOINT_PREFIX')->notEmpty();
