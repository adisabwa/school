<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="author" content="Adi Sabwa">
	<meta name="website" content="adisabwa.github.io">
	<!-- Site title -->
	<title>Kartu Pendaftaran Calon Santri</title>
	<link rel="icon" href="<?= base_url('assets/images/favicon.ico') ?>">
	<style>
		.kartu-wrap {
			padding: 20px;
			padding-bottom: 40px;
			border: 1px solid black;
			width: fit-content;
		}
		.kartu {
			border-collapse: collapse;
		}
		.kartu td {
			padding: 4px 5px;
		}
		.kartu tr:first-child td {
			border-bottom: 3px double black;
			padding-bottom: 15px;
		}
		.photo {
			margin-top: 10px;
			width: 120px;
			height: 160px;
			background-color: white;
			overflow: hidden;
			border: 1px solid black;
		}
		@page {
			margin: 1cm;
		}
	</style>
</head>
<body>
	<div class="kartu-wrap">
		<table class="kartu">
			<tr>
				<td colspan="4" align="center">
					<div>
						<img class="header"
							src="data:image/jpeg;base64,<?php echo base64_encode(file_get_contents(base_url('assets/images/logo-lebar.png'))); ?>"
							height="100px">
						</img>
					</div>
				</td>
			</tr>
			<tr><td colspan="4"></td></tr>
			<tr>
				<td width="120">Nomor Pendaftaran</td>
				<td>:</td>
				<td width="150"><?= $content->no_pendaftaran ?></td>
				<td rowspan="8" align="center" valign="top">
					Foto Santri<br/>
					<div class="photo">
						<?php if (empty($content->foto)): ?>
							<br/><br/><br/>
							<div>Photo 3 x 4</div>
						<?php else : ?>
							<img class="header"
								src="data:image/jpeg;base64,<?php echo base64_encode(file_get_contents($content->foto)); ?>"
								height="100%">
							</img>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			<tr>
				<td>Nama Calon Santri</td>
				<td>:</td>
				<td><?= $content->nama ?></td>
			</tr>
			<tr>
				<td>NISN</td>
				<td>:</td>
				<td><?= $content->nisn ?></td>
			</tr>
			<tr>
				<td>Tempat, Tanggal Lahir</td>
				<td>:</td>
				<td><?= $content->tempat_lahir ?>,  <?= date('d M Y', strtotime($content->tanggal_lahir)) ?></td>
			</tr>
			<tr>
				<td>Alamat</td>
				<td>:</td>
				<td><?= $content->alamat ?></td>
			</tr>
			<tr>
				<td>Nama Ayah</td>
				<td>:</td>
				<td><?= $content->ayah_nama ?></td>
			</tr>
			<tr>
				<td>Nama Ibu</td>
				<td>:</td>
				<td><?= $content->ibu_nama ?></td>
			</tr>
			<tr>
				<td>Nama Wali</td>
				<td>:</td>
				<td><?= $content->wali_nama ?></td>
			</tr>
		</table>
	</div>	
</body>
</html>