/**
 * Mendapatkan elemen DOM tunggal dari string selector atau elemen asli.
 */
export const getEl = (target) => {
  return target instanceof HTMLElement ? target : document.querySelector(target);
};

export const hasClass = (el, cls) => {
  const target = getEl(el);
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  return target ? target.classList.contains(...classes) : false;
};

export const addClass = (el, cls, delay = 0) => {
  const target = getEl(el);
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  if (target) setTimeout(() => target.classList.add(...classes), delay);
};

export const removeClass = (el, cls, delay = 0) => {
  const target = getEl(el);
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  if (target) setTimeout(() => target.classList.remove(...classes), delay);
};

export const toggleClass = (el, cls, delay = 0) => {
  const target = getEl(el);
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  if (target) setTimeout(() => target.classList.toggle(...classes), delay);
};

export const scrollToElement = (el, behavior = 'smooth') => {
  const target = getEl(el);
  if (target) target.scrollIntoView({ behavior });
};

/**
 * Scroll elemen parent ke koordinat tertentu.
 */
export const scrollToCoordinate = (element, coordinate, scroll = 'left', behavior = 'smooth') => {
  const target = getEl(element);
  if (target) {
    const options = scroll === 'left' 
      ? { left: coordinate, behavior } 
      : { top: coordinate, behavior };
    target.scrollTo(options);
  }
};

/**
 * Scroll elemen parent berdasarkan posisi elemen anak di dalamnya.
 */
export const scrollElement = (parent, destinationSelector, scroll = 'left', behavior = 'smooth') => {
  const parentEl = getEl(parent);
  const childEl = parentEl?.querySelector(destinationSelector);
  if (parentEl && childEl) {
    const coordinate = scroll === 'left' ? childEl.offsetLeft : childEl.offsetTop;
    scrollToCoordinate(parentEl, coordinate, scroll, behavior);
  }
};

/**
 * Mengecek arah scroll (Atas/Bawah).
 */
export const checkScroll = (lastPosition) => {
  const currentScrollPosition = window.scrollY;
  const up = currentScrollPosition < lastPosition;
  return { up, currentScrollPosition };
};

/**
 * Menghapus kolom tabel berdasarkan class tertentu.
 */
export const removeColumnByClass = (tbl, cls, excludeCls) => {
  const table = getEl(tbl);
  if (!table) return;

  const headers = Array.from(table.querySelectorAll("thead tr th"));
  const targetCols = [];

  headers.forEach((th, index) => {
    if (th.classList.contains(cls) || !th.classList.contains(excludeCls)) {
      targetCols.push(index);
      th.remove();
    }
  });

  const rows = table.querySelectorAll("tbody tr");
  targetCols.sort((a, b) => b - a).forEach(index => {
    rows.forEach(row => {
      if (row.cells[index]) row.deleteCell(index);
    });
  });
};