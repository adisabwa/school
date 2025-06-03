function scrollTo(to, duration, onDone) {
  var start = window.scrollY,
      change = to - start,
      startTime = performance.now(),
      val, now, elapsed, t;
  // console.log(to, start, change)
  function animateScroll(){
      now = performance.now();
      elapsed = (now - startTime)/1000;
      t = (elapsed/duration);

      window.scrollTo(0, start + change * easeInOutQuad(t));

      if( t < 1 )
          window.requestAnimationFrame(animateScroll);
      else
          onDone && onDone();
  };

  animateScroll();
} 

function scrollToCoordinate(parent, coordinate, duration, scroll = 'left', rerun = false, onDone){
  let to = coordinate; 
  var start = scroll == 'left' ? parent.scrollLeft : parent.scrollTop,
    change = to - start,
    startTime = performance.now(),
    padding = 0,
    val, now, elapsed, t;

  // console.log(coordinate, parent.clientWidth, parent.scrollWidth)
  if ( (coordinate + parent.clientWidth) > parent.scrollWidth ) {
    // console.log('more than max');
    coordinate = parent.scrollWidth - parent.clientWidth
    // return;
  }

  let scrolling = false;
  let scrollTimer = -1;
  parent.addEventListener("mousewheel", (event) => {
    // console.log('scroll')
    scrolling = true;
    if (scrollTimer != -1)
      clearTimeout(scrollTimer);

    scrollTimer = window.setTimeout(function(){
      // console.log('scroll-end')
      scrolling = false;
      window.setTimeout(function(){
        if (rerun) {
          // console.log('re-run animation')
          recursive();
        }
      }, 500);
    }, 500);

  });

  function recursive(){
    scrollToCoordinate(parent, coordinate, duration, scroll, rerun, onDone)
  }

  let reqAnim = 0;
  let strTime = startTime
  
  function animateScroll(){
    now = performance.now();
    elapsed = (now - startTime)/1000;
    t = (elapsed/duration);

    let plus = change * easeInOutQuad(t) - padding
    // console.log(change, easeInOutQuad(t), padding )
    if (scroll == 'left') {
      parent.scrollLeft = start + plus;
      if (Math.abs(parent.scrollWidth - parent.clientWidth - parent.scrollLeft) < 1) {
        // t = 0;
        startTime = performance.now()
        parent.scrollLeft = to;
        t = 1;
      }
    }
    else {
      parent.scrollTop = start + plus;
      if (Math.abs(parent.scrollHeight - parent.clientHeight - parent.scrollTop) < 1) {
        // t = 0;
        startTime = performance.now()
        parent.scrollTop = to;
        t = 1;
        // console.log(parent.scrollTop, 'scrolled-bottom')
      }
    }

    let continueAnimating = true;
    if (scrolling)
      continueAnimating = false;


    // console.log(parent.scrollTop)
    if( t < 1 && continueAnimating) {
        reqAnim = window.requestAnimationFrame(animateScroll);
    } else {
        onDone && onDone();
    }
  };

  reqAnim = window.requestAnimationFrame(animateScroll);
};

function getRelativePos(elm){
var pPos = elm.parentNode.getBoundingClientRect(), // parent pos
    cPos = elm.getBoundingClientRect(), // target pos
    pos = {};

// console.log(cPos, pPos)
pos.top    = cPos.top    - pPos.top,
pos.right  = cPos.right  - pPos.right,
pos.bottom = cPos.bottom - pPos.bottom,
pos.left   = cPos.left   - pPos.left;

return pos;
}

function getOffsetWithinContainer($child, $container) {
  const childOffset = $child.offset();
  const containerOffset = $container.offset();

  return {
    top: childOffset.top - containerOffset.top + $container.scrollTop(),
    left: childOffset.left - containerOffset.left + $container.scrollLeft()
  };
}

function easeInOutQuad(t){ 
return t <.5 ? 2*t*t : -1+(4-2*t)*t 
};

MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

const observer = new MutationObserver(function(mutationList, observer) {
  for (const mutation of mutationList) {
    if (mutation.type === "childList") {
      console.log("A child node has been added or removed.");
    } else if (mutation.type === "attributes") {
      console.log(`The ${mutation.attributeName} attribute was modified.`);
    }
  }
});

let listFunction = {
  jquery(target){
    let $target;

    if (typeof target === 'string') {
      // Assume class name or selector string
      $target = jquery(target); // converts string selector to jQuery object
    } else if (target instanceof jQuery) {
      // Already a jQuery object
      $target = target;
    } else if (target instanceof Element) {
      // Raw DOM element
      $target = jquery(target);
    } else {
      console.warn('Unsupported target type');
      return this;
    }

    return $target
  },
  observeMutation(target, config = { attributes: true }){
    let targetNode = jquery(target)[0];
    observer.observe(targetNode, config);
  },
  getRelativePos(el){
    // console.log(el)
    el = jquery(el)[0];
    // console.log(el)
    if (el) {
      return getRelativePos(el)
    } 
    return null;
  },
  scrollToElement(el, duration = 2){
    el = jquery(el)[0];
    if (el) {
      var pos = getRelativePos(el);
      // console.log(pos)
      scrollTo(pos.top , duration); 
      // console.log(getRelativePos(el))
      // el.scrollIntoView({ behavior: "smooth" });
    }
  },
  scrollElement(parent, destination, duration = 2, scroll = 'left', rerun = false, onDone = null){
    parent = jquery(parent)[0];
    let el = jquery(parent).find(destination)[0];
    if (el) {
      var pos = getOffsetWithinContainer(jquery(el), jquery(parent));
      let coordinate = scroll == 'left' ? pos.left : pos.top;
      // console.log(parent.scrollTop, pos, coordinate)
      scrollToCoordinate(parent, coordinate, duration, scroll, rerun, onDone)
    }
  },
  scrollToCoordinate(element, coordinate, duration = 2, scroll = 'left', rerun = false, onDone = null){
    element = jquery(element)[0];
    // console.log(element, coordinate, scroll)
    scrollToCoordinate(element, coordinate, duration, scroll, rerun, onDone)
  },
  toggleClass(el, cls, delay = 0){
    // console.log(el)
    window.setTimeout(function () {
      return jquery(el).toggleClass(cls);
    }, delay);
  },
  addClass(el, cls, delay = 0){
    // console.log('add')
    setTimeout(function(){
      return jquery(el).addClass(cls);
    }, delay);
  },
  removeClass(el, cls, delay = 0){
    setTimeout(function(){
      // console.log('remove')
      return jquery(el).removeClass(cls);
    }, delay);
  },
  checkScroll(position) {
    var currentScrollPosition = window.scrollY
    let up = true;
    if(currentScrollPosition < position){
      up = true;
      //your desire logic 
    }
    else{
      up = false;
      //your desire logic 
    }

    return {up, currentScrollPosition};
  },
}

export { listFunction };

export default {
  install: (app) => {
    let keys = Object.keys(listFunction)
    for (var i = 0; i < keys.length; i++) {
      let ind = keys[i]
      app.config.globalProperties[ind] = listFunction[ind]
    }
  }
}