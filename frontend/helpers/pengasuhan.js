
export function setLabelNilai(status){
    if (status == '0') return '-'
    else if (status == '1') return 'Kurang'
    else if (status == '2') return 'Cukup'
    else if (status == '3') return 'Baik'
    else if (status == '4') return 'Sangat Baik'
    else return '-'
  }