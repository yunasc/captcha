<?php

if ($is_valid_cap) {
	return '<span style="color: green">�������</span>';
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		return '<span style="color: red">������</span>';
	} else
		return '<span style="color: lightblue">�� �����������</span>';
}

?>