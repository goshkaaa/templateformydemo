<?php
require_once __DIR__ . '/config.php';
Auth::logout();
flash('Вы вышли из системы.');
redirect('login.php');
