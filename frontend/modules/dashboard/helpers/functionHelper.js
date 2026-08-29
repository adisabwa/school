
export const getRoleLabel = (role) => {
  switch (role) {
    case 'admin':
      return 'Administrator';
    case 'guru':
      return 'Guru';
    case 'walas':
      return 'Wali Kelas';
    case 'wamar':
      return 'Wali Kamar';
    case 'ortu':
      return 'Orang Tua';
    case 'santri':
      return 'Santri';
    default:
      return 'Tamu';
  }
    
};