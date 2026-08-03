<?php
// PDO 연결만 해제 — $func_library/$query_library까지 unset하면
// 이 파일을 include하는 *_ok.php에서 Intelephense가 전 구간을 null로 추론함
unset($stmt, $conn, $conn_core);
