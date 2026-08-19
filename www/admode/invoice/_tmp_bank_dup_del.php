<?php
$pdo = new PDO(
	'mysql:host=222.122.198.189;port=3306;dbname=greathumancorp;charset=utf8mb4',
	'greathumancorp',
	'ghcorpontra26!@',
	[
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]
);

$del_idx = [244, 279];
$placeholders = implode(',', array_fill(0, count($del_idx), '?'));

$check = $pdo->prepare('SELECT idx FROM gh_bank_table WHERE idx IN (' . $placeholders . ') ORDER BY idx');
$check->execute($del_idx);
$found = $check->fetchAll(PDO::FETCH_COLUMN);
echo 'FOUND=' . implode(',', $found) . PHP_EOL;

$del = $pdo->prepare('DELETE FROM gh_bank_table WHERE idx IN (' . $placeholders . ')');
$del->execute($del_idx);
echo 'DELETED=' . $del->rowCount() . PHP_EOL;

$left = $pdo->query('SELECT COUNT(*) FROM gh_bank_table')->fetchColumn();
echo 'REMAIN_TOTAL=' . $left . PHP_EOL;
