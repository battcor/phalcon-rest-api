<?php

$db = new SQLite3('invite_service.db');

$sql = 'CREATE TABLE invites (id INTEGER PRIMARY KEY AUTOINCREMENT, token CHARACTER, used TINYINT, void TINYINT, expiry VARCHAR, created VARCHAR)';

$exec = $db->exec($sql);

echo $exec ? "Successfully created table\n" : "Failed to create table\n";
