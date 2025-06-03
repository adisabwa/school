<?php

// override core en language system validation or define your own en language validation message
return [
	'required'		=> '{field} harus diisi.',
	'isset'			=> '{field} harus memiliki nilai.',
	'valid_email'	=> '{field} harus valid.',
	'valid_url'		=> '{field} harus valid.',
	'min_length'	=> '{field} minimal {param} karakter.',
	'numeric'		=> 'Kolom {field} harus berisi angka saja.',
	'is_numeric'	=> 'Kolom {field} harus berisi karakter numberik.',
	'integer'		=> 'Kolom {field} harus berisi sebuah integer.',
	'regex_match'	=> '{field} harus sesuai format',
	'matches'		=> '{field} tidak sama dengan {param}.',
	'is_unique' 	=> '{field} sudah digunakan.',
	'greater_than'	=> '{field} harus lebih besar dari {param}.',
	'double_greater_and_equal_to' => '{field} harus lebih besar atau sama dengan dari {param}.',
	'unique_combination' => 'Data sudah ada di database, tidak bisa dimasukkan lagi',
	'unique_input' => 'Data hanya boleh dimasukkan sekali saja',
];
