
export function setStatusColor(number){
    // console.log(number)
    number = parseInt(number)
    // console.log(number, typeof number, )
    let res = ''
      if (number == 100)
        res = '[&_*]:bg-green-100 [&_*]:text-green-900 bg-green-100 text-green-900'
      else if (number >= 75)
        res = '[&_*]:bg-sky-100 [&_*]:text-sky-900 bg-sky-100 text-sky-900'
      else if (number >= 50)
        res = '[&_*]:bg-purple-100 [&_*]:text-purple-900 bg-purple-100  text-purple-900'
      else if (number >= 25)
        res = '[&_*]:bg-orange-100 [&_*]:text-orange-900 bg-orange-100 text-orange-900'
      else if (number >= 0)
        res = '[&_*]:bg-red-500 [&_*]:text-white bg-red-500 text-white'
      else
        res = '[&_*]:bg-slate-50 [&_*]:text-slate-400 bg-slate-50 text-slate-400'
    // console.log(res)
    return res
  }
  
let options  = [
  {
    value:100,
    label:`Jama'ah`,
  },
  {
    value:75,
    label:`Masbuq`,
  },
  {
    value:50,
    label:`Sendiri`,
  },
  {
    value:25,
    label:`Lewat Waktu`,
  },
  {
    value:0,
    label:`Tidak Sholat`,
  },
]

export { options };

export function getCount(value){
  if (value >= 100) return 3
  else if (value >= 75) return 2
  else if (value >= 50) return 1
  else if (value >= 25) return 0
  else return -1
}

export function getCountSunnah(value){
  if (value >= 20) return 3
  else if (value >= 15) return 2
  else if (value >= 10) return 1
  else if (value >= 5) return 0
  else return -1
}

export function getLabel(value){
  let op = options.find( o => o.value == value )
  if (op)
    return op.label
  else
    return 'No Data';
}
export function setStatusText(status){
    if (status == '0') return 'Terdaftar'
    else if (status == '1') return 'Sudah Dibayar'
    else if (status == '2') return 'Terverifikasi'
    else if (status == '-1') return 'Koreksi Data'
    else return ''
  }