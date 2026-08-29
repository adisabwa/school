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
    .page-break {
        page-break-after: always;
    }
  </style>
</head>
<body>  
  <page>
    <?php
      switch(($unit->id ?? '')) {
        case '1':
          $kop = 'mts';
          $label = 'Kepala Sekolah';
          break;
        case '2':
          $kop = 'ma';
          $label = 'Kepala Sekolah';
          break;
        case '3':
          $kop = 'smk';
          $label = 'Kepala Sekolah';
          break;
        default:
          $kop = 'kmi';
          $label = 'Direktur KMI';
      };
    ?>
    <img src="<?= base_url() ?>assets/images/kmi/kop-<?= $kop ?>.png" width="100%"></img>
    <h3 style="text-align: center; margin-bottom: 10px;">
      Kalender Pendidikan Semester <?= ucfirst($semester->semester) ?> Tahun Ajaran <?= $semester->tahun_ajaran ?> <br>
    </h3>
    <table>
      <tbody>
        <?php foreach ($bulans as $bulan => $month): ?>
          <tr <?=  in_array($bulan, $breaks) ? 'style="page-break-after: always;"' : '' ?> >
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
                        <td>
                          <div style="
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            z-index: 0;
                            display: flex;
                            flex-direction: column;
                            background-color: white;">
                            <?php foreach ($value['color'] ?? [] as $color): ?>
                              <div style="width: 100%; height: <?php echo 100 / count($value['color'] ?? []) ?>%; background-color: <?= $color ?>; opacity: 0.5;"></div>
                            <?php endforeach; ?>
                          </div>
                          <div class="date"><?= $value['tanggal'] ?? ''?> </div>
                          <?php if (isset($value['shape'])): ?>
                            <img src="<?= base_url('assets/images/kmi/') ?><?= $value['shape'] ?>.png"
                              class="img-kalender shape"
                            ></img>
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
                  <?php foreach (array_values($keterangan[$bulan] ?? []) as $key => $value): ?>
                    <tr >
                      <td style="background-color: <?= $value->color?>;">
                        <?= penulisan_jarak_tanggal(
                        formatTanggalIndonesia($value->tanggal_mulai, false, 'd MMMM '),formatTanggalIndonesia($value->tanggal_selesai, false, 'd MMMM ')
                        ) ?>
                      </td>
                      <td style="background-color: <?= $key % 2 == 0 ? '#d7f9c6' : '' ?>;">
                        <?= $value->keterangan ?>
                      </td>
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
            <?= $label ?>
            <br>
            <br>
            <img src="<?= $unit ? $unit->kepala_signature : base_url('assets/images/kmi/ttd-kmi.png') ?>"
              width="200px"
              style="
                position: absolute;
                top:0;
                left:-30px;
              "></img>
            <img src="<?= base_url() ?>assets/images/kmi/cap-<?= $kop ?>.png"
              width="150px"
              style="
                position: absolute;
                top:0;
                left:-100px;
              "></img>
            <br>
            <br>
            <br>
            <span><b>
              <u><?=  $unit ? ucwords(strtolower($unit->nama_kepala_lengkap), " .") : 'Miftahul Bashor, S.Pd.I, M.Pd.' ?></u><br>
              NBM. <?=  $unit ? $unit->nbm_kepala : '1052760' ?>
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