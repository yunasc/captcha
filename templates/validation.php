<?php

if ($is_valid_cap) {
	return '<span style="color: green">Успешно</span>';
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		return '<span style="color: red">Ошибка</span>';
	} else
		return '<span style="color: lightblue">Не проводилась</span>';
}

?>