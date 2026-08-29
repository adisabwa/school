/**
 * Mendapatkan elemen DOM tunggal dari string selector atau elemen asli.
 */
export const getEl = (target) => {
  if (target instanceof HTMLElement)
    return target
  
  // console.log(target)
  // If it looks like an ID selector starting with a number
  if (target.startsWith('#') && !isNaN(target.charAt(1))) {
    return document.getElementById(target.substring(1));
  }
  
  return document.querySelector(target);
};

export const getElAll = (target) => {
  
  return document.querySelectorAll(target);
};

export const hasClass = (el, cls) => {
  const target = getEl(el);
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  return target ? target.classList.contains(...classes) : false;
};

export const addClass = (el, cls, delay = 0) => {
  const target = getElAll(el);
  // console.log(target, cls, target.classList)
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  // console.log(classes)
  if (target) {
    target.forEach(t => {
      setTimeout(() => t.classList.add(...classes), delay);
      // console.log('Class berhasil ditambahkan:', t.className);
    });
  }
};

export const removeClass = (el, cls, delay = 0) => {
  const target = getElAll(el);
  // console.log(target, cls, target.classList)
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  // console.log(classes)
  if (target) {
    target.forEach(t => {
      setTimeout(() => t.classList.remove(...classes), delay);
    })
  }
  // console.log(target)
};

export const toggleClass = (el, cls, delay = 0) => {
  const target = getElAll(el);
  const classes = cls.split(' ').filter(c => c.trim() !== '');
  if (target) {
    target.forEach(t => {
      setTimeout(() => t.classList.toggle(...classes), delay);
    })
  }
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

  let index = 0
  headers.forEach((th, index) => {
    let colspan = 1
    if (th.hasAttribute('colspan')) {
      colspan = parseInt(th.getAttribute('colspan'), 10);
    }
    if (th.classList.contains(cls) || !th.classList.contains(excludeCls)) {
      for (let i = 0; i < colspan; i++) {
        targetCols.push(index);
        index = index + i
      }
      th.remove();
    }
  });

  const rows = table.querySelectorAll("tbody tr");
  // console.log(targetCols)
  targetCols.sort((a, b) => b - a).forEach(index => {
    rows.forEach(row => {
      if (row.cells[index]) row.deleteCell(index);
    });
  });
};