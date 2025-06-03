
export function setStatusType(status){
    if (status == '0') return 'primary'
    else if (status == '1') return 'success'
    else return 'info'
  }
export function setStatusText(status){
    if (status == '0') return 'Proses'
    else if (status == '1') return 'Terlaksana'
    else return ''
  }