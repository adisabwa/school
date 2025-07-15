
export function setStatusType(status){
    if (status == '0') return 'warning'
    else if (status == '1') return 'primary'
    else if (status == '2') return 'success'
    else if (status == '-1') return 'danger'
    else return 'info'
  }
export function setStatusText(status){
    if (status == '0') return 'Terdaftar'
    else if (status == '1') return 'Sudah Dibayar'
    else if (status == '2') return 'Terverifikasi'
    else if (status == '-1') return 'Koreksi Data'
    else return ''
  }