export const getLabel = (ujian) => {
  if (ujian == 'nilai_harian') {
    return 'Nilai Harian'
  } else if (ujian == 'uts') {
    return 'UTS'
  } else if (ujian == 'uas') {
    return 'UAS'
  } else if (ujian == 'um' ) {
    return 'Ujian Madrasah'
  } else if (ujian == 'nilai_rapor') {
    return 'Nilai Raport'
  } else if (ujian == 'nilai_pengasuhan') {
    return 'Nilai Pengasuhan'
  } else if (ujian == 'katrol1') {
    return 'Nilai Dinas'
  } else if (ujian == 'katrol2') {
    return 'Ijazah'
  } else {
    return ''
  }
}

export const getLabelShort = (ujian) => {
  if (ujian == 'nilai_harian') {
    return 'NH'
  } else if (ujian == 'uts') {
    return 'UTS'
  } else if (ujian == 'uas') {
    return 'UAS'
  } else if (ujian == 'um' ) {
    return 'UM'
  } else if (ujian == 'nilai_rapor') {
    return 'Raport'
  } else if (ujian == 'nilai_pengasuhan') {
    return 'Pengasuhan'
  } else if (ujian == 'katrol1') {
    return 'Dinas'
  } else if (ujian == 'katrol2') {
    return 'Ijazah'
  } else {
    return ''
  }
}