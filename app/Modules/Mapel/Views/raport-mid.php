<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="author" content="Adi Sabwa">
	<meta name="website" content="adisabwa.github.io">
	<!-- Site title -->
	<title>Raport MID Semester <?= ucfirst($semester->semester) ?> Tahun Ajaran <?= $semester->tahun_ajaran ?></title>
	<link rel="icon" href="<?= base_url('assets/images/favicon.ico') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/paper-pdf.css') ?>">
	<?php 
		$fontPath = APPPATH . 'ThirdParty/fonts'; // absolute server path
	?>
	<style>
		@font-face {
			font-family: "Arial Narrow";
			src: url('file://<?= $fontPath ?>/arialnarrow.ttf') format('truetype');
		}
		@font-face {
			font-family: "Amiri";
			src: url('file://<?= $fontPath ?>/amiri_bold.ttf') format('truetype');
		}
		html, body {
			font-family: "Amiri", "Arial Narrow", "Times New Roman";
		}
		@page {
			margin: 1cm 0.5cm;
		}
		.table td {
			border: 1px solid black;
		}
		.no-border td {
			border: 0px;
		}
	</style>
</head>
<body>
	<?php foreach ($santris as $key => $santri) { ?>
		<section class="sheet font-10">
			<div class="font-11" style="text-align:center; font-weight:bold;">
				<img src="<?= base_url() ?>/assets/images/kmi/kop.png" height="100px" height="90%" alt="Logo">
				<div class="spacer"></div>
				<img src="<?= base_url() ?>/assets/images/kmi/title-raport-arab.png" height="50px">
				<div>LAPORAN HASIL BELAJAR MID SEMESTER <?= strtoupper($semester->semester) ?></div>
				<div>Tahun Ajaran: <?= $semester->tahun_ajaran ?></div>
			</div>
			<table style=" border-collapse: collapse; line-height: 14px; margin-top:10px;" class="" width="100%">
				<tbody>
					<tr>
						<td width="100px">Nama</td>
						<td width="20px">:</td>
						<td style="border-bottom:1px solid black;"><b><?= strtoupper($santri->nama) ?></b></td>
					</tr>
					<tr>
						<td >No. Stb</td>
						<td>:</td>
						<td style="border-bottom:1px solid black;"><b><?= $santri->no_stb ?? '' ?></b></td>
					</tr>
					<tr>
						<td >Semester</td>
						<td>:</td>
						<td style="border-bottom:1px solid black;"><b><?= ucfirst($semester->semester) ?></b></td>
					</tr>
					<tr>
						<td >No. Stb</td>
						<td>:</td>
						<td style="border-bottom:1px solid black;"><b><?= $kelas->kelas ?></b></td>
					</tr>
				</tbody>
			</table>
			<table style="line-height: 14px; margin-top:10px; text-align:center;" class="table" width="100%">
				<thead>
					<tr>
						<td width="20px" rowspan="2">No.</td>
						<td class="text-left" rowspan="2">Bidang Studi</td>
						<td colspan="2">Nilai</td>
						<td colspan="2">Nilai</td>
						<td class="text-right" rowspan="2">Bidang Studi</td>
						<td width="20px" rowspan="2">No.</td>
					</tr>
					<tr>
						<td  width="30px">Angka</td>
						<td >Huruf</td>
						<td >Huruf</td>
						<td  width="30px">Angka</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($santri->mapel as $key => $mapel) {?>
						<tr>
							<td ><?= $key+1 ?></td>
							<td class="text-left"><?= $mapel->nama_mapel ?></td>
							<td ><?= $mapel->uts ?? '-' ?></td>
							<td ><?= $mapel->uts ?? '-' ?></td>
							<td ><?= $mapel->uts ?? '-' ?></td>
							<td ><?= $mapel->uts ?? '-' ?></td>
							<td class="text-right"><?= $mapel->nama_mapel ?></td>
							<td ><?= $key+1 ?></td>
						</tr>
					<?php } ?>
					<tr style="font-weight: bold;">
						<td colspan="2">
							<table class="no-border">
								<tr>
									<td>Total Nilai</td>
									<td width="20px">:</td>
									<td><b><?= $santri->total_uts ?? '-' ?></b></td>
								</tr>
							</table>
						</td>
						<td colspan="2">
							<table class="no-border">
								<tr>
									<td>Rata-rata</td>
									<td width="20px">:</td>
									<td><b><?= $santri->rata_uts ?? '-' ?></b></td>
								</tr>
							</table>
						</td>
						<td colspan="2">
							<table class="no-border">
								<tr>
									<td>Rata-rata</td>
									<td width="20px">:</td>
									<td><b><?= $santri->rata_uts ?? '-' ?></b></td>
								</tr>
							</table>
						</td>
						<td colspan="2">
							<table class="no-border">
								<tr>
									<td>Total Nilai</td>
									<td width="20px">:</td>
									<td><b><?= $santri->total_uts ?? '-' ?></b></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="no-border">
						<td colspan="4" style="border-bottom:1px">
							Predikat / التقدير
						</td>
						<td colspan="4">
						</td>
					</tr>
					<tr class="no-border">
						<td colspan="4">
							BAIK SEKALI
						</td>
						<td colspan="4">
							Patean, <?= dateIndo(date('Y-m-d')) ?><br/>
							تحريرا بفاتيان, 8 يونيو 2023
						</td>
					</tr>
				</tbody>
			</table>
			<div class="text-right">
				<table class="text-center no-border" style="margin-top:10px; width:50% !important;">
					<tr>
						<td>
							<div>Direktur KMI / المدرسة مدير</div>
							<div class="spacer"></div>
							<div class="spacer"></div>
							<div style="font-weight:bold; text-decoration:underline;">Agus Budi Utomo, S.T, M.Pd.</div>
							<div>NBM. -</div>
						</td>
					</tr>
				</table>
			</div>
		</section>
		<div class="page-break"></div>
	<?php } ?>
</body>
</html>