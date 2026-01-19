<!DOCTYPE html>
<html lang="en">
<head> 
  <meta charset="UTF-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta name="author" content="Adi Sabwa">
  <meta name="website" content="adisabwa.github.io">
  <!-- Site title -->
  <title>Kalender Pendidikan</title>
  <link rel="icon" href="<?= base_url('assets/images/favicon.ico') ?>">
  <style>
		@page{ margin: 0.7cm 1cm; }
  </style>
</head>
<body>  
  <page>
    <img src="<?= base_url('assets/images/kmi/kop-kmi.png') ?>" width="100%"></img>
    <h3 style="text-align: center; margin-bottom: 10px;">
      Kalender Pendidikan Semester <?= ucfirst($semester->semester) ?> Tahun Ajaran <?= $semester->tahun_ajaran ?> <br>
    </h3>
    <table>
      <tbody>
        <?php foreach ($bulans as $bulan => $month): ?>
          <tr>
            <td valign="top" style="padding-right: 20px; padding-bottom: 20px; border: 0px;">
              <table class="kalender">
                <thead>
                  <tr style="background-color: #67C23A;">
                    <th colspan="7"><?= formatTanggalIndonesia($bulan, false, 'MMMM yyyy') ?></th>
                  </tr>
                  <tr>
                    <td>Sen</td>
                    <td>Sel</td>
                    <td>Rab</td>
                    <td>Kam</td> 
                    <td>Jum</td>
                    <td>Sab</td> 
                    <td>Min</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $weekOfMonth = 1;
                  $dayOfWeek = -1;
                  foreach ($month as $week => $days): ?>
                    <tr>
                      <?php foreach (range(1, 7) as $indDay): 
                        $value = $days["$indDay"] ?? []?>
                        <td style="background-color: <?= $value['color'] ?? 'white' ?>;">
                          <div class="date"><?= $value['tanggal'] ?? ''?> </div>
                          <?php if (isset($value['shape'])): ?>
                            <?php if ($value['shape'] == 'star'): ?>
                              <img src="<?= base_url('assets/images/kmi/star.png') ?>"
                                class="img-kalender star"></imd>
                            <?php elseif ($value['shape'] == 'circle'): ?>
                              <img src="<?= base_url('assets/images/kmi/circle.png') ?>"
                                class="img-kalender shape"></imd>
                            <?php endif; ?>
                          <?php endif; ?>
                        </td>
                      <?php endforeach; ?>
                    </tr>
                  <?php endforeach; ?>
              </table>
            </td>
            <td valign="top" style="padding-bottom: 20px; border: 0px;">
              <table class="keterangan" style="width: 100%; font-size: 14px;">
                <thead>
                  <tr style="background-color: #67C23A;">
                    <th width="130px">Tanggal</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach (array_values($keterangan[$bulan]) as $key => $value): ?>
                    <tr style="background-color: <?= $key % 2 == 0 ? '#d7f9c6' : '' ?>;">
                      <td><?= penulisan_jarak_tanggal(
                        formatTanggalIndonesia($value->tanggal_mulai, false, 'd MMMM '),formatTanggalIndonesia($value->tanggal_selesai, false, 'd MMMM ')
                        ) ?>
                      </td>
                      <td><?= $value->keterangan ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td></td>
          <td style="position:relative">
            Patean, <?= formatTanggalIndonesia($semester->tanggal_mulai, false, 'd MMMM Y') ?>
            <br>
            Direktur KMI
            <br>
            <br>
            <img src="<?= base_url('assets/images/kmi/ttd-kmi.png') ?>"
              width="200px"></img>
            <img src="<?= base_url('assets/images/kmi/cap-kmi.png') ?>"
              width="150px"
              style="
                position: absolute;
                top:0;
                left:-100px;
              "></img>
            <br>
            <span><b>
              <u>Agus Budi Utomo, S.T.,M.Pd.</u><br>
              NBM. 952.205
            </b></span>
          </td>
        </tr>
      </tbody>
    </table>
  </page>
</body>
<style>
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th {
    padding:3px;
  }
  td {
    vertical-align: top;
  }
  .kalender td {
    width: 35px;
    height: 25px;
    text-align: center;
    vertical-align: middle;
    position: relative;
    border: 1px solid #cccccc;
  }
  .keterangan td {
    padding: 3px 6px;
    border: 1px solid #cccccc;
  }
  .img-kalender {
    width: 30px;
    height: 25px;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translateY(-50%) translateX(-50%);
  }
  .date {
    z-index: 2;
  }
  .icon {
    width: 100%;
    height: 100%;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translateY(-50%) translateX(-50%);
    z-index: 1;
    color:#337413;
  }
</style>
</html>